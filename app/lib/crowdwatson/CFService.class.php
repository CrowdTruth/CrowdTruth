<?php

namespace crowdwatson;

require_once(dirname(__FILE__) . '/crowdflower/CFJob.inc.php');

class CFService {
	private $apikey = "c6b735ba497e64428c6c61b488759583298c2cf3";

	/**
	* @throws CFException
	*/
	public function createJob($data, $csv, $template, $gold, $options){
		try {
			$cmlfile = $template.'.cml';
			if(!file_exists($cmlfile) || !is_readable($cmlfile))
				throw new CFException('CML file does not exist or is not readable.');

			$data['cml'] = file_get_contents($cmlfile);
			$job = new Job($this->apikey); 
			$result = $job->createJob($data);
			$id = $result['result']['id'];

			if(isset($id)) {
				$optionsresult = $job->setOptions($id, $options);
				if(isset($optionsresult['error']['message']))
					throw new CFException($goldresult['error']['message']);

				$csvresult = $job->uploadInputFile($id, $csv);
				if(isset($csvresult['error']['message']))
					throw new CFException($csvresult['error']['message']);

				$channelsresult = $job->setChannels($id, array('cf_internal'));
				if(isset($channelsresult['error']['message']))
					throw new CFException($goldresult['error']['message']);

				if(is_array($gold) and count($gold) > 0){
					// TODO: Foreach? 
					$goldresult = $job->manageGold($id, array('check' => $gold[0]));
					if(isset($goldresult['error']['message']))
						throw new CFException($goldresult['error']['message']);
					dd($optionsresult);
				}

			} else {
				$err = $result['error']['message'];
				if(isset($err)) $msg = $err;
				else $msg = 'Unknown error.';
				throw new CFException($msg);
			}
		} catch (ErrorException $e) {
			throw new CFException($e->getMessage());
		} 

	}


		public function readJob($id){
			$job = new Job($this->apikey); 
			$result = $job->readJob($id);
			return $result;
		}


}