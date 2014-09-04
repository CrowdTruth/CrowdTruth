<?php namespace CrowdTruth\Crowdflower;

use \Exception;
use \Config;
use \App;
use \View;
use \Input;
use \CrowdTruth\Crowdflower\Cfapi\CFExceptions;
use \CrowdTruth\Crowdflower\Cfapi\Job;
use \CrowdTruth\Crowdflower\Cfapi\Worker;
//use Job;

class Crowdflower2 extends \FrameWork {

	protected $CFJob = null;

	public function getLabel(){
		return "Crowdsourcing platform: Crowdflower";
	}	

	public function getName(){
		return "CrowdFlower";
	}

	public function getExtension(){
		return 'cml';
	}

	public function getJobConfValidationRules(){
		return array(
			'workerunitsPerUnit' => 'required|numeric|min:1',
			'unitsPerTask' => 'required|numeric|min:1',
			'instructions' => 'required',
			'workerunitsPerWorker' => 'required|numeric|min:1');
	}

	public function __construct(){
		$this->CFJob = new Job(Config::get('crowdflower::apikey'));
	}
		
	public function createView(){
		// Validate settings
		if(Config::get('crowdflower::apikey')=='')
			Session::flash('flashError', 'API key not set. Please check the manual.');

		return View::make('crowdflower::create');
	}

	public function updateJobConf($jc){
		if(Input::has('workerunitsPerWorker')){ // Check if we really come from the CF page (should be the case)
			$c = $jc->content;
			$c['countries'] = Input::get('countries', array());
			$jc->content = $c;
		}
			
		return $jc;
	}

	/**
	* @return 
	*/
	public function publishJob($job, $sandbox){
		try {
			return $this->cfPublish($job, $sandbox);
		} catch (CFExceptions $e) {
			if(isset($id)) $this->undoCreation($id);
			throw new Exception($e->getMessage());
		}	
	}

	/**
	* @throws Exception
	*/
	public function undoCreation($id){
		if(!isset($id)) return;
		try {
			$this->CFJob->cancelJob($id);
			$this->CFJob->deleteJob($id);
		} catch (CFExceptions $e) {
			throw new Exception($e->getMessage()); // Let Job take care of this
		} 	

	}




	/**
    * @return String id of published Job
    */
    private function cfPublish($job, $sandbox){
    	$jc = $job->jobConfiguration;
    	//dd($jc->title);
		//$template = $job->template;
		$data = $this->jobConfToCFData($jc);	
		$csv = $this->batchToCSV($job->batch, $job->questionTemplate);
		$gold = $jc->answerfields;
		$options = array(	"req_ttl_in_seconds" => (isset($jc->content['expirationInMinutes']) ? $jc->content['expirationInMinutes'] : 30)*60, 
							"keywords" => (isset($jc->content['requesterWorkerunit']) ? $jc->content['requesterWorkerunit'] : ''),
							"mail_to" => (isset($jc->content['notificationEmail']) ? $jc->content['notificationEmail'] : ''));
    	
    	//if($jc->content['workerUnitsPerWorker'] < $jc->content['unitsPerTask'])
    	//	throw new CFExceptions('WorkerUnits per worker should be larger than units per task.');
    	
    	try {

    		// TODO: check if all the parameters are in the csv.
			// Read the files
			/*foreach(array('cml', 'css', 'js') as $ext){
				$filename = public_path() . "/templates/$template.$ext";
				if(file_exists($filename) && is_readable($filename))
					$data[$ext] = file_get_contents($filename);
			}

			if(empty($data['cml']))
				throw new CFExceptions("CML file $filename does not exist or is not readable.");
				*/
			/*if(!$sandbox) $data['auto_order'] = true; // doesn't seem to work */

			// Create the job with the initial data
			$result = $this->CFJob->createJob($data);
			$id = $result['result']['id'];

			// Add CSV and options
			if(isset($id)) {
				
				// Not in API or problems with API: 
				// 	- Channels (we can only order on cf_internal)
				//  - Tags / keywords
				//  - Worker levels (defaults to '1')
				//  - Expiration?
				$debug = false;

				if($debug) {
					print "\r\n\r\nRESULT";
					print_r($result);
				}	

				$csvresult = $this->CFJob->uploadInputFile($id, $csv);
				if(!$debug) unlink($csv); // DELETE temporary CSV.

				if($debug) {
					print "\r\n\r\nCSVRESULT";
					print_r($csvresult);
				}	

				if(isset($csvresult['result']['error']['message']))
					throw new CFExceptions("CSV: " . $csvresult['result']['error']['message']);

				$optionsresult = $this->CFJob->setOptions($id, array('options' => $options));

				if($debug) {
					print "\r\n\r\nOPTIONSRESULT";
					print_r($optionsresult);
				}


				if(isset($optionsresult['result']['error']['message']))
					throw new CFExceptions("setOptions: " . $optionsresult['result']['error']['message']);

				$channelsresult = $this->CFJob->setChannels($id, array('cf_internal'));
				
				if($debug) {
					print "\r\n\r\nCHANNELSRESULT";
					print_r($channelsresult);
				}

				if(isset($channelsresult['result']['error']['message']))
					throw new CFExceptions($channelsresult['result']['error']['message']); 

					
				if(is_array($gold) and count($gold) > 0){
					// TODO: Foreach? 
					$goldresult = $this->CFJob->manageGold($id, array('check' => $gold[0]));
					
					if($debug) {
						print "\r\n\r\nGOLDRESULT";
						print_r($goldresult);
					}

					if(isset($goldresult['result']['error']['message']))
						throw new CFExceptions("Gold: " . $goldresult['result']['error']['message']);

				}

				if(isset($jc->content['countries']) and is_array($jc->content['countries']) and count($jc->content['countries']) > 0){
					$countriesresult = $this->CFJob->setIncludedCountries($id, $jc->content['countries']);
					
					if($debug) {
						print "\r\n\r\nCOUNTRIESRESULT";
						print_r($countriesresult);
					}	

					if(isset($countriesresult['result']['error']['message']))
						throw new CFExceptions("Countries: " . $countriesresult['result']['error']['message']);
							
				}

				if(!$sandbox and isset($csvresult)){
					$orderresult = $this->CFJob->sendOrder($id, count($job->batch->parents), array("cf_internal"));
					if(isset($orderresult['result']['error']['message']))
						throw new CFExceptions("Order: " . $orderresult['result']['error']['message']);
				
					if($debug) {
						print "\r\n\r\nORDERRESULT";
						print_r($orderresult);
					}	
				}

				if($debug)
					dd("\r\n\r\nEND");

				$response = array('id' => $id);

				// Get the URL for CF_INTERNAL
				if(isset($result['result']['secret'])){
					$s = urlencode($result['result']['secret']);
					$response['url'] = "https://tasks.crowdflower.com/channels/cf_internal/jobs/$id/work?secret=$s";
				}

				return $response;

			// Failed to create initial job. Todo: more different errors.
			} else {
				$err = $result['result']['error']['message'];
				if(isset($err)) $msg = $err;
				elseif(isset($result['http_code'])){
					if($result['http_code'] == 503 or $result['http_code'] == 504) $msg = 'Crowdflower service is unavailable, possibly down for maintenance?';
					else $msg = "Error creating job on Crowdflower. HTTP code {$result['http_code']}";
				}	// empty(method) is not allowed in PHP <5.5
				elseif(Config::get('crowdflower::apikey')=='') $msg = 'Crowdflower API key not set. Please check the manual.';
				else $msg = "Invalid response from Crowdflower. Is the API down? <a href='http://www.crowdflower.com' target='_blank'>(link)</a>";
				throw new CFExceptions($msg);
			}
		} catch (ErrorException $e) {
			if(isset($id)) $this->CFJob->deleteJob($id);
			throw new CFExceptions($e->getMessage());
		} catch (CFExceptions $e){
			if(isset($id)) $this->CFJob->deleteJob($id);
			throw $e;
		} 
    }




	public function refreshJob($id){
		
		$job = \MongoDB\Entity::where('_id', $id)->first();
		$batch = \MongoDB\Entity::where('_id', $job->batch_id)->first();
		$jc = \MongoDB\Entity::where('_id', $job->jobConf_id)->first();
		$result = $this->CFJob->readJob($job->platformJobId);
		if(isset($result['result']['error']['message']))
			throw new Exception("Read: " . $result['result']['error']['message']);

		// dd($jc);
		//dd($result['result']['title']);

		$this->CFDataToJobConf($result['result'], $jc);
		
		$jc->update();

		//dd($jc);
		if(!isset($job->projectedCost)){
				$reward = $jc->content['reward'];
				$workerunitsPerUnit = intval($jc->content['workerunitsPerUnit']);
				$unitsPerTask = intval($jc->content['unitsPerTask']);
				$unitsCount = count($batch->wasDerivedFrom);
                if(!$unitsPerTask)
                    $projectedCost = 0;
                else
				    $projectedCost = round(($reward/$unitsPerTask)*($unitsCount*$workerunitsPerUnit), 2);

                $job->expectedWorkerunitsCount=$unitsCount*$jc->content['workerunitsPerUnit'];
                $job->projectedCost = $projectedCost;
            }
        $job->update();
	}

	 private function CFDataToJobConf($CFd, &$jc){ // TODO
		$jcco = $jc->content;
		if(isset($CFd['title']))  				$jcco['title'] = 				$CFd['title'];
		if(isset($CFd['instructions'])) 		$jcco['instructions'] =			$CFd['instructions'];
		if(isset($CFd['css'])) 					$jcco['css'] =					$CFd['css'];
		if(isset($CFd['cml'])) 					$jcco['cml'] =					$CFd['cml'];
		if(isset($CFd['js'])) 					$jcco['js'] =					$CFd['js'];
		if(isset($CFd['state'])) 				$jcco['status'] =				$CFd['state'];
		if(isset($CFd['payment_cents'])) 		$jcco['reward'] =				$CFd['payment_cents']/100;
		if(isset($CFd['minimum_requirements'])) 		$jcco['minimumRequirements'] =				$CFd['minimum_requirements'];

		if(isset($CFd['options']['req_ttl_in_seconds'])) 				$jcco['expirationInMinutes'] =	intval($CFd['options']['req_ttl_in_seconds']/60);
		if(isset($CFd['options']['mail_to'])) 				$jcco['notificationEmail'] =	$CFd['options']['mail_to'];
		if(isset($CFd['judgments_per_unit'])) 	$jcco['workerunitsPerUnit'] = 	$CFd['judgments_per_unit'];
		if(isset($CFd['units_per_assignment'])) $jcco['unitsPerTask'] = 		$CFd['units_per_assignment'];
		if(isset($CFd['max_judgments_per_worker'])) $jcco['workerunitsPerWorker'] = 		$CFd['max_judgments_per_worker'];
		if(isset($CFd['max_judgments_per_ip'])) $jcco['workerunitsPerWorker']     = 		$CFd['max_judgments_per_ip'];
		// reward, keywords, expiration, workers_level, 

		$jc->content = $jcco;
		
	}


    private function jobConfToCFData($jc){
		$jc=$jc->content;
		$data = array();
		//if(isset($jc['keywords'])) 			 	$data['tags']					 	= $jc['keywords'];
		if(isset($jc['title'])) 			 	$data['title']					 	= $jc['title'];
		if(isset($jc['css'])) 			 		$data['css']					 	= $jc['css'];
		if(isset($jc['cml'])) 			 		$data['cml']					 	= $jc['cml'];
		if(isset($jc['js'])) 			 		$data['js']					 		= $jc['js'];
		//if(isset($jc['reward'])) 			 	$data['payment_cents']		 		= $jc['reward'];

		if(isset($jc['instructions'])) 			$data['instructions']				= $jc['instructions'];
		if(isset($jc['workerunitsPerUnit'])) 	$data['judgments_per_unit']		  	= $jc['workerunitsPerUnit'];
		if(isset($jc['unitsPerTask']))			$data['units_per_assignment']		= $jc['unitsPerTask'];
		if(isset($jc['workerunitsPerWorker']))	{
			$data['max_judgments_per_worker']	= $jc['workerunitsPerWorker'];
			$data['max_judgments_per_ip']		= $jc['workerunitsPerWorker']; // We choose to keep this the same.
		}



		// Webhook doesn't work on localhost and the uri should be set. 
		if((!(strpos(\Request::url(), 'localhost')>0)) and (Config::get('crowdflower::webhookuri') != '')){
			$data['webhook_uri'] = Config::get('crowdflower::webhookuri');
			$data['send_judgments_webhook'] = 'true';
			\Log::debug("Webhook: {$data['webhook_uri']}");
		} else {
			$data['webhook_uri'] = Config::get('crowdflower::webhookuri');
			$data['send_judgments_webhook'] = 'true';
			\Log::debug("Warning: no webhook set.");
		}
		return $data;
	}

	/**
	* @return path to the csv, ready to be sent to the CrowdFlower API.
	*/
		public function batchToCSV($batch, $questionTemplate, $path = null){

		// Create and open CSV file
		if(empty($path)) {
			$path =storage_path() . '/temp/crowdflower.csv';
			if (!file_exists(storage_path() . '/temp'))
   			 	mkdir(storage_path() . '/temp', 0777, true);
		}
		$out = fopen($path, 'w');

		// Preprocess batch
		$array = array();
		$units = $batch->wasDerivedFrom;
		
		foreach ($units as $unit){
			unset($unit['content']['properties']);

/*
			if(!isset($unit['content']['sentence']['formatted']))// TODO SHOULDN'T HAPPEN!!!!
				$unit['content']['sentence']['formatted'] = $unit['content']['sentence']['text'];*/

/*			$c = array_change_key_case(array_dot($row['content']), CASE_LOWER);
			foreach($c as $key=>$val){
				$key = strtolower(str_replace('.', '_', $key));
				if(isset($replaceValues[$key][$val]))
					$val = $replaceValues[$key][$val];
				
				$content[$key] = $val;
			}*/
			//$content = $questionTemplate->flattenAndReplace($unit['content']);

			// Add fields
			//if ($unit['format'] === "image")
			//	$content['url'] = $unit['content']['url'];

			$c = array_change_key_case(array_dot($unit['content']), CASE_LOWER);
			foreach($c as $key=>$val){
				$key = strtolower(str_replace('.', '_', $key));
				$content[$key] = $val;
			}
			//dd($content);
			$content['uid'] = $unit['_id'];
			$content['_golden'] = 'false'; // TODO
			$array[] = $content;
		}	

		// Headers and fields
		fputcsv($out, array_keys($array[0]));
		foreach ($array as $row)
			fputcsv($out, $row);	
		
		// Close file
		rewind($out);
		fclose($out);

		return $path;
	}

    public function orderJob($job){
    	$id = $job->platformJobId;
    	$unitcount = count($job->batch->wasDerivedFrom);
    	$this->hasStateOrFail($id, 'unordered');
		$result = $this->CFJob->sendOrder($id, $unitcount, array("cf_internal"));
		if(isset($result['result']['error']['message']))
			throw new Exception("Order: " . $result['result']['error']['message']);
	}

	public function pauseJob($id){
		//$this->hasStateOrFail($id, 'running');
		$result = $this->CFJob->pauseJob($id);
		if(isset($result['result']['error']['message']))
			throw new Exception("Pause: " . $result['result']['error']['message']);
	}

	public function resumeJob($id){
		//$this->hasStateOrFail($id, 'paused');
		$result = $this->CFJob->resumeJob($id);
		if(isset($result['result']['error']['message']))
			throw new Exception("Resume: " . $result['result']['error']['message']);
	}

	public function cancelJob($id){
		//$this->hasStateOrFail($id, 'running'); // Rules?
		$result = $this->CFJob->cancelJob($id);
		if(isset($result['result']['error']['message']))
			throw new Exception("Cancel: " . $result['result']['error']['message']);
	}

	private function hasStateOrFail($id, $state){
		$result = $this->CFJob->readJob($id);

		if(isset($result['result']['error']['message']))
			throw new Exception("Read Job: " . $result['result']['error']['message']);

    	if($result['result']['state'] != $state)
    		throw new Exception("Can't perform action; state is '{$result['result']['state']}' (should be '$state')");
	}

	public function blockWorker($id, $message){
		$cfWorker = new Worker(Config::get('crowdflower::apikey'));
		try {
			$cfWorker->blockWorker($id, $message);
		} catch (CFExceptions $e){
			throw new Exception($e->getMessage());
		} 
	}

	public function unblockWorker($id, $message){
		$cfWorker = new Worker(Config::get('crowdflower::apikey'));
		try {
			$cfWorker->unblockWorker($id, $message);
		} catch (CFExceptions $e){
			throw new Exception($e->getMessage());
		} 
	}


	public function sendMessage($subject, $body, $workerids){
		throw new Exception('Messaging is currently not possible with CrowdFlower, sorry!');
	}

}

?>
