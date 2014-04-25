<?php

use crowdwatson\MechanicalTurkService;

class JobsController extends BaseController {

 	public $restful = true;

	public function getIndex() {
	   return Redirect::to('jobs/listview');
	}

	//Change JobConfiguration into JobConfiguration
	public function getListview() {
		$jobConfigurations = JobConfiguration::orderBy('annotationsPerUnit','asc')->paginate(15);
		return View::make('jobs.listview')->with('jobConfigurations', $jobConfigurations);
	}

	public function getTableview(){
		return View::make('jobs.tableview');
	}

	/** via routes this method for sorting is called
	param 1 is the sorting method and param 2 is desc/asc in string
	based on params  the result is returned in the right way **/
	public function sortModel($method, $sort){
	$jobConfigurations = JobConfiguration::orderBy($method, $sort)->paginate(15);
	return View::make('jobs.results')->with('jobConfigurations', $jobConfigurations);
	}	

	public function createdBy($term){
		// TODO refine query 
		return View::make('jobs.results')->with(JobConfiguration::where('createdBy', '=', $term ));
	}
	
}
