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
		$return = array('status'=>'ok');
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
		$domain = $input[1];
		$type = $input[2];
		
		// CREATE ACTIVITY FOR BATCH
		$activity = new Activity;
		$activity->label = "Images posted for processing.";
		$activity->softwareAgent_id = 'imagegetter'; 
		$activity->save();
		// LOOP THROUGH IMAGES CREATE ENTITIES WITH ACTIVITY-ID FOR NEW IMAGES
		$url_ids = "";
		foreach ($input[0] as $img){

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

				\Log::debug($e->getMessage());

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
			\Log::debug("Running $command");
		    exec($command, $output, $error);
		    
			$return['oo'] = $output; 			
			$return['ee'] = $error;
			//$return['a'] = $a;
						//throw $e; // for debugging.
			//return $error; 

		} catch (Exception $e){
			//throw $e; // for debugging.
			\Log::debug("ERROR: " . $e->getMessage());
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
		
		// Now on every call!!!
		throw new Exception("This feature is disabled for demo accounts. Sorry!"); 
	}

	private function returnJson($return){
		return Response::json($return);
	}

	/* Data in post an array of 'recipients' plus a 'message' array (with content and subject) */
	public function postMessage(){

		try {
			$return = array('status' => 'ok');
			$content = Input::get('messagecontent');
			$subject = Input::get('messagesubject');
			$recipients = explode(',', Input::get('messageto'));

			$this->authenticateUser();

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
					$crowdagent = CrowdAgent::where('_id', $workerid)->first();
					$crowdagent->recievedMessage($subject, $content);
				}		

			}

		} catch (Exception $e){
			$return['message'] = $e->getMessage();
			$return['status'] = 'bad';
		}
		return $return;
	}


	/**
	* needs workerId and message in postdata.
	*/
	public function postBlock(){
		try {
			$return = array('status' => 'ok');
			$this->authenticateUser();

			$message = Input::get('blockmessage');
			$workerid = Input::get('workerid');

			$crowdagent = CrowdAgent::where('_id', $workerid)->first();
			$crowdagent->block($message);
			$return['message'] = "Blocked worker $workerid successfully.";
		} catch (Exception $e){
			$return['message'] = $e->getMessage();
			$return['status'] = 'bad';
		}

		return $return;

	}

	/**
	* needs workerId and message in postdata.
	*/
	public function postUnblock(){
		try {
			$return = array('status' => 'ok');

			$this->authenticateUser();
			$message = Input::get('unblockmessage');
			$workerid = Input::get('workerid');

			$crowdagent = CrowdAgent::where('_id', $workerid)->first();
			$crowdagent->unblock($message);
			$return['message'] = "Unblocked worker $workerid successfully.";
		} catch (Exception $e){
			$return['message'] = $e->getMessage();
			$return['status'] = 'bad';
		}

		return $return;

	}


	public function getWorker($crowdagent, $platform, $id, $action){
		try {
			$return = array('status' => 'ok');
			$workerid = "crowdagent/$platform/$id";
			
			if($crowdagent!="crowdagent")
				throw new Exception('No crowdagent selected.');
			
			$workerid = "crowdagent/$platform/$id";
			$crowdagent = CrowdAgent::where('_id', $workerid)->first();

			if(!$crowdagent)
				throw new Exception('CrowdAgent not found.');

			switch ($action) {

				case 'flag':
					$crowdagent->flag();
					$return['message'] = "Flagged worker $workerid.";
					break;
				case 'unflag':
					$crowdagent->unflag();
					$return['message'] = "Unflagged worker $workerid.";
					break;

				default:
					throw new Exception('Action unknown.');
					break;
			}

		} catch (Exception $e) {
			$return['message'] = $e->getMessage();
			$return['status'] = 'bad';
		}

		return $return;	
	}

}

?>
