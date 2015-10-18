<?php
namespace SoftwareComponents;

use \Validator as Validator;
use \File as File;
use \Activity as Activity;

class FileUploader {
	protected $softwareComponent;
	
	protected $validationRules = ['file' => 'mimes:txt|max:900000'];
	
	protected $extraAllowedMimeTypes = [
					'text/plain',
					'text/anytext',
					'application/txt',
					'application/octet-stream',
					'text/x-c',
					'text/x-asm',
					'text/x-pascal',
					'text/x-c++'];

	/**
	 * Store a new file to the database. Construct all entity information for such file.
	 * 
	 * @param $fileFormat
	 * @param $domain
	 * @param $documentType
	 * @param $project			The name of the Project who owns the file data.
	 * @param $domainCreate
	 * @param $documentCreate
	 * @param $files
	 */
	public function store($files, $title, $project) {
		$validatedFiles = $this->performValidation($files);
		
		$status = [];
		
		try {
			$activity = new Activity;
			$activity->softwareAgent_id = $this->softwareComponent->_id;
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'] = $e->getMessage();
			return $status;
		}
		
		$files = $validatedFiles['passed'];
		foreach($files as $file){

			$filename = $file->getClientOriginalName();

			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->title = strtolower($title);
				$entity->filename = strtolower($filename);
				$entity->type = 'file';
				$entity->content = File::get($file->getRealPath());
				$entity->hash = md5(serialize([$entity->content]));
				$entity->activity_id = $activity->_id;
				$entity->project = $project;
				$entity->save();
		
				$status['success'][$title] = $title . " was successfully uploaded. (URI: {$entity->_id})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$activity->forceDelete();
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}
		}
		
		$files = $validatedFiles['failed'];
		foreach($files as $file) {
			$title = $file->getClientOriginalName();
			$status['error'][$title] = 'Validation failed';
		}
		
		return $status;
	}

	/**
	 * Perform Mime types and size validations.
	 * 
	 * @param $files  Files to be validated
	 * @param $format Format of files to be validated (different rules apply to different formats)
	 * @return An array with two lists: one of valid ('passed') and one of invalid ('failed') files.
	 */

	private function performValidation($files) {
		$validatedFiles = [];
		foreach($files as $fileKey => $file){
			$validator = Validator::make(array('file' => $file), $this->validationRules);

			if($validator->passes()){
				$validatedFiles['passed'][$fileKey] = $file;
			} else {
				// Sometimes the Validator fails because it does not recognize all MimeTypes
				// To solve this we check the MimeTypes in the uploaded files against our own list of allowed MimeTypes (extraAllowedMimeTypes)
				if(in_array($file->getMimeType(), $this->extraAllowedMimeTypes)){
					$validatedFiles['passed'][$fileKey] = $file;
				} else {
					$validatedFiles['failed'][$fileKey] = $file;
				}
			}
		}
		
		return $validatedFiles;
	}
}
