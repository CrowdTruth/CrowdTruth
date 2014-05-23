<?php
namespace CrowdTruth\Crowdflower\Cfapi;
require_once 'CFBasicRequests.inc.php';
require_once 'CFAddFunctions.php';

class Job extends CFBasicRequests {
    	function __construct($api_key) {
        	$this->setApiKey($api_key);
			$this->setReferenceResource("jobs");
    	}

	/**
    * Pause a CrowdFlower job in order to temporarily stop judgments from coming in.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function pauseJob($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/pause.json";
		$result = $this->curlRequest($url, "GET", null);
		return $result;
	}

	/**
    * Resume a paused CrowdFlower job.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function resumeJob($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/resume.json";
		$result = $this->curlRequest($url, "GET", null);
		return $result;
	}

	/**
    * Cancel a CrowdFlower job if you want to permanently stop judgments from coming in and refund your account for any judgments not yet received.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function cancelJob($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/cancel.json";
		$result = $this->curlRequest($url, "GET", null);
		return $result;
	}

	/**
    * Delete a CrowdFlower job that was either cancelled or not ordered.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function deleteJob($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . ".json";
		$result = $this->curlRequest($url, "DELETE", null);
		return $result;
	}

	/**
    * Copy a CrowdFlower job with all its units.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function copyJobWithAllUnits($job_id) {
		$data = array("all_units" => "true");
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/copy.json";
		$result = $this->curlRequest($url, "POST", $data);
		return $result;
	}

	/**
    * Copy a CrowdFlower job without units.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function copyJobWithoutUnits($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/copy.json";
		$result = $this->curlRequest($url, "POST", null);
		return $result;
	}

	/**
    * Copy a CrowdFlower job by extracting only its gold units.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function copyJobOnlyGoldUnits($job_id) {
		$data = array("gold" => "true");
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/copy.json";
		$result = $this->curlRequest($url, "POST", $data);
		return $result;
	}
	
	/**
    * Create a CrowdFlower job.
    * @param string $data (fields that can be part of the data: title, judgments_per_unit, max_judgments_per_worker, units_per_assignment, max_judgments_per_ip, webhook_uri, send_judgments_webhook => true, payment_cents, instructions, css, js, cml)
    * @return Array $result (answer returned by the cURL request) => from here we can extract the id of the job just created
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function createJob($data) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . ".json";
		$result = $this->curlRequest($url, "POST", prefixData($data, "job"));
		return $result;	
	}

	/**
    * Update an existing CrowdFlower job.
    * @param string $job_id 
	* @param string $data (same attributes as described for createJob)
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function updateJob($job_id, $data) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . ".json";
		$result = $this->curlRequest($url, "PUT", prefixData($data, "job"));
		return $result;	
	}

	/**
    * Add data to an existing job by uploading a CSV file.
    * @param string $job_id 
	* @param string $file_path 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#uploading
    */
	public function uploadInputFile($job_id, $file_path) {
		$data['file_path'] = $file_path;
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/upload.json";
		$result = $this->curlRequest($url, "UPLOAD", $data);
		return $result;	
	}

	/**
    * Check the status/progress of a CrowdFlower job.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function statusJob($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/ping.json";
		$result = $this->curlRequest($url, "GET", null);
		return $result;
	}

	/**
    * Check the legend of a CrowdFlower job (shows the generated keys that will end up being submitted with your form).
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function legendJob($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/legend.json";
		$result = $this->curlRequest($url, "GET", null);
		return $result;
	}

	/**
    * Read all the information about an existing CrowdFlower job.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function readJob($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . ".json";
		$result = $this->curlRequest($url, "GET", null);
		return $result;
	}

	/**
    * Set the channels for a CrowdFlower job (to be published on).
    * @param string $job_id 
	* @param Array $channels (cf_internal and on_demand)
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function setChannels($job_id, $channels) {
		$data = array();
		$data["stringRequest"] = "";
		for ($i = 0; $i < count($channels); $i ++) {
			$data["stringRequest"] .= "channels[]=".$channels[$i]."&";
		}
		$data["stringRequest"] = substr($data["stringRequest"], 0, -1);
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/channels";
		$result = $this->curlRequest($url, "PUT", $data);
		return $result;
	}

	/**
    * Get the channels of a CrowdFlower job.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request): contains the list of enabled channels
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function getChannels($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/channels";
		$result = $this->curlRequest($url, "GET", null);
		// for all the channels available, please use this return call:
		// return $result["result"]["available_channels"];
		// for both available and enabled channels, please use this return call:
		// return $result["result"];
		return $result["result"]["enabled_channels"];
	}

	/**
    * Set the countries whose workers are allowed to work on a CrowdFlower job.
    * @param string $job_id 
	* @param Array $countries (contains the codes for those countries) => please check the CF website for the list of codes
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function setIncludedCountries($job_id, $countries) {
		$data = array();
		$data["stringRequest"] = "";
		for ($i = 0; $i < count($countries); $i ++) {
			$data["stringRequest"] .= "job[included_countries][]=".$countries[$i]."&";
		}
		$data["stringRequest"] = substr($data["stringRequest"], 0, -1);
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . ".json";
		$result = $this->curlRequest($url, "PUT", $data);
		return $result;
	}

	/**
    * Set the list of options for a CrowdFlower job.
    * @param string $job_id 
	* @param Array $data (req_ttl_in_seconds, mail_to, keywords)
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function setOptions($job_id, $data) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . ".json";
		$result = $this->curlRequest($url, "PUT", prefixData($data, "job"));
		return $result;
	}

	/**
    * Set or reset the gold questions for a CrowdFlower job. Depends on the input file uploaded. 
    * @param string $job_id 
	* @param Array $data (possible fields: reset, check, with)
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
    */
	public function manageGold($job_id, $data) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/gold";
		if (isset($data["reset"]))
			$result = $this->curlRequest($url, "PUT", $data);
		else 
			$result = $this->curlRequest($url, "PUT", prefixData($data, "job"));
		return $result;
	}

	/**
    * Return a list of ids representing the CrowdFlower unit_id for each input sentence in the CSV file.
    * @param string $job_id 
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#units
    */
	public function getUnitsIds($job_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/units.json";
		$result = $this->curlRequest($url, "GET", null);
		return array_keys($result["result"]);
	}

	/**
    * Return the judgments received for a unit
    * @param string $job_id 
	* @param string $job_id
    * @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#judgments
    */
	public function getUnitJudgments($job_id, $unit_id) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/units/". $unit_id . ".json";
		//$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/". $unit_id . ".json";
		$result = $this->curlRequest($url, "GET", null);
		return $result["result"];
	}

	/**
	* Order a created job.
	* @param string $job_id 
	* @param string $unitsToOrder count of the units to be ordered.
	* @return Array $result (answer returned by the cURL request)
    * @throws 
    * @link http://crowdflower.com/docs-api#jobs
	*/
	public function sendOrder($job_id, $unitsToOrder, $channels) {
		$url = $this->getRequestURL() . $this->getReferenceResource() . "/" . $job_id . "/orders";
		$data["stringRequest"] = "debit[units_count]=" . $unitsToOrder;

		for ($i = 0; $i < count($channels); $i ++) {
			$data["stringRequest"] .= "&channels[]=".$channels[$i];
		}

		$result = $this->curlRequest($url, "POST", $data);
		return $result; 
	}

}

?>
