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
		
		return $entity;

	}

	/**
	 *	Create job configuration
	 */
	public function createJobconf($activity)
	{

		$entity = new Entity;
		$entity->domain = "cultural";
		$entity->format = "text";
		$entity->documentType = "jobconf";
		$entity->tags = array("VideoDescrHighlighting_");
		$entity->type = "VideoDescrHighlighting_";
				
		$content = array();
		$content["type"] = "VideoDescrHighlighting_v2_";
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
		$entity->content = $content;
		$entity->hash = md5(serialize([$entity->content]));             
		$entity->activity_id = $activity;  
		$entity->save();
		
		return $entity;
	}

	/**
	 * Create crowd agent
	 */
	public function createCrowdAgent($workerId, $country, $region, $city, $cfWorkerTrust) {
		$agent = new CrowdAgent;
		$agent->_id= "crowdagent/cf/$workerId";
		$agent->softwareAgent_id= "cf";
		$agent->platformAgentId = $workerId;
		$agent->city = $city;
		$agent->country = $country;
		$agent->region = $region;
		$agent->cfWorkerTrust = $cfWorkerTrust;
		$agent->save();
	  //  dd($agent);           
	}
	
	/**
	 * Create worker unit
	 */
	public function createWorkerUnit($activityId, $unitId, $acceptTime, $channel, $trust, $content, $agentId, $annVector, $jobId, $annId, $submitTime, $conceptType) 
	{
		$status = array();

			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->domain = "cultural";
				$entity->format = "text";
				$entity->documentType = "workerunit";
				$entity->activity_id = $activityId; 
				$entity->acceptTime = $acceptTime;
				$entity->cfChannel = $channel;
				$entity->type = "VideoDescrHighlighting_v2_" . $conceptType;
				$entity->cfTrust = $trust;
				$entity->content = $content;
				$entity->crowdAgent_id = $agentId;
				$annType = array();
				$annType[$conceptType] = $annVector;
				$entity->annotationVector = $annType;
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
	
		$status = array();

		try {
		
			// Create activity
			$activity = $this->createActivity();
			
			// read file content and put it into an array
			$data = $this->readCSV($file);

			
			// Create job configuration
			$jobconfig = $this->createJobconf($activity->id);
			
			// Create job
			$job = $this->createJob($jobconfig->_id, $activity->id);


		} catch (Exception $e) {
			$status['error'] = $e->getMessage();
			$activity->forceDelete();   
			$jobconfig->forceDelete();   
			$job->forceDelete();
			
			return $status;
		}
	
		// for all judgments, add a workerunit
		for ($i = 1; $i < count($data); $i ++) {
			
			$crowdagent = CrowdAgent::where('_id', "crowdagent/cf/" . $data[$i][7])->first();
			if(!$crowdagent) {
				$this->createCrowdAgent($data[$i][7], $data[$i][8], $data[$i][9], $data[$i][10], $data[$i][6]);
			}

			$this->createWorkerUnit($activity->id, $judgments[$j]["unit_data"]["_id"], $judgments[$j]["started_at"], 
			$judgments[$j]["external_type"], $judgments[$j]["worker_trust"], $judgments[$j]["data"], $workerId, 
			$annUnits[$units_id[$i]][$judgments[$j]["worker_id"]]["event"], $jobs["event"]["job_id"], $judgments[$j]["id"], 
			$judgments[$j]["created_at"], "event");
			   
			}
		   
		}
	}
}
