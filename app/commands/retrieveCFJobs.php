<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use crowdwatson\CFExceptions;
use \mongoDB\Entity;
use \mongoDB\CrowdAgent;
use \mongoDB\Activity;
use \mongoDB\Agent;

class retrieveCFJobs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:retrievecfjobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieve the information on the jobs from CrowdFlower.';

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
		if($this->option('judgment')) {
			$judgment = unserialize($this->option('judgment'));

			$job = Entity::where('documentType', 'job')
				->where('software_id', 'cf')
				->where('platformJobId', $judgment['job_id'])
				->first();

			if(!$job)
				throw new CFExceptions('Job not in local database; retrieving it would break provenance.');

			$this->storeJudgment($judgment, $job);

			// Update count and completion
			$job->annotationsCount = intval($job->annotationsCount)+1;
			$jpu = intval(Entity::find($job->jobConf_id)->first()->content['judgmentsPerUnit']);
			$uc = intval($job->unitsCount);
			if($uc > 0 and $jpu > 0) $job->completion = $job->annotationsCount / ($uc * $jpu);	
			else $job->completion = 0.00;

			$job->save();
		}


		if($this->option('jobid')){
			die('not yet implemented');
			// We could check for each annotation and add it if somehow it didn't get added earlier.
			// For this, we should add ifexists checks in the storeJudgment method.
			$cf = new crowdwatson\Job(Config::get('config.cfapikey'));
			$jobid = $this->option('jobid');
			$job = Entity::where('documentType', 'job')
				->where('software_id', 'cf')
				->where('platformJobId', $jobid)
				->first();

			if(!$job)
				throw new CFExceptions('Job not in local database; retrieving it would break provenance.');

			//foreach unitid

		}




		/*$cf = new crowdwatson\Job(Config::get('config.cfapikey'));
		//$jobid = $this->argument('jobid');
		$jobid = '351526';
		$result = $cf->readJob($jobid);

		if(isset($result['result']['errors']))
			throw new CFExceptions($result['result']['errors'][0]);


		$cfjob = array_keys($result['result']);
		
		//$job->annotationsCount = $cfjob['judgments_count'];
		*/
	}		


	private function storeJudgment($judgment, $job)
	{

		try {

			if(!$agent = CrowdAgent::where('platformAgentId', $judgment['worker_id'])->where('platform_id', 'cf')->first()){
				$agent = new CrowdAgent;
				$agent->_id= "/crowdagent/cf/{$judgment['worker_id']}";
				$agent->used = 'todo. UnitId?';
				$agent->platform_id= 'cf';
				$agent->platformAgentId = $judgment['worker_id'];
				$agent->country = $judgment['country'];
				$agent->region = $judgment['region'];
				$agent->city = $judgment['city'];
			}	
				
			$agent->cfWorkerTrust = $judgment['worker_trust'];
			$agent->save();

			// Create activity: annotate
			// TODO: (possibly) check if exists?
			$activity = new Activity;
			$activity->_id = mt_rand(0,10000);
			$activity->label = "Unit is annotated on crowdsourcing platform.";
			$activity->agent_id = $agent->_id; 
			$activity->used = 'todo. UnitId?';
			$activity->software_id = 'cf';
			$activity->save();

			$aentity = new Entity;
			$aentity->documentType = 'annotation';
			$aentity->domain = $job->domain;
			$aentity->format = $job->format;
			$aentity->job_id = $job->_id;
			$aentity->activity_id = $activity->_id;
			$aentity->agent_id = $agent->_id;
			$aentity->software_id = 'cf';
			$aentity->unit_id = 'todo';
			$aentity->platformAnnotationId = $judgment['id'];
			$aentity->cfChannel = $judgment['external_type'];
			$aentity->acceptTime = $judgment['started_at'];
			$aentity->cfTrust = $judgment['trust'];
			$aentity->content = $judgment['data'];

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

			// TODO: update job status, judgment count

			Log::debug("Saved annotation {$aentity->_id} to DB.");	
		} catch (Exception $e) {
			Log::debug("E:{$e->getMessage()} while saving annotation with CF id {$judgment['id']} to DB.");	
			if($activity) $activity->forceDelete();
			if($aentity) $aentity->forceDelete();
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
			array('judgments', null, InputOption::VALUE_OPTIONAL, 'A full serialized judgment from the CF API. Will insert into DB.', null),
			array('jobid', null, InputOption::VALUE_OPTIONAL, 'CF Job ID.', null)
		);
	}

}
