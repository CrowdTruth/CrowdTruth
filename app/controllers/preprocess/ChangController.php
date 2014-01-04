<?php

namespace preprocess;
use BaseController, fileRepository, Cart, View, App, Input, Redirect, Chang;

class ChangController extends BaseController {

	protected $fileRepository;

	public function __construct(Filerepository $fileRepository){
		$this->fileRepository = $fileRepository;
	}

	public function getIndex(){
		if(!count(Cart::content()) > 0){
			Session::flash('flashNotice', 'You have not added any items to your selection yet');
			return Redirect::to('files/browse');
		}
		return Redirect::to('preprocess/chang/info');
	}

	public function getInfo(){
		return View::make('preprocess.chang.info');
	}

	public function getActions(){
		return View::make('preprocess.chang.actions');
	}

	public function getPreview(){
		if($URI = Input::get('URI')){
			if($document = $this->fileRepository->getDocumentByURI($URI)){
				$chang = new Chang($document);
				$chang->process();
				$document = $chang->generate();
				return View::make('preprocess.chang.preview',  compact('document'));
			}
		} else {
			Session::flash('flashError', 'No valid URI given: ' . $URI);
			return Redirect::back();
		}	
	}

	public function getProcess(){
		if($URI = Input::get('URI')){
			if($document = $this->fileRepository->getDocumentByURI($URI)){
				$chang = new Chang($document);
				$chang->process();
				$document = $chang->save();
				dd('done');
			}
		} else {
			Session::flash('flashError', 'No valid URI given: ' . $URI);
			return Redirect::back();
		}
	}
}