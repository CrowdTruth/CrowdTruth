<?php

namespace MongoDB;
use \Moloquent;
class CrowdAgent extends Moloquent {

	protected $collection = 'crowdagents';
	protected $softDelete = true;
	protected static $unguarded = true;
    public static $snakeAttributes = false;
	
    public function updateStats(){
    	$countthese = array('type', 'domain', 'format');
    	$stats = array();

    	// Annotations
    	$total = array('count' => count($this->annotations));
    	foreach($this->annotations as $a){
    		foreach($countthese as $x){
    			if(isset($total[$x][$a->$x])) $total[$x][$a->$x]++;
    			else $total[$x][$a->$x] = 1;
    		}

    		$jobids[] = $a->job_id;
    		$unitids[] = $a->unit_id;
    	}
    	$this->annotationCount = $total;

        if(isset($jobids)){
        	// Jobs
        	$total = array('count' => count(array_unique($jobids)));
        	foreach(array_unique($jobids) as $jobid){
                if($a = \Job::id($jobid)->first()){
            		foreach($countthese as $x){
            			if(isset($total[$x][$a->$x])) $total[$x][$a->$x]++;
            			else $total[$x][$a->$x] = 1;
            		}
                }
        	}
        	$this->jobCount = $total;
		}

    	// Units
        if(isset($unitids)){
        	$countthese = array_diff($countthese, array('type')); // UNITs have no type, so remove this from the array.
        	$total = array('count' => count(array_unique($unitids)));
        	foreach(array_unique($unitids) as $unitid){
        		if($a = \MongoDB\Entity::id($unitid)->first()){
            		foreach($countthese as $x){
            			if(isset($total[$x][$a->$x])) $total[$x][$a->$x]++;
            			else $total[$x][$a->$x] = 1;
            		}
                }
        	}
        	$this->unitCount = $total;
        }
        
    	$this->save();
    }


	// TODO: Can be removed.
	public function hasGeneratedAnnotations(){
		return $this->hasMany('\MongoDB\Entity', 'crowdAgent_id', '_id');
	}

	public function annotations(){
		return $this->hasMany('Annotation', 'crowdAgent_id', '_id');
	}


	public function scopeId($query, $id)
    {
        return $query->where_id($id);
    }

	public static function createCrowdAgent($softwareAgent_id, $platformWorkerId, $additionalData = array()){
		// TODO
	}

}