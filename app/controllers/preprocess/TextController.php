<?php

namespace preprocess;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use BaseController, Cart, View, App, Input, Redirect, Session;
use League\Csv\Reader as Reader;

use \softwareComponents\FileUploader as FileUploader;

class TextController extends BaseController {
	protected $repository;
	protected $uploader;
	
	public function __construct(Repository $repository, FileUploader $uploader) {
		$this->repository = $repository;
		$this->uploader = $uploader;
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
	
	public function getConfigure() {
		if($URI = Input::get('URI')) {
			if($document = $this->repository->find($URI)) {
				// Use only first Nlines of file for information
				$nLines = 5;
				$dataTable = $this->getDocumentData($document['content'], $nLines);
				
				// Number the file columns
				$columns   = [];
				for($i=0; $i<count($dataTable[0]); $i = $i + 1) {
					$columns[$i] = 'Col '.($i+1);
				}
				
				// Load which functions are available for display
				$functions = $this->getAvailableFunctions();

				return View::make('media.preprocess.text.configure')
						->with('docTitle', $document['title'])
						->with('columns', $columns)
						->with('dataTable', $dataTable)
						->with('functions', $functions)
						->with('URI', $URI);
			} else {
				return Redirect::back()->with('flashError', 'Document does not exist: ' . $URI);
			}
		} else {
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}
	}

	private function getDocumentData($documentContent, $nLines) {
		$reader = Reader::createFromString($documentContent);
		$reader->setDelimiter($this->getSeparator($documentContent));
		$reader->setEnclosure("\"");
		$reader->setLimit($nLines);
		$dataTable = $reader->fetchAll();
		return $dataTable;
	}

	public function postConfigure() {
		$inputs = Input::all();		// Same as $_POST
		$response = '';
		
		// Prepare processor
		$rootProcessor = $this::buildGroupProcessor('root', '', $inputs);
		
		// Prepare document
		// TODO: Validate URI is present
		$URI = Input::get('URI');
		$document = $this->repository->find($URI);

		// if preview
		$isPreview = Input::get('preview');	// Preview comes as a string
		if($isPreview=='true') {
			return $this->doPreview($rootProcessor, $document);
		} else {
			return $this->doPreprocess($rootProcessor, $document);
		}
	}

	private function doPreprocess($rootProcessor, $document) {
		$nLines = -1;	// Process all lines
		$dataTable = $this->getDocumentData($document['content'], $nLines);
		
		foreach ($dataTable as $line) {
			$content = [];
			$rootProcessor->call($line, $content, $content);
		}
		
		$status = $this->uploader->store($document, $dataTable);
		
		return $status;
	}
		
	private function doPreview($rootProcessor, $document) {
		// Use only first Nlines of file for information
		$nLines = 5;
		$dataTable = $this->getDocumentData($document['content'], $nLines);
		
		$entities = [];
		foreach ($dataTable as $line) {
			$lineEntity = [];
			$rootProcessor->call($line, $lineEntity, $lineEntity);
			array_push($entities, $lineEntity);
		}
	
		return json_encode($entities, JSON_PRETTY_PRINT);
	}
	
	private function getAvailableFunctions() {
		// Each function extends AbstractTextPreprocessor.
		// see AbstractTextPreprocessor for more details
	
		// TODO: implement the following preprocessors:
		//     return [ asText, asNumber, format, replaceText, wordCount, relation ]
		$processor1 = new \Preprocess\TextToTypePreprocessor;
		$processor2 = new \Preprocess\RegExpPreprocessor;
		$processor3 = new \Preprocess\WordCountPreprocessor;
		$processor4 = new \Preprocess\TermReplacePreprocessor;
	
		return [ $processor1->getName() => $processor1,
		$processor2->getName() => $processor2,
		$processor3->getName() => $processor3,
		$processor4->getName() => $processor4
		];
	}

//		def processGroup(root) {
//			group = newEntity (root.name);
//			foreach element in root {
//				if element is group {
//					subGroup = processGroup(element);
//				} else if element is property {
//					prop = processProperty(element);
//				}
//			}
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

		// Available Preprocessor providers
		$providers = $this::getAvailableFunctions();

		// Preprocessor provider
		$propFuncName = $fullName.'_function';
		$propFunc = $inputs[$propFuncName];
		$provider = $providers[$propFunc];
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
}
