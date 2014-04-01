<?php

namespace MongoDB;

use Moloquent, URL, File, Exception, Auth, User;

class CrowdAgent extends Moloquent {

	protected $collection = 'crowdagents';
	protected $softDelete = true;
	protected static $unguarded = true;
    public static $snakeAttributes = false;
	
	public function jobCount(){
		// $jobs = $this->hasMany('Job', '_id', 'activity_id');
		// count(
	}

	public function annotationCount(){
		$annotations = $this->hasGeneratedAnnotations();
		$count = count($annotations);
		return $count;	
	}
	
	public function hasDoneJobs() {
		$annotations = $this->hasGeneratedAnnotations();
		dd($annotations->get()->toArray());
		return $jobs;
		// return $this->hasMany('\MongoDB\Entity', 'metrics.workers.withoutFilter', '_id');
	}

	public function hasGeneratedAnnotations(){
		return $this->hasMany('\MongoDB\Entity', 'crowdAgent_id', '_id');
	}

}