<?php namespace Joli\Services;

use Joli\Validators\DocumentValidator;
use Joli\Validators\ValidationException;

class DocumentCreatorService {

	protected $validator;
	protected $messages;

	public function __construct(DocumentValidator $validator){
		$this->validator = $validator;
	}

	public function make(array $attributes){
		if (!is_array($attributes['files'])) {
	        $attributes['files'] = array($attributes['files']);
	    } else {
	    	foreach($attributes['files'] as $file){
	    		$originalName = $file->getClientOriginalName();
				if($this->validator->isValid(array('file' => $file))){
					// Create Document

					return true;
				} else {
					$messages['error'][$originalName] = $this->$validator->messages()->first('file');
					echo "<pre>";
					print_r($messages);
					exit;
				}
	    	}
	    }

	}
}