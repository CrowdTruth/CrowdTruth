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
			
			return Response::json($output[0]);

		} catch (Exception $e){
			//throw $e; // for debugging.
			$return['error'] = $e->getMessage();
			$return['status'] = 'bad';
		} 

		return $this->returnJson($return);
	}

	public function postFeatures(){
		
		$input = Input::get();
		
		$url = $input[0];
		$domain = $input[1];
		$type = $input[2];
		$parse = parse_url($url);
		$source = $parse['host'];

		// CREATE ENTITY & ACTIVITY FOR NEW IMAGES
		try {
			// Save activity
			$activity = new Activity;
			$activity->label = "Images posted for processing.";
			$activity->softwareAgent_id = 'ImageGetter'; 
			$activity->save();
			
			// Save images as parent
			$image = new Entity;
			$image->domain = $domain;
			$image->format = "image";
			$content = $image->content; 
			$content['url'] = $url; 
			$image->content = $content;
			$image->documentType = $type;
			$image->source = $source;
			$image->softwareAgent_id = "imagegetter";


			// CHECK WHETHER URL EXISTS ALREADY
			$hash = md5(serialize($image->content));
            if($existingid = Entity::where('hash', $hash)->pluck('_id'))
                $imageid = $existingid; // Don't save, it already exists.
            else {
	            $image->hash = $hash;
				$image->activity_id = $activity->_id;
				$image->save();
				$id = $image->_id;
			}
			
			Session::flash('flashSuccess', "Stored image to database, features will be added shortly.");
				

		}	catch (Exception $e){
			//delete image
			if(isset($image))
				$image->forceDelete();
					
			//delete activity
			if($activity) $activity->forceDelete();
			
			Session::flash('flashError', $e->getMessage());
			return Redirect::to("temp");
		
		}

		// RUN PYTHON SCRIPT THAT CALLS APIs TO ADD FEATURES TO IMAGE
		try {
			$command = "/usr/bin/python2.7 /var/www/crowd-watson/app/lib/getAPIS/getMany.py" . $domain . " " . $type . " " .  $url . " " . $id;
			
			exec($command,$output,$error);
			
			return Response::json($output[0]);

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
					break;
				
				default:
					throw new Exception('Unknown documenttype.');
					break;
				}

			}
		} catch (Exception $e){
			//throw $e; // for debugging.
			$return['error'] = $e->getMessage();
			$return['status'] = 'bad';
		}

		return $this->returnJson($return);
	}


	private function returnJson($return){
		return Response::json($return);
	}

	/* Data in post is object with an array of recipients plus a message-object */
	public function postMessage(){
		

		$return = array('status' => 'ok');
		$groupedarray = array();

		try {
			foreach ($recipient as $r) {
				$explid = explode('/', $r);
				$platformid = $explid[1];
				$groupedarray[$platformid][] = $recipient;
			}

			foreach ($groupedarray as $platformworkers) {
				$platform = App::make(array_keys($platformworkers));
				//$platform->sendMessage(array_values($platformworkers), $subject, $content);
				dd(json_encode($platformworkers));
				$return['message'] = 'sent';
			}

		} catch (Exception $e){
			$return['error'] = $e->getMessage();
			$return['status'] = 'bad';
		}
		return $return;
	}

	/* Data in post is object with an array of recipients plus a message-object */
	public function postFlag(){

	}
}

?>