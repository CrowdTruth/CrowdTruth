<?php

class PreprocessController extends BaseController {

	protected $fileRepository;

	public function __construct(Filerepository $fileRepository){
		$this->fileRepository = $fileRepository;
	}

	public function getIndex(){
		if(!count(Cart::content()) > 0){
			Session::flash('flashNotice', 'You have not added any items to your selection yet');
			return Redirect::to('files/browse');
		}
        return View::make('preprocess.index');
	}
}