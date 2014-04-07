<?php
namespace Queues;
class UpdateJob {


	public function fire($job, $data){
		// Metrics
		$j = unserialize($data['job']);
		//dd($j);
		$annotations = $j->annotations;
        $result = array();

        foreach($annotations as $annotation){ 
			$uid = $annotation->unit_id;
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
		
		$j->results = $result;

        $j->annotationsCount = count($annotations);
    	//$this->annotationsCount+=$count;
		$jpu = intval($j->jobConfiguration->content['annotationsPerUnit']);		
		$uc = intval($j->unitsCount);
		if($uc > 0 and $jpu > 0) $j->completion = $j->annotationsCount / ($uc * $jpu);	
		else $j->completion = 0.00;

		if($j->completion == 1) $j->status = 'finished'; // Todo: Not sure if this works
		$j->update();
		Log::debug("Updated Job {$j->_id}.")

		$job->delete();
	}


}

?>