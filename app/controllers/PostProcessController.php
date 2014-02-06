<?php

use crowdwatson\MechanicalTurkService;

class PostProcessController extends BaseController {

 	public $restful = true;

	public function getIndex() {
	   return Redirect::to('postprocess/listview');
	}

	public function getListview() {
		// Uncomment for mock entry for listview
		// $ct = CrowdTask::fromJSON(base_path() . '/public/templates/relation_direction/relation_direction_multiple.json');
		// $ct->name = 'Mock-object #5';
		// $ct->judgmentsPerUnit = 2;
		// $ct->unitsPerTask = 10;
		// $ct->reward = 0.50;
		// $ct->platform = array('AMT');
		// $ct->flaggedWorkers = 2;
		// $ct->template = "Factor Span";
		// $ct->save();
		$crowdtasks = CrowdTask::orderBy('completion','desc')->paginate(15);
		return View::make('postprocess.listview')->with('crowdtasks', $crowdtasks);
	}
	

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
