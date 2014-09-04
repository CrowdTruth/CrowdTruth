<?php
namespace Queues;

use \Job;
use \Workerunit;
class UpdateUnits {

	/**
	* @param $data = array(unitids)
	*/
	public function fire($job, $data){


		// TODO: some error handling


        $jobIdsPerType = array();
        foreach(Job::get() as $j){
        	if (isset($jobIdsPerType[$j->type]))
        		array_push($jobIdsPerType[$j->type], $j->_id);
        	else
        		$jobIdsPerType[$j->type] = [$j->_id];
        }

        foreach($data as $id)
        {
        	set_time_limit(30);

        	$unit = \MongoDB\Entity::id($id)->first();
        //    dd($id);
            $batch['count'] = count(\MongoDB\Entity::where('documentType', 'batch')->where('parents', 'all', array($unit->_id))->get()->toArray());

            $workerunit = array('count'=>0, 'spam'=>0, 'nonSpam'=>0);
            $workerlist = $workersspam = $workersnonspam = $joblist = array();

            foreach(Workerunit::where('unit_id', $unit->_id)->get() as $a){
            	$joblist[] = $a->job_id;
            	$workerlist[] = $a->crowdAgent_id;

            	if($a->spam) {
            		$workerunit['spam']++;
            		$workersspam[] = $a->crowdAgent_id;
            	} else {
            		$workerunit['nonSpam']++;
            		$workersnonspam[] = $a->crowdAgent_id;
            	}	

            }

			$workerunit['count'] = ($workerunit['spam'] + $workerunit['nonSpam']);

			$workers['count'] = count(array_unique($workerlist));
            $workers['spam'] = count(array_unique($workersspam));
            $workers['nonSpam'] = count(array_unique($workersnonspam));
            $workers['potentialSpam'] = count(array_intersect($workersspam, $workersnonspam));

            // Jobs
            $jobs['count'] = count(array_unique($joblist));

            foreach (array_keys($jobIdsPerType) as $type){
                $jobs['types'] = array();
                $count = count(array_intersect(array_unique($joblist), $jobIdsPerType[$type]));
                if($count != 0) 
                    $jobs["types"][$type] = $count;
            }
            
            $jobs['distinct'] = count($jobs['types']);

            $platformField = array();
            $platformField['cf'] = count(\MongoDB\Entity::where('unit_id', $unit->_id)->where('softwareAgent_id','cf' )->get()->toArray());
            $platformField['amt'] = count(\MongoDB\Entity::where('unit_id', $unit->_id])->where('softwareAgent_id','amt' )->get()->toArray());
            //filtered
            $filteredField = array();
            $filteredField['job_ids'] = array_flatten(Job::where('metrics.filteredUnits.list','all', array($unit['_id']))->get(['_id'])->toArray());
            $filteredField['count'] = count($filteredField['job_ids']);

            $derivatives = \MongoDB\Entity::whereIn('parents', array($unit->_id]))->lists('_id');

            $children["count"] = count($derivatives);
            $children["list"] = $derivatives;

            $unit->cache = ["jobs" => $jobs,
                			"workers" => $workers,
                            "softwareAgent" => $platformField,
                			"workerunits" => $workerunit,
                            "filtered" => $filteredField,
                			"batches" => $batch,
                            "children" => $children];
            $unit->update();
	    $avg_clarity = \MongoDB\Entity::where('metrics.units.withoutSpam.'.$unit->_id, 'exists', 'true')->avg('metrics.units.withoutSpam.'.$unit->id.'.max_relation_Cos.avg');
	    if (!isset($avg_clarity)) $avg_clarity = 0;
        	$unit->avg_clarity = $avg_clarity;
        	$unit->update();

            \Log::debug("Updated unit {$unit->_id}.");
        }

		$job->delete(); // the Queue job...
	}


}

?>
