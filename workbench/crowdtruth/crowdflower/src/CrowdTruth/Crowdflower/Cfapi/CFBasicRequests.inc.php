<?php
namespace CrowdTruth\Crowdflower\Cfapi;
require_once 'CFAddFunctions.php';

class CFBasicRequests {
	private $content_header = array("Accept" => "application/json");
	private $upload_header = array("Content-Type" => "text/csv");
        private $api_key = "";
	private $url = "http://api.crowdflower.com/v1/";
	private $reference_resource = "";
	private $current_resource = "";
	private $retValue = array();
	private $fh;
        
	/**
        * Initiate a cURL request to CrowdFlower.
        * @param $url (request uri)
	* @param $method (request method: GET, POST, PUT, UPLOAD)
	* @param $data 
        * @return request result, errors, request info
        * @throws 
        * @link http://crowdflower.com/docs-api#curl
        */
  	public function curlRequest($url, $method, $data) {
		$url .= "?key=" . $this->getApiKey();
	    	$ch = curl_init();

		$this->setRequestHeaders($method, $ch);
		$this->setRequestMethod($method, $data, $ch);
		$this->setRequestOptions($url, $data, $ch);

		$result = objectToArray(json_decode(curl_exec($ch)));
        	$info = $this->getInfo($ch);
       		$error = $this->getError($ch);
        
        	curl_close($ch);
		if (is_resource($this->fh))
			fclose($this->fh);

		$retValue["result"] = $result;
		$retValue["error"] = $error; 
		$retValue["info"] = $info;
        	return $retValue;
	}

	/**
        * Set the options for the cURL request to CrowdFlower.
        * @param $url (request uri)
	* @param $method (request method: GET, POST, PUT, UPLOAD)
	* @param $data (data sent through request)
        * @throws 
        */
	protected function setRequestOptions($url, $data, $ch) {
        	if (!empty($data)) 
			if (!isset($data['stringRequest'])) {
				//echo http_build_query($data, '', '&');
            			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
			}
			else 
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data['stringRequest']);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
	}

	/**
        * Set the headers for the cURL request to CrowdFlower.
	* @param $method (request method: GET, POST, PUT, UPLOAD)
	* @param $ch (cURL request) 
        * @throws 
        */
	public function setRequestHeaders($method, $ch) {
	        $headers = array();
		if (strtoupper($method) == "UPLOAD") {
			foreach ($this->upload_header as $key => $value) {
            			$headers[] = $key.': '.$value;
       			}		
		}
		else {
			foreach ($this->content_header as $key => $value) {
            			$headers[] = $key.': '.$value;
       			}
		}
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
	}

	/**
        * Set the method for the cURL request to CrowdFlower.
	* @param $method (request method: GET, POST, PUT, UPLOAD)
	* @param $ch (cURL request) 
        * @throws 
        */
	public function setRequestMethod($method, $data, $ch) {
	    if (strtoupper($method) == "GET") {
           	curl_setopt($ch, CURLOPT_HTTPGET, true);
		} else if (strtoupper($method) == "UPLOAD") {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_UPLOAD, true);
			$this->fh = fopen($data['file_path'], 'r');
			curl_setopt($ch, CURLOPT_INFILE, $this->fh);
			curl_setopt($ch, CURLOPT_INFILESIZE, filesize($data['file_path']));
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
       	}
    }

	/**
        * Return information related to the cURL request to CrowdFlower.
	* @param $ch (cURL request) 
	* @return curl_getinfo
        */
	function getInfo($ch){
        	return curl_getinfo($ch);
	}

	/**
        * Return possible errors of the cURL request to CrowdFlower.
	* @param $ch (cURL request) 
	* @return curl_errno
        */
	function getError($ch) {
	        return curl_errno($ch) ? curl_error($ch) : false;
	}

	public function setHeaders($headers) {
    		$this->headers = $headers;
  	}
  
  	public function getHeaders() {
    		return $this->headers;
  	}
  
  	public function setApiKey($key) {
    		$this->api_key = $key;
  	}
  
  	public function getApiKey() {
    		return $this->api_key;
  	}
  
  	public function setRequestURL($url) {
    		$this->url = $url;
  	}
  
  	public function getRequestURL() {
    		return $this->url;
  	}
  
  	public function setCurrentResource($current_resource) {
      		$this->current_resource = $current_resource;
  	}

  	public function getCurrentResource() {
      		return $this->current_resource;
  	}

  	public function setReferenceResource($reference_resource) {
      		$this->reference_resource = $reference_resource;
  	}

  	public function getReferenceResource() {
      		return $this->reference_resource;
  	}

}



?>


