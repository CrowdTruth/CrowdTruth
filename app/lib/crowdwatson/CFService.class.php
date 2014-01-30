<?php

namespace crowdwatson;

require_once(dirname(__FILE__) . '/crowdflower/CFJob.inc.php');

class CFService {
	private $job;

	public function __construct(){
		$this->job = new Job("c6b735ba497e64428c6c61b488759583298c2cf3"); 
	}


	/**
	* @throws CFExceptions
	*/
	public function createJob($data, $csv, $template, $gold, $options){
		$job = $this->job;
		try {

			// Read the files
			foreach(array('cml', 'css', 'js') as $ext){
				$filename = "$template.$ext";
				if(file_exists($filename) || is_readable($filename))
					$data[$ext] = file_get_contents($filename);
			}

			if(empty($data['cml']))
				throw new CFExceptions('CML file does not exist or is not readable.');

			// Create the job with the initial data
			$result = $job->createJob($data);
			$id = $result['result']['id'];

			// Add CSV and options
			if(isset($id)) {
				// TODO: countries, workerskills, expiration, keywords

				$optionsresult = $job->setOptions($id, array('options' => $options));
				if(isset($optionsresult['result']['errors']))
					throw new CFExceptions($optionsresult['result']['errors'][0]);

				$csvresult = $job->uploadInputFile($id, $csv);
				if(isset($csvresult['result']['errors']))
					throw new CFExceptions($csvresult['result']['errors'][0]);

				$channelsresult = $job->setChannels($id, array('cf_internal'));
				if(isset($channelsresult['result']['errors']))
					throw new CFExceptions($goldresult['result']['errors'][0]);

				if(is_array($gold) and count($gold) > 0){
					// TODO: Foreach? 
					$goldresult = $job->manageGold($id, array('check' => $gold[0]));
					if(isset($goldresult['result']['errors']))
						throw new CFExceptions($goldresult['result']['errors'][0]);
				}
			// Failed to create initial job.
			} else {
				$err = $result['result']['errors'][0];
				if(isset($err)) $msg = $err;
				else $msg = 'Unknown error.';
				throw new CFExceptions($msg);
			}
		} catch (ErrorException $e) {
			if(isset($id)) $job->deleteJob($id);
			throw new CFExceptions($e->getMessage());
		} catch (CFExceptions $e){
			if(isset($id)) $job->deleteJob($id);
			throw $e;
		} 

	}


	public function readJob($id){
		return $this->job->readJob($id);
	}


}