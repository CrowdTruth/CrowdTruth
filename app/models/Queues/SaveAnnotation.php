<?php
namespace Queues;
use \Queue;
class SaveAnnotation {


	public function fire($job, $data){
		// Metrics
		$annotation = unserialize($data['annotation']);
		$annotation->save();
		\Log::debug("Saved annotation {$annotation->_id}.");

		Queue::push('Queues\UpdateUnits', [$annotation->unit_id]);

		$job->delete(); // the Queue job...
	}


}

?>
