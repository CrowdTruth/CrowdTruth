<?php

namespace softwareComponents;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareComponent as SoftwareComponent;
use \MongoDate as MongoDate;
use Auth;

class FileUploader {
	protected $activity, $softwareComponent;
	
	public function __construct(Activity $activity, SoftwareComponent $softwareComponent) {
		$this->activity = $activity;
		$this->softwareComponent = $softwareComponent;
	}
	
	public function store($document, $dataTable) {
		// Copy from FileUpload
		// Once this work, get rid of FileHelper & FileUpload ;-)
		$nEnts = count($dataTable);
		if($nEnts<=0 && $nEnts>=10000) {
			// We will have problems processing empty files or more than 10,000 entities
			return 'ERROR';
		}
		
		$format = $document['format'];
		$domain = $document['domain'];
		$docType = $document['documentType'].'-sentence';
		$title = $document['title'];
		$parentId = $document['_id'];
		$activityId = $this->activity->_id;
		if (Auth::check()) {
			$userId = Auth::user()->_id;
		} else  {
			$userId = "crowdwatson";
		}
		
		$idBase = 'entity/'.$format.'/'.$domain.'/'.$docType.'/';
		$inc = $this->getLastDocumentInc($format, $domain, $docType);
		
		$entities = [];
		foreach ($dataTable as $content) {
			$entity = [
				"_id" => $idBase . $inc,
				"title" => strtolower($title),
				"domain" => $domain,
				"format" => $format,
				"tags" => [ 'unit' ],
				"documentType" => $docType,
				"parents" => [ $parentId ],
				"content" => $content,
				"hash" => md5(serialize($content)),
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
		
		return 'Status OK';
	}
	
	public function getType() {}
	public function performValidation() {
		// Check file size ?
		// Validate mime types ?
	}

	private function getLastDocumentInc($format, $domain, $docType) {
		$lastMongoURIUsed = Entity::where('format', $format)
		->where('domain', $domain)
		->where('documentType', $docType)
		->get(array("_id"));
	
		if(count($lastMongoURIUsed) > 0) {
			$lastMongoURIUsed = $lastMongoURIUsed->sortBy(function($entity) {
				return $entity->_id;
			}, SORT_NATURAL)->toArray();
	
			if(end($lastMongoURIUsed)) {
				$lastMongoIDUsed = explode("/", end($lastMongoURIUsed)['_id']);
				$inc = end($lastMongoIDUsed) + 1;
			}
		} else {
			$inc = 0;
		}
		return $inc;
	}
	
}
