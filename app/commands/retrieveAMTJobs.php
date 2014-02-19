<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use crowdwatson\MechanicalTurk;
use \mongoDB\Entity;
use \mongoDB\CrowdAgent;
use \mongoDB\Activity;
use \mongoDB\Agent;

class retrieveAMTJobs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:retrieveamtjobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieve the information on the jobs from Mechanical Turk.';

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

		// TODO: Try/catch blocks, UNITID
		print("Retrieving jobs....");
		$turk = new MechanicalTurk;

		// Todo optimize query.
		$jobs = Entity::where('documentType', 'job')->where('software_id', 'amt')->orderBy('created_at', 'desc')->get();
		foreach($jobs as $job){
			$newannotationscount = 0;
			$newplatformhitid = array();
			$newstatus = 'review'; // if this doesn't change, nothing is running anymore, so 'review'. This doesn't work with disposed yet..
			foreach($job->platformJobId as $hitid){
				
				if($hitid['status'] == 'Disposed') // Can't recover from that.
					$newplatformhitid[] = $hitid;
				else {

					$hit = $turk->getHIT($hitid['id']);
					$h = $hit->toArray();

					//Do this once:
					if(empty($job->HITGroupId)) $job->HITGroupId = $h['HITGroupId'];
					if(empty($job->Expiration)) $job->Expiration = $h['Expiration'];
					if(empty($job->HITTypeId)) $job->HITTypeId = $h['HITTypeId'];


					if($h['HITStatus'] == 'Assignable' or $h['HITStatus'] == 'Unassignable')
						$newstatus = 'running';

					$newplatformhitid[] = array('id' => $hitid['id'], 
												'status' => $h['HITStatus']);
					
					// todo: IF each is disposed, newstatus = deleted.


					// Get Assignments.
					$jobId = $job->_id;
					$assignments = $turk->getAssignmentsForHIT($hitid['id']);
					print 'Got ' . count($assignments) . " Assignments for {$hitid['id']}\n";
					
					foreach ($assignments as $ass){
						$assignment = $ass->toArray();

						$aentity = Entity::where('job_id', $jobId)->where('platformAnnotationId', $assignment['AssignmentId'])->first();
						
						// Sometimes, there's more entities. But if there's at least one, we know we retrieved the Assignment.

						if($aentity) { // ASSIGNMENT already in DB.
							
							$oldstatus = $aentity->status;
							$newstatus = $assignment['AssignmentStatus'];

							if($oldstatus != $newstatus){
								Log::debug("Status of Annotation {$aentity->_id} changed from $oldstatus to $newstatus");
								
								$aentity->status = $newstatus;
								$aentity->update();
							}
						

						} else { // ASSIGNMENT entity not in DB: create activity, entity and refer to or create agent.

							// Create or retrieve Agent
							$agentId = $this->createCrowdAgent('amt', $assignment);
							
							// Create activity: annotate
							$activity = new Activity;
							$activity->_id = mt_rand(0,10000);
							$activity->label = "Unit is annotated on crowdsourcing platform.";
							$activity->agent_id = $agentId; 
							$activity->used = 'todo. UnitId?';
							$activity->software_id = 'amt';
							$activity->save();

							// Create entity FOR EACH UNIT

							// OPTIONAL: we could create an ASSIGNMENT entity to hold the metadata.

							foreach ($assignment['Answer'] as $q=>$ans){
								// TODO Do some tricks with UNITID's; Sometimes there are more answerfields in 1 UNIT.

								$aentity = new Entity;
								$aentity->documentType = 'annotation';
								$aentity->domain = $entity->domain;
								$aentity->format = $entity->format;
								$aentity->activity_id = $activity->_id;
								$aentity->agent_id = $agentId;
								$aentity->software_id = 'amt';
								$aentity->job_id = $jobId;
								$aentity->unit_id = 'todo';
								$aentity->platformAnnotationId = $assignment['AssignmentId'];
								$aentity->acceptTime = $assignment['AcceptTime'];
								$aentity->submitTime = $assignment['SubmitTime'];
								$aentity->content = $ans;
								$aentity->status = $assignment['AssignmentStatus']; // Submitted | Approved | Rejected
								$aentity->save();

								$newannotationscount++;

							}


							/*
								Possibly also:

								HITId				2P3Z6R70G5RC7PEQC857ZSST0J2P9T
								AutoApprovalTime	2014-02-06T13:10:01Z
								ApprovalTime		2014-02-04T13:11:00Z
								RejectionTime	
								Deadline	
								// or our own field: the difference between accept and submit.
							*/
							

						}

						if($newannotationscount>0){
							Log::debug("Got $newannotationscount new Assignments for {$h['HITId']} - total " . count($assignments));

						}
						


					} // foreach assignment
				} // if / else				
			} // foreach hit



			$job->annotationsCount = intval($job->annotationsCount)+$newannotationscount;
			$jpu = intval(Entity::find($job->jobConf_id)->first()->content['judgmentsPerUnit']);
			$uc = intval($job->unitsCount);
			if($uc > 0 and $jpu > 0) $job->completion = $job->annotationsCount / ($uc * $jpu);	
			else $job->completion = 0.00;

			// Change status and save.
			$oldstatus = $job->status;
			$job->status = $newstatus;
			// if($job->completion == 1) $newstatus = 'finished';
			if($newstatus != $oldstatus)
				Log::debug("Status of job {$job->_id} changed from $oldstatus to $newstatus");

			// Save JOB with new status and completion.
			$job->save();




		} // foreach JOB
	}		


	public function createCrowdAgent($platform, $data){

		$workerid = '';
		if($platform == 'amt') {
			$workerId = $data['WorkerId'];
		} else {
			throw new Exception("Unknown platform $platform");
			// CF is not (yet?) needed here -> webhook.
		}	

		if($id = CrowdAgent::where('platformAgentId', $workerId)->where('platform_id', $platform)->pluck('_id')) 
			return $id;

		else {
			$agent = new CrowdAgent;
			$agent->_id= "/crowdagent/$platform/$workerId";
			$agent->used = 'todo. UnitId?';
			$agent->platform_id= $platform;
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
