<?php

namespace preprocess;

use CoffeeScript\compact;
use BaseController, Cart, View, App, Input, Redirect, Session;
use League\Csv\Reader as Reader;

use \Security\PermissionHandler as PermissionHandler;
use \Security\Permissions as Permissions;

use \SoftwareComponents\TextSentencePreprocessor as TextSentencePreprocessor;

class TextController extends BaseController {
	protected $repository;
	protected $processor;
	protected $nLines;
	
	public function __construct(Repository $repository, TextSentencePreprocessor $processor) {
		$this->repository = $repository;
		$this->processor = $processor;
		$this->nLines = 5;
		
		//$this->beforeFilter('permission:'.Permissions::PROJECT_WRITE, [ 'only' => 'postConfigure']);
	}

	private function getSeparator($csvstring) {
		$fallback = '';
		$seps = array(';',',','|',"\t");
		$max = 0;
		$separator = false;
		foreach($seps as $sep){
			$count = substr_count($csvstring, $sep);
			if($count > $max){
				$separator = $sep;
				$max = $count;
			}
		}
		
		if($separator) return $separator;
		return $fallback;
	}
	
	public function getIndex() {
		$entities = Entity::where('activity_id', 'LIKE', '%fileuploader%')->get();
		
		$thisUser = \Auth::user();
		foreach ($entities as $ent) {
			$hasPermission = PermissionHandler::checkGroup($thisUser, $ent['project'], Permissions::PROJECT_WRITE);
			$ent['canWrite'] = $hasPermission;
		}
		
		if(count($entities) > 0) {
			return View::make('media.preprocess.text.pages.actions', compact('entities'));
		}
		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any documents yet');
	}
	
	public function getConfigure() {
		if($URI = Input::get('URI')) {
			if($document = $this->repository->find($URI)) {
				// Load which functions are available for display
				$functions = $this->getAvailableFunctions();

				$newLine = "\n";
				$docPreview = $document['content'];
				$docPreview = explode($newLine, $docPreview);
				$docPreview = array_slice($docPreview, 0, $this->nLines);
				$docPreview = implode($newLine, $docPreview);
				
				$config = $this->processor->getConfiguration($document["documentType"]);
				if($config!=null) {
					$delimiter = $config["delimiter"];
					$separator = $config["separator"];
					$ignoreHeader = !$config["useHeaders"];
					
					$previewTable = $this->doPreviewTable($document, $delimiter, $separator, $ignoreHeader);
				} else {
					$previewTable = null;
				}
				
				return View::make('media.preprocess.text.pages.configure')
						->with('URI', $URI)
						->with('docTitle', $document['title'])
						->with('docPreview', $docPreview)
						->with('functions', $functions)
						->with('configuration', $config)
						->with('previewTable', $previewTable)
				;
			} else {
				return Redirect::back()->with('flashError', 'Document does not exist: ' . $URI);
			}
		} else {
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}
	}

	private function getDocumentData($documentContent, $delimiter, $separator, $ignoreHeader, $nLines) {
		$reader = Reader::createFromString($documentContent);
		$reader->setDelimiter($separator);
		$reader->setEnclosure($delimiter);
		if($ignoreHeader) {
			$reader->setLimit($nLines);
		} else {
			$reader->setOffset(1);
			$reader->setLimit($nLines + 1);
		}

		$dataTable = $reader->fetchAll();
		return $dataTable;
	}

	public function postConfigure() {
		$postAction = Input::get('postAction');
		
		// Prepare document
		$URI = Input::get('URI');
		$document = $this->repository->find($URI);
		
		$delimiter = Input::get('delimiter');
		$separator = Input::get('separator');
		$ignoreHeader = !Input::get('useHeaders');
		
		if($delimiter=='') {
			$delimiter = '"';
		}
		if($separator=='') {
			$separator = ',';
		}

		if($postAction=='saveConfig') {
			$inputs = Input::all();		// Same as $_POST
			$rootProcessor = new RootProcessor($inputs, $this::getAvailableFunctions('extended'));
			
			$config = [
				"useHeaders" => !$ignoreHeader,
				"delimiter" => $delimiter,
				"separator" => $separator,
				"groups" => $rootProcessor->getGroupsConfiguration(),
				"props" => $rootProcessor->getPropertiesConfiguration()
			];
			
			return $this->processor->storeConfiguration($config, $document["documentType"]);
		} else if($postAction=='tableView') {
			return $this->doPreviewTable($document, $delimiter, $separator, $ignoreHeader);
		} else if($postAction=='processPreview' || $postAction=='process') {
			// Prepare processor
			$inputs = Input::all();		// Same as $_POST
			$rootProcessor = new RootProcessor($inputs, $this::getAvailableFunctions('extended'));
			
			// if preview
			if($postAction=='processPreview') {
				return $this->doPreview($rootProcessor, $document, $delimiter, $separator, $ignoreHeader);
			} else {	// $postAction=='process'
				return $this->doPreprocess($rootProcessor, $document, $delimiter, $separator, $ignoreHeader);
			}
		} else {
			return [ 'Error' => 'Unknown post action: '.$postAction ];
		}
	}

	private function doPreviewTable($document, $delimiter, $separator, $ignoreHeader) {
		// Number the file columns
		$columns  = [];
		if($ignoreHeader) {
			$dataTable = $this->getDocumentData($document['content'], $delimiter, $separator, $ignoreHeader, $this->nLines);
			for($i=0; $i<count($dataTable[0]); $i = $i + 1) {
				$columns[$i] = 'Col '.($i+1);
			}
		} else {
			$rawData = $this->getDocumentData($document['content'], $delimiter, $separator, true, $this->nLines + 1);
			$columns = array_slice($rawData, 0, 1)[0];
			$dataTable = array_slice($rawData, 1, $this->nLines);
		}
		$data = [
			'headers' => $columns,
			'content' => $dataTable
		];

		return $data;
	}

	private function doPreprocess($rootProcessor, $document, $delimiter, $separator, $ignoreHeader) {
		$nLines = -1;	// Process all lines
		$dataTable = $this->getDocumentData($document['content'], $delimiter, $separator, $ignoreHeader, $nLines);
		
		$entities = [];
		foreach ($dataTable as $line) {
			$lineEntity = $rootProcessor->call($line);
			array_push($entities, $lineEntity);
		}
		$status = $this->processor->store($document, $entities);
		
		return $this->getConfigure()
						->with('status', $status);
	}

	private function doPreview($rootProcessor, $document, $delimiter, $separator, $ignoreHeader) {
		// Use only first Nlines of file for information
		$dataTable = $this->getDocumentData($document['content'], $delimiter, $separator, $ignoreHeader, $this->nLines);
		
		$entities = [];
		foreach ($dataTable as $line) {
			$lineEntity = $rootProcessor->call($line);
			array_push($entities, $lineEntity);
		}

		return json_encode($entities, JSON_PRETTY_PRINT);
	}

	private function getAvailableFunctions($option = '') {
		// Each function extends AbstractTextPreprocessor.
		// see AbstractTextPreprocessor for more details

		// TODO: Load all AbstractTextPreprocessor dynamically (from DB? config file?
		$processorA = new \Preprocess\TextPreprocessor;
		$processorB = new \Preprocess\NumberPreprocessor;

		$processor1 = new \Preprocess\RegExpPreprocessor;
		$processor2 = new \Preprocess\WordCountPreprocessor;
		$processor3 = new \Preprocess\StringLengthPreprocessor;
		$processor4 = new \Preprocess\TermDifferencePreprocessor;
		$processor5 = new \Preprocess\TermReplacePreprocessor;

		$processor6 = new \Preprocess\Relex\RelationInSentencePreprocessor;
		$processor7 = new \Preprocess\Relex\RelationOutsideTermsPreprocessor;
		$processor8 = new \Preprocess\Relex\RelationBetweenTermsPreprocessor;
		$processor9 = new \Preprocess\Relex\SemicolonBetweenTermsPreprocessor;
		$processor10 = new \Preprocess\Relex\CommaSeparatedTermsPreprocessor;
		$processor11 = new \Preprocess\Relex\ParenthesisAroundTermsPreprocessor;
		$processor12 = new \Preprocess\Relex\OverlapingTermsPreprocessor;
		
		if($option == 'extended') {
				return [ $processorA->getName() => $processorA,
				$processorB->getName() => $processorB,
				$processor1->getName() => $processor1,
				$processor2->getName() => $processor2,
				$processor3->getName() => $processor3,
				$processor4->getName() => $processor4,
				$processor5->getName() => $processor5,
				$processor6->getName() => $processor6,
				$processor7->getName() => $processor7,
				$processor8->getName() => $processor8,
				$processor9->getName() => $processor9,
				$processor10->getName() => $processor10,
				$processor11->getName() => $processor11,
				$processor12->getName() => $processor12
			];
		} else {
			return [ $processor1->getName() => $processor1,
				$processor2->getName() => $processor2,
				$processor3->getName() => $processor3,
				$processor4->getName() => $processor4,
				$processor5->getName() => $processor5,
				$processor6->getName() => $processor6,
				$processor7->getName() => $processor7,
				$processor8->getName() => $processor8,
				$processor9->getName() => $processor9,
				$processor10->getName() => $processor10,
				$processor11->getName() => $processor11,
				$processor12->getName() => $processor12
			];
		}
	}
}

class RootProcessor {
	private $processor;
	private $providers;
	
	public function __construct($inputs, $providers) {
		$this->providers = $providers;
		$this->processor = $this::buildGroupProcessor('root', '', $inputs);
	}
	
	public function call($line) {
		$lineEntity = [];
		$this->processor->call($line, $lineEntity, $lineEntity);
		return $lineEntity['root'];
	}

	public function getGroupsConfiguration() {
		return $this->processor->getGroupsConfiguration('root');
	}

	public function getPropertiesConfiguration() {
		return $this->processor->getPropertiesConfiguration('root');
	}
	
	private function buildGroupProcessor($groupName, $parentName, $inputs) {
		if($parentName=='') {
			$fullName = $groupName;
		} else {
			$fullName = $groupName;
			$groupName = str_replace($parentName.'_', '', $groupName);
		}
		$processor = new GroupProcessor($groupName);

		// Find groups an properties
		$groups = [];
		$props  = [];
		// foreach element in group {
		foreach ($inputs as $name => $value)  {
			// if element is group (and is subgroup of this group)
			if(ends_with($name, '_groupParent') && $value==$fullName) {
				// Add to list of subgroups
				array_push($groups, str_replace('_groupParent', '', $name));
				unset($inputs[$name]);
				// else if element is property (and is property of this group)
			} else if( ends_with($name, '_propParent') && $value==$fullName) {
				array_push($props, str_replace('_propParent', '', $name));
				unset($inputs[$name]);
			}
		}
	
		// Generate processors for subgroups
		foreach ($groups as $subGrpName) {
			// Only pass inputs starting with $subGrpName_
			$subInputs = $this::getChildren($inputs, $subGrpName, $fullName);
			$subGrpProcessor = $this::buildGroupProcessor($subGrpName, $fullName, $subInputs);
			$processor->addSubgroupProcessor($subGrpProcessor);
		}
	
		// Generate processors for properties
		foreach ($props as $propName) {
			// Only pass inputs starting with $propName_
			$propInputs = $this::getChildren($inputs, $propName);
			$propProcessor = $this::buildPropertyProcessor($propName, $fullName, $propInputs);
			$processor->addPropertyProcessor($propProcessor);
		}
	
		return $processor;
	}
	
	private function buildPropertyProcessor($name, $parentName, $inputs) {
		$fullName = $name;
		$name =  str_replace($parentName.'_', '', $name);
		$processor = new PropertyProcessor($name, $parentName);
	
		// Preprocessor provider
		$propFuncName = $fullName.'_function';
		$propFunc = $inputs[$propFuncName];
		$provider = $this->providers[$propFunc];
		unset($inputs[$propFuncName]);
		$processor->setProvider($provider);
	
		// Preprocessor parameters
		$params = [];
		foreach ($inputs as $paramName => $paramValue) {
			$paramName = str_replace($fullName.'_', '', $paramName);
			$params[$paramName] = $paramValue;
		}
		$processor->setParameters($params);

		return $processor;
	}
	
	private function getChildren($inputs, $parentName) {
		$children = [];
		foreach ($inputs as $name => $value)  {
			if(starts_with($name, $parentName)) {
				$children[$name] = $value;
			}
		}
		return $children;
	}
}

class PropertyProcessor {
	private $propName;
	private $provider;	// Of type AbstractTextPreprocessor
	private $params;	// Array of parameters (AbstractTextPreprocessor should know what to do with them).
	
	public function __construct($name) {
		$this->propName = $name;
	}
	
	public function setProvider(AbstractTextPreprocessor $provider) {
		$this->provider = $provider;
	}
	
	public function setParameters($params) {
		$this->params = $params;
	}

	public function call($data, &$parentEntity, &$fullEntity) {
		try {
			$propValue = $this->provider->processItem($this->params, $data, $fullEntity);
		} catch(ErrorException $e) {
			$propValue = 'N/A';
		}

		$parentEntity[$this->propName] = $propValue;
	}
	
	public function asString() {
		return $this->propName.'<br>';
	}
	
	public function getConfiguration($parentName) {
		return [ 
			'parent' => $parentName,
			'name' => $this->propName,
			'function' => $this->provider->getName(),
			'values'=> $this->provider->getConfiguration($this->params)
		];
	}
}

class GroupProcessor {
	private $groupName;
	private $props;
	private $subGroups;

	public function __construct($name) {
		$this->groupName = $name;
		$this->props = [];
		$this->subGroups = [];
	}

	public function addPropertyProcessor($propProcessor) {
		array_push($this->props, $propProcessor);
	}
	
	public function addSubgroupProcessor($subProcessor) {
		array_push($this->subGroups, $subProcessor);
	}
	
	public function call($data, &$parentEntity, &$fullEntity) {
		$parentEntity[$this->groupName] = [];
		$entityContent = &$parentEntity[$this->groupName];		// NOTE: $entityContent is a reference !
		
		foreach($this->props as $prop) {
			$prop->call($data, $entityContent, $fullEntity);
		}

		foreach($this->subGroups as $subGrp) {
			$subGrp->call($data, $entityContent, $fullEntity);
		}
	}
	
	public function asString() {
		$strRep = '';
		$strRep = $strRep.$this->groupName.'{<br>';
		foreach($this->props as $prop) {
			$strRep = $strRep.$prop->asString();
		}
		foreach($this->subGroups as $sGrp) {
			$strRep = $strRep . $sGrp->asString();
		}
		$strRep = $strRep.'}<br>';
		return $strRep;
	}
	
	public function getGroupsConfiguration($parentName) {
		$groupList = [];
		foreach($this->subGroups as $sGrp) {
			$sGrpElement = [
				"parent"=> $parentName,
				"name"	=> $sGrp->groupName
			];
			array_push ($groupList, $sGrpElement);
		}
		foreach($this->subGroups as $sGrp) {
			$sGroupList = $sGrp->getGroupsConfiguration($this->groupName.'_'.$sGrp->groupName);
			$groupList = array_merge ($groupList, $sGroupList);
		}
		return $groupList;
	}
	
	public function getPropertiesConfiguration($parentGroup) {
		$groupList = [ ];
		foreach($this->props as $prop) {
			$propElement = $prop->getConfiguration($parentGroup);
			array_push ($groupList, $propElement);
		}
		foreach($this->subGroups as $sGrp) {
			$sGroupList = $sGrp->getPropertiesConfiguration($this->groupName.'_'.$sGrp->groupName);
			$groupList = array_merge ($groupList, $sGroupList);
		}
		return $groupList;
	}
}
