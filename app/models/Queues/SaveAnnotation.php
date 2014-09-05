<?php
namespace Queues;
use \Queue;
class SaveWorkerunit {


	public function fire($job, $data){
		// Metrics
		$workerunit = unserialize($data['workerunit']);
		try {
			$workerunit->save();
			\Log::debug("Saved workerunit {$workerunit->_id}.");
			Queue::push('Queues\UpdateUnits', [$workerunit->unit_id]);
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
		//	$activity->forceDelete();
			\Log::debug("Something went wrong with creating the workerunit {$workerunit->_id}.");
			$workerunit->forceDelete();
		}
		
		$job->delete(); // the Queue job...
	}


}

?>
