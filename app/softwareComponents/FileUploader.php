<?php
namespace SoftwareComponents;

use \MongoDB\Activity as Activity;
use \MongoDB\Entity as Entity;
use \MongoDB\SoftwareComponent as SoftwareComponent;

use \Validator as Validator;
use \File as File;

class FileUploader {
	protected $softwareComponent;
	
	protected $validationRules = [
			'text' => ['file' => 'mimes:txt|max:900000'],
			'images' => ['file' => 'mimes:png|jpg|max:2000'],
			'videos' => ['file' => 'mimes:mp4|avi|max:2000']
	];
	
	protected $extraAllowedMimeTypes = [
			'text' => [
					'text/plain',
					'text/anytext',
					'application/txt',
					'application/octet-stream',
					'text/x-c',
					'text/x-asm',
					'text/x-pascal',
					'text/x-c++',
					'text/html'
			],
			'images' => [
					'text/plain' // To be added
			],
			'videos' => [
					'text/plain' // To be added
			]
	];

	public function __construct() {
		$this->softwareComponent = SoftwareComponent::find('fileuploader');
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
	public function store($fileFormat, $domain, $documentType, $project, $domainCreate, $documentCreate, $files) {
		$format = $this->getType($fileFormat);
		$validatedFiles = $this->performValidation($files, $format);
		
		$newDomain = false;
		$newDocType = false;
		if($domain == 'domain_type_other') {
			// Add new domain to DB
			$domain = $domainCreate;
			$domain = str_replace(' ', '', $domain);
			$domain = strtolower($domain);
			$domain = 'domain_type_'.$domain;
			$newDomain = true;
		}
			
		if($documentType == 'document_type_other') {
			// Add new doc_type to DB
			$documentType = $documentCreate;
			$newDocType;
		}
			
		if($newDomain || $newDocType) {
			if($newDomain) {
				// newDomain and new DocType
				$domainName = $domainCreate;
				$upDomains = $this->softwareComponent->domains;
				$upDomains[$domain] = [
					"name" => $domainName,
					"file_formats" => [	$fileFormat ],
					"document_types" => [ $documentType ]
				];
				$this->softwareComponent->domains = $upDomains;
			} else if($newDocType) {
				// Only docType is new -- domain already existed...
				$docTypes = $this->softwareComponent->domains[$domain]["document_types"];
				array_push($docTypes, $documentType);
				$this->softwareComponent->domains[$domain]["document_types"] = $docTypes;
			}
			$this->softwareComponent->save();
		}
		
		$domain = str_replace("domain_type_", "", $domain);
		$documentType = str_replace("document_type_", "", $documentType);
		
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
			$title = $file->getClientOriginalName();
			
			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->activity_id = $activity->_id;
				$entity->softwareAgent_id = $this->softwareComponent->_id;
				$entity->project = $project;
				$entity->title = strtolower($title);
				$entity->domain = $domain;
				$entity->format = "text";
				$entity->documentType = $documentType;
				$entity->content = File::get($file->getRealPath());
				$entity->hash = md5(serialize([$entity->content]));
				$entity->tags = [ "unit" ];
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
	 * Validate that input format is of a know file format: file_format_text,
	 * file_format_image or file_format_video.
	 * 
	 * @param $format   Text representation of the file format.
	 * @throws Exception if file is of an unknown format type.
	 */
	private function getType($format){
		switch ($format) {
			case 'file_format_text':
				return 'text';
			case 'file_format_image':
				return 'image';
			case 'file_format_video':
				return 'video';
		}
		throw new \Exception('Invalid "Type of File" selected');
	}

	/**
	 * Perform Mime types and size validations.
	 * 
	 * @param $files  Files to be validated
	 * @param $format Format of files to be validated (different rules apply to different formats)
	 * @return An array with two lists: one of valid ('passed') and one of invalid ('failed') files.
	 */
	private function performValidation($files, $format) {
		$validatedFiles = [];
		$validatedFiles['passed'] = [];
		$validatedFiles['failed'] = [];
		
		
		foreach($files as $fileKey => $file){
			$validator = Validator::make(array('file' => $file), $this->validationRules[$format]);

			if($validator->passes()){
				$validatedFiles['passed'][$fileKey] = $file;
			} else {
				// Sometimes the Validator fails because it does not recognize all MimeTypes
				// To solve this we check the MimeTypes in the uploaded files against our own list of allowed MimeTypes (extraAllowedMimeTypes)
				if(in_array($file->getMimeType(), $this->extraAllowedMimeTypes[$format])){
					$validatedFiles['passed'][$fileKey] = $file;
				} else {
					$validatedFiles['failed'][$fileKey] = $file;
				}
			}
		}
		
		return $validatedFiles;
	}
}
