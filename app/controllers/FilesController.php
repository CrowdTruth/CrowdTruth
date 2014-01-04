<?php

class FilesController extends BaseController {

	public function getIndex()
	{
        return Redirect::to('files/upload');
	}

	public function getUpload()
	{
        return View::make('files.upload');
	}

	public function postUpload()
	{
		$fileHelper = new FileHelper(Input::all());
		$incrementIfExists = (Input::get('increment') ? true : false);

		try {
			$fileType = $fileHelper->getFileType();
			$domainType = $fileHelper->getDomainType();
			$documentType = $fileHelper->getDocumentType();
			$validatedFiles = $fileHelper->performValidation();

			if($fileType == 'text'){
				$mongoHelper = new \mongo\text\Helper;
			} elseif($fileType == 'images'){
				$mongoHelper = new \mongo\images\Helper;
			} elseif($fileType == 'videos'){
				$mongoHelper = new \mongo\videos\Helper;
			}

			$status_upload = $mongoHelper->storeFiles($validatedFiles['passed'], $domainType, $documentType, $incrementIfExists);

		} catch (Exception $e){
			Session::flash('flashError', $e->getMessage());
			return Redirect::back();
		}

		return View::make('files.upload', compact('status_upload'));
	}	

	public function getBrowse($fileType = 'none', $domainType = 'none', $documentType = 'none', $documentURI = 'none')
	{
		if($fileType == 'none'){
			return View::make('files/browse/collections');
		}elseif($fileType == 'text'){
			$Entity = new \mongo\text\Entity;
		} elseif($fileType == 'images'){
			$Entity = new \mongo\images\Entity;
		} elseif($fileType == 'videos'){
			$Entity = new \mongo\videos\Entity;
		} else {
			Session::reflash();
			return Redirect::to('files/browse');
		}

		$domainTypes = array();
		foreach($Entity::distinct('domain')->get() as $domType)
			array_push($domainTypes, $domType[0]);

		if(count($domainTypes) == 0){
			Session::flash('flashNotice', 'No documents for this file type have been uploaded yet.');
			return Redirect::to('files/browse/');
		}

		sort($domainTypes);
		if($domainType == 'none')
			return View::make('files/browse/' . $fileType . '/domains', compact('fileType', 'domainTypes'));

		if(!in_array(strtolower($domainType), $domainTypes)){
			Session::flash('flashNotice', 'Documents for this domain do not exist.');
			return Redirect::to('files/browse/' . $fileType);
		}

		$documentTypes = array();
		foreach($Entity::where('domain', $domainType)->distinct('documentType')->get() as $docType)
			array_push($documentTypes, $docType[0]);

		if($documentType == 'none')
			return View::make('files/browse/' . $fileType . '/documentTypes', compact('fileType', 'domainType', 'documentTypes'));


		if(!in_array(strtolower($documentType), $documentTypes)){
			Session::flash('flashNotice', 'Documents for this domain do not exist.');
			return Redirect::to('files/browse/' . $fileType . '/' . $domainType);
		}

		if($documentURI == 'none') {
			$entities = $Entity::where('domain', $domainType)->where('documentType', $documentType)->get();
			return View::make('files/browse/document',  compact('fileType', 'domainType', 'documentType', 'entities'));
		}

		if(!$document = $Entity::find($documentURI)){
			Session::flash('flashNotice', 'No document exists at URI: ' . $documentURI);
			return View::make('files/browse/document',  compact('fileType', 'domainType', 'documentType', 'entities'));
		}

		return View::make('files/browse/document',  compact('fileType', 'domainType', 'documentType', 'document'));
	}

	public function getView(){
		if(!$URI = Input::get('URI')){
			Session::flash('flashError', 'No URI given');
			return Redirect::back();
		}

		$documentHelper = new \mongo\DocumentHelper;

		if($entity = $documentHelper->find($URI)){
			return View::make('files/browse/text/entity', compact('entity'));
		} else {
			Session::flash('flashError', 'No document found at given URI: ' . $URI);
			return Redirect::to('files/browse');
		}
	}

	public function getDelete(){
		if(!$URI = Input::get('URI')){
			Session::flash('flashError', 'No URI given');
			return Redirect::back();
		}

		$documentHelper = new \mongo\DocumentHelper;

		if($documentHelper->delete($URI)){
			$selection = App::make('SelectionController');
			$selection->removeByURI($URI);
			Session::flash('flashSuccess', 'Entity and its corresponding activity have been deleted. URI: ' . $URI);
			return Redirect::to('files/browse');
		} else {
			Session::flash('flashError', 'There was an error deleting the Entity and its corresponding Activity at URI: ' . $URI);
			return Redirect::back();
		}		
	}

	public function postDelete(){
		if($URI = Input::get('URI')){
			$documentHelper = new \mongo\DocumentHelper;
			if($documentHelper->delete($URI)){
				$selection = App::make('SelectionController');
				$selection->removeByURI($URI);
				return $selection->returnInlineMenu();	
			}
		}
		return false;		
	}
}