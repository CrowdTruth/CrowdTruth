<?php

namespace preprocess;
use BaseController, Cart, View, App, Input, Redirect, Chang, Session;

class ChangController extends BaseController {

	public function getIndex(){
		return Redirect::to('preprocess/chang/info');
	}

	public function getInfo(){
		return View::make('preprocess.chang.pages.info');
	}

	public function getActions(){
		$items = Cart::content();
		if(!count($items) > 0){
			return Redirect::to('files/browse')->with('flashNotice', 'You have not added any "twrex" items to your selection yet');
		} else {
			$entities = array();
			$repository = new \mongo\Repository;

			foreach($items as $item){
				if($entity = $repository->find($item['id'])) {
					if($entity->documentType != "twrex")
						continue;

					$entity['rowid'] = $item['rowid'];
					array_push($entities, $entity);
				}
					
			}
		}

		return View::make('preprocess.chang.pages.actions', compact('entities'));
	}

	public function getPreview(){
		if($URI = Input::all()) {
			$repository = new \mongo\Repository;
			if($entity = $repository->find($URI)) {
				if($entity->documentType != "twrex")
					continue;

				$chang = new \preprocess\Chang;
				$document = $chang->process($entity);
				// print_r($document);
				// exit;
				return View::make('preprocess.chang.pages.view', array('entity' => $entity, 'lines' => $document));
			}


		} else {
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

	public function getProcess(){
		if($URI = Input::all()) {
			$repository = new \mongo\Repository;
			if($entity = $repository->find($URI)) {
				if($entity->documentType != "twrex")
					continue;

				$chang = new \preprocess\Chang;
				$document = $chang->process($entity);
				$chang->store($entity, $document);
				return Redirect::back();
			}
		} else {
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}
	}
}