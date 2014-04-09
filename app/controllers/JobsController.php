<?php

use crowdwatson\MechanicalTurkService;

class JobsController extends BaseController {

 	public $restful = true;

	public function getIndex() {
	   return Redirect::to('jobs/listview');
	}

	//Change JobConfiguration into JobConfiguration
	public function getListview() {
		return View::make('jobs.listview');
	}

	public function getTableview(){
		return View::make('jobs.tableview');
	}
}
