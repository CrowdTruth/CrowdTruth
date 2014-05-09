<?php
namespace Queues;

use \MongoDB\Activity;
use Exception;

class UpdateJob {


	public function fire($job, $data){
		
		$j = unserialize($data['job']);

		// Create the dictionary
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

		$j->realCost = ($count/$j->jobConfiguration->content['unitsPerTask'])*$j->jobConfiguration->content['reward'];

		// METRICS
		//if(($j->completion > .25) and ($j->latestMetrics < .25)){

		// If a page is done and there's a proper dictionary...

		try {
			//if(count($j->results['withSpam'])>1) and ($j->annotationsCount % $j->jobConfiguration->content['unitsPerTask'] == 0)){
			if(false){
				// do the metrics, we're in a queue anyway.
				\Log::debug("Starting metrics for Job {$j->_id}.");


				// TODO: of course all this hardcoded stuff has to go.
				if($j->type = 'FactSpan')
					$templateid = 'entity/text/medical/FactSpan/Factor_Span/0';
				elseif($j->type = 'RelEx')
					$templateid = 'entity/text/medical/RelEx/Relation_Extraction/0';
				elseif($j->type = 'RelDir')
					$templateid = 'entity/text/medical/RelDir/Relation_Direction/0';
				else
					throw new Exception("Type {$j->type} not recognised. We currently only have FactSpan, RelEx and RelDir.");

				/*if(!($template = \MongoDB\Entity::id($templateid)))
					throw new Exception("Template of type {$j->type} not found in database.");*/

				set_time_limit(3600); // One hour.
				$apppath = app_path();
				$command = "/usr/bin/python2.7 $apppath/lib/fakeMetrics.py '{$j->_id }' '$templateid'";
				\Log::debug("Command: $command");
				exec($command, $output, $return_var);
				\Log::debug("Metrics done.");
				
				//dd($output);

				$response = json_decode($output[0], true);
				dd($response);
				$j->metrics = $response['metrics'];
				//$j->results = array_merge($j->results, $response['results']);
				
				dd($j);
				//\Log::debug(end($output));
				//$j->latestMetrics = .25;
				
				$this->createMetricActivity($j->_id);
			}
		} catch (Exception $e) {
			\Log::debug("Error in running metrics: {$e->getMessage()}");
			echo $e->getMessage();
		}
		//

		//dd($j);
		$j->save();
		\Log::debug("Updated Job {$j->_id}.");


		$job->delete(); // This is the Queue job and not our Job!
	}

	private function createMetricActivity($jobid){

		// Todo: create software agent

		$activity = new Activity;
		$activity->label = "Metrics calculated on job.";
		$activity->used = $jobid;
		$activity->softwareAgent_id = 'metrics';
		$activity->save();
	}

}

?>
