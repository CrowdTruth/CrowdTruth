<?php

use crowdwatson\MechanicalTurkService;

class PostProcessController extends BaseController {

 	public $restful = true;

	public function getIndex() {
	   return Redirect::to('postprocess/listview');
	}

	//Change JobConfiguration into JobConfiguration
	public function getListview() {
		// Uncomment for mock entry for listview
		$ct = JobConfiguration::fromJSON(base_path() . '/public/templates/relation_direction/relation_direction_multiple.json');
		$ct->createdBy = 'Oana';
		$ct->name = 'Mock-object #4';
		$ct->judgmentsPerUnit = 12;
		$ct->unitsPerTask = 8;
		$ct->reward = 0.02;
		$ct->platform = array('CF');
		$ct->flaggedWorkers = 3;
		$ct->template = "Relation Direction";
		$ct->save();
		$jobConfigurations = JobConfiguration::orderBy('judgmentsPerUnit','asc')->paginate(15);
		return View::make('postprocess.listview')->with('jobConfigurations', $jobConfigurations);
	}

	/** via routes this method for sorting is called
	param 1 is the sorting method and param 2 is desc/asc in string
	based on params  the result is returned in the right way **/
	public function sortModel($method, $sort){
	$jobConfigurations = JobConfiguration::orderBy($method, $sort)->paginate(15);
	return View::make('postprocess.results')->with('jobConfigurations', $jobConfigurations);
	}	

	public function createdBy($term){
		// TODO refine query 
		return View::make('postprocess.results')->with(JobConfiguration::where('createdBy', '=', $term ));

	public function sortModel($method){
		
	 switch ($method) {
	    case "completion":
	        $crowdtasks = CrowdTask::orderBy('completion','desc')->paginate(15);
	        return View::make('postprocess.results')->with('crowdtasks', $crowdtasks);
	        break;
	    case "cost":
	        $crowdtasks = CrowdTask::orderBy('totalCost','desc')->paginate(15);;
	        return View::make('postprocess.results')->with('crowdtasks', $crowdtasks);
	        break;
	    case "runningtime":
	        $crowdtasks = CrowdTask::orderBy('created_at','asc')->paginate(15);
	        return View::make('postprocess.results')->with('crowdtasks', $crowdtasks);
	        break;
	    case "jobsize":
	        $crowdtasks = CrowdTask::orderBy('jobSize','desc')->paginate(15);
	        return View::make('postprocess.results')->with('crowdtasks', $crowdtasks);
	        break;
	    case "flagged":
	        $crowdtasks = CrowdTask::orderBy('flaggedWorkers','desc')->paginate(15);
	        return View::make('postprocess.results')->with('crowdtasks', $crowdtasks);
	        break;
	    default:
	    	$crowdtasks = CrowdTask::orderBy('completion','asc')->paginate(15);
	        return View::make('postprocess.results')->with('crowdtasks', $crowdtasks);
	        break;
		}

	}
}
