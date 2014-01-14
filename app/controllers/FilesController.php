<?php

class FilesController extends BaseController {

	public function getIndex()
	{
        return Redirect::to('files/upload');
	}

	public function getUpload()
	{
        return View::make('files.pages.upload');
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
			return Redirect::back()->with('flashError', $e->getMessage());
		}

		return View::make('files.pages.upload', compact('status_upload'));
	}	

	public function getBrowse($fileType = 'none', $domainType = 'none', $documentType = 'none', $documentURI = 'none')
	{
		if($fileType == 'none')
			return View::make('files.browse.pages.collections');

		$documentHelper = new \mongo\DocumentHelper;

		if(!$Entity = $documentHelper->getEntityObjectFor('resource/' . $fileType)){
			Session::reflash();
			return Redirect::to('files/browse');
		}

		if(!$domainTypes = $Entity::getDistinctFieldinArray('domain', array()))
			return Redirect::to('files/browse/')->with('flashNotice', 'No documents for this file type have been uploaded yet.');

		if($domainType == 'none')
			return View::make('files.browse.' . $fileType . '.pages.domains', compact('fileType', 'domainTypes'));

		if(!in_array(strtolower($domainType), $domainTypes))
			return Redirect::to('files/browse/' . $fileType)->with('flashNotice', 'Documents within this domain do not exist.');

		if(!$documentTypes = $Entity::getDistinctFieldinArray('documentType', array('domain' => $domainType)))
			return Redirect::to('files/browse/')->with('flashNotice', 'No documents for this file type have been uploaded yet.');

		if($documentType == 'none')
			return View::make('files.browse.' . $fileType . '.pages.documentTypes', compact('fileType', 'domainType', 'documentTypes'));

		if(!in_array(strtolower($documentType), $documentTypes))
			return Redirect::to('files/browse/' . $fileType . '/' . $domainType)->with('flashNotice', 'Documents for this domain do not exist.');

		if($documentURI == 'none') {
			$entities = $Entity::getEntitiesWithFields(array('domain' => $domainType, 'documentType' => $documentType));
			return View::make('files.browse.' . $fileType . '.pages.documentType' ,  compact('fileType', 'domainType', 'documentType', 'entities'));
		}

		if(!$entity = $Entity::find($documentURI))
			return View::make('files.browse.document',  compact('fileType', 'domainType', 'documentType', 'entities'))->with('flashNotice', 'No document exists at URI: ' . $documentURI);
		
		return View::make('files.browse.document',  compact('fileType', 'domainType', 'documentType', 'entity'));
	}

	public function getView(){
		if(!$URI = Input::get('URI'))
			return Redirect::back()->with('flashError', 'No URI given');

		$documentHelper = new \mongo\DocumentHelper;
		if($entity = $documentHelper->find($URI)){
			$documentType_view = 'files.view.' . $entity->fileType . '.pages.' . $entity->documentType;
			if(View::exists($documentType_view))
				return View::make($documentType_view, compact('entity'));
			
			return View::make('files/view/text/pages/entity', compact('entity'));
		}

		return Redirect::to('files/browse')->with('flashError', 'No document found at given URI: ' . $URI);
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

	public function postCreate(){
		dd(Input::all());
		if(($fromURI = Input::get('fromURI')) && $appliedFilters = Input::all()){
			$documentHelper = new \mongo\DocumentHelper;
			if($fromEntity = $documentHelper->find($fromURI)){
				$chang = new \preprocess\Chang;
				if($changChildURI = $chang->createAndStoreChangChild($fromEntity, $appliedFilters)){
					return Redirect::to('files/view?URI=' . $changChildURI);
				} else {
					dd('failed to create chang child..');
				}
			}
		}
	}
}