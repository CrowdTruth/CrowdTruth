<?php
namespace SoftwareComponents;

use \Validator as Validator;
use \MongoDate;

use \Entities\File as File;
use \Entities\Unit as Unit;
use \Entities\Batch as Batch;
use \Entities\Job as Job;
use \Entities\JobConfiguration as JobConfiguration;

use SoftwareAgent, CrowdAgent, Activity, Entity;

class ResultImporter {
	protected $softwareComponent;
	
	public function __construct() {
		if(!SoftwareAgent::find('resultimporter'))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "resultimporter";
			$softwareAgent->label = "This component adds existing results from a file";
			$softwareAgent->save();
		}
	}

	/**
	 *	Create result importer activity
	 */
	public function createActivity()
	{
		$activity = new Activity;
		$activity->softwareAgent_id = "resultimporter";
		$activity->save();

		return $activity;
	}
	
	
	/**
	 *	Translate csv file into array
	 */
	public function readCSV($file)
	{
		$data = [];
		$handle = fopen($file, 'r');
		while (($line = fgetcsv($handle)) !== FALSE) {
			array_push($data, $line);
		}
		fclose($handle);
		
		return $data;
	}
	
	
	/**
	 *	Create job configuration
	 */
	public function createJobconf($activity, $settings)
	{
		$content = array();
		$content["type"] = "virtual";
		$content["platform"] = array('cf');
		$content["expirationInMinutes"] = 3;
		$content["reward"] = 0.02;
		$content["workerunitsPerUnit"] = $settings['judgmentsPerUnit'];
		$content["workerunitsPerWorker"] = 6;
		$content["unitsPerTask"] = 3;
		$content["title"] = "Annotate the sounds";
		$content["description"] = "N/A";
		$content["keywords"] = "sound annotation";
		$content["instructions"] = "";

		$hash = md5(serialize([$content]));
		
		$entity = JobConfiguration::withTrashed()->where('hash', $hash)->first();
		// check if file already exists
		if($entity) {
			// do not delete this on rollback
			$entity->_existing = true;
			array_push($this->status['notice'], "Existing job configuration found (" . $entity->_id . ")");
		} else {
			$entity = new Entity;
			$entity->domain = "sound";
			$entity->format = "text";
			$entity->documentType = "jobconf";
			$entity->tags = array("sound");
			$entity->type = "sound";
			$entity->content = $content;
			$entity->hash = $hash;
			$entity->activity_id = $activity;  
			$entity->save();
		
			array_push($this->status['success'], "Job configuration created (" . $entity->_id . ")");
		}
		
		return $entity;
	}


		
	/**
	 *	Create job entity
	 * 	Note: the job does not get a platformJobId because this is unknown
	 */
	public function createJob($config, $activity, $batch, $jobid)
	{
		
		$entity = new Job;
		$entity->_id = $entity->_id;
		$entity->batch_id = $batch->_id;
		$entity->domain = "sound";
		$entity->format = "text";
		$entity->type = "sound";
		$entity->documentType = "job";
		$entity->completion = 1;
		$entity->expectedWorkerUnitsCount = 450;
		$entity->finishedAt = new MongoDate;
		$entity->jobConf_id = $config;
		$entity->platformJobId = $jobid;
		$entity->projectedCost = 12.00;
		$entity->realCost = 11.97;
		$entity->runningTimeInSeconds = 190714;
		$entity->softwareAgent_id = "cf";
		$entity->startedAt = new MongoDate;
		$entity->status = "imported";
		$entity->template = "text/sound/sound_annotation";
		$entity->activity_id = $activity;
		$entity->save();
		
		array_push($this->status['success'], "Job created (" . $entity->_id . ")");
		
		return $entity;

	}
	
	
	/**
	 * Create crowd agent
	 */
	public function createCrowdAgent($workerId, $country, $region, $city, $cfWorkerTrust)
	{
		$agent = CrowdAgent::where('_id', "crowdagent/cf/" . $workerId)->first();
		if($agent) {
			// do not delete this on rollback
			if(!array_key_exists($agent->_id, $this->crowdAgents)) {
				$this->duplicateCrowdAgents++;
				$agent->_existing = true;			
				$this->crowdAgents[$agent->_id] = $agent;
			}
		} else {
			$agent = new CrowdAgent;
			$agent->_id= "crowdagent/cf/$workerId";
			$agent->softwareAgent_id= "cf";
			$agent->platformAgentId = $workerId;
			$agent->city = $city;
			$agent->country = $country;
			$agent->region = $region;
			$agent->cfWorkerTrust = $cfWorkerTrust;
			$agent->save();
			
			$this->crowdAgents[$agent->_id] = $agent;
		}
		
		return $agent;
	}
	
	/**
	 * Create worker unit
	 */
	public function createWorkerUnit($activityId, $unitId, $acceptTime, $channel, $trust, $content, $agentId, $annVector, $jobId, $annId, $submitTime) 
	{
		$entity = Entity::where('platformWorkerUnitId', $annId)->first();
		if($entity) {
			// do not delete this on rollback
			if(!array_key_exists($entity->_id, $this->workerUnits)) {
				$this->duplicateWorkerUnits++;
				$entity->_existing = true;
				$this->workerUnits[$entity->_id] = $entity;
			}
		} else {
			$entity = new Entity;
			$entity->_id = $entity->_id;
			$entity->domain = "sounds";
			$entity->format = "text";
			$entity->documentType = "workerunit";
			$entity->activity_id = $activityId; 
			$entity->acceptTime = $acceptTime;
			$entity->cfChannel = $channel;
			$entity->type = "sounds";
			$entity->cfTrust = $trust;
			$entity->content = $content;
			$entity->crowdAgent_id = $agentId;
			$entity->annotationVector = $annVector;
			$entity->job_id = $jobId;
			$entity->platformWorkerUnitId = $annId;
			$entity->softwareAgent_id = "cf";
			$entity->spam = false;
			$entity->submitTime = $submitTime;
			$entity->unit_id = $unitId;
			$entity->save();
			
			$this->workerUnits[$entity->_id] = $entity;
		}
	
		return $entity;
	}
	 
	 
	/**
	 * Create worker unit
	 */
	public function process($document, $settings)
	{	
		// increase memory usage to support larger files with up to 10k judgments
		ini_set('memory_limit','256M');
		
		$this->status = ['notice' => [], 'success' => [], 'error' => []];

		try {
		
			$settings['units'] = [];
			
			// keep a list of all unique units, crowdAgents and workerUnits so that we can rollback only the unique ones on error
			$units = [];
			$this->crowdAgents = [];
			$this->workerUnits = [];
			$this->duplicateUnits = 0;
			$this->duplicateCrowdAgents = 0;
			$this->duplicateWorkerUnits = 0;
		
			// read document content and put it into an array
			$data = $this->readCSV($document);
		
			// Create activity
			$activity = $this->createActivity();
			
			// Create input file
			$file = File::store($document, $settings['project'], $activity);

			// log status
			if($file->exists()) {
				array_push($this->status['notice'], "Existing file found (" . $file->_id . ")");
			} else {
				array_push($this->status['success'], "File created (" . $file->_id . ")");
			}

			// temporary mapping of CF unit ids to CrowdTruth unit ids
			$unitMap = [];
			
			// Create Units
			$unitIds = array_keys(array_unique(array_column($data, 0)));
			for ($i = 1; $i < count($unitIds); $i ++) {
				$content = ['id' => $data[$unitIds[$i]][array_search('id',$data[0])], 'preview-hq-mp3' => $data[$unitIds[$i]][array_search('preview-hq-mp3',$data[0])]];
				$unit = Unit::store($settings['inputType'], $file->_id, $content, $settings['project'], $activity);
				$units[$unit->_id] = $unit;
				$unitMap[$data[$unitIds[$i]][0]] = $unit->_id;
			}


			// Create Batch
			$settings['units'] = array_keys($units);
			$settings['batch_title'] = "Imported batch";
			$settings['batch_description'] = "Batch added via result importer";
					
			$batch = Batch::store($settings, $activity);

			// Create job configuration
			$unitCount = count(array_unique(array_column($data, array_search('id',$data[0])))) - 1;
			$settings['judgmentsPerUnit'] = 10;

			$jobconfig = $this->createJobconf($activity->id, $settings);
		
			// Create job
			$job = $this->createJob($jobconfig->_id, $activity->_id, $batch, $settings['filename']);
		
		
			// temp for sounds, create annotation vector for each unit			
			$annVector = [];
			$result = [];
			for ($i = 1; $i < count($data); $i ++) {
		
				// for each keywords
				$keywords = explode(',', $data[$i][array_search('keywords',$data[0])]);
				foreach($keywords as $keyword) {
					$keyword = trim(strtolower(str_replace('.', '', $keyword)));
					if($keyword != "") {
						// add keyword to list of keywords for this unit
						if(!isset($annVector[$data[$i][0]][$keyword])) {
							$annVector[$data[$i][0]][$keyword] = 0;
						}
					}
				}
				$result[$unitMap[$data[$i][0]]] = ['keywords' => $annVector[$data[$i][0]]];
			}

			// loop through all the judgments and add workerUnits, media units and CrowdAgents.
			for ($i = 1; $i < count($data); $i ++) {
			
				// Get unit id
				$content = ['id' => $data[$i][array_search('id',$data[0])], 'preview-hq-mp3' => $data[$i][array_search('preview-hq-mp3',$data[0])]];
				$unit = Unit::store($settings['inputType'], $file->_id, $content, $settings['project'], $activity);
			
				// Create CrowdAgent
				$crowdAgent = $this->createCrowdAgent($data[$i][7], $data[$i][8], $data[$i][9], $data[$i][10], $data[$i][6]);
				
				// Create workerUnit
				$content = ['keywords' => trim(strtolower(str_replace('.', '', $data[$i][array_search('keywords',$data[0])])))];
				$vector = $annVector[$data[$i][0]];
				
				
				// for each keywords
				$keywords = explode(',', $content['keywords']);
				foreach($keywords as $keyword) {
					$keyword = trim($keyword);
					if($keyword != "") {
						$vector[$keyword]++;
					}
				}

				$workerUnit = $this->createWorkerUnit($activity->_id, $unit->_id, $data[$i][3], $data[$i][5], $data[$i][6], $content, $crowdAgent->_id, ['keywords' => $vector], $job->_id, $data[$i][2], $data[$i][1]);
			}	
			

			/*
			// aggregate all results	
			$result = array();
			$annotations = Entity::where("documentType", "=", "workerunit")->where("job_id", "=", $job->_id)->get();
			$count = 0;
			foreach($annotations as $workerUnit){
			   $uid = $workerUnit->unit_id; // to prevent mongoException: zero length key not allowed. Could also 'continue;'
			   if(empty($uid)) $uid = 'unknown';
				   else $count++;

			   if(!isset($result[$uid]))
				   $result[$uid] = $workerUnit->annotationVector;
			   else {
				   foreach($workerUnit->annotationVector as $key=>$val){
					   if(is_array($val)){ // term1 -> [k] -> 1
						   foreach($val as $k=>$v){
							   //if(isset($result[$uid][$key][$k]))
								   $result[$uid][$key][$k]+=$v;
							   //else $result[$uid][$key][$k]=$v; // THIS SHOULDN'T HAPPEN
						   }
					   } else {            // [key] -> 1
						   //if(isset($result[$uid][$key]))
							   $result[$uid][$key]+=$val;
						   //else $result[$uid][$key]=$val; // THIS SHOULDN'T HAPPEN
					   }
				   }
			   }
			}
			

			if(!isset($job->results)){
			   $job->results = array('withSpam' => $result);
			} else {
			   $r = $job->results;
			   $r['withSpam'] = $result;
			   $job->results = $r;
			}
			$job->update();
			
			// metrics
			$template = 'entity/text/medical/FactSpan/Factor_Span/0';
			exec('C:\Users\Benjamin\AppData\Local\Enthought\Canopy\User\python.exe ' . base_path()  . '/app/lib/generateMetrics.py '.$job->_id.' '.$template, $output, $error);
			$job->JobConfiguration->replicate();
			
			// save metrics in the job
			$response = json_decode($output[0],true);
			$job->metrics = $response['metrics'];
			$r = $job->results;
			$r['withoutSpam'] = $response['results']['withoutSpam'];
			$job->results = $r;
			$job->save();
			*/
			
			// update job cache
			\Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
	
			
			// update workerunits
			foreach ($this->workerUnits as $workerunit) {
				set_time_limit(60);
				\Queue::push('Queues\UpdateWorkerunits', array('workerunit' => serialize($workerunit)));
			}

			// update worker cache
			foreach ($this->crowdAgents as $worker) {
				set_time_limit(60);
				\Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($worker)));
			}
			
			// update units
			\Queue::push('Queues\UpdateUnits', $settings['units']);
			
			// Notice that units already existed in the database
			if($this->duplicateUnits > 0) { array_push($this->status['notice'], "Existing units found (" . $this->duplicateUnits . ")"); }
			if($this->duplicateCrowdAgents > 0) { array_push($this->status['notice'], "Existing crowd agents found (" . $this->duplicateCrowdAgents . ")"); }
			if($this->duplicateWorkerUnits > 0) { array_push($this->status['notice'], "Existing judgements found (" . $this->duplicateWorkerUnits . ")"); }



			
			// Job's done!
			array_push($this->status['success'], "Import finished successfully (" . $activity->_id . ")");

			return $this->status;
			
		} catch (Exception $e) {
		
			$activity->forceDelete();
			
			if(!$file->exists()) {
				$file->forceDelete();
			}
			
			if(!$jobconfig->_existing){
				$jobconfig->forceDelete();
			}
			$job->forceDelete();
			
			foreach($this->units as $unit) {
				if(!$unit->exists()) {
					$jobconfig->forceDelete();
				}
			}
			
			foreach($this->crowdAgents as $crowdAgent) {
				if(!$crowdAgent->_existing) {
					$crowdAgent->forceDelete();
				}
			}
			
			foreach($this->workerUnits as $workerUnit) {
				$workerUnit->forceDelete();
			}
			
			return $e;
		}
	}
}
