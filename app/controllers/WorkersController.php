<?php

use crowdwatson\MechanicalTurkService;

class WorkersController extends BaseController {

 	public $restful = true;

	public function getIndex() {
	   return View::make('workers.overview');
	}

	public function getWorker() {
		return View::make('workers.worker');
	}

	public function getMessage() {
		return View::make('workers.message');
	}

	public function getFlag() {
		return View::make('workers.flag');
	}
}
