<?php
namespace Queues;
use \Queue;
class SaveWorkerunit {


	public function fire($job, $data){
		// Metrics
		$workerunit = unserialize($data['workerunit']);
		$workerunit->save();
		\Log::debug("Saved workerunit {$workerunit->_id}.");

		Queue::push('Queues\UpdateUnits', [$workerunit->unit_id]);

		$job->delete(); // the Queue job...
	}


}

?>
