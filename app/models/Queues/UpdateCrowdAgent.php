<?php
namespace Queues;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

use \Entities\File as File;
use \Entities\Media as Media;
use \Entities\Batch as Batch;
use \Entities\Job as Job;
use \Entities\Workerunit as Workerunit;

class UpdateCrowdAgent {


	public function fire($job, $data){
		// Metrics
		set_time_limit(600);
		$crowdagent = unserialize($data['crowdagent']);
		$crowdagent->updateStats2();
		\Log::debug("Updated CrowdAgent {$crowdagent->_id}.");
		$job->delete();
	}


}

?>
