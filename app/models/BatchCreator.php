<?php

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
//use Moloquent, Schema, URL, File, Exception, Session;

class BatchCreator extends Moloquent {

	public function store(array $input){

		try {
			$this->createBatchCreatorSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['BatchCreator'] = $e->getMessage();
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

	public function createBatchCreatorSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('batchcreator'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "batchcreator";
			$softwareAgent->label = "This component is used for creating batches with units etc.";
			$softwareAgent->save();
		}
	}
}