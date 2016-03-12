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
	public function process($signal, $payload, $settings)
	{
		
		// increase memory usage to support larger files with up to 10k judgments
		ini_set('memory_limit','256M');
		set_time_limit(0);
		
		$this->status = ['notice' => [], 'success' => [], 'error' => []];
		$mapping = [];

		if ($settings['user'] != "" || isset($settings['user'])) {
			$user = UserAgent::where('api_key', $settings['user'])->first();

			if(is_null($user)) {
				array_push($this->status['error'], "Invalid auth key for user: " . $settings['user'] . "");
				return $this->status;
			}
			Auth::login($user);


			//UserController::loginWithToken($settings['user'], 'divedashboard');
		
			if(Auth::check()) {
				$user = Auth::user();
				
				if (isset($signal) && $signal == "new_units") {
					
					if ($settings['project'] != '' && isset($settings['project'])) {
						
						$projectsAll = ProjectHandler::listProjects();
						$projectsUser = ProjectHandler::getUserProjects($user, Permissions::PROJECT_READ);
						$projectUserNames = array_column($projectsUser, 'name');

						if (!in_array($settings['project'], $projectsAll)) {
							
							ProjectHandler::createGroup($settings['project']);
							ProjectHandler::grantUser($user, $settings['project'], Roles::PROJECT_MEMBER);
						}
						else {
							
							// add the user to the project if it has no access yet
							if(!ProjectHandler::inGroup($user['_id'], $settings['project'])) {
								ProjectHandler::grantUser($user, $settings['project'], Roles::PROJECT_MEMBER);
							}
							else {
								
								if ($settings['source'] != ''  && isset($settings['source'])) {
									if ($settings['description'] != '' && isset($settings['description'])) {
										if ($settings['docType'] != '' && isset($settings['docType'])) {
											if (isset($payload) && !empty($payload)) {
												
												$settings['units'] = [];
												$retUnits = json_decode ( $payload, true ) ;
												
												foreach ($retUnits as $unitContent) {
														// Create activity
													
													$activity = $this->createActivity();

													try {
														$mapping[$unitContent["id"]] = array();
														$mapping[$unitContent["id"]]["CT_ID"] = "";
														$mapping[$unitContent["id"]]["status"] = "";

														$hashing = array();
														$hashing["project"] = $settings["project"];
														$hashing["documentType"] = $settings["docType"];
														$hashing["content"] = $unitContent;
														$hash = md5(serialize($hashing));

														$searchForUnit = Entity::where("hash", $hash)->first();

														if ($searchForUnit != NULL) {
															
															$mapping[$unitContent["id"]]["CT_ID"] = $searchForUnit["_id"];
															$mapping[$unitContent["id"]]["status"] = "exists";
														} 
														else {
															$unit = new Unit();
															$unit->project = $settings['project'];
															$unit->activity_id = $activity->_id;
															$unit->documentType = $settings['docType'];
															$unit->type = "unit";
															$unit->parents = [];
															$unit->content = $unitContent;
															$unit->hash = $hash;
															$unit->source = $settings['source'];
															$unit->description = $settings['description'];
																//return $unit;
															$unit->save();
															//$units[$unit->_id] = $unit;
																
															array_push($settings['units'], $unit->_id);
															$mapping[$unitContent["id"]]["CT_ID"] = $unit->_id;
															$mapping[$unitContent["id"]]["status"] = "success";
																//	dd($settings['units']);
														}
													} catch (Exception $e) {
				
														$activity->forceDelete();
						
														$unit->forceDelete();
														$mapping[$unitContent["id"]]["CT_ID"] = null;
														$mapping[$unitContent["id"]]["status"] = "error";
														//return $e;
													}
												}
											}
											else {
												return 2;
												array_push($this->status['error'], "No units were sent from Dive Dashboard: payload -- " . $payload . "");
												return $this->status;
											}
										}
										else {
											array_push($this->status['error'], "The documentType of the data is unknown.");
											return $this->status;
										}
									}
									else {
										array_push($this->status['error'], "The description of the data is unknown.");
										return $this->status;
									}
								}
								else {
									array_push($this->status['error'], "The source of the data is unknown.");
									return $this->status;
								}

							}
						}

					}
					else {
						array_push($this->status['error'], "No project name was given in the request.");
						return $this->status;
					}
				}
				else {
					array_push($this->status['error'], "Unknown request from DIVE dashboard: signal -- " . $signal . ". Signal should be new_units");
					return $this->status;
				}
			}
			else {
				return "here";
				array_push($this->status['error'], "Authentication required. Please supply authkey.");
				return $this->status;
			}
		} 


		array_push($this->status['notice'], $mapping);
		return $this->status;

	}

}

