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
		$content["workerunitsPerWorker"] = 10;
		$content["unitsPerTask"] = 1;
		$content["title"] = $settings['filename'];
		$content["description"] = $settings["description"];
		$content["keywords"] = $settings["keywords"];
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
			$entity->type = "jobconf";
			$entity->project = $settings['project'];
			$entity->tags = array($settings['documentType']);
			$entity->documentType = $settings['documentType'];
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
		$entity->documentType = $settings['documentType'];
		$entity->templateType = $settings['templateType'];
		$entity->resultType = $settings['resultType'];
		$entity->type = "job";
		$entity->completion = 1;
		$entity->expectedWorkerUnitsCount = 450;
		$entity->finishedAt = new MongoDate;
		$entity->jobConf_id = $config;
		$entity->platformJobId = $settings['platformJobId'];
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
			$workerunit->documentType = $settings['documentType'];
			$workerunit->templateType = $settings['templateType'];
			$workerunit->project = $settings['project'];
			$workerunit->softwareAgent_id = $settings['platform'];
			$workerunit->softwareAgent_id = 'CF';
		//	$workerunit->contradiction = $settings['contradiction'];

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
		//	dd($data);
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
		//	dd($unitIds);

			for ($i = 1; $i < count($unitIds); $i ++) {
				
				// Temp mapping of files to document type structures. This should be done using the preprocessing functions
				
				// extracting event from video synopsys
				if($settings['project'] == 'openimages' && $settings['documentType'] == 'video-synopsis') {
					$content = [
						'id' => $data[$unitIds[$i]][array_search('id',$data[0])],
						'ct_id' => $data[$unitIds[$i]][array_search('uid',$data[0])],
						'description' => $data[$unitIds[$i]][array_search('description',$data[0])]
					];
					$settings["keywords"] = "event extraction";
					$settings["description"] = "event extraction in video synopsis";
					$settings["templateType"] = "MetaDEvents";
					$settings["platformJobId"] = substr($settings['filename'], 1);
				}

				if($settings['project'] == 'openimages' && $settings['documentType'] == 'time-enriched-synopsis') {
					$content = [
						'time' => $data[$unitIds[$i]][array_search('time',$data[0])],
						'timeCount' => $data[$unitIds[$i]][array_search('timeCount',$data[0])],
						'description' => $data[$unitIds[$i]][array_search('description',$data[0])],
						'events' => $data[$unitIds[$i]][array_search('events',$data[0])]
					];
					$settings["keywords"] = "link events with time";
					$settings["description"] = "link events with their time period";
					$settings["templateType"] = "LinkEventsTime";
					$settings["platformJobId"] = substr($settings['filename'], 1);
					//dd($content);
				}

				if($settings['project'] == 'openimages' && $settings['documentType'] == 'people-enriched-synopsis') {
					$content = [
						'people' => $data[$unitIds[$i]][array_search('people',$data[0])],
						'peopleCount' => $data[$unitIds[$i]][array_search('peopleCount',$data[0])],
						'description' => $data[$unitIds[$i]][array_search('description',$data[0])],
						'events' => $data[$unitIds[$i]][array_search('events',$data[0])]
					];
					$settings["keywords"] = "link events with participants";
					$settings["description"] = "link events with their participants";
					$settings["templateType"] = "LinkEventsParticipants";
					$settings["platformJobId"] = substr($settings['filename'], 1);
					//dd($content);
				}

				if($settings['project'] == 'openimages' && $settings['documentType'] == 'location-enriched-synopsis') {
					$content = [
						'location' => $data[$unitIds[$i]][array_search('location',$data[0])],
						'locationCount' => $data[$unitIds[$i]][array_search('locationCount',$data[0])],
						'description' => $data[$unitIds[$i]][array_search('description',$data[0])],
						'events' => $data[$unitIds[$i]][array_search('events',$data[0])]
					];
					$settings["keywords"] = "link events with locations";
					$settings["description"] = "link events with their locations";
					$settings["templateType"] = "LinkEventsLocation";
					$settings["platformJobId"] = substr($settings['filename'], 1);
					//dd($content);
				}
				// Sounds
				if($settings['project'] == 'Sounds' && $settings['documentType'] == 'sound') {
					$content = [
					'id' => $data[$unitIds[$i]][array_search('id',$data[0])],
					'preview-hq-mp3' => $data[$unitIds[$i]][array_search('preview-hq-mp3',$data[0])]
					];
					$settings["keywords"] = "sound annotation";
					$settings["description"] = "semantic annotation of sounds";
					$settings["templateType"] = "sound";
					$settings["platformJobId"] = $settings['filename'];
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


				// Passage Alignment
				if($settings['project'] == 'Quantum' && $settings['documentType'] == 'sound') {
					$content = [
						'id' => $data[$unitIds[$i]][array_search('id',$data[0])],
						'sound1' => [
							'id' => $data[$unitIds[$i]][array_search('s1_id',$data[0])],
							'name' => $data[$unitIds[$i]][array_search('s1_name',$data[0])],
							'description' => $data[$unitIds[$i]][array_search('s1_description',$data[0])],
							'duration' => $data[$unitIds[$i]][array_search('s1_duration',$data[0])],
							'url' => $data[$unitIds[$i]][array_search('s1_url',$data[0])]
						],
						'sound2' => [
							'id' => $data[$unitIds[$i]][array_search('s2_id',$data[0])],
							'name' => $data[$unitIds[$i]][array_search('s2_name',$data[0])],
							'description' => $data[$unitIds[$i]][array_search('s2_description',$data[0])],
							'duration' => $data[$unitIds[$i]][array_search('s2_duration',$data[0])],
							'url' => $data[$unitIds[$i]][array_search('s2_url',$data[0])]
						]
					];
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
			$result = [];

			// loop through all the judgments and add workerUnits, media units and CrowdAgents.
			for ($i = 1; $i < count($data); $i++) {
			
				// loop through all values in the file, and add them as content
				$content = [];
				for($c = $startColumn; $c < $endColumn; $c++) {
					$key = str_replace('.','_',$data[0][$c]);
					$content[$key] = $data[$i][$c];
				}
			
				$trust = 1;
				
				// Create CrowdAgent
				$crowdAgent = $this->createCrowdAgent($data[$i][$column['worker']], $data[$i][$column['country']], $data[$i][$column['region']], $data[$i][$column['city']], $trust, $settings);
				
				// Create WorkerUnit
				$workerUnit = $this->createWorkerUnit($activity->_id, $unitMap[$data[$i][0]], $data[$i][$column['start_time']], $data[$i][$column['channel']], $trust, $content, $crowdAgent->_id, $job->_id, $data[$i][$column['id']], $data[$i][$column['submit_time']], $settings);
			}	
				
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
