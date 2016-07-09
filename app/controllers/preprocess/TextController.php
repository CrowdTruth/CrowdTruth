<?php

namespace preprocess;

use CoffeeScript\compact;
use BaseController, View, Input, Redirect;
use League\Csv\Reader as Reader;
use \Repository as Repository;
use \Entities\File as File;
use \Entities\Unit as Unit;

use \Security\PermissionHandler as PermissionHandler;
use \Security\Permissions as Permissions;

use \SoftwareComponents\TextSentencePreprocessor as TextSentencePreprocessor;

/**
 * This controller provides functionality for configuring and executing preprocessing 
 * of text documents to create new entitites in CrowdTruth.
 */
class TextController extends BaseController {
	protected $repository;
	protected $processor;
	protected $nLines;
	
	/**
	 * Initialize controller and set default parameters.
	 */
	public function __construct(Repository $repository, TextSentencePreprocessor $processor) {
		$this->repository = $repository;
		$this->processor = $processor;
		$this->nLines = 5;
		
		//$this->beforeFilter('permission:'.Permissions::PROJECT_WRITE, [ 'only' => 'postConfigure']);
	}

	/**
	 * Return view for selecting a document for preprocessing.
	 */
	public function getIndex() {
		$files = File::get();
		
		$thisUser = \Auth::user();
		foreach ($files as $ent) {
			$hasPermission = PermissionHandler::checkProject($thisUser, $ent['project'], Permissions::PROJECT_WRITE);
			$ent['canWrite'] = $hasPermission;
		}
		
		if(count($files) > 0) {
			return View::make('media.preprocess.text.pages.actions', compact('files'));
		}
		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any documents yet');
	}
	
	/**
	 * Return view for configuring preprocessing.
	 */
	public function getConfigure() {
		$URI = Input::get('URI');
		if($document = File::where('_id', $URI)->first()) {
			
			// Load which functions are available for display
				$functions = $this->getAvailableFunctions();

				$newLine = "\n";
				$docPreview = $document['content'];
				$project = $document['project'];
				$docPreview = explode($newLine, $docPreview);
				$docPreview = array_slice($docPreview, 0, $this->nLines);
				$docPreview = implode($newLine, $docPreview);
				
				$docTypes = Unit::select('documentType')->where('project', $document->project)->distinct()->get()->toArray();
				
				// default preview of files
				$previewTable = $this->doPreviewTable($document, '"', ',', false);
				
				return View::make('media.preprocess.text.pages.configure')
						->with('URI', $URI)
						->with('docTitle', $document['title'])
						->with('docPreview', $docPreview)
						->with('functions', $functions)
						->with('project',$project)
						//->with('configuration', $config)
						->with('previewTable', $previewTable)
						->with('docTypes', $docTypes)
				;
		} else {
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}
	}

	/**
	*
	*/
	public function postConfiguration(){
		$project = Input::get('project');
		$documentType = Input::get('documentType');
		$config = $this->processor->getConfiguration($project, $documentType);
		return $config;
	}

	/**
	 * Load content of a document (CSV) as a data matrix.
	 * 
	 * @param $documentContent		Raw content of CSV file.
	 * @param $delimiter			CSV field delimiter.
	 * @param $separator			CSV column separator.
	 * @param $ignoreHeader			Boolean flag for ignoring headers.
	 * @param $nLines				Maximum number of lines to read from file (-1 to read all)
	 * @return Data matrix representing the file contents.
	 */
	private function getDocumentData($documentContent, $delimiter, $separator, $ignoreHeader, $nLines) {
		$reader = Reader::createFromString($documentContent);
		$reader->setDelimiter($separator);
		$reader->setEnclosure($delimiter);

		// remove empty lines
		$reader->addFilter(function($row) {
			if(count($row) == 1 && $row[0] == NULL) {
				return false;
			}
			return true;
		});
	
		// start at the second row if the file contains headers
		if($ignoreHeader) {
			$reader->setLimit($nLines);

		} else {
			$reader->setOffset(1);
			$reader->setLimit($nLines);
		}

		$dataTable = $reader->fetchAll();
		return $dataTable;
	}

	/**
	 * Process various post actions from preprocess configuration page. Possible 
	 * postAction's
	 * 
	 *   - saveConfig		Saves current configuration
	 *   - tableView		Load nLines of CSV file as a preview of CSV parsing.
	 *   - processPreview	Load nLines of CSV file as a preview of CSV processing.
	 *   - process			Process CSV file, according to the current configuration.
	 * @return Depending on the post action:
	 *   - saveConfig		JSON confirmation message.
	 *   - tableView		JSON structure containing table data.
	 *   - processPreview	JSON structure containing preview of entities.
	 *   - process			Redirects to configuration page with status message.
	 */
	public function postConfigure() {
		$postAction = Input::get('postAction');
		
		// Prepare document
		$URI = Input::get('URI');
		$document = $this->repository->find($URI);
		$project = $document['project'];
		// need to verify project access here

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
			return $this->doSaveConfig($ignoreHeader, $delimiter, $separator, $rootProcessor, $project, $type);
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
				$type = Input::get('document');
				if($type == '_new' || !$type) {
					$type = Input::get('new_doctype');
				}
				if($type == '') {
					return [ 'Error' => 'No document type given' ];
				}
				$inputs = Input::all();
				$rootProcessor = new RootProcessor($inputs, $this::getAvailableFunctions('extended'));
				$this->doSaveConfig($ignoreHeader, $delimiter, $separator, $rootProcessor, $project, $type);
				return $this->doPreprocess($rootProcessor, $document, $type, $delimiter, $separator, $ignoreHeader);

			}
		} else {
			return [ 'Error' => 'Unknown post action: '.$postAction ];
		}
	}
	
	/**
	 * Execute postAction='saveConfig' command.
	 */
	private function doSaveConfig($ignoreHeader, $delimiter, $separator, $rootProcessor, $project, $type) {
		$config = [
				"groups" => $rootProcessor->getGroupsConfiguration(),
				"props" => $rootProcessor->getPropertiesConfiguration()
		];
		return $this->processor->storeConfiguration($config, $project, $type);
	}
	
	/**
	 * Execute postAction='tableView' command
	 */
	private function doPreviewTable($document, $delimiter, $separator, $ignoreHeader) {
		$columns  = [];

		// if the header should be ignored we get one row more data.
		if($ignoreHeader) {
			$dataTable = $this->getDocumentData($document['content'], $delimiter, $separator, true, $this->nLines);
			for($i=0; $i<count($dataTable[0]); $i = $i + 1) {
				$columns[$i] = 'Col'.($i+1);
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
	
	/**
	 * Execute postAction='processPreview' command
	 */
	private function doPreview($rootProcessor, $document, $delimiter, $separator, $ignoreHeader) {
		// Use only first Nlines of file for information
				
		// get the data
		$dataTable = $this->getDocumentData($document['content'], $delimiter, $separator, $ignoreHeader, $this->nLines);
	
		$entities = [];
		foreach ($dataTable as $line) {
			$lineEntity = $rootProcessor->call($line);
			array_push($entities, $lineEntity);
		}
		return json_encode($entities, JSON_PRETTY_PRINT);
	}
	
	/**
	 * Execute postAction='process' command
	 */
	private function doPreprocess($rootProcessor, $document, $type, $delimiter, $separator, $ignoreHeader) {
		// get the data
		$dataTable = $this->getDocumentData($document['content'], $delimiter, $separator, $ignoreHeader, -1);

		$entities = [];
		foreach ($dataTable as $line) {
			$lineEntity = $rootProcessor->call($line);
			array_push($entities, $lineEntity);
		}

		$status = $this->processor->store($document, $entities, $type);
		
		return $this->getConfigure()
						->with('status', $status);
	}

	/**
	 * Construct an array of all available TextPreprocessor's
	 * @param $option 'extended' to include 'DataType' text processors.
	 * @return A list of name => preprocessor objects.
	 */
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
		
		$processor13 = new \Preprocess\Extra\JsonTextPreprocessor;
		
		// List all processors
		$processors = [ $processorA, $processorB, $processor1, $processor2,
				$processor3, $processor4, $processor5, $processor6, $processor7,
				$processor8, $processor9, $processor10, $processor11, $processor12,
				$processor13, ];
		
		$retList = [];
		foreach ($processors as $proc) {
			// If isDataType, only include in 'extended' list.
			if($option=='extended' || !$proc->isDataType()) {
				$retList[$proc->getName()] = $proc;
			}
		}
		return $retList;
	}
}

/**
 * A RootProcessor is first configured to parse input lines according to a 
 * given preprocessing configuration, and can be called for processing each 
 * line and produce an entity.
 * 
 * RootProcessor can contain any number of GROUPS and any number of PROPERTIES.
 * Groups, in turn can contain GROUPS and PROPERTIES. PROPERTIES do the actual 
 * processing of input's, delegating the processing to a class implementing the 
 * AbstractTextPreprocessor interface.
 */
class RootProcessor {
	private $processor;
	private $providers;
	
	/**
	 * Initialize Root processor.
	 */
	public function __construct($inputs, $providers) {
		$this->providers = $providers;
		$this->processor = $this::buildGroupProcessor('root', '', $inputs);
	}
	
	/**
	 * Given an input line, create an entity.
	 * @param $line		A line from a CSV file.
	 * @return Entity created according to the configuration of this processor.
	 */
	public function call($line) {
		$lineEntity = [];
		$this->processor->call($line, $lineEntity, $lineEntity);
		return $lineEntity['root'];
	}

	/**
	 * Get configuration of children groups.
	 */
	public function getGroupsConfiguration() {
		return $this->processor->getGroupsConfiguration('root');
	}

	/**
	 * Get configuration of children properties.
	 */
	public function getPropertiesConfiguration() {
		return $this->processor->getPropertiesConfiguration('root');
	}
	
	/**
	 * Create configuration (recursively) of groups/properties in the 
	 * RootProcessor based on the given inputs. 
	 * 
	 * @param $groupName Name of group to be configured
	 * @param $parentName Name of the parent group (for reference)
	 * @param $inputs Inputs given to create configuration.
	 * @return A GroupProcessor with configuration for ROOT processor.
	 */
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
	
	/**
	 * Create property processor from the given inputs.
	 * @param $name Name of the property
	 * @param $parentName Name of the parent (for reference).
	 * @param $inputs Inputs given to create configuration.
	 * @return PropertyProcessor created.
	 */
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
	
	/**
	 * Return a list of children nodes of a given parent found on inputs.
	 */
	private function getChildren($inputs, $parentName) {
		$children = [];
		foreach ($inputs as $name => $value)  {
			if(starts_with($name, $parentName)) {
				$children[$name] = $value;
			}
		}
		return $children;
	}

	/**
	 * String representation of RootProcessor (for debugging).
	 */
	public function __toString() {
		return 'RootProcessor: [<br>'.$this->processor->asString().']';
	}
}

/**
 * Processor wrapping a AbstractTextPreprocessor.
 */
class PropertyProcessor {
	private $propName;
	private $provider;	// Of type AbstractTextPreprocessor
	private $params;	// Array of parameters (AbstractTextPreprocessor should know what to do with them).
	
	/**
	 * Initialize a PropertyProcessor with the given property name.
	 */
	public function __construct($name) {
		$this->propName = $name;
	}
	
	/**
	 * Set AbstractTextPreprocessor which does the actual processing.
	 */
	public function setProvider(AbstractTextPreprocessor $provider) {
		$this->provider = $provider;
	}
	
	/**
	 * Set name of parameters for the propert processor.
	 * @param $params List of names of parameters taken by this processor.
	 */
	public function setParameters($params) {
		$this->params = $params;
	}

	/**
	 * Execute processItem.
	 */
	public function call($data, &$parentEntity, &$fullEntity) {
		try {
			$propValue = $this->provider->processItem($this->params, $data, $fullEntity);
		} catch(ErrorException $e) {
			$propValue = 'N/A';
		}
		$parentEntity[$this->propName] = $propValue;
	}
	
	/**
	 * Retrieve the configuration of this processor.
	 * @param $parentName name of parent group (for reference)
	 * @return configuration as array structure.
	 */
	public function getConfiguration($parentName) {
		return [ 
			'parent' => $parentName,
			'name' => $this->propName,
			'function' => $this->provider->getName(),
			'values'=> $this->provider->getConfiguration($this->params)
		];
	}

	/**
	 * String representation of RootProcessor (for debugging).
	 */
	public function asString() {
		return $this->propName.': '.$this->provider->getName().'<br>';
	}
}

/**
 * Group processor containing groups and properties. When the 'call' method 
 * of a Group processor is invoked, it triggers the 'call' methods for its 
 * subgroups and properties.
 */
class GroupProcessor {
	private $groupName;
	private $props;
	private $subGroups;

	/**
	 * Initialize the GROUP with the given name.
	 */
	public function __construct($name) {
		$this->groupName = $name;
		$this->props = [];
		$this->subGroups = [];
	}

	/**
	 * Add's a PropertyProcessor as child of this GROUP.
	 */
	public function addPropertyProcessor($propProcessor) {
		array_push($this->props, $propProcessor);
	}

	/**
	 * Add's a GroupProcessor as child of this GROUP.
	 */
	public function addSubgroupProcessor($subProcessor) {
		array_push($this->subGroups, $subProcessor);
	}
	
	/**
	 * Invoke the 'call' method for all GROUP and PROPERTY children 
	 * of this group.
	 * 
	 * @param $data Data to be processed.
	 * @param $parentEntity parent entity for reference to data 
	 * @param $fullEntity Entity in which data is deposited.
	 */
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
	
	/**
	 * Retrieve the configuration of the GROUP children of this processor.
	 * @param $parentName name of parent group (for reference)
	 * @return configuration as array structure.
	 */
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
	
	/**
	 * Retrieve the configuration of the PROPERTY children of this processor.
	 * @param $parentName name of parent group (for reference)
	 * @return configuration as array structure.
	 */
	public function getPropertiesConfiguration($parentGroup) {
		$groupList = [];
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
	
	/**
	 * String representation of RootProcessor (for debugging).
	 */
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
