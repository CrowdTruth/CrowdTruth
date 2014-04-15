<?php

class PreprocessController extends BaseController {
	
	public function getIndex(){
		// if(!count(Cart::content()) > 0){
		// 	Session::flash('flashNotice', 'You have not added any items to your selection yet');
		// 	return Redirect::to('files/browse');
		// }
        return Redirect::to('preprocess/fullvideo');
	}
}
