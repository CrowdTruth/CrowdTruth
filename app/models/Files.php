<?php

class Files {

	protected $rules = array(
		'file' => 'mimes:gif,jpg,jpeg,bmp,zip,zipx,txt,csv,doc,docx,xls,xlsx,pdf|max:2000'	
	);

	protected $extraAllowedMimeTypes = array(
	    'text/csv',
	    'text/plain',
	    'application/csv',
	    'text/comma-separated-values',
	    'application/excel',
	    'application/vnd.ms-excel',
	    'application/vnd.msexcel',
	    'text/anytext',
	    'application/octet-stream',
	    'application/txt',
	    'application/download'
	);

	public $message = array();

	public function process($all_uploads){
	    if (!is_array($all_uploads)) {
	        $all_uploads = array($all_uploads);
	    }

	    foreach ($all_uploads as $upload) {
	        if (!is_a($upload, 'Symfony\Component\HttpFoundation\File\UploadedFile')) {
	            continue;
	        }

	        $originalName = $upload->getClientOriginalName();
	        $validator = Validator::make(array('file' => $upload), $this->rules);

	        if ($validator->passes()) {
	           	$this->moveFile($upload);
	        } else {
	        	if(in_array($upload->getMimeType(), $this->extraAllowedMimeTypes)){
	        		$this->moveFile($upload);
	        	} else {
	     	       $this->message['error'][$originalName] = 'File "' . $upload->getClientOriginalName() . '":' . $validator->messages()->first('file');
	        	}
	        }
	    }

	    return $this->message;
	}

	public function moveFile($upload){
		$originalExtension = $upload->getClientOriginalExtension();
		$originalName = $upload->getClientOriginalName();
		
		try {
			$this->message['success'][$originalName] = $upload->move('uploads/' . $originalExtension, $originalName);
		} catch (Exception $e){
	        $this->message['error'][$originalName] = 'File "' . $upload->getClientOriginalName() . '":' . $e->getMessage();
		}
		
	}
}
