<?php

class FileHelper {

	protected $input;
	protected $format;

	protected $validationRules = array(
		'text' => array(
					'file' => 'mimes:txt|max:900000'
					),
		'images' => array(
					'file' => 'mimes:png|jpg|max:2000'
					),
		'videos' => array(
					'file' => 'mimes:mp4|avi|max:2000'
					)	
	);

	protected $extraAllowedMimeTypes = array(
		'text' => array(
					    'text/plain',
					    'text/anytext',
					    'application/txt',
					    'application/octet-stream',
					    'text/x-c',
					    'text/x-asm',
					    'text/x-pascal',
					    'text/x-c++'
					),
		'images' => array(
					    'text/plain' // To be added
					),
		'videos' => array(
					    'text/plain' // To be added
					)
	);

	public function __construct(array $input){
		$this->input = $input;
	}
	
	public function getType(){
		switch ($this->input['file_format']) {
		    case 'file_format_text':
		        return $this->format = 'text';
		    case 'file_format_image':
		        return $this->format = 'image';
		    case 'file_format_video':
		        return $this->format = 'video';
		}
		throw new Exception('No "Type of File" selected');
	}

	public function getDomain(){
		switch ($this->input['domain_type']) {
		    case 'domain_type_medical':
		        return 'medical';
		    case 'domain_type_news':
		        return 'news';
		    case 'domain_type_other':
		        return 'other';
		}
		throw new Exception('No Domain selected');		
	}

	public function getDocumentType(){
		switch ($this->input['document_type']) {
		    case 'document_type_twrex':
		        return 'TWrex';
		    case 'document_type_article':
		        return 'Article';
		    case 'document_type_book':
		        return 'Book';
		}
		throw new Exception('No "Type of Document" selected');		
	}

	public function performValidation(){
		if(!Input::hasFile('files'))
			throw new Exception('No files selected');

		$files = $this->input['files'];
		$validatedFiles = array();
    	foreach($files as $fileKey => $file){

    		$validator = Validator::make(array('file' => $file), $this->validationRules[$this->format]);

    		if($validator->passes()){
    			$validatedFiles['passed'][$fileKey] = $file;
    		} else {
	        	// Sometimes the Validator fails because it does not recognize all MimeTypes
	        	// To solve this we check the MimeTypes in the uploaded files against our own list of allowed MimeTypes (extraAllowedMimeTypes)
	        	if(in_array($file->getMimeType(), $this->extraAllowedMimeTypes[$this->format])){
    				$validatedFiles['passed'][$fileKey] = $file;
	        	} else {
    				$validatedFiles['failed'][$fileKey] = $file;
	        	}
    		}
    	}

    	return $validatedFiles;
	}
}