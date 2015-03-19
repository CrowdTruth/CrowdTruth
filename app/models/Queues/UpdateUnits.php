<?php
namespace Queues;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

use \Entities\File as File;
use \Entities\Unit as Unit;
use \Entities\Batch as Batch;
use \Entities\Job as Job;
use \Entities\Workerunit as Workerunit;

class UpdateUnits {

	/**
	* @param $data = array(unitids)
	*/
	public function fire($job, $data){

		foreach($data as $id)
        {
		
	
        	set_time_limit(600);
			
        	$unit = Entity::id($id)->first();
			
            $batches = count(Batch::whereIn('parents', [$unit->_id])->get()->toArray());

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

            $platformField = array();
            $platformField['cf'] = count(Entity::where('unit_id', $unit->_id)->where('softwareAgent_id','cf' )->get()->toArray());
            $platformField['amt'] = count(Entity::where('unit_id', $unit->_id)->where('softwareAgent_id','amt' )->get()->toArray());
            //filtered
            $filteredField = array();
            $filteredField['job_ids'] = array_flatten(Job::where('metrics.filteredUnits.list','all', array($unit['_id']))->get(['_id'])->toArray());
            $filteredField['count'] = count($filteredField['job_ids']);

            $derivatives = Entity::whereIn('parents', array($unit->_id))->lists('_id');

            $children["count"] = count($derivatives);
            $children["list"] = $derivatives;

            $unit->cache = ["job" => "test",
                			"workers" => $workers,
                            "softwareAgent" => $platformField,
                			"workerunits" => $workerunit,
                            "filtered" => $filteredField,
                			"batches" => $batches,
                            "children" => $children];

			$avg_clarity = Entity::where('metrics.units.withoutSpam.'.$unit->_id, 'exists', 'true')->avg('metrics.units.withoutSpam.'.$unit->id.'.avg.max_relation_Cos');
			if (!isset($avg_clarity)) {
				$avg_clarity = 0;
			}
			
			$unit->avg_clarity = $avg_clarity;
			$unit->update();

            \Log::debug("Updated unit {$unit->_id}.");
			
        }
		
		$job->delete(); // the Queue job...
		
	}
}
?>
