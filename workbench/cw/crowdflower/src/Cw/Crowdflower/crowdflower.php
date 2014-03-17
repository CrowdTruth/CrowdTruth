<?php namespace Cw\Crowdflower;

use \Exception;
use \Config;
use \App;
use \View;
use Cfapi\CFExceptions;
//use Cfapi\Job;

class Crowdflower {

	public $label = "Crowdsourcing platform: Crowdflower";
	protected $CFJob = null;

	public $jobConfValidationRules = array(
		'annotationsPerUnit' => 'required|numeric|min:1', // AMT: defaults to 1 
		'unitsPerTask' => 'required|numeric|min:1',
		'instructions' => 'required',
		'annotationsPerWorker' => 'required|numeric|min:1'
	);

	public function createView(){
		return View::make('crowdflower::create');
	}

	/**
	* @return 
	*/
	public function publishJob($job, $sandbox){
		try {
			if(is_null($this->CFJob)) $this->CFJob = new Cfapi\Job(Config::get('crowdflower::apikey'));
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
		if(is_null($this->CFJob)) $this->CFJob = new Cfapi\Job(Config::get('crowdflower::apikey'));
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
		
		$options = array(	"req_ttl_in_seconds" => $jc->expirationInMinutes*60, 
							"keywords" => $jc->requesterAnnotation, 
							"mail_to" => $jc->notificationEmail);
    	try {

    		// TODO: check if all the parameters are in the csv.
			// Read the files
			foreach(array('cml', 'css', 'js') as $ext){
				$filename = "$template.$ext";
				if(file_exists($filename) || is_readable($filename))
					$data[$ext] = file_get_contents($filename);
			}

			if(empty($data['cml']))
				throw new CFExceptions('CML file does not exist or is not readable.');


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

				if(is_array($jc->countries) and count($jc->countries) > 0){
					$countriesresult = $this->CFJob->setIncludedCountries($id, $jc->countries);
					if(isset($countriesresult['result']['error']))
						throw new CFExceptions("Countries: " . $countriesresult['result']['error']['message']);
				//print "\r\n\r\nCOUNTRIESRESULT";
				//print_r($countriesresult);				
				}

				if(!$sandbox){
					$orderresult = $this->CFJob->sendOrder($id, count($job->batch->ancestors), array("cf_internal"));
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
				else $msg = 'Unknown error.';
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

    private function jobConfToCFData($jc){
		$data = array();

		if (!empty($jc->title)) 			 	$data['title']					 	= $jc->title; 
		if (!empty($jc->instructions)) 			$data['instructions']				= $jc->instructions; 
		if (!empty($jc->annotationsPerUnit)) 	$data['judgments_per_unit']		  	= $jc->annotationsPerUnit;
		if (!empty($jc->unitsPerTask))			$data['units_per_assignment']		= $jc->unitsPerTask;
		if (!empty($jc->annotationsPerWorker))	{
			$data['max_judgments_per_worker']	= $jc->annotationsPerWorker;
			$data['max_judgments_per_ip']		= $jc->annotationsPerWorker; // We choose to keep this the same.
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