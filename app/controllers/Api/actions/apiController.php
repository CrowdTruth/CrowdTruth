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
		$return = array('status'=>'ok');
		$input = Input::get();
		//dd($input);
		$urlset = array();
		foreach($input[0] as $url){
			array_push($urlset, $url);
		} // Jelle..
				
		$domain = $input[1];
		$type = $input[2];
		
		// CREATE ACTIVITY FOR BATCH
		$activity = new Activity;
		$activity->label = "Images posted for processing.";
		$activity->softwareAgent_id = 'imagegetter'; 
		$activity->save();
		// LOOP THROUGH IMAGES CREATE ENTITIES WITH ACTIVITY-ID FOR NEW IMAGES
		$url_ids = "";
		foreach ( $urlset as $img){
			
			try {
				
				$parse = parse_url($img);
				$source = $parse['host'];
								
				// Save images as parent
				$image = new Entity;
				$image->domain = $domain;
				$image->format = "image";
				$content = $image->content; 
				$content['url'] = $img; 
				$image->content = $content;
				$image->documentType = $type;
				$image->source = $source;
				$image->activity_id = $activity->_id;
				$image->softwareAgent_id = "imagegetter";
				// Take last part of URL as image title
				$temp = explode('/', $img);
				$image->title = end($temp);


				// CHECK WHETHER URL EXISTS ALREADY
				$hash = md5(serialize($image->content));
	            if($existingid = Entity::where('hash', $hash)->pluck('_id'))
	                $imageid = $existingid; // Don't save, it already exists.
	            else {
		            $image->hash = $hash;
					$image->activity_id = $activity->_id;
					$image->save();
					$existingid = $image->_id;
					
				}
				$url_ids .= "$img $existingid ";
						
			}	catch (Exception $e){
				//delete image
				if(isset($image))
					$image->forceDelete();
				
						
				//delete activity
				if(isset($activity)) $activity->forceDelete();
				
				//Session::flash('flashError', $e->getMessage());
				$return['error'] = $e->getMessage();
				$return['status'] = 'bad';
				return $return;
			
			}
			// RUN PYTHON SCRIPT THAT CALLS APIs TO ADD FEATURES TO IMAGE
		}
		//return $url_ids;
		try {
			//$command = "/usr/bin/python2.7 /var/www/crowd-watson/app/lib/getAPIS/getRijks.py " . $domain . " " . $type . " " . 4 . " " . "vogel";
			$command = "/usr/bin/python2.7 /var/www/crowd-watson/app/lib/getAPIS/getMany.py " . $domain . " " . $type . " " . $url_ids;
			//$command = "/usr/bin/python2.7 /var/www/crowd-watson/app/lib/getAPIS/getMany.py art painting http://lh3.ggpht.com/Q1GZTdmwa8iTLgdbu5uAgzovmLbb7lsYhG-QgVcoN8A-WJtIsNUo4-VyTMd9iKHLp-XNm812WyUaSgQdHdjQjDioJQI=s0 999";
			//return $command;
		    exec($command, $output, $error);	
			$return['oo'] = $output; 			
			$return['ee'] = $error;
			//$return['a'] = $a;
						//throw $e; // for debugging.
			//return $error; 

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
					
					$this->authenticateUser();

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
						case 'delete':
							$job->cancel(); // TODO SOFT DELETE
							$return['message'] = 'Job canceled, soft delete not yet implemented.';
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


	private function authenticateUser(){
		if (!\Auth::check())
			throw new Exception("Authentication failed.");   

		if (\Auth::user()->role == 'demo')
			throw new Exception("Demo accounts are not allowed to do this.");  
		
	}

	private function returnJson($return){
		return Response::json($return);
	}

	/* Data in post an array of 'recipients' plus a 'message' array (with content and subject) */
	public function postMessage(){
		$return = array('status' => 'ok');
		$content = Input::get('content');
		$subject = Input::get('subject');
		$recipients = Input::get('recipients');

		$content = 'testttt';
		$subject = 'subject of message';
		$recipients = array('crowdagent/amt/1111', 'crowdagent/amt/2222', 'crowdagent/cf/3333', 'crowdagent/amt/4444');
		
		try {
			$groupedarray = array();

			foreach ($recipients as $recipient) {
				$explid = explode('/', $recipient);
				$platformid = $explid[1];
				$groupedarray[$platformid][] = $recipient;
			}
			foreach ($groupedarray as $platformid=>$workers) {
				$platform = \App::make($platformid);
				$platform->sendMessage($workers, $subject, $content);
				$return['message'] = 'Message' . (count($workers)>1 ? 's' : '') . ' sent successfully!';
				
				foreach($workers as $workerid){
					//$crowdagent = CrowdAgent::where('_id', $workerid)->first();
					//$crowdagent->
				}		

			}

		} catch (Exception $e){
			$return['message'] = $e->getMessage();
			$return['status'] = 'bad';
		}
		return $return;
	}


	/**
	* Needs 'message' and 'workerid' in POST
	*/
	public function postFlag(){
		try {
			$return = array('status' => 'ok');
			$message = Input::get('message');
			$workerid = Input::get('workerid');

			$crowdagent = CrowdAgent::where('_id', $workerid)->first();
			$crowdagent->flag();

		} catch (Exception $e){
			$return['message'] = $e->getMessage();
			$return['status'] = 'bad';
		}

		return $return;

	}

	public function getGetdropdowninfos(){
		foreach(Job::get() as $job){
			$format[] = $job->format;
			$domain[] = $job->domain;
			$user[] = $job->user_id;
			$template[] = $job->template;
			$platform[] = $job->platform;
			$status[] = $job->status;
		}

		return array('format'=> array_unique($format),
					'domain'=> array_unique($format),
					'user'=> array_unique($format),
					'template'=> array_unique($format),
					'status'=> array_unique($format));
	}
}

?>
