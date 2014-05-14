<?php

use \MongoDB\Repository as Repository;

class MediaController extends BaseController {

	protected $repository;

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

	public function getIndex()
	{
        return Redirect::to('media/upload');
	}

	public function getUpload()
	{
        return View::make('media.pages.upload');
	}

	public function getPreprocess($action = "twrex")
	{
		return Redirect::to('media/preprocess/' . $action);
	}	

	public function postUpload()
	{
		$fileHelper = new FileHelper(Input::all());

		try {
			$format = $fileHelper->getType();
			$domain = $fileHelper->getDomain();
			$documentType = $fileHelper->getDocumentType();
			$validatedFiles = $fileHelper->performValidation();

			$mongoDBFileUpload = new \FileUpload;
			$status_upload = $mongoDBFileUpload->store($validatedFiles['passed'], $domain, $documentType);
		} catch (Exception $e){
			return Redirect::back()->with('flashError', $e->getMessage());
		}

		return View::make('media.pages.upload', compact('status_upload'));
	}	


	public function postOnlinedata()
	{	
		if (Input::get("source_name") == "source_rijksmuseum"){
			return Redirect::to('onlinesource/imagegetter');
		}

		/* Change template to add online source */
		// if (Input::get("source_name") == "source_template"){
		// 	return Redirect::to('onlinesource/onlinesourcetemplate')
		// }

		$onlineDataHelper = new OnlineDataHelper(Input::all());
		try {
			$format = $onlineDataHelper->getType();
			$domain = $onlineDataHelper->getDomain();
			$documentType = $onlineDataHelper->getDocumentType();
			$noOfVideos = $onlineDataHelper->getNoOfVideos();
			$sourceName = $onlineDataHelper->getOnlineSource();
			$mongoDBOnlineData = new \OnlineData;
			$source = explode("_", $sourceName);
			$parameters = array();
			$parameters["set"] = $source[1];
			$parameters["metadataPrefix"] = "oai_oi";
			$parameters["set"] = "beeldengeluid";
			$status_onlinedata = $mongoDBOnlineData->store($format, $domain, $documentType, $parameters, $noOfVideos);
		} catch (Exception $e){
			return Redirect::back()->with('flashError', $e->getMessage());
		}

		return View::make('media.pages.upload', compact('status_onlinedata'));
	}	


	public function getView()
	{
		if(!$URI = Input::get('URI'))
		{
			return Redirect::back()->with('flashError', 'No URI given');
		}

		if($entity = $this->repository->find($URI))
		{
			$documentType_view = 'media.view.' . $entity->format . '.pages.' . $entity->documentType;

			if(View::exists($documentType_view))
			{
				return View::make($documentType_view, compact('entity'));
			}

			return View::make('media/view/text/pages/entity', compact('entity'));
		}

		return Redirect::to('media/browse')->with('flashError', "No document found at given URI: {$URI}");
	}

	public function getSearch()
	{
		// $facetedSearch = App::make('FacetedSearch');
		$mainSearchFilters = \MongoDB\Temp::getMainSearchFiltersCache()['filters'];

		// dd($mainSearchFilters);

		return View::make('media.search.pages.media', compact('mainSearchFilters'));
	}

	public function anyBatch(){
		if(Input::has('batch_description'))
		{
			$batchCreator = App::make('BatchCreator');
			$status = $batchCreator->store(Input::all());
			return Redirect::to('media/search');
		}

		$units = Input::get('selection');
		natsort($units);
		$units = array_values($units);

		$fields = explode("/", $units[0]);

		return View::make('media.pages.createbatch', compact('units', 'fields'));
	}
}