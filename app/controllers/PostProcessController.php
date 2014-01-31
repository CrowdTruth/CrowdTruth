<?php

use crowdwatson\MechanicalTurkService;

class PostProcessController extends BaseController {

	public function getIndex() {
	   return Redirect::to('postprocess/listview');
	}

	public function getListview() {
		$ct = CrowdTask::fromJSON(base_path() . '/public/templates/relation_direction/relation_direction_multiple.json');
		$ct->name = 'Mock-object #2';
		$ct->judgmentsPerUnit = 5;
		$ct->unitsPerTask = 10;
		$ct->reward = 0.05;
		$ct->platform = array('cf', 'amt');
		$ct->save();
		$crowdtasks = CrowdTask::all();
	   	return View::make('postprocess.listview')->with('crowdtasks', $crowdtasks);	
	}
	
	// public function getTableview() {
	// 	// $ct->name = 'Table view mock';
	// 	// $ct->save();
	//    $crowdtasks = CrowdTask::all();
	//    return View::make('postprocess.tableview')->with('crowdtasks', $crowdtasks);
	// }

		
}
