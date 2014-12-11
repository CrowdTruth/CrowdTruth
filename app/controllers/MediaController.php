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
		return $this->loadMediaUploadView();
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
		$types = $searchComponent->getKeyTypes();
		return View::make('media.search.pages.listindex')->with('labels', $labels)->with('types', $types);
	}

	public function getRefreshindex()
	{
		return View::make('media.search.pages.refreshindex');
	}

	/**
	 * refresh search index
	 */
	public function postRefreshindex()
	{	
		$searchComponent = new MediaSearchComponent();
		
		// amount of units to index per iteration
		$batchsize = 1000;
		$from = Input::get('next');
		$count = Entity::whereIn('tags', ['unit'])->count();
		
		// reset index on start
		if($from == 0) {
			$searchComponent->clear();
		}
		
		// reduce last batch to remaining units
		if($from + $batchsize > $count) {
			$batchsize = $count - $from;
		}
		
		$allIds = Entity::distinct('_id')->where('tags', ['unit'])->skip($from)->take($batchsize)->get();
			 
		// get keys for each unit in this batch
		$allKeys = [];
		for($i = 0; $i < $batchsize; $i++) {
			$e = Entity::where('_id', $allIds[$i][0])->first();
			$keys = $this->getKeys($e->attributesToArray());
			$allKeys = array_unique(array_merge($allKeys, $keys));
		}
		$searchComponent->store($allKeys);
			 
		return [
			'next' => $from + $batchsize,
			'last' => $count
		 ];
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
			
			$uploadView = $this->loadMediaUploadView()->with(compact('status_upload'));
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
	private function loadMediaUploadView() {
		// Load properties from file uploader software component.
		// TODO: replace for $data = new FileUploader ?
		$data = SoftwareComponent::find("fileuploader");
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
		$mainSearchFilters = \MongoDB\Temp::getMainSearchFiltersCache()['filters'];
		return View::make('media.search.pages.media', compact('mainSearchFilters'));
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
