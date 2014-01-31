<?php
// namespace crowdwatson;
	require_once 'CFAddFunctions.php';

	/**
        * The -signal- parameter describes the type of event that has occurred. 
	* The -payload- parameter will contain a JSON representation of the associated object.
        * @return type of signal, job id
        * @link http://crowdflower.com/docs-api#webhooks
        */ 
	function getSignal() {
		$signal = $_POST["signal"];
		$payload = $_POST["payload"];
		if ($signal == "new_judgments")	{
			$retValue = objectToArray(json_decode($payload));
			$job_id = $retValue[0]["job_id"];
			handleNewJudgment($job_id);
		}
		if ($signal == "job_complete") {
			$retValue = objectToArray(json_decode($payload));
			$job_id = $retValue["id"];
			handleJobComplete($job_id);
		}
	}

	/**
        * TODO: update the number of judgments for a job and the completion percentage
        * @link http://crowdflower.com/docs-api#webhooks
        */ 
	function handleNewJudgments($job_id) {
		//here we should update in the DB the number of judgments for a job
		//update the job with the following id (increment by 1 the number of judgments made and then recompute the job completion percentage)
	}

	/**
        * TODO: update the running time for a job and based on the type of job apply the sentence and worker metrics
        * @link http://crowdflower.com/docs-api#webhooks
        */ 
	function handleJobComplete($job_id) {
		return ;
	}

	getSignal();

?>
