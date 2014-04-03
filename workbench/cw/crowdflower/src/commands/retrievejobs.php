<?php
namespace Cw\Crowdflower;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cw\Crowdflower\Cfapi\CFExceptions;
use \MongoDB\Entity;
use \Annotation;
use \MongoDB\CrowdAgent;
use \MongoDB\Activity;
use \MongoDB\Agent;
use \Job;
use \Log;
use \QuestionTemplate;
use \MongoDate;

class RetrieveJobs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cf:retrievejobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieve annotations from CrowdFlower and update job status.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$newJudgmentsCount = 0;

		try {
			if($this->option('jobid')){
				die('not yet implemented');
				// We could check for each annotation and add it if somehow it didn't get added earlier.
				// For this, we should add ifexists checks in the storeJudgment method.
				$cfjobid = $this->option('jobid');
				$cf = new Cw\Crowdflower\Cfapi\Job(Config::get('crowdflower::apikey'));
				$judgments = ''; //todo			
			}

			if($this->option('judgments')) {
				$judgments = unserialize($this->option('judgments'));
				$cfjobid = $judgments[0]['job_id']; // We assume that all judgments have the same jobic
			}
			
			$judgment = $judgments[0];
			$agent = CrowdAgent::where('platformAgentId', $judgment['worker_id'])
								->where('softwareAgent_id', 'cf')
								->first();
			if(!$agent){
				$agent = new CrowdAgent;
				$agent->_id= "crowdagent/cf/{$judgment['worker_id']}";
				$agent->softwareAgent_id= 'cf';
				$agent->platformAgentId = $judgment['worker_id'];
				$agent->country = $judgment['country'];
				$agent->region = $judgment['region'];
				$agent->city = $judgment['city'];
			}	
			
			if( $agent->cfWorkerTrust != $judgment['worker_trust']){
				$agent->cfWorkerTrust = $judgment['worker_trust'];
				$agent->save();
			}

			$ourjobid = $this->getJob($cfjobid)->_id;


			// TODO: check if exists. How?
			// For now this hacks helps: else a new activity would be created even if this 
			// command was called as the job is finished. It doesn't work against manual calling the command though.
			if($this->option('judgments')) {
				$activity = new Activity;
				$activity->label = "Units are annotated on crowdsourcing platform.";
				$activity->crowdAgent_id = $agent->_id; 
				$activity->used = $ourjobid;
				$activity->softwareAgent_id = 'cf';
				$activity->save();
			}

			// Store judgment and update job.
			foreach($judgments as $judgment)
				if($annotation = $this->storeJudgment($judgment, $ourjobid, $activity->_id, $agent->_id)){
					$job = $this->getJob($platformjobid);
					$job->addResults($annotation);
					$job->save();
				}
			
			//Log::debug("Saved new annotations to {$job->_id} to DB.");	
		} catch (CFExceptions $e){
			Log::warning($e->getMessage());
			throw $e;
		} catch (Exception $e) {
			Log::warning($e->getMessage());
			throw $e;
		}
		// If we throw an error, crowdflower will recieve HTTP 500 (internal server error) from us (and try again).

	}		

	/**
	* Retrieve Job from database. 
	* @return Entity (documentType:job)
	* @throws CFExceptions when no job is found. 
	*/
	private function getJob($jobid){
		if(!$job = Job::where('softwareAgent_id', 'cf')
						->where('platformJobId', intval($jobid)) /* Mongo queries are strictly typed! We saved it as int in Job->store */
						->first())
		{
			$job = Job::where('softwareAgent_id', 'cf')
						->where('platformJobId', (string) $jobid) /* Try this to be sure. */
						->first();
		}

		// Still no job found, this job is probably not made in our platform (or something went wrong earlier)
		if(!$job) {
			Log::warning("Callback from CF to our server for Job $jobid, which is not in our DB.");
			throw new CFExceptions("CFJob not in local database; retrieving it would break provenance.");
			// TODO discuss: we could also decide to create a new job with all the info we can get.
		}
		return $job;
	}


	/**
	* @return true if created, false if exists
	*/
	private function storeJudgment($judgment, $ourjobid, $activityId, $agentId)
	{

		// If exists return false. 
		if(Annotation::where('softwareAgent_id', 'cf')
			->where('platformAnnotationId', $judgment['id'])
			->first())
			return false;	

		try {
			$annotation = new Annotation;
			$annotation->job_id = $ourjobid;
			$annotatoin->platformJobId = $judgment['job_id'];
			$annotation->activity_id = $activityId;
			$annotation->crowdAgent_id = $agentId;
			$annotation->softwareAgent_id = 'cf';
			$annotation->unit_id = $judgment['unit_data']['uid']; // uid field in the csv we created in $batch->toCFCSV().
			$annotation->platformAnnotationId = $judgment['id'];
			$annotation->cfChannel = $judgment['external_type'];
			$annotation->acceptTime = new MongoDate(strtotime($judgment['started_at']));
			$annotation->submitTime = new MongoDate(strtotime($judgment['created_at']));
			$annotation->cfTrust = $judgment['trust'];
			$annotation->content = $judgment['data'];
			$annotation->save();
			Log::debug("--+1-- ({$judgment['id']})");	
			return $annotation;
			// TODO: golden

			/*  Possibly also:

				unit_state (but will be a hassle to update)
				rejected
				reviewed
				tainted
				golden (todo!)
				missed
				webhook_sent_at

			*/

		} catch (Exception $e) {
			Log::warning("E:{$e->getMessage()} while saving annotation with CF id {$judgment['id']} to DB.");	
			if($annotation) $annotation->forceDelete();
			// TODO: more?
		}
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('jobid', InputArgument::OPTIONAL, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('judgments', null, InputOption::VALUE_OPTIONAL, 'A full serialized collection of judgments from the CF API. Will insert into DB.', null),
			array('jobid', null, InputOption::VALUE_OPTIONAL, 'CF Job ID.', null)
		);
	}

}
