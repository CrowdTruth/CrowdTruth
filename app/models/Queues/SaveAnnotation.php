<?php
namespace Queues;
use \Queue;
class SaveWorkerUnit {


	public function fire($job, $data){
		// Metrics
		$workerUnit = unserialize($data['workerUnit']);
		$workerUnit->save();
		\Log::debug("Saved workerUnit {$workerUnit->_id}.");

		Queue::push('Queues\UpdateUnits', [$workerUnit->unit_id]);

		$job->delete(); // the Queue job...
	}


}

?>
