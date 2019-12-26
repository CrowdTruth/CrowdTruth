<?php

use Illuminate\Support\Facades\View;

use CoffeeScript\compact;

use \SoftwareComponents\MediaSearchComponent as MediaSearchComponent;
use \SoftwareComponents\ResultImporter as ResultImporter;

use \Security\ProjectHandler as ProjectHandler;
use \Security\Permissions as Permissions;
use \Security\Roles as Roles;

use \Entities\File as File;
use \Entities\Unit as Unit;
use \Entities\Batch as Batch;

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
		return Redirect::to('media/preprocess/text');
	}



	/**
	 * page to add results
	 */
	public function getImportresults()
	{
		$types = $this->userDocTypes();
		
		return View::make('media.search.pages.importresults')->with('types', $types[0]);
	}
	
	
	
	/**
	 * function to add results
	 */
	public function postImportresults()
	{
		$files = Input::file('file');
		
		$settings = [];
		$inputClass = explode('__', Input::get('inputClass'));
		$outputClass = explode('__', Input::get('outputClass'));
		$settings['filename'] = basename($files->getClientOriginalName(), '.csv');
		
		// input project
		if(Input::get('input-project') != "") {
			$settings['project'] = Input::get('input-project');
		} else {
			$settings['project'] = $inputClass[0];			
		}
		
		// input type
		if(Input::get('input-type') != "") {
			$settings['documentType'] = Input::get('input-type');
		} else {
			$settings['documentType'] = $inputClass[1];
		}
		
		// output type
		if(Input::get('output-type') != "") {
			$settings['resultType'] = Input::get('output-type');
		} else {
			$settings['resultType'] = $outputClass[1];
		}
		
		$settings['domain'] = 'opendomain';
		$settings['format'] = 'text';

		// process file
		$importer = new ResultImporter();
		$status = $importer->process($files, $settings);
		
		// flash appropriate message
		if(!$status['error']) {
			Session::flash('flashSuccess', $status['success']);
			if($status['notice']) {
				Session::flash('flashNotice', $status['notice']);
			}
		} else {
			Session::flash('flashError', $status['error']);
		}

		$mainSearchFilters = Temp::getMainSearchFiltersCache()['filters'];
		$projects = ProjectHandler::getUserProjects(Auth::user());
		$projects = array_column($projects, 'name');
		
		$types = $this->userDocTypes();

		return View::make('media.search.pages.importresults')->with('types', $types[0]);
	}

	
	/**
	 * get keys for (a set of) selected document types
	 */
	public function postKeys()
	{	
		// get the document types
		$documents = explode("|", Input::get('documents'));
		$searchComponent = new MediaSearchComponent();
		// store all keys in this array
		$docKeys = [];

		// go through each selected document type and get the keys
		foreach($documents as $type) {

			// skip if value is empty
			if($type == "") {
				continue;
			} elseif($type == "all") {
				$units = Unit::select('content')->get();
			} else {
				// split the document type string so that we can get the project name from it.
				$type = explode('__', $type);
				// get the content of the units for this document type in this project
				// if the load on the system is too high limit this to e.g. 100 random units.
				$units = Unit::select('content')->where('project', $type[0])->where('documentType', $type[1])->get();
			}

			// get the keys for the units in this document type
			foreach($units as $unit) {
				$unit->attributesToArray();
				$keys = $searchComponent->getKeys($unit['attributes']);
				$docKeys = array_unique(array_merge($docKeys, $keys));
			}
		}

//		asort($keys);
		
		return $docKeys;
	}

	

	public function postUpload()
	{

		try {
			$files = Input::file('files');
			$project = Input::get('projectname');
			

			if(!SoftwareAgent::find('filecreator'))
			{
				$softwareAgent = new SoftwareAgent;
				$softwareAgent->_id = "filecreator";
				$softwareAgent->label = "This component is used for creating files in the database";
				$softwareAgent->save();
			}
			
			$activity = new Activity;
			$activity->label = "File added to the platform";
			$activity->softwareAgent_id = 'filecreator';
			$activity->save();
			$success = [];
			$entities = [];
			foreach($files as $file)
			{
				try {
					$entity = new File();
					$entity->project = $project;
					$entity->activity_id = $activity->_id;
					$entity->store($file);
					
					$entity->save();
					array_push($success, 'Added ' . $entity->title . ' to ' . $entity->project);
					array_push($entities, $entity);
				
				} catch (Exception $e){
					foreach($entities as $en) {
						$en->forceDelete();
					}
					throw $e ;
				}
			}
			Session::flash('flashSuccess', $success);
			return $this->loadMediaUploadView();

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
			$documentType = $onlineDataHelper->getDocumentType();
			$noOfVideos = $onlineDataHelper->getNoOfVideos();
			$sourceName = $onlineDataHelper->getOnlineSource();
			$mongoDBOnlineData = new \OnlineData;
			$source = explode("_", $sourceName);
			$parameters = array();
			$parameters["metadataPrefix"] = "oai_oi";
			$parameters["set"] = "beeldengeluid";

			$status_onlinedata = $mongoDBOnlineData->store($documentType, $parameters, $noOfVideos);
		//	Session::flash('flashError', $status_onlinedata);
			//return Redirect::back()->with('flashError', "hhoi");
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

		// get all the projects a user is in
		$userprojects = ProjectHandler::getUserProjects(Auth::user());
		$userprojects = array_column($userprojects, 'name');
		
		return View::make('media.pages.upload')->with('projects', $userprojects);
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

	// get a list of all the projects and document types a user has access to
	private function userDocTypes() {
		// get all projects a user has access to
		$projects = ProjectHandler::getUserProjects(Auth::user());
		$projects = array_column($projects, 'name');
		
		$types = [];
		$allunits = 0;
		
		$searchComponent = new MediaSearchComponent();

		// for each project get the document types in it
		foreach($projects as $key => $project) {
			$docTypes = Unit::distinct('documentType')->where('project', $project)->get()->toArray();

			// skip if there is no data
			if(!empty($docTypes[0])) {
				// for each document type get the number of units
				$types[$project] = [];
				foreach($docTypes as $key => $type) {

					$count = Unit::where('project', $project)->where('documentType', $type[0])->count();
					$allunits += $count;

					$types[$project][$type[0]] = $count;
				}
			}
		}
		return [$types,$allunits];
	}


	public function getSearch()
	{
		$types = $this->userDocTypes();

		return View::make('media.search.pages.media')->with('unitcount', $types[1])->with('types', $types[0]);
	}


	public function anyBatch()
	{
		if(Input::has('batch_description'))
		{
			$batch = new Batch;
			$status = $batch->store(Input::all());

			return Redirect::to('media/search');
		}

		$units = Input::get('selection');
		natsort($units);
		$units = array_values($units);

		$fields = explode("/", $units[0]);
		return View::make('media.pages.createbatch', compact('units', 'fields'));
	}
}
