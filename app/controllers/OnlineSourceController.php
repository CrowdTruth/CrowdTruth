<?php

class OnlineSourceController extends BaseController {

 	public function getIndex() {
	  	return View::make('onlinesource.imagegetter');
	}

	
	public function getImagegetter(){
		return View::make('onlinesource.imagegetter');
	}
}
