<?php namespace Joli\Validators;

class DocumentValidator extends Validator {

	protected static $rules = array(
	//	'file' => 'mimes:gif,jpg,jpeg,png,bmp,zip,zipx,txt,csv,doc,docx,xls,xlsx,pdf|max:2000'	
		'file' => 'mimes:gif|max:2000'	
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
}