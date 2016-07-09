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

/*
 * This class updates the workerunit after the job metrics are computed.
 * Each workerunit is classified as spam, if the worker is triggered as spammer in that job
 * These results are after this used to update the worker statistics
*/

class UpdateWorkerunits {

	public function fire($job, $data){
		// Metrics
		//set_time_limit(600);
		$workerunit = unserialize($data['workerunit']);
		
		$spammer = Job::where('_id', $workerunit->job_id)->whereIn('metrics.spammers.list', [$workerunit->crowdAgent_id])->exists();
		
		$workerunit->spam = $spammer;
		$workerunit->save();
		
		\Log::debug("Updated Workerunit {$workerunit->_id}.");
		$job->delete();
	}


}

?>