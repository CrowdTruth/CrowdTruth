<?php
namespace SoftwareComponents;

use \MongoDB\SoftwareComponent as SoftwareComponent;

// TODO: DO NOT USE INPUT HERE -- pass in all required parameters !
use \Input as Input;

class FileUploader {
	protected $softwareComponent;
	
	public function __construct() {
		$this->softwareComponent = SoftwareComponent::find('fileuploader');
	}

	public function store() {
		// TODO: pass in parameters, don't do Input::all here!
		
		$fileHelper = new \FileHelper(Input::all());
		
		// TODO: Move this code to FileHelper ?? --> Ask Khalid
		// $format = $fileHelper->getType();
		// $domain = $fileHelper->getDomain();
		// $documentType = $fileHelper->getDocumentType();
			
		$format = $fileHelper->getType();		// text
		$domain = Input::get('domain_type');	// other ==> NewDomain
		$documentType = Input::get('document_type');	//document_type_other==>newType
		// END TODO
		$validatedFiles = $fileHelper->performValidation();
		
		// TODO: Move this code to FileUpload ?? --> Ask Khalid
		$newDomain = false;
		$newDocType = false;
		if($domain == 'domain_type_other') {
			// Add new domain to DB
			$domain = Input::get('domain_create');
			$domain = str_replace(' ', '', $domain);
			$domain = strtolower($domain);
			$domain = 'domain_type_'.$domain;
			$newDomain = true;
		}
			
		if($documentType == 'document_type_other') {
			// Add new doc_type to DB
			$documentType = Input::get('document_create');
			$newDocType;
		}
			
		if($newDomain || $newDocType) {
			$uploader = SoftwareAgent::find("fileuploader");
		
			// TODO: Move this code to new class UploadComponent extends SoftwareComponent ?
			if($newDomain) {
				$domainName = Input::get('domain_create');
				$fileFormat = Input::get('file_format');
				$upDomains = $uploader->domains;
				$upDomains[$domain] = [
				"name" => $domainName,
				"file_formats" => [	$fileFormat ],
				"document_types" => [ $documentType ]
				];
				$uploader->domains = $upDomains;
			} else if($newDocType) {
				// Only docType is new -- domain already existed...
				$docTypes = $uploader->domains[$domain]["document_types"];
				array_push($docTypes, $documentType);
				$uploader->domains[$domain]["document_types"] = $docTypes;
			}
			$uploader->save();
			// END TODO
		}
		// END TODO
		
		$domain = str_replace("domain_type_", "", $domain);
		$documentType = str_replace("document_type_", "", $documentType);
		
		$mongoDBFileUpload = new \FileUpload;
		$status_upload = $mongoDBFileUpload->store($validatedFiles['passed'], $domain, $documentType);
		
		return $status_upload;
	}
}