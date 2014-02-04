<?php

use crowdwatson\MechanicalTurkService;

class PostProcessController extends BaseController {

	public function getIndex() {
	   return Redirect::to('postprocess/listview');
	}

	public function getListview() {
		// Uncomment for mock entry for listview
		// $ct = CrowdTask::fromJSON(base_path() . '/public/templates/relation_direction/relation_direction_multiple.json');
		// $ct->name = 'Mock-object #3';
		// $ct->judgmentsPerUnit = 10;
		// $ct->unitsPerTask = 25;
		// $ct->reward = 0.02;
		// $ct->platform = array('CF');
		// $ct->flaggedWorkers = 5;
		// $ct->template = "Relation Direction";
		// $ct->save();
		$crowdtasks = CrowdTask::all();
	   	return View::make('postprocess.listview')->with('crowdtasks', $crowdtasks);	
	}
	
	public function getListModel() {
		
	}

	// public function getTableview() {
	// 	// $ct->name = 'Table view mock';
	// 	// $ct->save();
	//    $crowdtasks = CrowdTask::all();
	//    return View::make('postprocess.tableview')->with('crowdtasks', $crowdtasks);
	// }

		
}
