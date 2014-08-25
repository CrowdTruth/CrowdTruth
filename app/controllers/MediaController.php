<?php

use CoffeeScript\compact;

use \MongoDB\Repository as Repository;
use \MongoDB\SoftwareAgent as SoftwareAgent;

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

	public function getUpload() {
		return $this->loadMediaUploadView();
	}

	public function getPreprocess($action = "relex")
	{
		// TODO: Change default from RELEX to TEXT
		return Redirect::to('media/preprocess/' . $action);
	}	

	public function postUpload()
	{
		$fileHelper = new FileHelper(Input::all());

		try {
			// TODO: Move this code to FileHelper ?? --> Ask Khalid
			// $format = $fileHelper->getType();
			// $domain = $fileHelper->getDomain();
			// $documentType = $fileHelper->getDocumentType();
			
			$format = $fileHelper->getType();		// text
			$domain = Input::get('domain_type');	// other ==> NewDomain
			$documentType = Input::get('document_type');	//document_type_other==>newType
			// END TODO
			$validatedFiles = $fileHelper->performValidation();

			// TODO: Move this code to FileUpload ?? --> Ask Khalid
			$newDomain = false;
			$newDocType = false;
			if($domain == 'domain_type_other') {
				// Add new domain to DB
				$domain = Input::get('domain_create');
				$domain = str_replace(' ', '', $domain);
				$domain = strtolower($domain);
				$domain = 'domain_type_'.$domain;
				$newDomain = true;
			}
			
			if($documentType == 'document_type_other') {
				// Add new doc_type to DB
				$documentType = Input::get('document_create');
				$newDocType;
			}

			
			if($newDomain || $newDocType) {
				$uploader = SoftwareAgent::find("fileuploader");

				// TODO: Move this code to new class UploadAgent extends SoftwareAgent ?
				if($newDomain) {
					$domainName = Input::get('domain_create');
					$fileFormat = Input::get('file_format');
					$upDomains = $uploader->domains;
					$upDomains[$domain] = [
						"name" => $domainName,
						"file_formats" => [	$fileFormat ],
						"document_types" => [ $documentType ]
					];
					$uploader->domains = $upDomains;
				} else if($newDocType) {
					// Only docType is new -- domain already existed...
					$docTypes = $uploader->domains[$domain]["document_types"];
					array_push($docTypes, $documentType);
					$uploader->domains[$domain]["document_types"] = $docTypes;
				}
				$uploader->save();
				// END TODO
			}
			// END TODO

			$domain = str_replace("domain_type_", "", $domain);
			$documentType = str_replace("document_type_", "", $documentType);

			$mongoDBFileUpload = new \FileUpload;
			$status_upload = $mongoDBFileUpload->store($validatedFiles['passed'], $domain, $documentType);

			$uploadView = $this->loadMediaUploadView()->with(compact('status_upload'));
			return $uploadView;
		} catch (Exception $e){
			return Redirect::back()->with('flashError', $e->getMessage());
		}
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

	private function loadMediaUploadView() {
		// Load properties from file uploader software agent.
		$data = SoftwareAgent::find("fileuploader");
		$dbDomains = $data->domains;
		
		$domains = [];
		$names = [];
		$fileTypes = [];
		$doctypes = [];
		foreach($dbDomains as $domainKey => $domain) {
			// $domainKey = $domain['key'];
		
			array_push($domains, $domainKey);
			$names[$domainKey] = $domain['name'];
		
			$fileTypeList = '';
			foreach($domain['file_formats'] as $fileType) {
				$fileTypeList = $fileTypeList.' '.$fileType;
			}
		
			$fileTypes[$domainKey] = $fileTypeList;
			$doctypes[$domainKey] = $domain['document_types'];
		}

		return View::make('media.pages.upload')
			->with('domains', $domains)
			->with('names', $names)
			->with('fileTypes', $fileTypes)
			->with('doctypes', $doctypes);
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