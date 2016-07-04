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
          'ticket' => $ticket,
          // HERE WE NEED SOME CODE TO CHECK THE STATUS OF A TICKET
          'status' => 'pending'
        ];
        array_push($annotationStatus, $ticketStatus);
      }

      return [
        'status'  =>  'success',
        'message' =>  'Supplying status for requested tickets',
        'annotationStatus'=> $annotationStatus
      ];
    }

    // public function getCollect()
    public function anyCollect()
    {
      $body = file_get_contents('php://input');
      $tickets = json_decode( $body );

      $annotations = [];
      foreach ($tickets as $ticket)
      {
        $ticketData = [ // This is a list of entities
            [
              "id"   => "The ID the ticket has on the originating system",
              "data" => [// More key/value pairs can be present in the data.
                [
                  "key"   => "VALIDATION",
                  "value" => "OK"
                ]
              ]
            ]
        ];
        $ticketProvenance = [
          "data" => [
            [
              "key"   => "AnnotationTool",
              "value" => "CrowdTruth"
            ],
            [
              "key"   => "AnnotationTemplate",
              "value" => "Template1"
            ]
          ]
        ];
        $ticketAnnotations = [
          "ticket"     => $ticket,
          "data"       => $ticketData,
          "provenance" => $ticketProvenance
        ];
        array_push($annotations, $ticketAnnotations);
      }

      return [
        "message"  =>  "string",
        "status"   =>  'Sending annotations for requested tickets',
        "annotations" => $annotations
      ];
    }

    // public function getSend($capability)
    public function postSend($capability)
    {
      $user_token = Input::get('token');
      $project = Input::get('project');
      $template_id = $capability;

      $body = file_get_contents('php://input');

      $settings = [];
      $settings['token'] = $user_token;
      $settings['project'] = $project;
      $settings['template_id'] = $template_id;

      // process request
      $importer = new DIVEUnitsImporter();
      $status = $importer->process($body, $settings);

      if (count($status["error"] == 0)) {
        return [
          'status'  => 'success',
          'message' => $status['success'][0],
          'annotationStatus' => $status['annotationStatus']
        ];
      }
      else {
        return [
          'status'  => 'error',
          'message' => $status['error'][0],
          'annotationStatus' => $status['annotationStatus']
        ];
      }
/*
      $annotationStatus = [];
      foreach ($data as $datapoint)
      {
        // $datapoint=>data -- this is the data we need to annotate
        $datapointStatus = [
          'id'     => $datapoint->id,  // This is the original ID of the data sent
          'status' => 'accepted',
          'ticket' => 'CT_generated_ID_'.$datapoint->id
        ];
        array_push($annotationStatus, $datapointStatus);
      }

      return [
        'status'  => 'success',
        'message' => 'We are applying '.$capability.' to your data',
        'annotationStatus' => $annotationStatus
      ];
*/
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
