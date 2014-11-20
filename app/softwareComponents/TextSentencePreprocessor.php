<?php
namespace SoftwareComponents;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareComponent as SoftwareComponent;
use \MongoDate as MongoDate;
use Auth;

class TextSentencePreprocessor {
	protected $softwareComponent;
	
	public function __construct() {
		$this->softwareComponent = SoftwareComponent::find('textsentencepreprocessor');
	}
	
	public function store($document, $sentences) {
		$nEnts = count($sentences);
		if($nEnts<=0 && $nEnts>=10000) {
			// We will have problems processing empty files or more than 10,000 entities
			$status = [];
			$status['error'] = 'Unable to process files with more than 10,000 sentences: '.$nEnts.'found';
			return $status;
		}
		
		$activity = new Activity();
		$activity->softwareAgent_id = $this->softwareComponent->_id;
		$activity->save();
		
		$format = $document['format'];
		$domain = $document['domain'];
		$docType = $document['documentType'].'-sentence';
		$title = $document['title'];
		$parentId = $document['_id'];
		$activityId = $activity->_id;
		if (Auth::check()) {
			$userId = Auth::user()->_id;
		} else  {
			$userId = "crowdwatson";
		}
		
		$idBase = 'entity/'.$format.'/'.$domain.'/'.$docType.'/';
		$inc = $this->getLastDocumentInc($format, $domain, $docType);
		
		$entities = [];
		foreach ($sentences as $sentence) {
			$entity = [
				"_id" => $idBase . $inc,
				"title" => strtolower($title),
				"domain" => $domain,
				"format" => $format,
				"tags" => [ 'unit' ],
				"documentType" => $docType,
				"parents" => [ $parentId ],
				"content" => $sentence,
				"hash" => md5(serialize($sentence)),
				"activity_id" => $activityId,
				"user_id" => $userId,
				"updated_at" => new MongoDate(time()),
				"created_at" => new MongoDate(time())
			];
			$inc++;
			
			array_push($entities, $entity);
		}
		
		\DB::collection('entities')->insert($entities);
		\MongoDB\Temp::truncate();
		
		return [ 'success' => 'Sentences created successfully' ];
	}

	public function getConfiguration($documentType) {
		$avlConfigs = $this->softwareComponent['configurations'];
		$configKey = $documentType;
		if(array_key_exists($configKey, $avlConfigs)) {
			return $avlConfigs[$configKey];
		} else {
			return null;
		}
	}

	public function storeConfiguration($config, $documentType) {
		$configKey = $documentType;
		$avlConfigs = $this->softwareComponent['configurations'];
		$avlConfigs[$configKey] = $config;
		$this->softwareComponent['configurations'] = $avlConfigs;
		$this->softwareComponent->save();
		return [ 'status' => 'Configuration saved successfully' ];
	}
}
