<?php

namespace CurlRequest;

class SVBasicRequest {
        private $content_header = array("Content-Type" => "application/xml");
        private $url = null;
	private $retValue = array();

//	function __construct() {
//       	}
        
        /**
        * Initiate a cURL request to Sound and Vision.
        * @param $url (request uri)
        * @param $method (request method: GET, POST)
        * @param $data 
        * @return request result, errors, request info
        * @throws 
        */
        public function curlRequest($url, $method, $data) {
                $ch = curl_init();

                $this->setRequestHeaders($method, $ch);
                $this->setRequestMethod($method, $data, $ch);
                $this->setRequestOptions($url, $data, $ch);

                $result = curl_exec($ch);
                $info = $this->getInfo($ch);
                $error = $this->getError($ch);
        
                curl_close($ch);

                $retValue["result"] = $result;
                $retValue["error"] = $error; 
                $retValue["info"] = $info;
                return $retValue;
        }

        /**
        * Set the options for the cURL request to Sound and Vision.
        * @param $url (request uri)
        * @param $method (request method: GET, POST)
        * @param $data (data sent through request)
        * @throws 
        */
        protected function setRequestOptions($url, $data, $ch) {
		if (isset($data)) {
        		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
		}
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
        }

        /**
        * Set the headers for the cURL request to Sound and Vision.
        * @param $method (request method: GET, POST)
        * @param $ch (cURL request) 
        * @throws 
        */
        public function setRequestHeaders($method, $ch) {
                $headers = array();
                foreach ($this->content_header as $key => $value) {
                	$headers[] = $key.': '.$value;
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        }

        /**
        * Set the method for the cURL request to Sound and Vision.
        * @param $method (request method: GET, POSTls )
        * @param $ch (cURL request) 
        * @throws 
        */
        public function setRequestMethod($method, $data, $ch) {
                if (strtoupper($method) == "GET") {
                        curl_setopt($ch, CURLOPT_HTTPGET, true);
                }
                else {
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                }
        }

        /**
        * Return information related to the cURL request to Sound and Vision.
        * @param $ch (cURL request) 
        * @return curl_getinfo
        */
        function getInfo($ch){
                return curl_getinfo($ch);
        }

        /**
        * Return possible errors of the cURL request to Sound and Vision.
        * @param $ch (cURL request) 
        * @return curl_errno
        */
        function getError($ch) {
                return curl_errno($ch) ? curl_error($ch) : false;
        }
  
        public function setRequestURL($url) {
                $this->url = $url;
        }
  
        public function getRequestURL() {
                return $this->url;
        }
}


?>

