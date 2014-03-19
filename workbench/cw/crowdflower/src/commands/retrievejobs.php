<?php
namespace Cw\Crowdflower;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cfapi\CFExceptions;
use \MongoDB\Entity;
use \MongoDB\CrowdAgent;
use \MongoDB\Activity;
use \MongoDB\Agent;
use \Job;
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
				$job = $this->getJob($this->option('jobid'));
				$cf = new crowdwatson\Job(Config::get('crowdflower::apikey'));

				$judgments = ''; //todo			
			}

			if($this->option('judgments')) {
				$judgments = unserialize($this->option('judgments'));
				$job = $this->getJob($judgments[0]['job_id']);
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

			// TODO: check if exists. How?
			// For now this hacks helps: else a new activity would be created even if this 
			// command was called as the job is finished. It doesn't work against manual calling the command though.
			if($this->option('judgments')) {
				$activity = new Activity;
				$activity->label = "Units are annotated on crowdsourcing platform.";
				$activity->crowdAgent_id = $agent->_id; 
				$activity->used = $job->_id;
				$activity->softwareAgent_id = 'cf';
				$activity->save();
			}

			foreach($judgments as $judgment){
					if($this->storeJudgment($judgment, $job, $activity->_id, $agent->_id))
						$newJudgmentsCount++;
			}

			// Update count and completion
			// TODO: robustness
			// TODO: know bug: AnnotationsCount lags behind. 
			$job->annotationsCount = intval($job->annotationsCount)+$newJudgmentsCount;
			$jpu = intval($job->jobConfiguration->content['annotationsPerUnit']);		
			$uc = intval($job->unitsCount);
			if($uc > 0 and $jpu > 0) $job->completion = $job->annotationsCount / ($uc * $jpu);	
			else $job->completion = 0.00;
			$job->save();
			Log::debug("Saved $newJudgmentsCount new annotations to {$job->_id} to DB.");	
		} catch (CFExceptions $e){
			Log::warning($e->getMessage());
			throw $e;
		} catch (Exception $e) {
			Log::warning($e->getMessage());
			throw $e;
		}
		// If we throw an error, crowdflower will recieve HTTP 500 (internal server error) from us (and send an e-mail?).
		// We could also choose to just die(), but we'll need heavier error reporting on our side.

	}		

	/**
	* Retrieve Job from database. 
	* @return Entity (documentType:job)
	* @throws CFExceptions when no job is found. 
	*/
	private function getJob($jobid){
		if(!$job = Job::where('documentType', 'job')
					->where('softwareAgent_id', 'cf')
					->where('platformJobId', intval($jobid)) /* Mongo queries are strictly typed! We saved it as int in Job->store */
					->first())
		{
			$job = Job::where('documentType', 'job')
				->where('softwareAgent_id', 'cf')
				->where('platformJobId', (string) $jobid) /* Try this to be sure. */
				->first();
		}

		// Still no job found, this job is probably not made in our platform (or something went wrong earlier)
		if(!$job) {
			Log::warning("Callback from CF to our server for Job $jobid, which is not in our DB.");
			throw new CFExceptions("CFJob not in local database; retrieving it would break provenance.");
		}
		return $job;
	}


	/**
	* @return true if created, false if exists
	*/
	private function storeJudgment($judgment, $job, $activityId, $agentId)
	{

		// If exists return false. 
		if(Entity::where('documentType', 'annotation')
			->where('softwareAgent_id', 'cf')
			->where('platformAnnotationId', $judgment['id'])
			->first())
			return false;	

		try {
			$aentity = new Entity;
			$aentity->documentType = 'annotation';
			$aentity->domain = $job->domain;
			$aentity->format = $job->format;
			$aentity->job_id = $job->_id;
			$aentity->activity_id = $activityId;
			$aentity->crowdAgent_id = $agentId;
			$aentity->softwareAgent_id = 'cf';
			$aentity->unit_id = $judgment['unit_data']['uid']; // uid field in the csv we created in $batch->toCFCSV().
			$aentity->platformAnnotationId = $judgment['id'];
			$aentity->cfChannel = $judgment['external_type'];
			$aentity->acceptTime = new MongoDate(strtotime($judgment['started_at']));
			$aentity->submitTime = new MongoDate(strtotime($judgment['created_at']));
			$aentity->cfTrust = $judgment['trust'];
			$aentity->content = $judgment['data'];

			// QuestionDictionary
			$unit = Entity::where('_id', $aentity->unit_id)->first();
			$aentity->questionDictionary = $job->questionTemplate->getDictionary($unit, $aentity->content);

			$aentity->save();

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
			return true;
		} catch (Exception $e) {
			Log::warning("E:{$e->getMessage()} while saving annotation with CF id {$judgment['id']} to DB.");	
			if($aentity) $aentity->forceDelete();
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
