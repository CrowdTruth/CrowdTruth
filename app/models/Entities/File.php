<?php

/**
*   The file class is used to create new files.
*	If the file already exists, it will return the existing file
*/
namespace Entities;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

class File extends Entity { 
    
	private $validationRules = ['file' => 'mimes:txt|max:900000'];
	
	private $extraAllowedMimeTypes = [
					'text/plain',
					'text/anytext',
					'application/txt',
					'application/octet-stream',
					'text/x-c',
					'text/x-asm',
					'text/x-pascal',
					'text/x-c++'];
	

	public static function boot() {
		
	
	}
	
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
	public static function store($files, $project, $activity = false) {
		$validatedFiles = self::performValidation($files);
		
	
		// Create the SoftwareAgent if it doesnt exist
		SoftwareAgent::store('filecreator', 'File creation');
		
		$entities = [];
		
		if(!isset($activity)){
			$activity = new Activity;
			$activity->label = "File added to the platform";
			$activity->softwareAgent_id = 'filecreator';
			$activity->save();
		}
		
		$files = $validatedFiles['passed'];
		
		foreach($files as $file){

			$file = File::withTrashed()->where('hash', $hash)->first();
			
			// check if file already exists
			if($file) {
				$file->existing = true;
				array_push($entities, $file);
			} else {
				try {
					$file = new File;
					$file->_id = $entity->_id;
					$filename = $file->getClientOriginalName();
					$file->title = strtolower($filename);
					$file->documentType = 'file';
					$file->content = File::get($file->getRealPath());
					$file->hash = md5(serialize([$entity->content]));
					$file->activity_id = $activity->_id;
					$file->project = $project;
					$file->save();
			
					array_push($entities, $file);

				} catch (Exception $e) {
					// Something went wrong with creating the file
					$file->forceDelete();
					throw $e;
				}
			}
		}
		return $entities;
	}

	/**
	 * Perform Mime types and size validations.
	 * 
	 * @param $files  Files to be validated
	 * @param $format Format of files to be validated (different rules apply to different formats)
	 * @return An array with two lists: one of valid ('passed') and one of invalid ('failed') files.
	 */
	public static function performValidation($files) {
		$validatedFiles = [];
		foreach($files as $fileKey => $file){
			$validator = \Validator::make(array('file' => $file), $this->validationRules);

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
	
	public static function getContent($document) {
		$content = \File::get($document->getRealPath());
		return $content;
	}
	
	public static function getHash($document) {
		$content = self::getContent($document);
		$hash = md5(serialize([$content]));
		return $hash;
	}
}
?>
