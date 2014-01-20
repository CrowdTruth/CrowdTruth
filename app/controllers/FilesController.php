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
			$type = $fileHelper->getType();
			$domain = $fileHelper->getDomain();
			$documentType = $fileHelper->getDocumentType();
			$validatedFiles = $fileHelper->performValidation();

			if($type == 'text'){
				$mongoHelper = new \mongo\text\Helper;
			} elseif($type == 'images'){
				$mongoHelper = new \mongo\images\Helper;
			} elseif($type == 'videos'){
				$mongoHelper = new \mongo\videos\Helper;
			}

			$status_upload = $mongoHelper->storeFiles($validatedFiles['passed'], $domain, $documentType, $incrementIfExists);
			
		} catch (Exception $e){
			return Redirect::back()->with('flashError', $e->getMessage());
		}

		return View::make('files.pages.upload', compact('status_upload'));
	}	

	public function getBrowse($type = 'none', $domain = 'none', $documentType = 'none', $documentURI = 'none')
	{
		if($type == 'none')
			return View::make('files.browse.pages.collections');

		$repository = new \mongo\Repository;

		if(!$Entity = $repository->returnCollectionObjectFor($type, 'Entity')){
			Session::reflash();
			return Redirect::to('files/browse');
		}

		if(!$domains = $repository->getDistinctFieldinCollection($Entity, 'domain', array()))
			return Redirect::to('files/browse/')->with('flashNotice', 'No documents for this file type have been uploaded yet.');

		if($domain == 'none')
			return View::make('files.browse.' . $type . '.pages.domains', compact('type', 'domains'));

		if(!in_array(strtolower($domain), $domains))
			return Redirect::to('files/browse/' . $type)->with('flashNotice', 'Documents within this domain do not exist.');

		if(!$documentTypes = $repository->getDistinctFieldinCollection($Entity, 'documentType', array('domain' => $domain)))
			return Redirect::to('files/browse/')->with('flashNotice', 'No documents for this file type have been uploaded yet.');

		if($documentType == 'none')
			return View::make('files.browse.' . $type . '.pages.documentTypes', compact('type', 'domain', 'documentTypes'));

		if(!in_array(strtolower($documentType), $documentTypes))
			return Redirect::to('files/browse/' . $type . '/' . $domain)->with('flashNotice', 'Documents for this domain do not exist.');

		$entities = $repository->getDocumentsWithFieldsInCollection($Entity, array('domain' => $domain, 'documentType' => $documentType));
		return View::make('files.browse.' . $type . '.pages.documentType' ,  compact('type', 'domain', 'documentType', 'entities'));
	}

	public function getView(){
		if(!$URI = Input::all())
			return Redirect::back()->with('flashError', 'No URI given');

		$repository = new \mongo\Repository;
		if($entity = $repository->find($URI)){
			$documentType_view = 'files.view.' . $entity->type . '.pages.' . $entity->documentType;
			if(View::exists($documentType_view))
				return View::make($documentType_view, compact('entity'));
			
			return View::make('files/view/text/pages/entity', compact('entity'));
		}

		return Redirect::to('files/browse')->with('flashError', 'No document found at given URI: ' . http_build_query($URI));
	}

	public function getDelete(){
		if(!$URI = Input::get('URI')){
			Session::flash('flashError', 'No URI given');
			return Redirect::back();
		}

		$repository = new \mongo\Repository;

		if($repository->delete($URI)){
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
			$repository = new \mongo\Repository;
			if($repository->delete($URI)){
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
			$repository = new \mongo\Repository;
			if($fromEntity = $repository->find($fromURI)){
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