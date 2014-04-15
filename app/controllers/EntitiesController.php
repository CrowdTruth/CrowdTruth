<?php

class EntitiesController extends BaseController {

 	public $restful = true;

	public function getIndex() {
	   return Redirect::to('/');
	}

	public function getAnnotation() {
		return View::make('entities.annotation');
	}

	public function getUnit() {
		return View::make('entities.unit');
	}
}
