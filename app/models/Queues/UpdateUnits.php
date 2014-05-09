<?php
namespace Queues;

use \Job;
use \Annotation;
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

            $batch['count'] = count(\MongoDB\Entity::where('documentType', 'batch')->where('parents', 'all', array($unit->_id))->get()->toArray());
            
            $annotation = array('count'=>0, 'spam'=>0, 'nonSpam'=>0);
            $workerlist = $workersspam = $workersnonspam = $joblist = array();

            foreach(Annotation::where('unit_id', $unit->_id)->get() as $a){
            	$joblist[] = $a->job_id;
            	$workerlist[] = $a->crowdAgent_id;

            	if($a->spam) {
            		$annotation['spam']++;
            		$workersspam[] = $a->crowdAgent_id;
            	} else {
            		$annotation['nonSpam']++;
            		$workersnonspam[] = $a->crowdAgent_id;
            	}	

            }

			$annotation['count'] = ($annotation['spam'] + $annotation['nonSpam']);

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


            //filtered
            $filteredField = array();
            $filteredField['job_ids'] = array_flatten(Job::where('metrics.filteredUnits.list','all', array($unit['_id']))->get(['_id'])->toArray());
            $filteredField['count'] = count($filteredField['job_ids']);

            $unit->cache = ["jobs" => $jobs,
                			"workers" => $workers,
                			"annotations" => $annotation,
                            "filtered" => $filteredField,
                			"batches" => $batch];

            $unit->update();
            \Log::debug("Updated unit {$unit->_id}.");
        }

		$job->delete(); // the Queue job...
	}


}

?>
