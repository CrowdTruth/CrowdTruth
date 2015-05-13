<?php

/**
*   The unit class is used to create unit entities. This can be both raw and structured documents.
*/

namespace Entities;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

class Unit extends Entity { 

	// Function to store a new unit unit
	public static function store($settings, $parent, $content, $platform_id = false, $activity = false)
	{
		$hash = self::getHash($content);
		$unit = Unit::withTrashed()->where('hash', $hash)->first();
		
		// check if unit already exists
		if($unit) {
			return $unit;
		} else {
			try {

				// Create the SoftwareAgent if it doesnt exist
				SoftwareAgent::store('unitcreator', 'Unit creation');
				
				// create an activity if it does not exist yet
				if(!$activity){
					$activity = new Activity;
					$activity->label = "Unit added to the platform";
					$activity->softwareAgent_id = 'unitcreator';
					$activity->save();
				}
				
				// create a new unit
				$unit = new unit;
				$unit->_id = $unit->_id;
				$unit->domain = $settings['domain'];
				$unit->format = $settings['format'];
				$unit->documentType = $settings['documentType'];
				$unit->parents = [$parent];
				$unit->content = $content;
				$unit->hash = $hash;
				if(isset($platform_id)) {
					$unit->platformId = $platform_id;
				}
				$unit->activity_id = $activity->_id;
				$unit->project = $settings['project'];
				$unit->tags = [ "unit" ];
				$unit->save();
				
				return $unit;

			} catch (Exception $e) {
				// Something went wrong with creating the unit
				$unit->forceDelete();
				throw $e;
			}
		}
	}
	

	
	public static function getHash($content) {
		$hash = md5(serialize([$content]));
		return $hash;
	}
}
?>
