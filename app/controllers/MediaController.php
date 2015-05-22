<?php

use Illuminate\Support\Facades\View;

use CoffeeScript\compact;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\UserAgent as UserAgent;
use \MongoDB\SoftwareComponent as SoftwareComponent;
use \SoftwareComponents\FileUploader as FileUploader;
use \SoftwareComponents\MediaSearchComponent as MediaSearchComponent;

use \MongoDB\Security\ProjectHandler as ProjectHandler;
use \MongoDB\Security\PermissionHandler as PermissionHandler;
use \MongoDB\Security\Permissions as Permissions;
use \MongoDB\Security\Roles as Roles;

class MediaController extends BaseController {

	protected $repository;

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
		$this->beforeFilter('permission:'.Permissions::PROJECT_WRITE, [ 'only' => 'postUpload']);
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

	public function getPreprocess() {
		return Redirect::to('media/preprocess/text')	;
	}

	/**
	 * map properties to keys with formats
	 */
	public function getKeys($entity, $parent = "", $type = "") {
		$blacklist = [ '/withoutSpam/', '/withSpam/', '/withFilter/', '/withoutFilter/' ];
		$poundSign = '/#/';
		foreach($blacklist as $pattern) {
			if(preg_match($pattern, $parent)) {
				return [];
			}
		}
		
		if($parent == "") {
			$type = $entity['documentType'];
		}
		
		$keys = [];
		// loop through all properties
		foreach($entity as $key => $value) {
			// exclude keys that are numbers
			if(! is_numeric($key) && !preg_match($poundSign, $key)) {
				// if the property has children
				if(is_array($entity[$key])) {
					// get properties of children
					$subKeys = $this->getKeys($entity[$key], $parent.$key.".", $type);
					$keys = array_merge($keys, $subKeys);
				} else {
					if($parent != "") { // this has a parent
						$subkey = str_replace('.', '_', $parent.$key);
						$keys[$subkey] = ['key' => $parent.$key, 'label' => $this->getLabel($parent.$key), 'format' => $this->getFormat($value), 'document' => $type];
					} else {
						$keys[$key] = ['key' => $key, 'label' => $this->getLabel($key), 'format' => $this->getFormat($value), 'document' => $type];
					}
				}
			}
		};
		return $keys;
	}

	/**
	 * trace format of a value
	 */
	public function getFormat($value) {
		
		if(preg_match('/^.*\.(mp3|ogg|wmv)$/i',$value)) {
			$format = 'sound';
		} else if(preg_match('/^(http\:\/\/.*\.ggpht\.com.*|.*\.(jpg|jpeg|png|gif))$/i',$value)) {
			$format = 'image';
		} else if(preg_match('/^.*\.(avi|mpeg|mpg|mp4)$/i',$value)) {
			$format = 'video';
		} else if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}.*$/i',$value)) {
			$format = 'time';
		} else if(is_numeric($value)) {
			$format = 'number';
		} else {
			$format = 'string';
		}		

		return $format;
	}
	
	/**
	 * create a fancy label based on a key
	 */
	public function getLabel($label) {
		$label = str_replace(".", " > ", $label);
		$label = str_replace("_", " ", $label);
		$label = ucfirst($label);
		return $label;
	}

	public function getListindex()
	{
		$searchComponent = new MediaSearchComponent();
		$keys = $searchComponent->getKeys();
		$formats = $searchComponent->getFormats();
		return View::make('media.search.pages.listindex')->with('keys', $keys)->with('formats',$formats);
	}
	
	
	/**
	 * get keys for (a set of) document types
	 */
	public function postKeys()
	{	
		$documents = explode("|", Input::get('documents'));
		$searchComponent = new MediaSearchComponent();

		$keys = $searchComponent->getKeys($documents);
		asort($keys);
		$formats = $searchComponent->getFormats();
		
		// default columns
		$default = ['_id','documentType','title','created_at','project','user_id']; // default visible columns
		
		return [
			'log' => $documents,
			'keys' => $keys,
			'formats' => $formats,
			'default' => $default
		];
	}

	public function getRefreshindex()
	{
		return View::make('media.search.pages.refreshindex');
	}
	
	// update db page
	public function getUpdatedb()
	{
		return View::make('media.search.pages.updatedb');
	}


	
	/**
	 * update units to new data structure
	 */
	public function postUpdatedb()
	{	
		$searchComponent = new MediaSearchComponent();
		
		// amount of units to index per iteration
		$batchsize = 50;
		$from = Input::get('next');
		$unitCount = Entity::whereIn('tags', ['unit'])->count();
		
		// reset index on start
		if($from == 0) {
			$searchComponent->clear();
		}
		
		// reduce last batch to remaining units
		if($from + $batchsize > $unitCount) {
			$batchsize = $unitCount - $from;
		}
		
		// all units in this range
		$units = Entity::distinct('_id')->where('tags', ['unit'])->skip($from)->take($batchsize)->get();

		// get list of existing projects
		$projects = ProjectHandler::listGroups();
			 
		// for each unit get the keys and check if the project exists
		$allKeys = [];
		for($i = $from; $i < $from + $batchsize; $i++) {
			// get data of unit
			$unit = Entity::where('_id', $units[$i][0])->first();

			switch($unit['documentType']) {
				case 'annotatedmetadatadescription':
					$unit['project'] = 'soundandvision';
				break;
				case 'biographynet-sentence':
					$unit['project'] = 'biographynet';
				break;
				case 'drawing':
					$unit['project'] = 'rijksmuseum';
				break;
				case 'enrichedvideo':
					$unit['project'] = 'soundandvision';
				break;
				case 'enrichedvideov2':
					$unit['project'] = 'soundandvision';
				break;
				case 'enrichedvideov3':
					$unit['project'] = 'soundandvision';
				break;
				case 'fullvideo':
					$unit['project'] = 'soundandvision';
				break;
				case 'metadatadescription':
					$unit['project'] = 'soundandvision';
				break;
				case 'metadatadescription-event':
					$unit['project'] = 'soundandvision';
				break;
				case 'painting':
					$unit['project'] = 'rijksmuseum';
				break;
				case 'relex':
					$unit['project'] = 'ibmrelex';
				break;
				case 'relex-sentence':
					$unit['project'] = 'ibmrelex';
				break;
				case 'relex-structured-sentence':
					$unit['project'] = 'ibmrelex';
				break;
				case 'termpairs-sentence':
					$unit['project'] = 'ibmdisdis';
				break;
			}

			// add the project if it doesnt exist yet
			if(!in_array($unit['project'], $projects)) {
				ProjectHandler::createGroup($unit['project']);
				// add the project to the temporary list
				array_push($projects, $unit['project']);
			}
			
			// add the user to the project if it has no access yet
			if(!ProjectHandler::inGroup($unit['user_id'], $unit['project'])) {
				$user = UserAgent::find($unit['user_id']);
				ProjectHandler::grantUser($user, $unit['project'], Roles::PROJECT_MEMBER);
			}

			$id = explode('/', $unit['_id']);
			if(sizeof($id) == 5) {			
				$entity = new Entity;
				
				// copy properties
				
				$entity['documentType'] = $unit['documentType'];
				$entity['test'] = 'new';
				$entity->_id = 'entity/' . $entity->documentType . '/' . $id[4];
				$entity->save();
			}

		}
			 
		return [
			'log' => 'test',
			'next' => $from + $batchsize,
			'last' => $unitCount
		 ];
	}


	
	/**
	 * refresh search index
	 */
	public function postRefreshindex()
	{	
		$searchComponent = new MediaSearchComponent();
		
		// amount of units to index per iteration
		$batchsize = 500;
		$from = Input::get('next');
		$unitCount = Entity::whereIn('tags', ['unit'])->count();
		
		// reset index on start
		if($from == 0) {
			$searchComponent->clear();
		}
		
		// reduce last batch to remaining units
		if($from + $batchsize > $unitCount) {
			$batchsize = $unitCount - $from;
		}
		
		// all units in this range
		$units = Entity::distinct('_id')->where('tags', ['unit'])->skip($from)->take($batchsize)->get();
			 
		 
		// get keys for each unit in this batch
		$allKeys = [];
		for($i = $from; $i < $from + $batchsize; $i++) {
			// get data of unit
			$unit = Entity::where('_id', $units[$i][0])->first();
			
			// map all properties into keys with formats
			$keys = $this->getKeys($unit->attributesToArray());
			
			// merge keys with set of keys and get the right format (e.g. if it occurs both at string and int we treat all of them as a string
			foreach($keys as $k => $v) {
				
				if(!array_key_exists($k,$allKeys)) {
					$allKeys[$k] = [
					'key' => $keys[$k]['key'],
					'label' => $keys[$k]['label'],
					'format' => $keys[$k]['format'],
					'documents' => [$keys[$k]['document']]
					];
				} else {
					$allKeys[$k]['format'] = $searchComponent->prioritizeFormat([$allKeys[$k]['format'],$keys[$k]['format']]);
					
					// add document type if its not in the list yet
					if(!in_array($keys[$k]['document'], $allKeys[$k]['documents'])) {
						array_push($allKeys[$k]['documents'], $keys[$k]['document']);
					}

				}

			}
		}
		$searchComponent->store($allKeys);
			 
		return [
			'log' => $from . ' to ' . ($from + $batchsize) . ' of ' . $unitCount,
			'next' => $from + $batchsize,
			'last' => $unitCount
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
			$project = Input::get('projectname');
			
			$uploader = new FileUploader();
			$status_upload = $uploader->store($fileFormat, $domain, $documentType, $project, $domainCreate, 
					$documentCreate, $files);
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
		$userprojects = ProjectHandler::getUserProjects(Auth::user());
		$userprojects = array_column($userprojects, 'name');
		
		return View::make('media.pages.upload')
			->with('domains', $domains)
			->with('names', $names)
			->with('fileTypes', $fileTypes)
			->with('doctypes', $doctypes)
			->with('projects', $userprojects);
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
		
		// get projects of a user
		$user = Auth::user();
		foreach($mainSearchFilters['media']['categories'] as $key => $value) {
			// $key is the name of a project
			if(! PermissionHandler::checkProject($user, $key, Permissions::PROJECT_READ)) {
				unset($mainSearchFilters['media']['categories'][$key]);
			}
		}
		
		return View::make('media.search.pages.media')
			->with('mainSearchFilters', $mainSearchFilters);
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
	//	dd($units);

		$fields = explode("/", $units[0]);
	//	dd($fields);
		return View::make('media.pages.createbatch', compact('units', 'fields'));
	}
}
