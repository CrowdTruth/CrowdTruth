<?php
namespace CrowdTruth\Crowdflower\Cfapi;
require_once 'CFBasicRequests.inc.php';

class Worker extends CFBasicRequests {
    	function __construct($api_key) {
        	$this->setApiKey($api_key);
		$this->setReferenceResource("jobs");
        	$this->setCurrentResource("workers");
    	}

	/**
        * Flags a worker that was identified as a spammer (persistent flagging). 
        * @param $job_id
        * @param $worker_id
        * @param $reason
        * @throws 
        */ 
	public function blockWorker($job_id, $worker_id, $reason) {
		$data = array('flag' => $reason, 'persist' => 'true'); 
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/" . $this->getCurrentResource() . "/" . $worker_id . '/flag';
		$result = $this->curlRequest($url, "PUT", $data);
		$this->analyzeWorkerResult($result, "block", $worker_id);	
	}

	/**
        * Unflags a worker that was previously identified as a spammer and flagged by mistake. 
        * @param $job_id
        * @param $worker_id
        * @param $reason
        * @throws 
        */
	public function unblockWorker($job_id, $worker_id, $reason) {
		$data = array('deflag' => $reason); 
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/" . $this->getCurrentResource() . "/" . $worker_id . '/deflag';
		$result = $this->curlRequest($url, "PUT", $data);	
		$this->analyzeWorkerResult($result, "unblock", $worker_id);
	}

	/**
        * Descriptive message for blockWorker and unblockWorker functions
        * @param $result (result of the curl request)
        * @param $worker_id
        * @param $reason
	* @return response message 
        * @throws 
        */
	protected function analyzeWorkerResult($result, $operation, $worker_id) {
		if ($result["error"] != "") {
			echo "An error occurred";
		}
		else {
			$keys = array_keys($result["result"]);
                	if ($keys[0] == "error") {
                        	echo "An error occurred when " . $operation . "ing contributor $worker_id! ";
                	}
                	else if ($keys[0] == "warning") {
                        	echo "Contributor $worker_id has already been " . $operation . "ed! ";         
                	}
                	else if ($keys[0] == "success") { 
                        	echo "Contributor $worker_id $operation" . "ed! ";  
			
			}
		}
	}
}

/* use case for flagging a worker */
/* argument: api key 
$worker = new Worker("c6b735ba497e64428c6c61b488759583298c2cf3"); 
$worker->blockWorker("365837", "3603786", "You performed as spammer on this job.");
*/
?>
