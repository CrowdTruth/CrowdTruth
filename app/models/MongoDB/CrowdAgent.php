<?php

namespace MongoDB;
use \Moloquent;
use \Job;

class CrowdAgent extends Moloquent {

	protected $collection = 'crowdagents';
	protected $softDelete = true;
	protected static $unguarded = true;
    public static $snakeAttributes = false;
	
    // TODO: optimize
    public function updateStats2(){
      
        // take all the jobs for that worker
        if($crowdAgentJobs = Job::where('metrics.workers.withFilter.' . $this->_id, 'exists', true)->get(['_id']))
        {
            //if there is at least one job with that worker
            if(count($crowdAgentJobs->toArray()) > 0)
            {   

                $domains = $formats = $types = $jobids = array();
                $spam = $nonspam = $totalNoOfAnnotations = 0;
                foreach($this->annotations as $a){
                    $totalNoOfAnnotations++;

                    if($a->spam) $spam++;
                    else $nonspam++;

                    $domains[] = $a->domain;
                    $formats[]=$a->format;
                    $types[]= $a->type;
                    $jobids[] = $a->job_id;
                    $unitids[] = $a->unit_id;
       
                }

               // $this->annotationStats = array('count'=>$total['count'], 'spam'=>$spam, 'nonspam'=>$nonspam);
                $distinctAnnotationTypes = array_unique($types); // These actually are the Annotation types
                $distinctMediaFormats = array_unique($formats);
                $distinctMediaDomains = array_unique($domains);
                $workerParticipatedIn = count(array_unique($unitids));

                $cache["annotations"] = [
                        "count" => $totalNoOfAnnotations,
                        "spam" => $spam,
                        "nonspam" => $nonspam];


                // take all distinct batches
                $distinctBatchIds = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->distinct('batch_id')->get(['_id']);


                $cache["mediaTypes"] = [
                    //  "distinct" => count($distinctMediaTypes),
                        "count" => count($distinctAnnotationTypes), //,
                        "types" => []
                    ];

               
                
                foreach($distinctBatchIds as $distinctBatchId) {
                    $batchParents = array_flatten(\MongoDB\Entity::where('_id', '=', $distinctBatchId[0])->lists('parents'));
                    //print_r($batchParents[0]);
                    $batchParentsType = \MongoDB\Entity::where('_id', '=', $batchParents[0])->distinct('documentType')->get(['documentType']);
                    //print_r(array_flatten($batchParentsType->toArray())[0]);
                    if(isset($cache["mediaTypes"]["types"][array_flatten($batchParentsType->toArray())[0]])) {
                        $cache["mediaTypes"]["types"][array_flatten($batchParentsType->toArray())[0]] = $cache["mediaTypes"][array_flatten($batchParentsType->toArray())[0]] + 1;
                    }
                    else {
                        $cache["mediaTypes"]["types"] = [];
                        $cache["mediaTypes"]["types"][array_flatten($batchParentsType->toArray())[0]] = 1;
                    }
                }
                $cache["mediaTypes"]["distinct"] = sizeof(array_keys($cache["mediaTypes"]["types"]));
                


                if(count($distinctAnnotationTypes) > 0)
                {
                    $cache["jobTypes"] = [
                        "distinct" => count($distinctAnnotationTypes),
                        "count" => count(array_unique($jobids)),
                        "types" => []
                    ];
                    foreach($distinctAnnotationTypes as $distinctJobType)
                    {
                        $distinctJobTypeCount = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->where('documentType', 'job')->where('type', $distinctJobType)->count();
                        
                        $distinctJobTemplateTypes = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->where('documentType', 'job')->where('type', '=',  $distinctJobType)->distinct('template')->get()->toArray();
                        $countJobTemplateTypes = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->where('documentType', 'job')->where('type', '=',  $distinctJobType)->count();
                        //$cache["jobTypes"]["types"][$distinctJobType[0]] = [];
                        $cache["jobTypes"]["types"][$distinctJobType]['distinct'] = count($distinctJobTemplateTypes);
                        $cache["jobTypes"]["types"][$distinctJobType]['count'] = count($countJobTemplateTypes);
                        $cache["jobTypes"]["types"][$distinctJobType]["templates"] = [];
                        foreach($distinctJobTemplateTypes as $distinctJobTemplateType)
                        {
                        
                            $distinctJobTemplateAndCount = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->where('documentType', 'job')->where('template', $distinctJobTemplateType)->count();
                            
                            $cache["jobTypes"]["types"][$distinctJobType]["templates"][$distinctJobTemplateType[0]] = $distinctJobTemplateAndCount;
                        }
                    }   
                }


                if(count($distinctMediaFormats) > 0)
                {
                    $cache["mediaFormats"] = [
                        "distinct" => count($distinctMediaFormats),
                        "count" => $workerParticipatedIn,
                        "formats" => []
                    ];


                    $cache["mediaDomains"] = [
                        "distinct" => count($distinctMediaDomains),
                        "count" => $workerParticipatedIn,
                        "domains" => []
                    ];


                    foreach($distinctMediaFormats as $distinctMediaFormat)
                    {
                        $distinctMediaFormatAndCount = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->where('documentType', 'job')->where('format', $distinctMediaFormat)->count();
                        $cache["mediaFormats"]["formats"][$distinctMediaFormat] = $distinctMediaFormatAndCount;
                    }           


                    foreach($distinctMediaDomains as $distinctMediaDomain)
                    {
                        $distinctMediaDomainAndCount = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->where('documentType', 'job')->where('domain', $distinctMediaDomain)->count();
                        $cache["mediaDomains"]["domains"][$distinctMediaDomain] = $distinctMediaDomainAndCount;
                    }                                                   
                }

                $jobsAsSpammer = \MongoDB\Entity::whereIn('_id', array_flatten($crowdAgentJobs->toArray()))->whereIn('metrics.spammers.list', [$this->_id])->lists('platformJobId');
                $cache["spammer"]["count"] = count($jobsAsSpammer);
                $cache["spammer"]["jobs"] = array_flatten($jobsAsSpammer);
                $cache['flagged'] = 'false';
                $cache['sentMessagesToWorkers']['count'] = 0;
                $cache['sentMessagesToWorkers']['messages'] = [];
                $cache['avg_agreement'] = 0.0;
                $cache['avg_cosine'] = 0.0;

                $this->cache = $cache;
                $this->save();        
                     
            }
        }
  
    }








    public function updateStats(){
    	$countthese = array('type', 'domain', 'format');
    	$stats = array();

    	// Annotations
    	$total = array('count' => count($this->annotations));
        $spam = $nonspam = 0;
    	foreach($this->annotations as $a){
    		foreach($countthese as $x){
    			if(isset($total[$x][$a->$x])) $total[$x][$a->$x]++;
    			else $total[$x][$a->$x] = 1;
    		}

            if($a->spam) $spam++;
            else $nonspam++;

    		$jobids[] = $a->job_id;
    		$unitids[] = $a->unit_id;
    	}

    	$this->annotationStats = array('count'=>$total['count'], 'spam'=>$spam, 'nonspam'=>$nonspam);

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
        	//$this->jobTypes = array('distinct' => $total;
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

    /**
    * @throws Exception
    */
    public function flag($message){
        if($this->flagged==true)
            throw new Exception('Worker is already blocked.');

        $platformid = $this->softwareAgent_id;
        $platform = App::make($platformid);
        $platform->blockWorker($this->platformWorkerId, $message);
        $this->flagged = true;
    }


}