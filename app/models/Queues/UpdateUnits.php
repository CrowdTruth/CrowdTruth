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
            
            $annotation = array('count'=>0, 'spamCount'=>0, 'nonSpamCount'=>0);
            $workerlist = $workersspam = $workersnonspam = $joblist = array();

            foreach(Annotation::where('unit_id', $unit->_id)->get() as $a){
            	$joblist[] = $a->job_id;
            	$workerlist[] = $a->crowdAgent_id;

            	if($a->spam) {
            		$annotation['spamCount']++;
            		$workersspam[] = $a->crowdAgent_id;
            	} else {
            		$annotation['nonSpamCount']++;
            		$workersnonspam[] = $a->crowdAgent_id;
            	}	

            }

			$annotation['count'] = ($annotation['spamCount'] + $annotation['nonSpamCount']);

			$workers['count'] = count(array_unique($workerlist));
            $workers['spamCount'] = count(array_unique($workersspam));
            $workers['nonSpamCount'] = count(array_unique($workersnonspam));

             $jobs['count'] = count(array_unique($joblist));

            foreach (array_keys($jobIdsPerType) as $type){
                $jobs["types"][$type] = count(array_intersect(array_unique($joblist), $jobIdsPerType[$type]));
            }

            $unit->cache = ["jobs" => $jobs,
                			"workers" => $workers,
                			"annotations" => $annotation,
                			"batches" => $batch];

            $unit->update();
            \Log::debug("Updated unit {$unit->_id}.");
        }

		$job->delete(); // the Queue job...
	}


}

?>
