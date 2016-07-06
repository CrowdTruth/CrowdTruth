<?php
namespace SoftwareComponents;

use \Validator as Validator;
use \MongoDate;

use \Security\ProjectHandler as ProjectHandler;
use \Security\Permissions as Permissions;
use \Security\Roles as Roles;

use \Entities\Unit as Unit;

use \Auth as Auth;

use SoftwareAgent, Activity, Entity, UserAgent;
use UserController as UserController;
use \Template as Template;

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
	public function process($data, $settings)
	{

		// increase memory usage to support larger files with up to 10k judgments
		ini_set('memory_limit','256M');
		set_time_limit(0);

		$this->status = ['annotationStatus' => [], 'success' => [], 'error' => []];
		$mapping = [];

		// Validate template
		$templateEntity = Template::where("_id", $settings["template_id"])->first();
		if ($templateEntity == NULL) {
			array_push($this->status['error'], "The enrichment capability provided is no longer available in CrowdTruth");
			return $this->status;
		}

		// Validate user
		if ($settings["token"] == NULL || $settings["token"] == "") {
			array_push($this->status['error'], "Authentication required. Please supply authentication token for CrowdTruth.");
			return $this->status;
		}

		if ($settings['template_id'] == NULL || $settings["template_id"] == "") {
			array_push($this->status['error'], "No enrichment capability provided.");
			return $this->status;
		}

		$user = UserAgent::where('api_key', $settings['token'])->first();
		if(!$user) {
			array_push($this->status['error'], "Invalid auth key for user: " . $settings['token'] . "");
			return $this->status;
		}

		Auth::login($user);
		if( ! Auth::check()) {
			array_push($this->status['error'], "Authentication required. Please supply authkey.");
			return $this->status;
		}
		$user = Auth::user();

		// Validate project
		if ($settings['project'] == NULL || $settings['project'] == '') {
			array_push($this->status['error'], "No project name was given in the request.");
			return $this->status;
		}

		$projectsAll = ProjectHandler::listProjects();
		// Project does not exist -- create it
		if (!in_array($settings['project'], $projectsAll)) {
			ProjectHandler::createGroup($settings['project']);
		}

		// add the user to the project if it has no access yet
		if(!ProjectHandler::inGroup($user['_id'], $settings['project'])) {
			ProjectHandler::grantUser($user, $settings['project'], Roles::PROJECT_MEMBER);
		}

		// Validate data
		if ( !isset($data) || empty($data)) {
			array_push($this->status['error'], "No units were sent");
			return $this->status;
		}

		// Validate that data has fields required by template
		$templateParamNames = array_column($templateEntity['parameters']['input'], 'name');
		foreach ($data as $unitContent) {
			$unitKeys = array_column($unitContent['content'], 'key');
			// check that data unit has all required keys
			foreach ($templateParamNames as $paramName) {
				if( !in_array($paramName, $unitKeys)) {
					array_push($this->status['error'], "Data unit should have key: ".$paramName);
					return $this->status;
				}
			}
		}

		$settings['units'] = [];
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
				$hashing["project"] = $settings["project"];
				$hashing["documentType"] = "document_for_" . $templateEntity["type"];
				$hashing["content"] = $content;
				$hash = md5(serialize($hashing));

				$searchForUnit = Entity::where("hash", $hash)->first();
				if ($searchForUnit != NULL) {
					$mapping[$unitContent["id"]]["id"] = $searchForUnit["content"]["id"];
					$mapping[$unitContent["id"]]["status"] = "exists";
					$mapping[$unitContent["id"]]["ticket"] = $searchForUnit["_id"] . "_" . $searchForUnit["content"]["id"];
					$mapping[$unitContent["id"]]["CT_generated_ID"] = $searchForUnit["_id"];

					array_push($this->status['annotationStatus'], $mapping[$unitContent["id"]]);
				}
				else {
					$unit = new Unit();
					$unit->project = $settings['project'];
					$unit->activity_id = $activity->_id;
					$unit->documentType = "document_for_" . $templateEntity["type"];
					$unit->type = "unit";
					$unit->parents = [];
					$unit->content = $content;
					$unit->hash = $hash;
					$unit->source = "dashboard";
					$unit->description = 'data received from the dashboard';
					$unit->save();

					array_push($settings['units'], $unit->_id);
					$mapping[$unitContent["id"]]["CT_generated_ID"]= $unit->_id;
					$mapping[$unitContent["id"]]["status"] = "accepted";
					$mapping[$unitContent["id"]]["ticket"] = $unit->_id . "_" . $unitContent["id"];;

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
			}
		}

		array_push($this->status["success"], "Units successfully saved!");
		return $this->status;
	}
}
