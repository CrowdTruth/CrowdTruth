<?php

use MongoDB\Entity;

class Batch extends Entity {
	
	protected $attributes = array('documentType' => 'batch');


	public function store(array $input){

		try {
			$this->createBatchSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['FileUpload'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "batchcreator";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			Session::flash('flashError', $e->getMessage());
			return false;
		}

		try {
			

			$entity = new Entity;
			$entity->title = $input['batch_title'];
			// $entity->extension = $file->getClientOriginalExtension();
			$entity->format = $input['format'];	
			$entity->domain = $input['domain'];	
			$entity->documentType = "batch";
			$entity->parents = $input['units'];
			$entity->content = $input['batch_description'];
			$entity->hash = md5(serialize($entity->parents));
			$entity->activity_id = $activity->_id;
			$entity->save();

			Queue::push('Queues\UpdateUnits', $input['units']);
			
			Session::flash("flashSuccess", $input['batch_title'] . " batch was successfully created. (URI: {$entity->_id})");
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$activity->forceDelete();			
			$entity->forceDelete();
			Session::flash('flashError', $e->getMessage());
			return false;
		}

		return $entity;
	}

	public function createBatchSoftwareAgent(){
		if(!SoftwareAgent::find('batchcreator'))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "batchcreator";
			$softwareAgent->label = "This component is used for creating batches with units etc.";
			$softwareAgent->save();
		}
	}
}
















?>
