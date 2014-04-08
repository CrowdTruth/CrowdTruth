<?php

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;

class FileUpload extends Moloquent {

	public function store(array $files, $domain, $documentType){
		$status = array();

		try {
			$this->createFileUploaderSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['FileUpload'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "fileuploader";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();				
			return $status;
		}

		foreach($files as $file){
			$title = $file->getClientOriginalName();

			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->title = strtolower($title);
				// $entity->extension = $file->getClientOriginalExtension();
				$entity->domain = $domain;
				$entity->format = "text";
				$entity->documentType = $documentType;
				$entity->content = File::get($file->getRealPath());
				$entity->hash = md5(serialize([$entity->content]));				
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully uploaded. (URI: {$entity->_id})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$activity->forceDelete();
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}			
		}

		return $status;
	}

	public function createFileUploaderSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('fileuploader'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "fileuploader";
			$softwareAgent->label = "This component is used for storing files as documents within MongoDB";
			$softwareAgent->save();
		}
	}
}