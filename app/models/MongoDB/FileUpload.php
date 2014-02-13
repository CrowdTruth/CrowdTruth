<?php

namespace MongoDB;

use Moloquent, Schema, URL, File, Exception;

class FileUpload extends Moloquent {

	public function store(array $files, $domain, $documentType){
		$status = array();

		try {
			$this->createFileUploaderSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['FileUpload'] = $e->getMessage();
			return $status;
		}

		foreach($files as $file){
			$title = $file->getClientOriginalName();

			try {
				$entity = new Entity;
				$entity->title = strtolower($title);
				// $entity->extension = $file->getClientOriginalExtension();
				$entity->domain = strtolower($domain);
				$entity->format = "text";
				$entity->documentType = strtolower($documentType);
				$entity->parent_id = null;
				$entity->ancestors = null;
				$entity->content = File::get($file->getRealPath());
				$entity->save();

				$status['success'][$title] = $title . " was successfully uploaded. (URI: {$entity->_id})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
				continue;
			}

			try {
				$activity = new Activity;
				$activity->_id = $entity->activity_id;
				$activity->softwareAgent_id = "fileuploader";
				$activity->save();

			} catch (Exception $e) {
				// Something went wrong with creating the Activity
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