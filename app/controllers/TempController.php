<?php

class TempController extends BaseController {

 	public function getIndex() {
	  	return View::make('temp.imagegetter');
	}

	public function getImgselection() {
		return View::make('temp.imgselection');
	}
	
}
