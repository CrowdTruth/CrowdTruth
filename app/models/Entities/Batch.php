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

	public static function boot ()
    {
        parent::boot();

        static::creating(function ( $batch )
        {
		
			/**
			 * Store a new batch to the database. Construct all entity information for such file.
			 * 
			 */
		
			// Create the SoftwareAgent if it doesnt exist
			SoftwareAgent::store('batchcreator', 'Batch creation');

			
			if(!isset($batch->activity_id)){
				$activity = new Activity;
				$activity->label = "Batch added to the platform";
				$activity->softwareAgent_id = 'batchcreator';
				$activity->save();
				$batch->activity_id = $activity->_id;
			}
		});
	}

	public static function store($settings)
	{

		try {
			
			// Create the SoftwareAgent if it doesnt exist
			//SoftwareAgent::store('batchcreator', 'Batch creation');

			$batch = new Batch;
			$batch->_id = $batch->_id;
			$batch->title = $settings['batch_title'];
			$batch->content = $settings['batch_description'];
			$batch->project = $settings['project'];
			$batch->parents = $settings['units'];
			$batch->size = count($settings['units']);
			$hashing = array();
			$hashing["project"] = $settings['project'];
			$hashing["content"] = $settings['units'];
			$batch->hash = md5(serialize($hashing));

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
