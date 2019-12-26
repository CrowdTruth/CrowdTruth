<?php
namespace SoftwareComponents;

use \Validator as Validator;
use \MongoDate;

use \Security\ProjectHandler as ProjectHandler;
use \Security\Permissions as Permissions;
use \Security\Roles as Roles;

use \Entities\Unit as Unit;

use \Auth as Auth;
use \App;

use SoftwareAgent, Activity, Entity, UserAgent, Config;
use UserController as UserController;
use \Template as Template;
use \Entities\Batch as Batch;
use \Entities\Job as Job;
use \Entities\JobConfiguration as JobConfiguration;
use \Exception as Exception;

class DIVEUnitsImporter {
	protected $softwareComponent;

	public function objectToArray($obj) {
		if (is_object($obj)) {
			$obj = get_object_vars($obj);
		}
		if (is_array($obj)) {
			return array_map(__FUNCTION__, $obj);
		}
		else {
			return $obj;
		}
	}

	public function __construct() {
		if(!SoftwareAgent::find('diveimporter'))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "diveimporter";
			$softwareAgent->label = "This component imports units from dive";
			$softwareAgent->save();
		}
	}

	/**
	 *	Create result importer activity
	 */
	public function createActivity()
	{
		$activity = new Activity;
		$activity->softwareAgent_id = "diveimporter";
		$activity->save();

		return $activity;
	}

	/**
	 * Create worker unit
	 */
	public function process($data, $template_id)
	{
		// increase memory usage to support larger files with up to 10k judgments
		ini_set('memory_limit','256M');
		set_time_limit(0);

		$project = Config::get('config.clariah_project');
		$userId = Config::get('config.clariah_user');

		$this->status = ['annotationStatus' => [], 'success' => [], 'error' => []];
		$mapping = [];

		// Validate template
		$templateEntity = Template::where("_id", $template_id)->first();
		if ($templateEntity == NULL) {
			array_push($this->status['error'], "The enrichment capability provided is no longer available in CrowdTruth");
			return $this->status;
		}

		if ($template_id == NULL || $template_id == "") {
			array_push($this->status['error'], "No enrichment capability provided.");
			return $this->status;
		}

		// Validate user
		$user = UserAgent::where('_id', $userId)->first();
		if(!$user) {
			array_push($this->status['error'], "Invalid auth key for user: ".$userId);
			return $this->status;
		}

		Auth::login($user);
		if( ! Auth::check()) {
			array_push($this->status['error'], "Authentication required. Please supply authkey.");
			return $this->status;
		}
		$user = Auth::user();

		// Validate project
		if ($project == NULL || $project == '') {
			array_push($this->status['error'], "No project name was given in the request.");
			return $this->status;
		}

		$projectsAll = ProjectHandler::listProjects();
		// Project does not exist -- create it
		if (!in_array($project, $projectsAll)) {
			ProjectHandler::createGroup($project);
		}

		// add the user to the project if it has no access yet
		if(!ProjectHandler::inGroup($userId, $project)) {
			ProjectHandler::grantUser($user, $project, Roles::PROJECT_MEMBER);
		}

		// Validate data
		if ( !isset($data) || empty($data)) {
			array_push($this->status['error'], "No units were sent");
			return $this->status;
		}

		// Validate that data has fields required by template
		$templateParamNames = array_column($templateEntity['parameters']['input'], 'name');
		$associationsTemplBatch = array();
		//dd($templateParamNames);
		foreach ($data as $unitContent) {
			$unitKeys = array_column($unitContent['content'], 'key');

			// check that data unit has all required keys
			foreach ($templateParamNames as $paramName) {
				if( !in_array($paramName, $unitKeys)) {
					array_push($this->status['error'], "Data unit should have key: ".$paramName);
					return $this->status;
				}
				else {
					array_push($associationsTemplBatch, $paramName . " - " . $paramName);
				}
			}
		}
		$units = array();

		foreach ($data as $unitContent) {
			// Create activity
			$activity = $this->createActivity();
			try {
				$mapping[$unitContent["id"]] = array();
				$mapping[$unitContent["id"]]["id"] = $unitContent["id"];
				$mapping[$unitContent["id"]]["status"] = "";
				$mapping[$unitContent["id"]]["ticket"] = "";
				$mapping[$unitContent["id"]]["CT_generated_ID"] = "";

				$content = array();
				$content["id"] = $unitContent["id"];
				foreach ($unitContent["content"] as $field) {
					$content[$field['key']] = $field['value'];
				}

				$hashing = array();
				$hashing["project"] = $project;
				$hashing["documentType"] = "document_for_" . $templateEntity["type"];
				$hashing["content"] = $content;
				$hash = md5(serialize($hashing));

				$searchForUnit = Entity::where("hash", $hash)->first();
				if ($searchForUnit != NULL) {
					$mapping[$unitContent["id"]]["id"] = $searchForUnit["content"]["id"];
					$mapping[$unitContent["id"]]["status"] = "exists";
					$mapping[$unitContent["id"]]["ticket"] = $searchForUnit["_id"] . " - " . $searchForUnit["content"]["id"];
					$mapping[$unitContent["id"]]["CT_generated_ID"] = $searchForUnit["_id"];

					array_push($units, $searchForUnit["_id"]);

					array_push($this->status['annotationStatus'], $mapping[$unitContent["id"]]);
				}
				else {
					$unit = new Unit();
					$unit->project = $project;
					$unit->activity_id = $activity->_id;
					$unit->documentType = "document_for_" . $templateEntity["type"];
					$unit->type = "unit";
					$unit->parents = [];
					$unit->content = $content;
					$unit->hash = $hash;
					$unit->source = "dashboard";
					$unit->description = 'data received from the dashboard';
					$unit->save();

					$mapping[$unitContent["id"]]["CT_generated_ID"]= $unit->_id;
					$mapping[$unitContent["id"]]["status"] = "accepted";
					$mapping[$unitContent["id"]]["ticket"] = $unit->_id . "_" . $unitContent["id"];

					array_push($units, $unit->_id);

					array_push($this->status['annotationStatus'], $mapping[$unitContent["id"]]);
				}
			} catch (Exception $e) {
				$activity->forceDelete();
				$unit->forceDelete();
				$mapping[$unitContent["id"]]["id"] = $unitContent["id"];
				$mapping[$unitContent["id"]]["status"] = "error";
				$mapping[$unitContent["id"]]["ticket"] = "";
				$mapping[$unitContent["id"]]["CT_generated_ID"] = "";
				array_push($this->status['annotationStatus'], $mapping[$unitContent["id"]]);
				array_push($this->status["error"], "An error occurred when saving the units!");
				return $this->status;
			}
		}

		array_push($this->status["success"], "Units successfully saved!");
		$batch_id = "";
		$job_id = "";

		if (count($this->status["error"] == 0)) {
			// create batch
			$settings = array();
			$settings['units'] = $units;
			$settings['batch_title'] = "Batch for project " . $project;
			$settings['batch_description'] = "Batch added via CLARIAH dashboard";
			$settings['project'] = $project;

			$hashing = array();
			$hashing["project"] = $project;
			$hashing["content"] = $settings['units'];

			$searchForBatch = \Entity::where("hash", md5(serialize($hashing)))->first();

			if ($searchForBatch == NULL) {
				$batch = Batch::store($settings, null);

				if ($batch == false) {
					array_push($this->status["error"], "An error occurred when saving the batch!");
					return $this->status;
				}

				$batch_id = $batch["_id"];
			}
			else {
				$batch_id = $searchForBatch["_id"];
			}

			array_push($this->status["success"], "Batch successfully saved!");

			// create the job configuration entity
			$settings = array();
			$settings["project"] = $project;
			$settings["documentType"] = "document_for_" . $templateEntity["type"];
			$settings["templateType"] = $templateEntity["type"];
			$settings["templateDescription"] = $templateEntity["description"];
			$settings["platform"] = $templateEntity["platform"];
			$settings["associationsTemplBatch"] = $associationsTemplBatch;
			$settings["instructions"] = $templateEntity["instructions"];
			$settings["title"] = "Temporary title - job from dashboard";
			$jobconfig = $this->createJobconf(null, $settings);

			if ($jobconfig) {
				// Create job
				$job = $this->createJob($jobconfig->_id, $activity->_id, $batch_id, $settings);
			}
			else {
				array_push($this->status["error"], "An error occurred when creating the job!");

				// Create job
				$settings = array();
				$settings["templateType"] = $templateEntity["type"];
				$settings["platform"] = "CF";

				$job = $this->createJob($jobconfig["_id"], $jobconfig["activity_id"], $batch_id, $settings);
				$job_id = $job["_id"];
			}

		}

		foreach ($this->status["annotationStatus"] as $index => $addedUnit) {
			$this->status["annotationStatus"][$index]["ticket"] = $job["_id"] . " - " . $this->status["annotationStatus"][$index]["ticket"];
		}

		return $this->status;
	}

	public function createJobconf($activity, $settings)
	{
		$content = array();

		$content["type"] = $settings["templateType"];
		$content["platform"] = $settings["platform"];
		$content["expirationInMinutes"] = 0;
		$content["reward"] = 0.00;
		$content["workerunitsPerUnit"] = 15;
		$content["workerunitsPerWorker"] = 10;
		$content["unitsPerTask"] = 1;
		$content["title"] = $settings["title"];
		$content["description"] = $settings["templateDescription"];
		$content["keywords"] = "created via dashboard";
		$content["instructions"] = $settings["instructions"];

		$hash = md5(serialize([$content]));

		$entity = JobConfiguration::withTrashed()->where('hash', $hash)->first();
		// check if file already exists
		if($entity) {
			// do not delete this on rollback
			$entity->_existing = true;
			//array_push($this->status['success'], "Existing job configuration found (" . $entity->_id . ")");
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
		$entity->batch_id = $batch;
		$entity->project = $settings['project'];
		$entity->documentType = $settings['documentType'];
		$entity->templateType = $settings['templateType'];
		$entity->type = "job";
		$entity->completion = 0;
		$entity->expectedWorkerUnitsCount = 0;
		$entity->finishedAt = new MongoDate;
		$entity->jobConf_id = $config;
		$entity->projectedCost = 0.00;
		$entity->realCost = 0.00;
		$entity->runningTimeInSeconds = 0;
		$entity->softwareAgent_id = $settings["platform"];
		$entity->startedAt = new MongoDate;
		//$entity->status = "unordered";
		$entity->activity_id = $activity;
		$extraInfoBatch = array();
		$extraInfoBatch["batchColumnsNewTemplate"] = array();
		$extraInfoBatch["batchColumnsExtraChosenTemplate"] = array();
		$extraInfoBatch["associationsTemplBatch"] = $settings["associationsTemplBatch"];
		$extraInfoBatch["ownTemplate"] = false;
		$entity->extraInfoBatch = $extraInfoBatch;
		$entity->save();


		try {
			$entity->publish(true);
		} catch (Exception $e) {
			array_push($this->status["error"], "An error occurred when publishing the job!\n". $e->getMessage());
			return $entity;
		}

		$successmessage = "Created job with jobConf :-)";
		$platformApp = App::make($settings["platform"]); //TODOJORAN
		$platformApp->refreshJob($entity->_id);

		array_push($this->status['success'], "Job created (" . $entity->_id . ")");

		return $entity;
	}

}
