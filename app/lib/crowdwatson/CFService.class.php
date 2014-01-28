<?php

namespace crowdwatson;

require_once(dirname(__FILE__) . '/crowdflower/CFJob.inc.php');

class CFService {
	private $apikey = "c6b735ba497e64428c6c61b488759583298c2cf3";

	public function createJob($data){
		dd($data);
		$job = new Job($this->apikey); 
		$result = $job->createJob($data);
		dd($result);

	}




}