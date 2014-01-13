<?php
	/**
	*
	* Rename this file to config.php and fill out your API keys and debugging settings.
	*
	*/
	
	// Comment out one of the following lines. The first is for testing and the second is the 'real' one.
	define('AMT_ROOT_URL', 'https://mechanicalturk.sandbox.amazonaws.com/');
	//define('AMT_ROOT_URL', 'https://mechanicalturk.amazonaws.com/';

	// Your API Keys ( https://portal.aws.amazon.com/gp/aws/securityCredentials )
	define('AWS_ACCESS_KEY', 'youraccesskey');
	define('AWS_SECRET_KEY', 'yoursecretkey');
	
	
	// Currently: echo messages when performing API calls.
	// See log() in MechanicalTurk.class.php to customize this behaviour.
	define('DEBUG', false);  
	error_reporting(E_ALL);
	
	// Functions throw an AMTException. If you don't handle them, this function does it for you.
	function handleException($exception) {
		echo "<b>Unhandled Exception:</b> " , $exception->getMessage();
	}

	set_exception_handler('handleException');


	
?>