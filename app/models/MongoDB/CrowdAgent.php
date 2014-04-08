<?php

namespace MongoDB;

use Moloquent, URL, File, Exception, Auth, User;

class CrowdAgent extends Moloquent {

	protected $collection = 'crowdagents';
	protected $softDelete = true;
	protected static $unguarded = true;
    public static $snakeAttributes = false;
	
	public function hasGeneratedAnnotations(){
		return $this->hasMany('\MongoDB\Entity', 'crowdAgent_id', '_id');
	}

	public static function createCrowdAgent($softwareAgent_id, $platformWorkerId, $additionData = array()){
		// TODO
	}

}