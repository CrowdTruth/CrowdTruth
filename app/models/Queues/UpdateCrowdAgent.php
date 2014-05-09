<?php
namespace Queues;
class UpdateCrowdAgent {


	public function fire($job, $data){
		// Metrics
		$crowdagent = unserialize($data['crowdagent']);
		$crowdagent->updateStats2();
		\Log::debug("Updated CrowdAgent {$crowdagent->_id}.");
		$job->delete();
	}


}

?>
