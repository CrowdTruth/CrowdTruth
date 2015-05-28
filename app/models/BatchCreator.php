<?php

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;

// TODO: Document ?
class BatchCreator extends Moloquent {

	/**
	 * 
	 * @param array $input
	 * @return result status structure containing
	 * 		'status'	'ok' or 'error'
	 * 		'message'	'A status message'
	 * 		'batch'		the batch or null if error
	 */
	public function store(array $input){
		try {
			$this->createBatchCreatorSoftwareAgent();
		} catch (Exception $e) {
			return [
				'status'	=> 'error',
				'message'	=> $e->getMessage(),
				'batch'		=> null
			];
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "batchcreator";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			return [
				'status'	=> 'error',
				'message'	=> $e->getMessage(),
				'batch'		=> null
			];
		}

		try {
			$entity = new Entity;
			$entity->title = $input['batch_title'];
			$entity->format = $input['format'];	
			$entity->domain = $input['domain'];	
			$entity->documentType = 'batch';
			$entity->softwareAgent_id = 'batchcreator';
			$entity->parents = $input['units'];
			$entity->content = $input['batch_description'];
			$entity->hash = md5(serialize($entity->parents));
			$entity->activity_id = $activity->_id;
			$entity->save();

			Queue::push('Queues\UpdateUnits', $input['units']);
			return [
				'status'	=> 'ok',
				'message'	=> $input['batch_title'] . " batch was successfully created. (URI: {$entity->_id})",
				'batch'		=> $entity
			];
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$activity->forceDelete();			
			$entity->forceDelete();
			return [
				'status'	=> 'ok',
				'message'	=> $e->getMessage(),
				'batch'		=> null
			];
		}
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