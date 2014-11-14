<?php

use Illuminate\Support\Facades\View;

use CoffeeScript\compact;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\SoftwareComponent as SoftwareComponent;
use \SoftwareComponents\FileUploader as FileUploader;
use \SoftwareComponents\MediaSearchComponent as MediaSearchComponent;

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

	/**
	 * Return the media upload view.
	 */
	public function getUpload() {
		return $this->setDocTypes();
	}

	public function getPreprocess($action = "relex")
	{
		// TODO: Change default from RELEX to TEXT
		return Redirect::to('media/preprocess/' . $action);
	}

	public function getKeys($entity, $parent = "") {
		$blacklist = [ '/withoutSpam/', '/withSpam/', '/withFilter/', '/withoutFilter/' ];
		$poundSign = '/#/';
		foreach($blacklist as $pattern) {
			if(preg_match($pattern, $parent)) {
				return [];
			}
		}

		$keys = [];
		foreach($entity as $key => $value) {
			if(! is_numeric($key) && !preg_match($poundSign, $key)) {
				if(is_array($entity[$key])) {
					array_push($keys, $parent.$key);
					$subKeys = $this->getKeys($entity[$key], $parent.$key.".");
					$keys = array_merge($keys, $subKeys);
				} else {
					array_push($keys, $parent.$key);
				}
			}
		};
		return $keys;
	}

	public function getListindex()
	{
		$searchComponent = new MediaSearchComponent();
		$labels = $searchComponent->getKeyLabels();
		return View::make('media.search.pages.listindex')->with('labels', $labels);
	}

	public function getRefreshindex()
	{
		return View::make('media.search.pages.refreshindex');
	}

	public function postRefreshindex()
	{
		$from = Input::get('next', -1);
		$allIds = Entity::distinct('_id')->get();
		if($from==-1) {
			return [
				'next' => 0,	// Meaning we should start from 0
				'last' => sizeof($allIds)
			 ];
		} else {
			$allKeys = [];
			$batchSize = 100;
			$lastOne = sizeof($allIds);
			for($i = $from; $i < ($from + $batchSize) && $i < $lastOne; $i = $i + 1) {
				$e = Entity::where('_id', $allIds[$i][0])->first();
				$keys = $this->getKeys($e->attributesToArray());
				$allKeys = array_unique(array_merge($allKeys, $keys));
			}
			$searchComponent = new MediaSearchComponent();
			$searchComponent->store($allKeys);
			return [
				'next' => $i,	// Meaning we should start from 0
				'last' => $lastOne
			 ];
		}
	}

	public function postUpload()
	{
		try {
			$fileFormat = Input::get('file_format');
			$domain = Input::get('domain_type');
			$documentType = Input::get('document_type');
			$domainCreate = Input::get('domain_create');
			$documentCreate = Input::get('document_create');
			$files = Input::file('files');
			
			$uploader = new FileUploader();
			$status_upload = $uploader->store($fileFormat, $domain, $documentType, $domainCreate, 
					$documentCreate, $files);
			
			$uploadView = $this->setDocTypes()->with(compact('status_upload'));
			return $uploadView;
		} catch (Exception $e){
			dd([$e->getMessage(),Input::all()]);
			return Redirect::back()->with('flashError', $e->getMessage());
		}
	}

	public function postOnlinedata()
	{	
		if (Input::get("source_name") == "source_rijksmuseum"){
			return Redirect::to('onlinesource/imagegetter');
		}

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
		//	$parameters["set"] = $source[1];
			$parameters["metadataPrefix"] = "oai_oi";
		//	$parameters["set"] = "beeldengeluid";
			$status_onlinedata = $mongoDBOnlineData->store($format, $domain, $documentType, $parameters, $noOfVideos);
		//	\Session::flash('flashSuccess', 'Your video description is being pre-processed');
		} catch (Exception $e){
			return Redirect::back()->with('flashError', $e->getMessage());
		}

		return View::make('media.pages.upload', compact('status_onlinedata'));
	}

	/**
	 * Load data for the Media Upload View and return the view ready to be sent 
	 * back to the user.
	 */
	private static function loadMediaUploadView() {
	
		// Load properties from file uploader software component.
		$data = SoftwareComponent::find("fileuploader");
		
		$formats = $data->formats;
		
		// get all the unique domains so that we can display the document types per domain
		$domains = [];
		$doctypes = [];
		foreach($formats as $format) {
			foreach($format['document_types'] as $doctypeKey => $doctype) {
			
				// add to list of unique document type names
				$doctypes[$doctypeKey] = $doctype['label'];
				foreach($doctype['domains'] as $domainKey => $domain) {
				
					// add to list of unique domain names
					$domains[$domainKey] = $domain['label'];
				}
			}
		}

		// sort document types and domains
		asort($domains);
		asort($doctypes);

		// remove open domain and add it to the beginning of the array
		unset($domains['opendomain']);
		$domains = ['opendomain' => 'Open Domain'] + $domains;

		return ['docTypeData' => $formats, 'uniqueDomains' => $domains, 'uniqueDocTypes' => $doctypes];
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
		// load domains and document types
		$data = static::loadMediaUploadView();
		
		//$databaseData = \MongoDB\Temp::getMainSearchFiltersCache()['filters'];
				
		return View::make('media.search.pages.media')->with('docTypeData', $data['docTypeData'])->with('uniqueDomains', $data['uniqueDomains']);
	}
	
	public function setDocTypes()
	{
		$data = static::loadMediaUploadView();
		return View::make('media.pages.upload')->with('docTypeData', $data['docTypeData'])->with('uniqueDomains', $data['uniqueDomains'])->with('uniqueDocTypes', $data['uniqueDocTypes']);
	}

	public function anyBatch()
	{
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
