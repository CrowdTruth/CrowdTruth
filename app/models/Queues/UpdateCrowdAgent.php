<?php
namespace Queues;
class UpdateCrowdAgent {


	public function fire($job, $data){
		// Metrics
		$crowdagent = unserialize($data['crowdagent']);
		$crowdagent->updateStats();
		\Log::debug("Updated CrowdAgent {$crowdagent->_id}.");
		$job->delete();
	}


}

?>
