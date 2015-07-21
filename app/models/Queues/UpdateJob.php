<?php
namespace Queues;

use Exception;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;
use \CrowdAgent as CrowdAgent;

use \Entities\File as File;
use \Entities\Unit as Unit;
use \Entities\Batch as Batch;
use \Entities\Job as Job;
use \Entities\Workerunit as Workerunit;

class UpdateJob {


	public function fire($job, $data){
		
		$j = unserialize($data['job']);

		// Create the annotationVector
		$workers = array();
		$workerunits = $j->workerunits;
        $result = array();
		$count = 0;
			
		
		// aggregate workerUnits
        foreach($workerunits as $workerunit){
        	
        	if(empty($workerunit->annotationVector))
        		continue; // Skip if no annotationVector.

        	$workers[] = $workerunit->crowdAgent_id;

			$uid = $workerunit->unit_id; // to prevent mongoException: zero length key not allowed. Could also 'continue;'
			if(empty($uid)) $uid = 'unknown';
			else $count++;

			if(!isset($result[$uid]))
				$result[$uid] = $workerunit->annotationVector;
			else {
				foreach($workerunit->annotationVector as $key=>$val){
					if(is_array($val)){ // term1 -> [k] -> 1
						foreach($val as $k=>$v){
							if(isset($result[$uid][$key][$k]))
								$result[$uid][$key][$k]+=$v;
							else $result[$uid][$key][$k]=$v; // THIS SHOULDN'T HAPPEN
						}
					} else {			// [key] -> 1
						if(isset($result[$uid][$key]))
	                    	$result[$uid][$key]+=$val;
	                    else $result[$uid][$key]=$val; // THIS SHOULDN'T HAPPEN
					}
				}
			}
		}
		
		// expand annotationVector with annotations from other workers
        foreach($workerunits as $workerunit){
        	
        	if(empty($workerunit->annotationVector)) {
        		continue; // Skip if no annotationVector.
			} else {
				$annotationVector = $workerunit->annotationVector;
			}
			
			$uid = $workerunit->unit_id; // to prevent mongoException: zero length key not allowed. Could also 'continue;'

			
			foreach($result[$uid] as $key=>$val){
				if(is_array($val)){ // term1 -> [k] -> 1
					foreach($val as $k=>$v){
						if(!isset($annotationVector[$key][$k]))
							$annotationVector[$key][$k] = 0;
					}
				} else {			// [key] -> 1
					if(!isset($annotationVector[$key]))
						$annotationVector[$key] = 0;
				}
			}
			$workerunit->annotationVector = $annotationVector;
			$workerunit->save();
		}
		
		
		if(!isset($j->results)){
			$j->results = array('withSpam' => $result);
		} else {
			$r = $j->results;
			$r['withSpam'] = $result;
			$j->results = $r;
		}

		$j->workersCount = count(array_unique($workers));
        $j->workerunitsCount = $count;

		$jpu = intval($j->jobConfiguration->content['workerunitsPerUnit']);
		$uc = intval($j->unitsCount);
		if($uc > 0 and $jpu > 0) $j->completion = $j->workerunitsCount / ($uc * $jpu);
		else $j->completion = 0.00;
		
		if($j->completion>1)
			$j->completion = 1.00; // TODO: HACK

		if($j->completion == 1) {
			if($j->status != 'imported') { $j->status = 'finished'; }
			if(!isset($j->finishedAt)) 
				$j->finishedAt = new \MongoDate; 
			
			if(isset($j->startedAt) and isset($j->startedAt->sec))
				$j->runningTimeInSeconds = $j->finishedAt->sec - $j->startedAt->sec;
		}

		$j->realCost = ($count/$j->jobConfiguration->content['unitsPerTask'])*$j->jobConfiguration->content['reward'];

		// METRICS
		//if(($j->completion > .25) and ($j->latestMetrics < .25)){

		// If a page is done and there's a proper annotationVector...

		try {
			//if(count($j->results['withSpam'])>1) and ($j->workerunitsCount % $j->jobConfiguration->content['unitsPerTask'] == 0)){
			if($j->completion==1){
				// do the metrics, we're in a queue anyway.
				\Log::debug("Starting metrics for Job {$j->_id}.");


				// TODO: of course all this hardcoded stuff has to go.
				if($j->type == 'FactSpan')
					$templateid = 'entity/text/medical/FactSpan/Factor_Span/0';
				else if($j->type == 'RelEx')
					$templateid = 'entity/text/medical/RelEx/Relation_Extraction/0';
				else if($j->type == 'RelDir')
					$templateid = 'entity/text/medical/RelDir/Relation_Direction/0';
				else if($j->type == 'MetaDEvents')
					$templateid = 'entity/text/cultural/MetaDEvents/MetadataDescription_Events/0';
				else if($j->type == 'DistributionalDisambiguation')
					$templateid = 'entity/text/opendomain/termpairs/0';
				else
					$templateid = 'entity/text/medical/FactSpan/Factor_Span/0';

				/*if(!($template = Entity::id($templateid)))
					throw new Exception("Template of type {$j->type} not found in database.");*/

				set_time_limit(3600); // One hour.
				$apppath = app_path();
				//$command = "/usr/bin/python2.7 $apppath/lib/generateMetrics.py '{$j->_id }' '$templateid'";
				$command = "C:\Users\IBM_ADMIN\AppData\Local\Enthought\Canopy\User\python.exe $apppath/lib/generateMetrics.py {$j->_id } $templateid";
				\Log::debug("Command: $command");
				exec($command, $output, $return_var);
				\Log::debug("Metrics done.");
				
				//dd($output);

				$response = json_decode($output[0], true);
				
				if(!$response or !isset($response['metrics']))
					throw new Exception("Incorrect response from generateMetrics.py.");

					
				// update list of spammers
				foreach($response['metrics']['spammers']['list'] as $spammer) {
					$response['metrics']['workers']['withoutFilter'][$spammer]['spam'] = 1;
				}			
					
				$j->metrics = $response['metrics'];
				$r = $j->results;
				$r['withoutSpam'] = $response['results']['withoutSpam'];
				$j->results = $r;
				$j->spam = $response['metrics']['filteredWorkerunits']['count'] / $j->workerunitsCount * 100;
				
				//\Log::debug(end($output));
				//$j->latestMetrics = .25;
			
				$this->createMetricActivity($j->_id);
				$j->save();

					
				// update workerunits
				foreach ($workerunits as $workerunit) {
					set_time_limit(60);
					\Queue::push('Queues\UpdateWorkerunits', array('workerunit' => serialize($workerunit)));
				}

				// update worker cache
				foreach ($response['metrics']['workers']['withoutFilter'] as $workerId => $workerData) {
					set_time_limit(60);
					$agent = CrowdAgent::where("_id", $workerId)->first();
					\Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($agent)));
				}
		
				// create output units
				foreach ($response['results']['withoutSpam'] as $unitId => $content) {
					set_time_limit(60);
					$unit = Unit::where("_id", $unitId)->first();
					
					// create new settings copied from the job
					$settings = [];
					$settings['project'] = $j['project'];
					$settings['format'] = $j['format'];
					$settings['domain'] = $j['domain'];
					$settings['documentType'] = $j['resultType'];
					
					// merge existing content with new generated content
					$newcontent = array_merge($content, $unit['content']);

					$childUnit = Unit::store($settings, $unitId, $newcontent);
				}
				
				// update input units
				$units = array_keys($response['metrics']['units']['withSpam']);
				\Queue::push('Queues\UpdateUnits', $units);
			
			
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