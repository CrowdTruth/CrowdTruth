<?php
namespace Queues;
class UpdateJob {


	public function fire($job, $data){
		// Metrics
		$j = unserialize($data['job']);
		//dd($j);
		//dd($j);
		$workers = array();
		$annotations = $j->annotations;
        $result = array();
		$count = 0;
        foreach($annotations as $annotation){ 
        	
        	if(empty($annotation->dictionary))
        		continue; // Skip if no dictionary.

        	$workers[] = $annotation->crowdAgent_id;

			$uid = $annotation->unit_id; // to prevent mongoException: zero length key not allowed. Could also 'continue;'
			if(empty($uid)) $uid = 'unknown';
			else $count++;

			if(!isset($result[$uid]))
				$result[$uid] = $annotation->dictionary;
			else {
				foreach($annotation->dictionary as $key=>$val){ 
					if(is_array($val)){ // term1 -> [k] -> 1
						foreach($val as $k=>$v){
							//if(isset($result[$uid][$key][$k]))
								$result[$uid][$key][$k]+=$v;
							//else $result[$uid][$key][$k]=$v; // THIS SHOULDN'T HAPPEN
						}
					} else {			// [key] -> 1
						//if(isset($result[$uid][$key]))
	                    	$result[$uid][$key]+=$val;
	                    //else $result[$uid][$key]=$val; // THIS SHOULDN'T HAPPEN
					}
				}
			}
		}
		
		if(!isset($j->results)){
			$j->results = array('withSpam' => $result);
		} else {
			$r = $j->results;
			$r['withSpam'] = $result;
			$j->results = $r;
		}

		$j->workersCount = count(array_unique($workers));
        $j->annotationsCount = $count;

		$jpu = intval($j->jobConfiguration->content['annotationsPerUnit']);		
		$uc = intval($j->unitsCount);
		if($uc > 0 and $jpu > 0) $j->completion = $j->annotationsCount / ($uc * $jpu);	
		else $j->completion = 0.00;
		
		if($j->completion>1)
			$j->completion = 1.00; // TODO: HACK

		if($j->completion == 1) {
			$j->status = 'finished';
			if(!isset($j->finishedAt)) 
				$j->finishedAt = new \MongoDate; 
			
			if(isset($j->startedAt) and isset($j->startedAt->sec))
				$j->runningTimeInSeconds = $j->finishedAt->sec - $j->startedAt->sec;
		}

		if(($j->completion > .25) and ($j->latestMetrics < .25)){
			// do the metrics, we're in a queue anyway.
			$j->latestMetrics = .25;
			$this->createMetricActivity();
		}


		$j->realCost = ($count/$j->jobConfiguration->content['unitsPerTask'])*$j->jobConfiguration->content['reward'];

		//dd($j);
		$j->save();
		\Log::debug("Updated Job {$j->_id}.");




		$job->delete(); // This is the Queue job and not our Job!
	}

}

?>
