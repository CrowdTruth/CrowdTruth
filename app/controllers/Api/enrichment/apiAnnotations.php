<?php namespace Api\enrichment;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;

use \SoftwareComponents\ResultImporter as ResultImporter;
use \SoftwareComponents\DIVEUnitsImporter as DIVEUnitsImporter;

use \Exception;

class apiAnnotations extends BaseController
{
    // public function getStatus()
    public function anyStatus()
    {
      $body = file_get_contents('php://input');
      $tickets = json_decode( $body );

      $annotationStatus = [];
      foreach ($tickets as $ticket)
      {
        $ticketStatus = [
          "ticket" => $ticket,
          // HERE WE NEED SOME CODE TO CHECK THE STATUS OF A TICKET
          "status" => "pending"
        ];
        array_push($annotationStatus, $ticketStatus);
      }

      return [
        "status"  =>  "success",
        "message" =>  "string",
        "annotationStatus"=> $annotationStatus
      ];
    }

    // public function getCollect()
    public function anyCollect()
    {
      // TO BE IMPLEMENTED
      return ['ok - annotation collect -- should be POST'];
    }

    // public function getSend($capability)
    public function anySend($capability)
    {
      // TO BE IMPLEMENTED
      return ['ok - annotation send ('.$capability.') -- should be POST'];
    }

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

    public function postImportdiveunits()
    {
		$signal = Input::get('signal');
		$user = Input::get('user');
		$project = Input::get('project');
		$source = Input::get('source');
		$description = Input::get('description');
		$docType = Input::get('documentType');
		$payload = Input::get('payload');

		$settings = [];
		$settings['user'] = $user;
		$settings['project'] = $project;
		$settings['source'] = $source;
		$settings['docType'] = $docType;
		$settings['description'] = $description;

		// process request
		$importer = new DIVEUnitsImporter();
		$status = $importer->process($signal, $payload, $settings);

		return $status;
    }


}

?>
