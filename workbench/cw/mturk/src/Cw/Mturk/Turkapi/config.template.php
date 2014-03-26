<?php

	// Echo messages when performing API calls.
	// Adapt log() in MechanicalTurk.class.php to suit your needs.
	define('DEBUG', false);  

	// Comment out one of the following lines. The first is for testing and the second is the 'real' one.
	define('AMT_ROOT_URL', 'https://mechanicalturk.sandbox.amazonaws.com/');
	//define('AMT_ROOT_URL', 'https://mechanicalturk.amazonaws.com/';

	// Your API Keys ( https://portal.aws.amazon.com/gp/aws/securityCredentials )
	define('AWS_ACCESS_KEY', 'yourkey');
	define('AWS_SECRET_KEY', 'yoursecret');

	
?>