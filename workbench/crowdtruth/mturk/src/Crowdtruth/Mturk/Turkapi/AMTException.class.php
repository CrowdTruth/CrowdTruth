<?php
    /**
     * AMTException
     * 
     */
	namespace CrowdTruth\Mturk\Turkapi;
    class AMTException extends \Exception {
        public function __construct($message, $code = 0, \Exception $previous = null) {
			parent::__construct($message, $code, $previous);
        }
    }




?>