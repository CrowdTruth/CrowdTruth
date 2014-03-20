<?php namespace Cw\Crowdflower;

use \Exception;
use \Config;
use \App;
use \View;
use \Input;
use \Cw\Crowdflower\Cfapi\CFExceptions;
use \Cw\Crowdflower\Cfapi\Job;
//use Job;

class Crowdflower {

	public $label = "Crowdsourcing platform: Crowdflower";
	protected $CFJob = null;

	public $jobConfValidationRules = array(
		'annotationsPerUnit' => 'required|numeric|min:1', // AMT: defaults to 1 
		'unitsPerTask' => 'required|numeric|min:1',
		'instructions' => 'required',
		'annotationsPerWorker' => 'required|numeric|min:1'
	);

	public function __construct(){
		$this->CFJob = new Job(Config::get('crowdflower::apikey'));
	}
		
	public function createView(){
		return View::make('crowdflower::create');
	}

	public function updateJobConf($jc){
		if(Input::has('annotationsPerWorker'))
			$jc->countries = Input::get('countries', array());
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
		$template = $job->template;
		$data = $this->jobConfToCFData($jc);	
		$csv = $this->batchToCSV($job->batch);
		$gold = $jc->answerfields;

		$options = array(	"req_ttl_in_seconds" => (isset($jc->content['requesterAnnotation']) ? $jc->content['requesterAnnotation'] : '')*60, 
							"keywords" => (isset($jc->content['notificationEmail']) ? $jc->content['notificationEmail'] : ''), 
							"mail_to" => (isset($jc->content['expirationInMinutes']) ? $jc->content['expirationInMinutes'] : 0));
    	try {

    		// TODO: check if all the parameters are in the csv.
			// Read the files
			foreach(array('cml', 'css', 'js') as $ext){
				$filename = public_path() . "/templates/$template.$ext";
				if(file_exists($filename) || is_readable($filename))
					$data[$ext] = file_get_contents($filename);
			}

			if(empty($data['cml']))
				throw new CFExceptions("CML file $filename does not exist or is not readable.");

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

				//print "\r\n\r\nRESULT";
				//print_r($result);				
				$csvresult = $this->CFJob->uploadInputFile($id, $csv);
				unlink($csv); // DELETE temporary CSV.
				if(isset($csvresult['result']['error']))
					throw new CFExceptions("CSV: " . $csvresult['result']['error']['message']);
				//print "\r\n\r\nCSVRESULT";
				//print_r($csvresult);
				$optionsresult = $this->CFJob->setOptions($id, array('options' => $options));
				if(isset($optionsresult['result']['error']))
					throw new CFExceptions("setOptions: " . $optionsresult['result']['error']['message']);
				//print "\r\n\r\nOPTIONSRESULT";
				//print_r($optionsresult);
				$channelsresult = $this->CFJob->setChannels($id, array('cf_internal'));
				if(isset($channelsresult['result']['error']))
					throw new CFExceptions($channelsresult['result']['error']['message']); 
				//print "\r\n\r\nCHANNELSRESULT";
				//print_r($channelsresult);
				if(is_array($gold) and count($gold) > 0){
					// TODO: Foreach? 
					$goldresult = $this->CFJob->manageGold($id, array('check' => $gold[0]));
					if(isset($goldresult['result']['error']))
						throw new CFExceptions("Gold: " . $goldresult['result']['error']['message']);
				//print "\r\n\r\nGOLDRESULT";
				//print_r($goldresult);
				}

				if(isset($jc->content['countries']) and is_array($jc->content['countries']) and count($jc->content['countries']) > 0){
					$countriesresult = $this->CFJob->setIncludedCountries($id, $jc['countries']);
					if(isset($countriesresult['result']['error']))
						throw new CFExceptions("Countries: " . $countriesresult['result']['error']['message']);
				//print "\r\n\r\nCOUNTRIESRESULT";
				//print_r($countriesresult);				
				}

				if(!$sandbox and isset($csvresult)){
					$orderresult = $this->CFJob->sendOrder($id, count($job->batch->parents), array("cf_internal"));
					if(isset($orderresult['result']['error']))
						throw new CFExceptions("Order: " . $orderresult['result']['error']['message']);
				//print "\r\n\r\nORDERRESULT";
				//print_r($orderresult);
				//dd("\r\n\r\nEND");
				}

				return $id;

			// Failed to create initial job.
			} else {
				$err = $result['result']['error']['message'];
				if(isset($err)) $msg = $err;
				else $msg = 'Unknown error. Is the Crowdflower API key set correctly?';
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


    public function orderJob($id){
    	$this->hasStateOrFail($id, 'unordered');
		$result = $this->CFJob->sendOrder($id, count($job->batch->parents), array("cf_internal"));
		if(isset($result['result']['error']))
			throw new Exception("Order: " . $result['result']['error']['message']);
	}

	public function pauseJob($id){
		$this->hasStateOrFail($id, 'running');
		$result = $this->CFJob->pauseJob($id);
		if(isset($result['result']['error']))
			throw new Exception("Pause: " . $result['result']['error']['message']);
	}

	public function resumeJob($id){
		$this->hasStateOrFail($id, 'paused');
		$result = $this->CFJob->resumeJob($id);
		if(isset($result['result']['error']))
			throw new Exception("Resume: " . $result['result']['error']['message']);
	}

	public function cancelJob($id){
		//$this->hasStateOrFail($id, 'running'); // Rules?
		$result = $this->CFJob->cancelJob($id);
		if(isset($result['result']['error']))
			throw new Exception("Cancel: " . $result['result']['error']['message']);
	}

	private function hasStateOrFail($id, $state){
		$result = $this->CFJob->readJob($id);

		if(isset($result['result']['error']))
			throw new Exception("Read Job: " . $result['result']['error']['message']);

    	if($result['result']['state'] != $state)
    		throw new Exception("Can't order job with status '{$result['result']['state']}'");
	}

    private function jobConfToCFData($jc){
		$jc=$jc->content;
		$data = array();


		if(isset($jc['title'])) 			 	$data['title']					 	= $jc['title'];
		if(isset($jc['instructions'])) 			$data['instructions']				= $jc['instructions'];
		if(isset($jc['annotationsPerUnit'])) 	$data['judgments_per_unit']		  	= $jc['annotationsPerUnit'];
		if(isset($jc['unitsPerTask']))			$data['units_per_assignment']		= $jc['unitsPerTask'];
		if(isset($jc['annotationsPerWorker']))	{
			$data['max_judgments_per_worker']	= $jc['annotationsPerWorker'];
			$data['max_judgments_per_ip']		= $jc['annotationsPerWorker']; // We choose to keep this the same.
		}

		// Webhook doesn't work on localhost and we the uri should be set. 
		if((App::environment() != 'local') and (Config::get('config.cfwebhookuri')) != ''){
			
			$data['webhook_uri'] = Config::get('config.cfwebhookuri');
			$data['send_judgments_webhook'] = 'true';
		}
		return $data;
	}

	/**
	* @return path to the csv, ready to be sent to the CrowdFlower API.
	*/
	public function batchToCSV($batch, $path = null){

		if(empty($path)) {
			$path = base_path() . '/app/storage/temp/crowdflower.csv';
			if (!file_exists(base_path() . '/app/storage/temp')) {
   			 	mkdir(base_path() . '/app/storage/temp', 0777, true);
			}
		}

		//$tmpfname = tempnam("/tmp", "csv");
		$out = fopen($path, 'w');
		//$out = fopen('php://memory', 'r+');

		$units = $batch->wasDerivedFrom;
		$array = array();
		foreach ($units as $row){
			$content = $row['content'];
			$content['uid'] = $row['_id'];
			$content['_golden'] = 'false';
			unset($content['properties']);
			$array[] = $content;
		}	

		$headers = $array[0];

		fputcsv($out, array_change_key_case(str_replace('.', '_', array_keys(array_dot($headers))), CASE_LOWER));
		
		foreach ($array as $row){
			// TODO: replace
			fputcsv($out, array_dot($row));	
		}
		
		rewind($out);
		fclose($out);

		return $path;
	}

}

?>
