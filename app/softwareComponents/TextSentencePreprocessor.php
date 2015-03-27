<?php
namespace SoftwareComponents;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareComponent as SoftwareComponent;
use \MongoDate as MongoDate;
use Auth;

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
	 * Fetch the last existing ID for the given format / domain / docType combination.
	 */
	private function getLastDocumentInc($format, $domain, $docType) {
		$lastMongoURIUsed = Entity::where('format', $format)
								->where('domain', $domain)
								->where('documentType', $docType)
								->get(array("_id"));
		
		if(count($lastMongoURIUsed) > 0) {
			$lastMongoURIUsed = $lastMongoURIUsed->sortBy(
				function($entity) {return $entity->_id;}, SORT_NATURAL)->toArray();
				
			if(end($lastMongoURIUsed)) {
				$lastMongoIDUsed = explode("/", end($lastMongoURIUsed)['_id']);
				$inc = end($lastMongoIDUsed) + 1;
			}
		} else {
			$inc = 0;
		}
		return $inc;
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
	public function store($document, $entities) {
		$nEnts = count($entities);
		if($nEnts<=0 && $nEnts>=10000) {
			// We will have problems processing empty files or more than 10,000 entities
			return [ 'error' => 'Unable to process files with more than 10,000 lines: '.$nEnts.'found' ];
		}
		
		$activity = new Activity();
		$activity->softwareAgent_id = $this->softwareComponent->_id;
		$activity->save();
		
		$format = $document['format'];
		$domain = $document['domain'];
		$docType = $document['documentType'].'-sentence';
		$title = $document['title'];
		$parentId = $document['_id'];
		$project = $document['project'];
		$activityId = $activity->_id;
		if (Auth::check()) {
			$userId = Auth::user()->_id;
		} else  {
			$userId = "crowdwatson";
		}
		
		$idBase = 'entity/'.$format.'/'.$domain.'/'.$docType.'/';
		$inc = $this->getLastDocumentInc($format, $domain, $docType);
		
		$fullEntities = [];
		foreach ($entities as $entitiy) {
			$fullEntity = [
				"_id" => $idBase . $inc,
				"title" => strtolower($title),
				"domain" => $domain,
				"format" => $format,
				"tags" => [ 'unit' ],
				"documentType" => $docType,
				"parents" => [ $parentId ],
				"content" => $entitiy,
				"hash" => md5(serialize($entitiy)),
				"activity_id" => $activityId,
				"user_id" => $userId,
				"project" => $project,
				"updated_at" => new MongoDate(time()),
				"created_at" => new MongoDate(time())
			];
			$inc++;
			array_push($fullEntities, $fullEntity);
		}
		\DB::collection('entities')->insert($fullEntities);
		\MongoDB\Temp::truncate();
		
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
