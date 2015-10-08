<?php
namespace SoftwareComponents;

use \Validator as Validator;
use \MongoDate;

use \Entities\File as File;
use \Entities\Unit as Unit;
use \Entities\Workerunit as Workerunit;
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
		$content["platform"] = $settings['platform'];
		$content["expirationInMinutes"] = 3;
		$content["reward"] = 0.02;
		$content["workerunitsPerUnit"] = $settings['judgmentsPerUnit'];
		$content["workerunitsPerWorker"] = 6;
		$content["unitsPerTask"] = 3;
		$content["title"] = $settings['filename'];
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
			$entity->documentType = "jobconf";
			$entity->project = $settings['project'];
			$entity->tags = array($settings['documentType']);
			$entity->type = $settings['documentType'];
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
	public function createJob($config, $activity, $batch, $settings)
	{
		
		$entity = new Job;
		$entity->_id = $entity->_id;
		$entity->batch_id = $batch->_id;
		$entity->project = $settings['project'];
		$entity->type = $settings['documentType'];
		$entity->resultType = $settings['resultType'];
		$entity->documentType = "job";
		$entity->completion = 1;
		$entity->expectedWorkerUnitsCount = 450;
		$entity->finishedAt = new MongoDate;
		$entity->jobConf_id = $config;
		$entity->platformJobId = $settings['filename'];
		$entity->projectedCost = 12.00;
		$entity->realCost = 11.97;
		$entity->runningTimeInSeconds = 190714;
		$entity->softwareAgent_id = $settings['platform'];
		$entity->startedAt = new MongoDate;
		$entity->status = "imported";
		$entity->template = "imported";
		$entity->activity_id = $activity;
		$entity->save();
		
		array_push($this->status['success'], "Job created (" . $entity->_id . ")");
		
		return $entity;
	}
	
	
	/**
	 * Create crowd agent
	 */
	public function createCrowdAgent($workerId, $country, $region, $city, $cfWorkerTrust, $settings)
	{
		$agent = CrowdAgent::where('_id', "crowdagent/".$settings['platform']."/" . $workerId)->first();
		if($agent) {
			// do not delete this on rollback
			if(!array_key_exists($agent->_id, $this->crowdAgents)) {
				$this->duplicateCrowdAgents++;
				$agent->_existing = true;			
				$this->crowdAgents[$agent->_id] = $agent;
			}
		} else {
			$agent = new CrowdAgent;
			$agent->_id= "crowdagent/".$settings['platform']."/".$workerId;
			$agent->softwareAgent_id= $settings['platform'];
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
	public function createWorkerUnit($activityId, $unitId, $acceptTime, $channel, $trust, $content, $agentId, $jobId, $annId, $submitTime, $settings) 
	{
		$workerunit = Workerunit::where('platformWorkerUnitId', $annId)->first();
		if($workerunit) {
			// do not delete this on rollback
			if(!array_key_exists($workerunit->_id, $this->workerUnits)) {
				$this->duplicateWorkerUnits++;
				$workerunit->_existing = true;
				$this->workerUnits[$workerunit->_id] = $workerunit;
			}
		} else {
			$workerunit = new Workerunit;
			$workerunit->activity_id = $activityId;			
			$workerunit->unit_id = $unitId;			
			$workerunit->acceptTime = $acceptTime;
			$workerunit->cfChannel = $channel;
			$workerunit->cfTrust = $trust;
			$workerunit->content = $content;
			$workerunit->crowdAgent_id = $agentId;
			$workerunit->job_id = $jobId;
			$workerunit->platformWorkerunitId = $annId;
			$workerunit->submitTime = $submitTime;
			$workerunit->type = $settings['documentType'];
			$workerunit->project = $settings['project'];
			$workerunit->softwareAgent_id = $settings['platform'];
			$workerunit->softwareAgent_id = 'cf2';
			$workerunit->contradiction = $settings['contradiction'];

			\Queue::push('Queues\SaveWorkerunit', array('workerunit' => serialize($workerunit)));		
			
			$this->workerUnits[$workerunit->_id] = $workerunit;
		}
	
		return $workerunit;
	}
	 
	 
	/**
	 * Create worker unit
	 */
	public function process($document, $settings)
	{
		// increase memory usage to support larger files with up to 10k judgments
		ini_set('memory_limit','256M');
		set_time_limit(0);
		
		$this->status = ['notice' => [], 'success' => [], 'error' => []];

		try {

			/*
			$workerUnits = Entity::where('documentType', 'workerunit')->select('_id')->get();
			foreach($workerUnits as $workerUnit) {
				$entity = Entity::where('_id', $workerUnit->_id)->first();
				
				$none = 0;
				$vector = $entity['annotationVector'];
				$vector['justification']['none'] = 0;
				if(array_sum($vector['justification']) == 0) {
					$none = 1;
				}
				$vector['justification']['none'] = $none;
				$entity['annotationVector'] = $vector;
				$entity->save();
			}
			*/
		
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
			$file = new File();
			$file->project = $settings['project'];
			$file->store($document, $settings, $activity);
			$file->save();

			// log status
			if($file->exists()) {
				array_push($this->status['notice'], "Existing file found (" . $file->_id . ")");
			} else {
				array_push($this->status['success'], "File created (" . $file->_id . ")");
			}

			// temporary mapping of unit ids to CrowdTruth unit ids
			$unitMap = [];
			
			
			// Detect if this is an AMT or CF file
			$column = [];
			if($data[0][0] == "HITId")
			{
				// AMT
				$settings['platform'] = 'AMT';
				$prefix = "Answer."; // Frefix for answer columns
				$startColumn = 27;
				$endColumn = count($data[0]);
				$column['submit_time'] = 18;
				$column['id'] = 14;
				$column['start_time'] = 17;
				$column['channel'] = 22; // empty
				$column['trust'] = 22; // empty
				$column['worker'] = 15;
				$column['country'] = 22; // empty			
				$column['region'] = 22; // empty			
				$column['city'] = 22; // empty
			} else {
				// CrowdFlower
				$settings['platform'] = 'CF'; 
				$prefix = "";
				$startColumn = 12;
				$endColumn = count($data[0]);				
				$column['submit_time'] = 1;
				$column['id'] = 2;
				$column['start_time'] = 3;
				$column['channel'] = 5;
				$column['trust'] = 6;
				$column['worker'] = 7;				
				$column['country'] = 8;				
				$column['region'] = 9;				
				$column['city'] = 10;				
			}			
			
			// Create Units
			$unitIds = array_keys(array_unique(array_column($data, 0)));

			for ($i = 1; $i < count($unitIds); $i ++) {
				
				// Temp mapping of files to document type structures. This should be done using the preprocessing functions
				
				// Sounds
				if($settings['documentType'] == 'sound') {
					$content = [
					'id' => $data[$unitIds[$i]][array_search('id',$data[0])],
					'preview-hq-mp3' => $data[$unitIds[$i]][array_search('preview-hq-mp3',$data[0])]
					];
				}

				$platform_id = $data[$unitIds[$i]][0];
				
				// Passage Alignment
				if($settings['documentType'] == 'passage_alignment') {
					$content = [
						'question' => [
							'id' => $data[$unitIds[$i]][array_search('id',$data[0])],
							'passage' => $data[$unitIds[$i]][array_search('question',$data[0])]
						],
						'answer' => [
							'id' => $data[$unitIds[$i]][array_search('id',$data[0])],
							'passage' => $data[$unitIds[$i]][array_search('passage',$data[0])]
						]];
				}
				
				// Passage Justification
				if($settings['documentType'] == 'passage_justification') {
					$content = [
						'question' => [
							'id' => $data[$unitIds[$i]][array_search('Input.ID',$data[0])],
							'passage' => $data[$unitIds[$i]][array_search('Input.Question',$data[0])]
						],
						'answers' => []
						];

					for($k = 1; $k <= 6; $k++) {
						if($data[$unitIds[$i]][array_search('Input.id'.$k,$data[0])] != "") {
							$content['answers'][$k] = [
								'id' => $data[$unitIds[$i]][array_search('Input.id'.$k,$data[0])],
								'passage' => $data[$unitIds[$i]][array_search('Input.Passage'.$k,$data[0])]
							];
						}
					}
						
				}
				
				

				$unit = new Unit();
				$unit->project = $settings['project'];
				$unit->activity_id = $activity->_id;
				$unit->documentType = $settings['documentType'];
				$unit->type = "unit";
				$unit->parents = [$file->_id];
				$unit->content = $content;
				$unit->hash = md5(serialize($content));
				$unit->platformId = $platform_id;
				$unit->save();
				
				$units[$unit->_id] = $unit;
				$unitMap[$data[$unitIds[$i]][0]] = $unit->_id;

			}

			// Create Batch
			$settings['units'] = array_keys($units);
			$settings['batch_title'] = "Imported batch";
			$settings['batch_description'] = "Batch added via result importer";
					
			$batch = Batch::store($settings, $activity);

			// Create job configuration
			$unitCount = count(array_unique(array_column($data, 0))) - 1;
			// Get number of judgments per unit
			$settings['judgmentsPerUnit'] = round((count($data)-1)/($unitCount));

			$jobconfig = $this->createJobconf($activity->id, $settings);
		
			// Create job
			$job = $this->createJob($jobconfig->_id, $activity->_id, $batch, $settings);
		
			// temp for sounds, create annotation vector for each unit			
			$annVector = [];
			$result = [];
			
			
			// passage alignment
			if($settings['documentType'] == 'passage_alignment') {
				for ($i = 1; $i < count($data); $i ++) {
					for ($j = 0; $j < 30; $j ++) {
				
						// for each passage get the tags
						if($data[$i][array_search('rel' . $j,$data[0])] != "") {
							$term1 = $data[$i][array_search('rel' . $j . 'a',$data[0])];
							$term2 = $data[$i][array_search('rel' . $j . 'b',$data[0])];
							$key = $term1 . ',' . $term2;
							// add keyword to list of keywords for this unit
							if(!isset($annVector[$data[$i][0]][$key])) {
								$annVector[$data[$i][0]][$key] = 0;
							}
						}
					}
				}
			}
			
			// Passage Justification
			if($settings['documentType'] == 'passage_justification') {
				$questionTypes = ['Subjective' => 0,'YesNo' => 0,'NotYesNo' => 0,'Unanswerable' => 0];
				$answers = ['Noanswer' => 0,'Yes' => 0,'No' => 0,'Other' => 0,'Unanswerable' => 0];
				
				
				for ($i = 1; $i < count($data); $i ++) {
				
				
					// add answer possibilities to hit
					$annVector[$data[$i][0]]['question'] = $questionTypes;
					$annVector[$data[$i][0]]['answer'] = $answers;

					// add existing passages to vector
					for($k = 1; $k <= 6; $k++) {
						if($data[$i][array_search('Input.id'.$k,$data[0])] != "") {
							$annVector[$data[$i][0]]['justification']['p'.$data[$i][array_search('Input.id'.$k,$data[0])]] = 0;
						}
					}
				}
			}

			// Sounds
			if($settings['documentType'] == 'sound') {
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
			}

			// loop through all the judgments and add workerUnits, media units and CrowdAgents.
			for ($i = 1; $i < count($data); $i++) {
			
				// loop through all values in the file, and add them as content
				$content = [];
				for($c = $startColumn; $c < $endColumn; $c++) {
					$key = str_replace('.','_',$data[0][$c]);
					$content[$key] = $data[$i][$c];
				}
			
				$trust = 1;
				
				$vector = $annVector[$data[$i][0]];
				$settings['contradiction'] = 0;
									


				// Create CrowdAgent
				$crowdAgent = $this->createCrowdAgent($data[$i][$column['worker']], $data[$i][$column['country']], $data[$i][$column['region']], $data[$i][$column['city']], $trust, $settings);
				
				// Create WorkerUnit
				$workerUnit = $this->createWorkerUnit($activity->_id, $unitMap[$data[$i][0]], $data[$i][$column['start_time']], $data[$i][$column['channel']], $trust, $content, $crowdAgent->_id, $job->_id, $data[$i][$column['id']], $data[$i][$column['submit_time']], $settings);
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

			
			$job_id = 'entity/text/opendomain/job/94';
			
			$job = Job::where('_id', $job_id)->first();
			
			// metrics
			$template = 'entity/text/medical/FactSpan/Factor_Span/0';
			exec('C:\Users\IBM_ADMIN\AppData\Local\Enthought\Canopy\User\python.exe ' . base_path()  . '/app/lib/generateMetrics.py '.$job->_id.' '.$template, $output, $error);
			$job->JobConfiguration->replicate();
			
			// save metrics in the job
			$response = json_decode($output[0],true);
			$job->metrics = $response['metrics'];
			$r = $job->results;
			$r['withoutSpam'] = $response['results']['withoutSpam'];
			$job->results = $r;
			$job->save();

			
	
			$jobs = Job::select('_id')->get();
			
			foreach($jobs as $jobId) {
			
				$job = Job::where('_id', $jobId->_id)->first();
				
				// update job cache
				\Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
			
			}
			
*/
			
			// update job cache
			\Queue::push('Queues\UpdateJob', array('job' => serialize($job)));

			
			// Notice that units already existed in the database
			if($this->duplicateUnits > 0) { array_push($this->status['notice'], "Existing units found (" . $this->duplicateUnits . ")"); }
			if($this->duplicateCrowdAgents > 0) { array_push($this->status['notice'], "Existing crowd agents found (" . $this->duplicateCrowdAgents . ")"); }
			if($this->duplicateWorkerUnits > 0) { array_push($this->status['notice'], "Existing judgements found (" . $this->duplicateWorkerUnits . ")"); }



			
			// Job's done!
			array_push($this->status['success'], "Successfully imported " . $settings['filename'] . "");
			array_push($this->status['success'], "Logged activities as " . $activity->_id . "");
			
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
