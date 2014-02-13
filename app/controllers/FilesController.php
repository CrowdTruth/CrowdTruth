<?php

use \MongoDB\Repository as Repository;

class FilesController extends BaseController {

	protected $repository;

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

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

		try {
			$format = $fileHelper->getType();
			$domain = $fileHelper->getDomain();
			$documentType = $fileHelper->getDocumentType();
			$validatedFiles = $fileHelper->performValidation();

			$mongoDBFileUpload = new \MongoDB\FileUpload;
			$status_upload = $mongoDBFileUpload->store($validatedFiles['passed'], $domain, $documentType);
		} catch (Exception $e){
			return Redirect::back()->with('flashError', $e->getMessage());
		}

		return View::make('files.pages.upload', compact('status_upload'));
	}	

	public function getBrowse($format = 'none', $domain = 'none', $documentType = 'none', $documentURI = 'none')
	{
		if($format == 'none')
			return View::make('files.browse.pages.collections');

		if(!$domains = \MongoDB\Entity::getDistinctValuesForField('domain'))
			return Redirect::to('files/browse/')->with('flashNotice', 'No documents for this file format have been uploaded yet.');

		if($domain == 'none')
			return View::make('files.browse.' . $format . '.pages.domains', compact('format', 'domains'));

		if(!in_array(strtolower($domain), $domains))
			return Redirect::to('files/browse/' . $format)->with('flashNotice', 'Documents within this domain do not exist.');

		if(!$documentTypes = \MongoDB\Entity::getDistinctValuesForField('documentType', array('domain' => $domain)))
			return Redirect::to('files/browse/')->with('flashNotice', 'No documents for this file format have been uploaded yet.');

		if($documentType == 'none')
			return View::make('files.browse.' . $format . '.pages.documentTypes', compact('format', 'domain', 'documentTypes'));

		if(!in_array(strtolower($documentType), $documentTypes))
			return Redirect::to('files/browse/' . $format . '/' . $domain)->with('flashNotice', 'Documents for this domain do not exist.');

		$entities = \MongoDB\Entity::where('domain', $domain)->where('documentType', $documentType)->get();
		return View::make('files.browse.' . $format . '.pages.documentType' ,  compact('format', 'domain', 'documentType', 'entities'));
	}

	public function getView(){
		if(!$URI = Input::get('URI'))
		{
			return Redirect::back()->with('flashError', 'No URI given');
		}
			
		if($entity = $this->repository->find($URI))
		{
			$documentType_view = 'files.view.' . $entity->format . '.pages.' . $entity->documentType;

			if(View::exists($documentType_view))
			{
				return View::make($documentType_view, compact('entity'));
			}

			return View::make('files/view/text/pages/entity', compact('entity'));
		}

		return Redirect::to('files/browse')->with('flashError', "No document found at given URI: {$URI}");
	}

	public function getDelete(){
		if(!$URI = Input::get('URI'))
		{
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

			if($this->repository->delete($URI))
			{
				$selection = App::make('SelectionController');
				$selection->removeByURI($URI);
				return $selection->returnInlineMenu();	
			}
		}
		return false;		
	}

	public function getSearch($format = null, $domain = null)
	{

		if($format == null)
		{
			return Redirect::to('files/search/text/');
		}
		
		$searchFields = $this->repository->getSearchFieldsAndValues($format, $domain);

		// dd($searchFields);


		return View::make('files.search.pages.index', compact('searchFields'));
	}
}