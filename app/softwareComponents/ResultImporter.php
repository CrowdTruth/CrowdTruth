<?php
namespace SoftwareComponents;

use \Validator as Validator;
use \File as File;
use \MongoDate;

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
		
		if(!SoftwareComponent::find('resultimporter'))
		{
			$SoftwareComponent = new SoftwareComponent;
			$SoftwareComponent->_id = "resultimporter";
			$SoftwareComponent->label = "This component adds existing results from a file";
			$SoftwareComponent->save();
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
	 *	Create job entity
	 * 	Note: the job does not get a platformJobId because this is unknown
	 */
	public function createJob($config, $activity)
	{
		
		$entity = new Job;
		$entity->_id = $entity->_id;
		$entity->domain = "sound";
		$entity->format = "text";
		$entity->documentType = "job";
		$entity->type = "sound";
		$entity->workerUnitsCount = 450;
		$entity->batch_id = "entity/sound/sound/batch/1";
		$entity->completion = 1;
		$entity->expectedWorkerUnitsCount = 450;
		$entity->finishedAt = new MongoDate;
		$entity->jobConf_id = $config;
		$entity->projectedCost = 12.00;
		$entity->realCost = 11.97;
		$entity->runningTimeInSeconds = 190714;
		$entity->softwareAgent_id = "cf";
		$entity->startedAt = new MongoDate;
		$entity->status = "imported";
		$entity->template = "text/sound/sound_annotation";
		$entity->unitsCount = 30;
		$entity->workersCount = 72;
		$entity->activity_id = $activity;
		$entity->save();
		
		array_push($this->status['success'], "Job created (" . $entity->_id . ")");
		
		return $entity;

	}

	
	/**
	 *	Create input file entity
	 */
	public function createInputFile($file, $activity, $project)
	{
		$content = File::get($file->getRealPath());
		$hash = md5(serialize([$content]));
		
		$entity = Entity::withTrashed()->where('hash', $hash)->first();
		// check if file already exists
		if($entity)
		{
			// throw exception for now, as we dont want readding processed files
			throw new \Exception("This file already exists");
		
			// do not delete this on rollback
			$entity->_existing = true;
			array_push($this->status['notice'], "Existing file found (" . $entity->_id . ")");
		} else {
			$entity = new Entity;
			$entity->_id = $entity->_id;
			$entity->title = strtolower($file->getClientOriginalName());
			$entity->domain = 'sound';
			$entity->format = "text";
			$entity->documentType = 'file';
			$entity->content = $content;
			$entity->hash = $hash;
			$entity->activity_id = $activity;
			$entity->project = $project;
			$entity->tags = [ "unit" ];
			$entity->save();
			
			array_push($this->status['success'], "File created (" . $entity->_id . ")");
		}
		return $entity;
	}

	/**
	 *	Create unit
	 */
	public function createUnit($docType, $parent, $content, $activity, $project)
	{
		$hash = md5(serialize([$content]));
		
		$entity = Entity::withTrashed()->where('hash', $hash)->first();
		
		// check if unit already exists
		if($entity) {
			// check if already in this job
			if(!array_key_exists($entity->_id, $this->units)) {
				$this->duplicateUnits++;
				// do not delete this on rollback
				$entity->_existing = true;
				
				$this->units[$entity->_id] = $entity;
			}
		} else {
			$entity = new Entity;
			$entity->_id = $entity->_id;
			$entity->domain = 'sound';
			$entity->format = "text";
			$entity->documentType = $docType;
			$entity->parents = [$parent];
			$entity->content = $content;
			$entity->hash = $hash;
			$entity->activity_id = $activity;
			$entity->project = $project;
			$entity->tags = [ "unit" ];
			$entity->save();
			
			$this->units[$entity->_id] = $entity;
		}
		return $entity;
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
		
		$entity = Entity::withTrashed()->where('hash', $hash)->first();
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
	public function process($file, $settings)
	{	
		$this->status = ['notice' => [], 'success' => [], 'error' => []];

		try {
			
			// keep a list of all unique units, crowdAgents and workerUnits so that we can rollback only the unique ones on error
			$this->units = [];
			$this->crowdAgents = [];
			$this->workerUnits = [];
			$this->duplicateUnits = 0;
			$this->duplicateCrowdAgents = 0;
			$this->duplicateWorkerUnits = 0;
		
			// read file content and put it into an array
			$data = $this->readCSV($file);
		
			// Create activity
			$activity = $this->createActivity();
			
			// Create input file
			$inputFile = $this->createInputFile($file, $activity->id, $settings['project']);
			
			$unitCount = count(array_unique(array_column($data, 42))) - 1;
			$settings['judgmentsPerUnit'] = 30;
			
			// Create job configuration
			$jobconfig = $this->createJobconf($activity->id, $settings);
			
			// Create job
			$job = $this->createJob($jobconfig->_id, $activity->id);
		
			// temp for sounds, create annotation vector for each unit			
			$annVector = [];
			for ($i = 1; $i < count($data); $i ++) {
		
				// for each keywords
				$keywords = explode(',', $data[$i][35]);
				foreach($keywords as $keyword) {
					$keyword = trim(strtolower(str_replace('.', '', $keyword)));
					if($keyword != "") {
						// add keyword to list of keywords for this unit
						if(!isset($annVector[$data[$i][0]][$keyword])) {
							$annVector[$data[$i][0]][$keyword] = 0;
						}
					}
				}
			}

			// loop through all the judgments and add workerUnits, media units and CrowdAgents.
			for ($i = 1; $i < count($data); $i ++) {
				
				// try to add unit
				$content = ['id' => $data[$i][42], 'preview-hq-mp3' => $data[$i][46]];
				$unit = $this->createUnit($settings['inputType'], $inputFile->_id, $content, $activity->_id, $settings['project']);
				
				// Create CrowdAgent
				$crowdAgent = $this->createCrowdAgent($data[$i][7], $data[$i][8], $data[$i][9], $data[$i][10], $data[$i][6]);
				
				// Create workerUnit
				$content = ['keywords' => trim(strtolower(str_replace('.', '', $data[$i][35])))];
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
			//exec('C:\Users\IBM_ADMIN\AppData\Local\Enthought\Canopy\User\python.exe ' . base_path()  . '/app/lib/generateMetrics.py '.$job->_id.' '.$template, $output, $error);
			//$job->JobConfiguration->replicate();
			//dd($job->jobConfiguration->content['workerunitsPerUnit']);
//			\Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
	
			// update worker cache
			foreach ($this->crowdAgents as $worker) {
				set_time_limit(30);
				\Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($worker)));
			}
			
			// update units
//			foreach ($this->units as $unit) {
//				set_time_limit(30);
//				\Queue::push('Queues\UpdateUnits', array('unit' => serialize($unit)));
//			}

			
			// Notice that units already existed in the database
			if($this->duplicateUnits > 0) { array_push($this->status['notice'], "Existing media units found (" . $this->duplicateUnits . ")"); }
			if($this->duplicateCrowdAgents > 0) { array_push($this->status['notice'], "Existing crowd agents found (" . $this->duplicateCrowdAgents . ")"); }
			if($this->duplicateWorkerUnits > 0) { array_push($this->status['notice'], "Existing judgements found (" . $this->duplicateWorkerUnits . ")"); }



			
			// Job's done!
			array_push($this->status['success'], "Import finished successfully (" . $activity->_id . ")");

			return $this->status;
			
		} catch (Exception $e) {
		
			$activity->forceDelete();
			
			if(!$inputFile->_existing) {
				$inputFile->forceDelete();
			}
			
			if(!$jobconfig->_existing) {
				$jobconfig->forceDelete();
			}
			$job->forceDelete();
			
			foreach($this->units as $unit) {
				if(!$unit->_existing) {
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
