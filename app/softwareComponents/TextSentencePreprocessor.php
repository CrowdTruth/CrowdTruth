<?php
namespace SoftwareComponents;

use \MongoDate as MongoDate;
use Auth;
use \SoftwareComponent as SoftwareComponent;
use \Entities\Unit as Unit;

/**
 * Software component for input file preprocessing.
 * 
 * This software component creates entities out of a text document, given using a given
 * configuration. 
 * 
 * @author carlosm
 */
class TextSentencePreprocessor {
	protected $softwareComponent;
	
	/**
	 * Create a TextSentencePreprocessor instance.
	 */
	public function __construct() {
		$this->softwareComponent = SoftwareComponent::find('textsentencepreprocessor');
	}

	/**
	 * Store to the database the given entities as entities decendent from the given
	 * document.
	 * 
	 * @param $document Parent document -- Must be a document entity on the database.
	 * @param $entities List of entities to be created as decendents from the given document. 
	 * 
	 * @return multitype:string A status array containing the result status information.
	 */
	public function store($parent, $units) {

		if(count($units)<=0 && count($units)>=10000) {
			// We will have problems processing empty files or more than 10,000 entities
			return [ 'error' => 'Unable to process files with more than 10,000 lines: '.$nEnts.'found' ];
		}
		
		$activity = new \Activity();
		$activity->softwareAgent_id = $this->softwareComponent->_id;
		$activity->save();

		
		$entities = [];
		foreach($units as $unit)
		{
			try {
				$entity = new Unit();
				$entity->project = $parent->project;
				$entity->activity_id = $activity->_id;
				$entity->documentType = "unit";
				$entity->type = "test";
				$entity->parents = [$parent->_id];
				$entity->content = $unit;
				$entity->hash = md5(serialize($entity));
				
				$entity->save();
				array_push($entities, $entity);
			
			} catch (Exception $e){
				foreach($entities as $en) {
					$en->forceDelete();
				}
				throw $e ;
			}
		}
		
		\Temp::truncate();
		
		return [ 'success' => 'Sentences created successfully' ];
	}

	/**
	 * Retrieve the Preprocessor configuration for a given document type.
	 * 
	 * @param $documentType Type of document for which configuration is required.
	 * 
	 * @return The configuration, or NULL if no configuration is available.
	 */
	public function getConfiguration($documentType) {
		$avlConfigs = $this->softwareComponent['configurations'];
		$configKey = $documentType;
		if(array_key_exists($configKey, $avlConfigs)) {
			return $avlConfigs[$configKey];
		} else {
			return null;
		}
	}

	/**
	 * Store a given configuration associated with a given document type.
	 * 
	 * @param $config The configuration.
	 * @param $documentType The document type.
	 * 
	 * @return The completion status for the save operation.
	 */
	public function storeConfiguration($config, $documentType) {
		$configKey = $documentType;
		$avlConfigs = $this->softwareComponent['configurations'];
		$avlConfigs[$configKey] = $config;
		$this->softwareComponent['configurations'] = $avlConfigs;
		$this->softwareComponent->save();
		return [ 'status' => 'Configuration saved successfully' ];
	}
}
