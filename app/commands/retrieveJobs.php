<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use crowdwatson\MechanicalTurk;
use \mongoDB\Entity;
use \mongoDB\Activity;
use \mongoDB\Agent;

class retrieveJobs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:retrievejobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieve the information on the jobs from the different platforms.';

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
		$turk = new MechanicalTurk;
		//$hit = $turk->getHIT($first->platformJobId));

		// MECHANICAL TURK

		// For..next. How many?

		print "Searching for HITs...\n";
		$hits = $turk->searchHITs(3, 1, null, 'Descending'); // Time Desc
		$newhits = 0;
		foreach ($hits as $hit){
			$h = $hit->toArray();

			if(!$entity = Entity::where('platformJobId', $h['HITId'])->first()){
				// Entity does not exist. This can only be because it's not created on our platform
				// (or possibly some weird database error, but we assume it's the former)

				// Create activity: FOUND HIT THAT'S NOT IN OUR DATABASE
				$activity = new Activity;
				$activity->_id = mt_rand(0,10000);
				$activity->label = "HIT found on AMT.";
				$activity->agent_id = 'todo';
				$activity->software_id = 'todo:retrievejobs';
				$activity->used = 'todo. UnitId?';
				$activity->save();

				// Save JobConf (Create it from the data inside the HIT)
				$jobConf = JobConfiguration::getFromHIT($hit);
				$jobConfId = $jobConf->store(null, $activity->_id);

				// Create new entity and fill with known fields. Will be saved later.
				$entity = new Entity;
				$entity->_id = "/job/amt/{$h['HITId']}";
				$entity->documentType = "job";
				$entity->activity_id = $activity->_id;

				$entity->jobConfigurationId = $jobConfId;
				$entity->templateId = 'todo_unknown';
				$entity->platformId = 'todo_amt'; 
				$entity->platformJobId = $h['HITId'];

				$newhits++;

			}

			// Only set these once.
			if(empty($entity->HITGroupId)) $entity->HITGroupId = $h['HITGroupId'];
			if(empty($entity->Expiration)) $entity->Expiration = $h['Expiration'];

			// STATUS. 
			$oldstatus = $entity->HITStatus;

			// We lose some detail here.
			if ($h['HITStatus'] == 'Assignable' or $h['HITStatus'] == 'Unassignable')
				$newstatus = 'running';
			elseif ($h['HITStatus'] == 'Reviewable' or $h['HITStatus'] == 'Reviewing')
				$newstatus = 'review';
			elseif ($h['HITStatus'] == 'Disposed')
				$newstatus = 'deleted';
			else
				$newstatus = 'unknown';

			$entity->HITStatus = $newstatus; 

			if($newstatus != $oldstatus)
				Log::debug("Status of job {$entity->_id} changed from $oldstatus to $newstatus");

			$entity->save();
			
			// GET ASSIGNMENTS FOR HIT (annotations for job)
			$newassignmentscount = 0;
			$jobId = $entity->_id;
			$assignments = $turk->getAssignmentsForHIT($h['HITId']);
			print 'Got ' . count($assignments) . " Assignments for {$h['HITId']}\n";
			
			foreach ($assignments as $ass){
				$assignment = $ass->toArray();
				$entity = Entity::where('jobId', $jobId)->where('platformAnnotationId', $assignment['AssignmentId'])->first();
				
				if($entity) {
					// ASSIGNMENT already in DB.

					$oldstatus = $entity->status;
					$newstatus = $assignment['AssignmentStatus'];

					if($oldstatus != $newstatus)
						Log::debug("Status of Annotation {$entity->_id} changed from $oldstatus to $newstatus");

					// If isset approval time: status->done?. 

					$entity->status = $newstatus;
					$entity->save();

				} else {
					// ASSIGNMENT entity does not exist: create activity, entity and refer to or create agent.

					// Create or retrieve Agent
					$agentId = $this->createCrowdAgent('amt', $assignment);
					
					// Create activity: annotate
					$activity = new Activity;
					$activity->_id = mt_rand(0,10000);
					$activity->label = "Unit is annotated on crowdsourcing platform.";
					$activity->agent_id = $agentId;
					$activity->used = 'todo. UnitId?';
					$activity->software_id = 'todo_amt';
					$activity->save();

					// Create entity
					$entity = new Entity;
					$entity->_id = mt_rand(0,10000);
					$entity->documentType = 'annotation';
					$entity->activity_id = $activity->_id;
					$entity->agent_id = $agentId;
					$entity->software_id = 'todo_amt'; 
					$entity->jobId = $jobId;
					$entity->unitId = 'todo';
					$entity->platformAnnotationId = $assignment['AssignmentId'];
					$entity->acceptTime = $assignment['AcceptTime'];
					$entity->status = $assignment['AssignmentStatus']; // Submitted | Approved | Rejected
					$entity->content = $assignment['Answer'];
					$entity->save();
					/*
						Possibly also:

						HITId				2P3Z6R70G5RC7PEQC857ZSST0J2P9T
						AutoApprovalTime	2014-02-06T13:10:01Z
						AcceptTime			2014-02-04T13:08:00Z
						SubmitTime			2014-02-04T13:10:01Z
						ApprovalTime		2014-02-04T13:11:00Z
						RejectionTime	
						Deadline	
						// or our own field: the difference between accept and submit.
					*/
					$newassignmentscount++;
				}
	
			}
			if($newassignmentscount>0)
				Log::debug("Got $newassignmentscount new Assignments for {$h['HITId']} - total " . count($assignments));
		}

		if($newhits>0)
			Log::info("CRON: Created new database entries for $newhits HITs");
		// more (debug)

	}


	public function createCrowdAgent($platform, $data){

		$workerid = '';
		$platformId = '';
		switch ($platform) {
			case 'amt':
				$workerId = $data['WorkerId'];
				$platformId = 'todo_amt';
				break;
			
			default:
				# code...
				break;
		}
		
		//TODO: where platformId.
		if($id = Entity::where('platformAnnotationId', $data['AssignmentId'])->pluck('_id')) 
			return $id;
		else {
			$agent = new Agent;
			$agent->type = 'crowdAgent';
			$agent->id= mt_rand(0,1000);
			$agent->used = 'todo. UnitId?';
			$agent->platformId= $platformId;
			$agent->platformAgentId = $workerId;
			$agent->save();
			
			return $agent->_id;
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
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
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
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
