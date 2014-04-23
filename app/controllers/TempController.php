<?php

class TempController extends BaseController {

 	public function getIndex() {
	  	return View::make('temp.imagegetter');
	}

	
	public function getImagegetter(){
		return View::make('temp.imagegetter');
	}
}
