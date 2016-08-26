<?php namespace Api\enrichment;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;

use \SoftwareComponents\ResultImporter as ResultImporter;
use \SoftwareComponents\DIVEUnitsImporter as DIVEUnitsImporter;

use \Exception;

/**
 * This class implements the 'annotation' calls from the Enrichment API. For more
 * details on the Enrichment API see:
 * https://github.com/beeldengeluid/labs-enrichment-api/
 */
class apiAnnotations extends BaseController
{
    // This function implements the POST /annotation/status/ API call
    public function postStatus()
    {
      $body = file_get_contents('php://input');
      $body = json_decode( $body , true);

      $tickets = $body['tickets'];

      $annotationStatus = [];
      foreach ($tickets as $ticket)
      {
        $ticketIds = explode(" - ", $ticket);
        $jobContent = \Entity::where("_id", $ticketIds[0])->first();

        if ($jobContent == NULL) {
          return [
            'status'  =>  'error',
            'message' =>  'Ticket ID ' . $ticket . ' does not exist!',
            'annotationStatus'=> $annotationStatus
          ];
        }
        else {

          if (isset($jobContent["metrics"])) {
            $ticketStatus = [
              'ticket' => $ticket,
              'status' => $jobContent["status"]
            ];
            array_push($annotationStatus, $ticketStatus);
          }
          else {
            $ticketStatus = [
              'ticket' => $ticket,
              'status' => "pending"
            ];
            array_push($annotationStatus, $ticketStatus);
          }
        }
      }

      return [
        'status'  =>  'success',
        'message' =>  'Supplying status for requested tickets',
        'annotationStatus'=> $annotationStatus
      ];
    }


    function array_flatten($array) { 
      if (!is_array($array)) { 
        return false; 
      } 
      $result = array(); 
      foreach ($array as $key => $value) { 
        if (is_array($value)) { 
          $result = array_merge($result, array_flatten($value)); 
        } else { 
          $result[$key] = $value; 
        } 
      } 
      return $result; 
    }

    // This function implements the POST /annotation/collect/ API call
    public function postCollect()
    {
      $body = file_get_contents('php://input');
      $body = json_decode( $body , true);
      $tickets = $body['tickets'];

      $annotations = [];

      foreach ($tickets as $ticket)
      {
        $data = [];
        $ticketComponents = explode(" - ", $ticket);
        $ticketJob = \Entity::where("_id", $ticketComponents[0])->first();
        $ticketUnit = \Entity::where("parents", [$ticketComponents[0], $ticketComponents[1]])->first();
        $ticketTemplate = \Entity::where("_id", $ticketJob["template_id"])->first();
        $outputParameters = $ticketTemplate["parameters"]["output"];

        foreach ($outputParameters as $parameter) {
          if (strpos($parameter["type"], "[]") !== false) {
            $dataParam = [
              "key" => $parameter["name"],
              "value" => array_flatten($ticketUnit["content"][$parameter["name"]])
            ];
          }
          else {
            $dataParam = [
              "key" => $parameter["name"],
              "value" => $ticketUnit["content"][$parameter["name"]];
            ];
          }
          array_push($data, $dataParam);
        }
        $ticketData = [ // This is a list of entities
            [
              "id"   => $ticket,
              "data" => $dataParam
            ]
        ];
        $ticketProvenance = [
          "data" => [
            [
              "key"   => "AnnotationTemplate",
              "value" => $ticketTemplate["type"];            
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

    // This function implements the POST /annotation/send/{capability} API call
    public function postSend($capability)
    {
      $template_id = $capability;

      $body = file_get_contents('php://input');
      $body = json_decode( $body , true);

      // process request
      $importer = new DIVEUnitsImporter();
      $status = $importer->process($body['data'], $template_id);

      if (count($status["error"]) == 0) {
        return [
          'status'  => 'success',
          'message' => implode(", ", $status['success']),
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
