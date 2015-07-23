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
    
	private static $rules = [
					'file' => 'mimes:txt|max:900000',
					'filename' => 'required'
					];
					
	protected $fillable = [
		'project'
	];
	
	private static $extraAllowedMimeTypes = [
				'text' => [
					'text/plain',
					'text/anytext',
					'application/txt',
					'application/octet-stream',
					'text/x-c',
					'text/x-asm',
					'text/x-pascal',
					'text/x-c++']];
	

    public static function boot ()
    {
        parent::boot();

        static::creating(function ( $file )
        {
		dd($file);
	
		/**
		 * Store a new file to the database. Construct all entity information for such file.
		 * 
		 */
		$validatedFiles = self::performValidation($files);

	
		// Create the SoftwareAgent if it doesnt exist
		SoftwareAgent::store('filecreator', 'File creation');

		$entities = [];
		
		if(!$activity){
			$activity = new Activity;
			$activity->label = "File added to the platform";
			$activity->softwareAgent_id = 'filecreator';
			$activity->save();
		}
	
			$files = $validatedFiles['passed'];


			// check if file already exists
				try {

					$file = new File;
					$file->_id = $file->_id;
					dd($file->_id);
					$file->title = strtolower($filename);
					$file->documentType = 'file';
					$file->content = $content;
					$file->hash = $hash;
					$file->activity_id = $activity->_id;
					$file->project = $project;
					$file->save();


				} catch (Exception $e) {
					// Something went wrong with creating the file
					$file->forceDelete();
					throw $e;
				}

		});
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'title' => 'required|unique:posts|max:255',
			'body' => 'required',
		]);

		// The blog post is valid, store in database...
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
			$validator = \Validator::make(['file' => $file], self::$validationRules);

			if($validator->passes()){
				$validatedFiles['passed'][$fileKey] = $file;
			} else {
				// Sometimes the Validator fails because it does not recognize all MimeTypes
				// To solve this we check the MimeTypes in the uploaded files against our own list of allowed MimeTypes (extraAllowedMimeTypes)
				if(in_array($file->getMimeType(), self::$extraAllowedMimeTypes)){
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
