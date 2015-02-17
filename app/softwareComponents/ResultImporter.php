<?php
namespace SoftwareComponents;

use \MongoDB\Activity as Activity;
use \MongoDB\Entity as Entity;
use \MongoDB\CrowdAgent as CrowdAgent;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\SoftwareComponent as SoftwareComponent;

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
	 */
	public function createJob($config, $activity)
	{
		
		$entity = new Entity;
		$entity->_id = $entity->_id;
		$entity->domain = "cultural";
		$entity->format = "text";
		$entity->documentType = "job";
		$entity->type = "VideoDescrHighlighting_";
		$entity->workerUnitsCount = 450;
		$entity->batch_id = "entity/video/cultural/batch/1";
		$entity->completion = 1;
		$entity->expectedWorkerUnitsCount = 450;
		$entity->finishedAt = new MongoDate;
		$entity->jobConf_id = $config;
		//$entity->platformJobId = $jobId;
		$entity->projectedCost = 12.00;
		$entity->realCost = 11.97;
		$entity->runningTimeInSeconds = 190714;
		$entity->softwareAgent_id = "cf";
		$entity->startedAt = new MongoDate;
		$entity->status = "finished";
		$entity->template = "text/VideoDescr/VideoDescrHighlighting_v2_";
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
			array_push($this->status['notice'], "Existing file found (" . $entity->_id . ")");
		} else {
			$entity = new Entity;
			$entity->_id = $entity->_id;
			$entity->title = strtolower($file->getClientOriginalName());
			$entity->domain = 'none';
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
			$this->duplicateUnits++;
		} else {
			$entity = new Entity;
			$entity->_id = $entity->_id;
			$entity->domain = 'none';
			$entity->format = "text";
			$entity->documentType = $docType;
			$entity->parents = [$parent];
			$entity->content = $content;
			$entity->hash = $hash;
			$entity->activity_id = $activity;
			$entity->project = $project;
			$entity->tags = [ "unit" ];
			$entity->save();
		}
		return $entity;
	}
	
	
	
	/**
	 *	Create job configuration
	 */
	public function createJobconf($activity)
	{
		$content = array();
		$content["type"] = "virtual";
		$content["platform"] = array('cf');
		$content["expirationInMinutes"] = 3;
		$content["reward"] = 0.02;
		$content["workerunitsPerUnit"] = 15;
		$content["workerunitsPerWorker"] = 10;
		$content["unitsPerTask"] = 7;
		$content["title"] = "Tag events and event-related concepts (Dutch language required)";
		$content["description"] = "N/A";
		$content["keywords"] = "event tagging, event-related concepts tagging, text annotation";
		$content["instructions"] = "Perform the two steps in the following TEXT: \n\n STEP1: Highlight the PHRASES (i.e. SINGLE or MULTIPLE-word phrases) that refer to EVENTS, event LOCATION, event TIME, event PARTICIPANTS, or OTHER event-related concepts. \n\n OBS: Please do not highlight time concepts that clearly represent SHOTS timings (e.g. 00:54). \n\n STEP2.: Select a corresponding TYPE for each selected PHRASE in STEP 1: Event, Location, Time, Participants, Other. \n\n To highlight a SINGLE word click on it in text. \n\n To highlight a MULTIPLE-WORD PHRASE drag your cursor across the range of words in the text you want to select. \n\n You can remove highlighted PHRASES by clicking on the [X] button in STEP2.";

		$hash = md5(serialize([$content]));
		
		$entity = Entity::withTrashed()->where('hash', $hash)->first();
		// check if file already exists
		if($entity) {
			array_push($this->status['notice'], "Existing job configuration found (" . $entity->_id . ")");
		} else {
			$entity = new Entity;
			$entity->domain = "cultural";
			$entity->format = "text";
			$entity->documentType = "jobconf";
			$entity->tags = array("VideoDescrHighlighting_");
			$entity->type = "VideoDescrHighlighting_";
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
		$crowdagent = CrowdAgent::where('_id', "crowdagent/cf/" . $workerId)->first();
		if(!$crowdagent) {
			$this->duplicateCrowdAgents++;
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
		}
		$this->newCrowdAgents++;
	}
	
	/**
	 * Create worker unit
	 */
	public function createWorkerUnit($activityId, $unitId, $acceptTime, $channel, $trust, $content, $agentId, $annVector, $jobId, $annId, $submitTime) 
	{

			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->domain = "cultural";
				$entity->format = "text";
				$entity->documentType = "workerunit";
				$entity->activity_id = $activityId; 
				$entity->acceptTime = $acceptTime;
				$entity->cfChannel = $channel;
				$entity->type = "VideoDescrHighlighting_v2_";
				$entity->cfTrust = $trust;
				$entity->content = $content;
				$entity->crowdAgent_id = $agentId;
				//$entity->annotationVector = "";
				$entity->job_id = $jobId;
				$entity->platformWorkerUnitId = $annId;
				$entity->softwareAgent_id = "cf";
				$entity->spam = false;
				$entity->submitTime = $submitTime;
				$entity->unit_id = $unitId;
				$entity->save();
				
			//    $status['success'][$title] = $title . " was successfully uploaded. (URI: {$entity->_id})";
			} catch (Exception $e) {
						// Something went wrong with creating the Entity
			   
				$entity->forceDelete();
				$status['error']["smth"] = $e->getMessage();
			}
	   // }

		return $status;
	}
	 
	 
	/**
	 * Create worker unit
	 */
	public function process($file, $settings)
	{
	
		$this->status = ['notice' => [], 'success' => []];

		try {

			// read file content and put it into an array
			$data = $this->readCSV($file);
		
			// Create activity
			$activity = $this->createActivity();
			
			// Create input file
			$inputFile = $this->createInputFile($file, $activity->id, $settings['project']);
			
			// Create job configuration
			$jobconfig = $this->createJobconf($activity->id);
			
			// Create job
			$job = $this->createJob($jobconfig->_id, $activity->id);

			// variables to log status
			$this->newUnits = 0;
			$this->duplicateUnits = 0;
			$this->newWorkerUnits = 0;
			$this->duplicateWorkerUnits = 0;
			$this->newCrowdAgents = 0;
			$this->duplicateCrowdAgents = 0;
			$units = array_unique(array_column($data, 7));
			$crowdagents = array_unique(array_column($data, 7));
			
			// loop through all the judgments and add workerUnits, media units and CrowdAgents.
			for ($i = 1; $i < count($data); $i ++) {
				
				// try to add unit
				$content = "";
				$unit = $this->createUnit($settings['inputType'], $inputFile->_id, $content, $activity->_id, $settings['project']);

				// Create CrowdAgent
				$crowdAgent = $this->createCrowdAgent($data[$i][7], $data[$i][8], $data[$i][9], $data[$i][10], $data[$i][6]);
				
				// Create workerUnit
				$content = "";
				$workerUnit = $this->createWorkerUnit($activity->_id, $unit->_id, $data[$i][3], $data[$i][5], $data[$i][6], $content, $crowdAgent->_id, $annVector, $job->_id, $data[$i][2], $data[$i][1]);
				
				
			}

			
			
			// Notice that units already existed in the database
			if($this->duplicateUnits > 0) { array_push($this->status['notice'], "Existing input media found for " . $this->duplicateUnits . " units"); }
			if($this->duplicateCrowdAgents > 0) { array_push($this->status['notice'], "Existing CrowdAgents " . $this->duplicateUnits . " units"); }

			// Job's done!
			array_push($this->status['success'], "Import finished successfully (" . $activity->_id . ")");
			
		} catch (Exception $e) {

			$activity->forceDelete();
			$inputFile->forceDelete();
			$jobconfig->forceDelete();
			$job->forceDelete();
			
			return $e;
		}
		
		return $this->status;
	
		// for all judgments, add a workerunit
		for ($i = 1; $i < count($data); $i ++) {
			


			$this->createWorkerUnit($activity->id, $judgments[$j]["unit_data"]["_id"], $judgments[$j]["started_at"], 
			$judgments[$j]["external_type"], $judgments[$j]["worker_trust"], $judgments[$j]["data"], $workerId, 
			$annUnits[$units_id[$i]][$judgments[$j]["worker_id"]]["event"], $jobs["event"]["job_id"], $judgments[$j]["id"], 
			$judgments[$j]["created_at"], "event");
			   
		   
		}
	}
}
