<?php namespace Api\actions;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;
use \Job;
use \Exception;

class apiController extends BaseController {
	public $restful = true;
	private $content_type = 'application/json';

	//i.e.: image/art/painting/40/boat
	public function getImage($domain, $type, $numImg, $keyphrase){
		try {
			$command = "/usr/bin/python2.7 /var/www/crowd-watson/app/lib/getAPIS/getRijks.py " . $domain . " " . $type . " " . $numImg . " " . $keyphrase;
			
			exec($command,$output,$error);
			
			
			return $output[0];


		} catch (Exception $e){
			//throw $e; // for debugging.
			$return['error'] = $e->getMessage();
			$return['status'] = 'bad';
		} 

		return $this->returnJson($return);
	}

	//i.e.: entity/text/medical/job/1
	public function getEntity($format, $domain, $docType, $incr, $action){
		try {
			$return = array('status' => 'ok');
			$id = "entity/$format/$domain/$docType/$incr";
			switch ($docType) {

				case 'job':
					
					$job = Job::where('_id', $id)->first();

					if(!$job)
						throw new Exception('Job not found.');
					
					switch ($action) {
						case 'pause':
							$job->pause();
							$return['message'] = 'Job paused successfully.';
							break;
						case 'resume':
							$job->resume();
							$return['message'] = 'Job resumed successfully.';
							break;
						case 'cancel':
							$job->cancel();
							$return['message'] = 'Job canceled successfully.';
							break;
						case 'order':
							$job->order();
							$return['message'] = 'Job ordered successfully.';
							break;
						default:
							throw new Exception('Action unknown.');
							break;
					}
				break;
				default:
					throw new Exception("Unknown documenttype '$docType'.");
					break;
				}



		} catch (Exception $e){
			//throw $e; // for debugging.
			$return['message'] = $e->getMessage();
			$return['status'] = 'bad';
		}

		return $this->returnJson($return);
	}


	private function returnJson($return){
		return Response::json($return);
	}

}

?>