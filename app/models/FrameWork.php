<?php

abstract class FrameWork {

	/**
	* @return String the label of the platform
	*/
	abstract public function getLabel();

	/**
	* @return String the name of the platform, displayed in the 'platform' tab
	*/
	abstract public function getName();

	/**
	* @return String the extension of a question template
	*/
	abstract public function getExtension();
	
	/**
	* @return Array with Laravel style validationrules, to validate the fields added to JobConf.
	* @link http://laravel.com/docs/validation
	*/
	abstract public function getJobConfValidationRules();

	/**
	* Create the view where the user can select JobConf settings specific to this platform.
	* @return return View::make('[platform]::create');
	*/
	abstract public function createView();

	/**
	* Is fired after submitting the specific JobConf settings. 
	* Can handle the Input of the view, optionally performing some logic on it.
	* @return JobConfiguration
	*/
	public function updateJobConf($jc){
		return $jc;
	}

	/**
	* Create the job on the platform and in our database, but don't order it yet.
	* Important: when you catch an error in creating, call $this->undoCreation
	* @return platformJobId
	*/
	abstract public function publishJob($job, $sandbox);

	/**
	* If an error occurs during creation, this method gets called. Make sure the job is deleted on the platform.
	* @throws Exception
	*/
	abstract public function undoCreation($ids);

	/**
	* @throws Exception
	*/
    abstract public function orderJob($job);

	/**
	* @throws Exception
	*/
	abstract public function pauseJob($id);

	/**
	* @throws Exception
	*/
	abstract public function resumeJob($id);


}

?>