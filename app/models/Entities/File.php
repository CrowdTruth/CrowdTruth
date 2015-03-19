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
    
	// Function to store a new file
	public static function store($document, $project, $activity = false)
	{
		
		$hash = self::getHash($document);
		$file = File::withTrashed()->where('hash', $hash)->first();
		
		// check if file already exists
		if($file) {
			$file->existing = true;
			return $file;
		} else {
			try {

				// Create the SoftwareAgent if it doesnt exist
				SoftwareAgent::store('filecreator', 'File creation');
				
				if(!isset($activity)){
					$activity = new Activity;
					$activity->label = "File added to the platform";
					$activity->softwareAgent_id = 'filecreator';
					$activity->save();
				}

				// create a new file
				$file = new File;
				$file->_id = $file->_id;
				$file->activity_id = $activity->_id;
				$file->project = $project;
				
				$file->title = strtolower($document->getClientOriginalName());
				$file->domain = 'sound';
				$file->format = "text";
				$file->documentType = 'file';
				$file->content = $file::getContent($document);
				$file->hash = $hash;
				$file->tags = [ "unit" ];
				$file->save();

				return $file;

			} catch (Exception $e) {
				// Something went wrong with creating the file
				$file->forceDelete();
				throw $e;
			}
		}
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
