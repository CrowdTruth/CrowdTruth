<?php namespace Api\import;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;

use \SoftwareComponents\ResultImporter as ResultImporter;

use \Exception;

class apiController extends BaseController
{

    public function postImportresults()
    {
	
		$files = Input::file('file');
		
		$settings = [];
		$settings['filename'] = basename($files->getClientOriginalName(), '.csv');
		$settings['project'] = Input::get('input-project');
		$settings['documentType'] = Input::get('input-type');
		$settings['resultType'] = Input::get('output-type');
		
		$settings['domain'] = 'opendomain';
		$settings['format'] = 'text';

		// process file
		$importer = new ResultImporter();
		$status = $importer->process($files, $settings);

		echo 'done';
    }


}

?>
