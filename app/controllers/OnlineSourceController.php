<?php

class OnlineSourceController extends BaseController {

 	public function getIndex() {
	  	return View::make('onlinesource.imagegetter');
	}

	
	public function getImagegetter(){
		return View::make('onlinesource.imagegetter');
	}

	/* Rename the template below to fit your online source */
	// public function getOnlinesourcetemplate() {
	// 	return View::make('onlinesource.onlinesourcetemplate');
	// }
}
