<?php
namespace Cw\Mturk;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \MongoDB\Entity;
use \MongoDB\CrowdAgent;
use \MongoDB\Activity;
use \MongoDB\Agent;
use \Job;
use \MongoDate;
use \Config;
use \QuestionTemplate;
use \Log;

class RetrieveJobs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'amt:retrievejobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieve annotations from Mechanical Turk and update job status.';

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

		print("Retrieving jobs....\r\n");
		$turk = new Turkapi\MechanicalTurk(Config::get('mturk::rooturl'), false, Config::get('mturk::accesskey'), Config::get('mturk::secretkey'));


		// Todo optimize query.
		$jobs = Job::where('documentType', 'job')
						->where('softwareAgent_id', 'amt')
						->orderBy('created_at', 'desc')
						->where('status', '!=', 'finished')
						->get();

		foreach($jobs as $job){
			$newannotations = array();
			$newplatformhitid = array();

			foreach($job->platformJobId as $hitid){
				
				if($hitid['status'] == 'deleted') // Can't recover from that. Don't update.
					$newplatformhitid[] = $hitid;
				else {

					$hit = $turk->getHIT($hitid['id']);
					$h = $hit->toArray();

					//Do this once:
					if(empty($job->Expiration)) $job->Expiration = new MongoDate(strtotime($h['Expiration']));
					if(empty($job->HITGroupId)) $job->HITGroupId = $h['HITGroupId'];
					if(empty($job->HITTypeId)) 	$job->HITTypeId  = $h['HITTypeId'];


					if(($h['HITStatus'] == 'Assignable' or $h['HITStatus'] == 'Unassignable')
					and ($job->Expiration->sec > time())) // Not yet expired. TODO: Timezones
						$newstatus = 'running';
					elseif($h['HITStatus'] == 'Reviewable' or $h['HITStatus'] == 'Reviewing')
						$newstatus = 'review';
					elseif($h['HITStatus'] == 'Disposed')
						$newstatus = 'deleted';

					$newplatformhitid[] = array('id' => $hitid['id'], 
												'status' => $newstatus);
					
					// todo: IF each is disposed, newstatus = deleted.

					// Get Assignments.
					$jobId = $job->_id;
					$assignments = $turk->getAssignmentsForHIT($hitid['id']);
					print 'Got ' . count($assignments) . " Assignments for {$hitid['id']}\n";
					
					foreach ($assignments as $ass){
						$assignment = $ass->toArray();

						$annentities = Entity::where('job_id', $jobId)
										->where('platformAnnotationId', $assignment['AssignmentId'])
										->get();
						
						//print_r($annentities); die();
						if(count($annentities)>0) { 
							$annoldstatus = $annentities[0]['status'];
							$annnewstatus = $assignment['AssignmentStatus'];

							if($annoldstatus != $annnewstatus){
								foreach($annentities as $annentity){
									$annentity->status = $annnewstatus;
									$annentity->update();
									print "Status changed to $annnewstatus.";
								}

								Log::debug("Status of Annotation {$annentity->_id} changed from $annoldstatus to $annnewstatus");
							}
						} else { // ASSIGNMENT entity not in DB: create activity, entity and refer to or create agent.

							// Create or retrieve Agent
							$agentId = $this->createCrowdAgent('amt', $assignment);
							
							// Create activity: annotate
							$activity = new Activity;
							$activity->label = "Units are annotated on crowdsourcing platform.";
							$activity->crowdAgent_id = $agentId; 
							$activity->used = $jobId;
							$activity->softwareAgent_id = 'amt';
							$activity->save();

							
							// OPTIONAL: we could create an ASSIGNMENT entity to hold the metadata.
							$groupedbyid = array();
							foreach ($assignment['Answer'] as $q=>$ans){
								// Retrieve the unitID and the QuestionId from the name of the input field.
								$split = strrpos($q, "_");
								$unitid = substr($q, 0, $split); 	 // after the last _
								$qid = substr($q, $split+1);		// before the last _
								$groupedbyid[$unitid][$qid] = $ans;// grouped to create an entity for every ID.
							}
							
							// Create entity FOR EACH UNIT
							foreach($groupedbyid as $uid=>$qidansarray){
								$annentity = new Entity;
								$annentity->documentType = 'annotation';
								$annentity->domain = $job->domain;
								$annentity->format = $job->format;
								//$annentity->type = $job->type;
								$annentity->activity_id = $activity->_id;
								$annentity->crowdAgent_id = $agentId;
								$annentity->softwareAgent_id = 'amt';
								$annentity->job_id = $jobId;
								$annentity->unit_id = $uid;
								$annentity->platformAnnotationId = $assignment['AssignmentId'];
								$annentity->acceptTime = new MongoDate(strtotime($assignment['AcceptTime']));
								$annentity->submitTime = new MongoDate(strtotime($assignment['SubmitTime']));
								//
								// Todo: Optionally compute time spent doing the assignment here.
								//
								if(!empty($assignment['AutoApprovalTime']))
									$annentity->autoApprovalTime = new MongoDate(strtotime($assignment['AutoApprovalTime']));
								if(!empty($assignment['ApprovalTime']))
									$annentity->autoApprovalTime = new MongoDate(strtotime($assignment['ApprovalTime']));
								if(!empty($assignment['RejectionTime']))
									$annentity->autoApprovalTime = new MongoDate(strtotime($assignment['RejectionTime']));

								$annentity->content = $qidansarray;
								$annentity->status = $assignment['AssignmentStatus']; // Submitted | Approved | Rejected
								
								// QuestionDictionary
								$unit = Entity::where('_id', $uid)->first();
								$annentity->questionDictionary = $job->questionTemplate->getDictionary($unit, $qidansarray);
								$annentity->save();

								$newannotations[] = $annentity;

							}

							/*
								Possibly also:

								HITId				2P3Z6R70G5RC7PEQC857ZSST0J2P9T
								Deadline	
							*/
						}

						if(count($newannotations)>0)
							Log::debug("Got " . count($newannotations) . " new annotations for {$h['HITId']} - total " . count($assignments) . " annotations.");

					} // foreach assignment
				} // if / else				
			} // foreach hit

			$job->addResults($newannotations);
			$job->platformJobId = $newplatformhitid;
			$job->save();
		} // foreach JOB
	}		


	// todo: move?
	public function createCrowdAgent($platform, $data){

		$workerid = '';
		if($platform == 'amt') {
			$workerId = $data['WorkerId'];
		} else {
			throw new Exception("Unknown platform $platform");
			// CF is not needed here -> webhook.
		}	

		if($id = CrowdAgent::where('platformAgentId', $workerId)->where('softwareAgent_id', $platform)->pluck('_id')) 
			return $id;

		else {
			$agent = new CrowdAgent;
			$agent->_id= "crowdagent/$platform/$workerId";
			$agent->softwareAgent_id= $platform;
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
