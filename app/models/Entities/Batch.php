<?php
/*
 * Main class for creating and managing batches
 * A batch is a type of entity
*/

namespace Entities;

use \Entity as Entity;
use \Queue as Queue;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

class Batch extends Entity {
	
	protected $attributes = array('type' => 'batch');

	public static function store($settings, $activity = false)
	{

		try {
			
			// Create the SoftwareAgent if it doesnt exist
			SoftwareAgent::store('batchcreator', 'Batch creation');

			$batch = new Batch;
			$batch->_id = $batch->_id;
			$batch->title = $settings['batch_title'];
			$batch->project = $settings['project'];
			$batch->parents = $settings['units'];
			$batch->content = $settings['batch_description'];
			$batch->hash = md5(serialize($batch->parents));
		//	$batch->activity_id = 

			if(!isset($activity)){
				$activity = new Activity;
				$activity->label = "Batch added to the platform";
				$activity->softwareAgent_id = 'mediacreator';
				$activity->save();
				$batch->activity_id = $activity->_id;
			}

			$batch->save();

			Queue::push('Queues\UpdateUnits', $settings['units']);
			return $batch;

		} catch (Exception $e) {

			// Something went wrong with creating the Batch
			$activity->forceDelete();			
			$batch->forceDelete();
			return false;
		}
	}
}

?>
