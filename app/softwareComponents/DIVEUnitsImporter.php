<?php
namespace SoftwareComponents;

use \Validator as Validator;
use \MongoDate;

use \Entities\Unit as Unit;
use \Entities\Batch as Batch;

use SoftwareAgent, Activity, Entity;

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
		
		//return "blabla";

		// increase memory usage to support larger files with up to 10k judgments
		ini_set('memory_limit','256M');
		set_time_limit(0);
		
		$this->status = ['notice' => [], 'success' => [], 'error' => []];

		try {
		
			if (!empty($payload)) {
				if ($signal == "new_units") {

				
					$settings['units'] = [];

					$retUnits = json_decode ( $payload, true ) ;

					//return $retUnits;
					// Create activity
					$activity = $this->createActivity();
					
					if ($settings['documentType'] == "") {
						$settings['documentType'] = "diveunit";
					}
					
					if ($settings["batch_description"] == "") {
						$settings["batch_description"] = "Batch imported from DIVE dashboard";
					}

					foreach ($retUnits as $unitContent) {

						$hashing = array();
						$hashing["project"] = $settings["project"];
						$hashing["documentType"] = $settings["documentType"];
						$hashing["content"] = $unitContent;
						$hash = md5(serialize($hashing));

						$searchForUnit = \Entity::where("hash", $hash)->first();

						if ($searchForUnit != NULL) {
							//$units[$searchForUnit["_id"]] = $searchForUnit;
							array_push($settings['units'], $searchForUnit["_id"]);
						} 
						else {
							$unit = new Unit();
							$unit->project = $settings['project'];
							$unit->activity_id = $activity->_id;
							$unit->documentType = $settings['documentType'];
							$unit->type = "unit";
							$unit->parents = [];
							$unit->content = $unitContent;
							$unit->hash = $hash;
							$unit->source = "divedashboard";

							//return $unit;
							$unit->save();
							$units[$unit->_id] = $unit;
							
							array_push($settings['units'], $unit->_id);
							//	dd($settings['units']);
						}

					}

					// Create Batch
					$hashBatch = array();
					$hashBatch["project"] = $settings["project"];
					$hashBatch["batch_description"] = $settings["batch_description"];
					$hashBatch["content"] = $settings["units"];
					$settings['batch_title'] = "Imported batch from Dive dashboard";

					$searchForBatch = \Entity::where("hash", md5(serialize($hashBatch)))->first();

					if ($searchForBatch != NULL) {
						array_push($this->status['notice'], "Batch already exists " . $searchForBatch['_id'] . "");
					}
					else {
						$batch = Batch::store($settings, $activity);
					}	
					
					array_push($this->status['success'], "Successfully imported " . $settings['documentType'] . "");
					array_push($this->status['success'], "Logged activities as " . $activity->_id . "");
					
					return $this->status;

				}
				else {
					array_push($this->status['error'], "Unknown request from DIVE dashboard -- " . $signal . "");
					return $this->status;
				}
			}
			else {
				array_push($this->status['error'], "The content of the units is empty -- " . $payload . "");
				return $this->status;
			}
			
		} catch (Exception $e) {
		
			$activity->forceDelete();
			
			foreach($this->units as $unit) {
				if(!$unit->exists()) {
					$unit->forceDelete();
				}
			}
			return $e;
		}
	}
}
