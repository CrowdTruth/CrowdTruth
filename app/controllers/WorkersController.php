<?php

use crowdwatson\MechanicalTurkService;

class WorkersController extends BaseController {

 	public $restful = true;

	public function getIndex() {
	   return View::make('workers.overview');
	}

	
}
