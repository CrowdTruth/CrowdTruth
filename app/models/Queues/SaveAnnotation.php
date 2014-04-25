<?php
namespace Queues;
class SaveAnnotation {


	public function fire($job, $data){
		// Metrics
		$annotation = unserialize($data['annotation']);
		$annotation->save();
		\Log::debug("Saved annotation {$annotation->_id}.");
		$job->delete();
	}


}

?>
