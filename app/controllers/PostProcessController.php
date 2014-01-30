<?php

use crowdwatson\MechanicalTurkService;

class PostProcessController extends BaseController {

	public function getIndex() {
	   return Redirect::to('postprocess/listview');
	}

	public function getListview() {
		$crowdtasks = CrowdTask::all();
	   	return View::make('postprocess.listview')->with('crowdtasks', $crowdtasks);	
	}
	
	public function getTableview() {
		// $ct->name = 'Table view mock';
		// $ct->save();
	   $crowdtasks = CrowdTask::all();
	   return View::make('postprocess.tableview')->with('crowdtasks', $crowdtasks);
	}

		
}
