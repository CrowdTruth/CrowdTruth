<?php

class ProcessController extends BaseController {

	public function getIndex() {
		// if(!count(Cart::content()) > 0){
		// 	Session::flash('flashNotice', 'You have not added any items to your selection yet');
		// 	return Redirect::to('files/browse');
		// }
        return View::make('process.index');
	}

	public function getSelectfile() {
		return View::make('process.tabs.selectfile');
	}

	public function getDetails() {
		return View::make('process.tabs.details');
	}

	public function getPlatform() {
		return View::make('process.tabs.platform');
	}

	public function getTemplate() {
		return View::make('process.tabs.template');
	}
}