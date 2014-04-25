<?php
namespace Cw\Mturk;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \MongoDB\Entity;
use \MongoDB\CrowdAgent;
use \MongoDB\Activity;
use \MongoDB\Agent;
use \Annotation;
use \Job;
use \MongoDate;
use \Config;
use \QuestionTemplate;
use \Log;
use \Queue;

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

		if 	(Config::get('mturk::rooturl') == '' or
			Config::get('mturk::accesskey')  == '' or
			Config::get('mturk::secretkey')  == '')
				throw new Exception('API key not set. Please check the configuration file.');

		$turk = new Turkapi\MechanicalTurk(Config::get('mturk::rooturl'), false, Config::get('mturk::accesskey'), Config::get('mturk::secretkey'));

		// Todo optimize query.
		$jobs = Job::where('softwareAgent_id', 'amt')
					->orderBy('created_at', 'desc')
					->where('status', '!=', 'finished')
					->get();

		foreach($jobs as $job){
			$newannotations = array();
			$newannotationscount = 0;
			$newplatformhitid = array();

			foreach($job->platformJobId as $hitid){
				set_time_limit (30);
				if($hitid['status'] == 'deleted') // Can't recover from that. Don't update.
					$newplatformhitid[] = array('id' => $hitid['id'], 
												'status' => 'deleted');
				else {

					$hit = $turk->getHIT($hitid['id']);
					$h = $hit->toArray();

					//Do this once:
					if(empty($job->Expiration)) $job->Expiration = new MongoDate(strtotime($h['Expiration']));
					if(empty($job->HITGroupId)) $job->HITGroupId = $h['HITGroupId'];
					if(empty($job->HITTypeId)) 	$job->HITTypeId  = $h['HITTypeId'];

					// Convert status to our language
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

						$annotation = Annotation::where('job_id', $jobId)
										->where('platformAnnotationId', $assignment['AssignmentId'])
										->first();
						
						//print_r($annotations); die();
						if($annotation) { 
							$annoldstatus = $annotation['status'];
							$annnewstatus = $assignment['AssignmentStatus'];

							if($annoldstatus != $annnewstatus){
								$annotation->status = $annnewstatus;
								Queue::push('SaveAnnotation', array('annotation' => $annotation));
								print "Status '$annoldstatus' changed to '$annnewstatus'.";
								Log::debug("Status of Annotation {$annotation->_id} changed from $annoldstatus to $annnewstatus");
							}
						} else { // ASSIGNMENT entity not in DB: create activity, entity and refer to or create agent.

							// Pre-create agentid.
							$agentId = "crowdagent/amt/{$assignment['WorkerId']}";
							
							// Create activity: annotate
							$activity = new Activity;
							$activity->label = "Units are annotated on crowdsourcing platform.";
							$activity->crowdAgent_id = $agentId; 
							$activity->used = $jobId;
							$activity->softwareAgent_id = 'amt';
							$activity->save();

							$groupedbyid = array();
							foreach ($assignment['Answer'] as $q=>$ans){
								// Retrieve the unitID and the QuestionId from the name of the input field.
								$split = strpos($q, "_");
								$unitid = substr($q, 0, $split); 	 // before the first _
								$qid = substr($q, $split+1);		// after the first _
								$groupedbyid[$unitid][$qid] = $ans;// grouped to create an entity for every ID.
							}
							
							// Create entity FOR EACH UNIT
							foreach($groupedbyid as $uid=>$qidansarray){
								$annotation = new Annotation;
								$annotation->activity_id = $activity->_id;
								$annotation->crowdAgent_id = $agentId;
								$annotation->softwareAgent_id = 'amt';
								$annotation->job_id = $jobId;
								$annotation->unit_id = $uid;
								$annotation->platformAnnotationId = $assignment['AssignmentId'];
								$annotation->acceptTime = new MongoDate(strtotime($assignment['AcceptTime']));
								$annotation->submitTime = new MongoDate(strtotime($assignment['SubmitTime']));
								//
								// Todo: Optionally compute time spent doing the assignment here.
								//
								if(!empty($assignment['AutoApprovalTime']))
									$annotation->autoApprovalTime = new MongoDate(strtotime($assignment['AutoApprovalTime']));
								if(!empty($assignment['ApprovalTime']))
									$annotation->autoApprovalTime = new MongoDate(strtotime($assignment['ApprovalTime']));
								if(!empty($assignment['RejectionTime']))
									$annotation->autoApprovalTime = new MongoDate(strtotime($assignment['RejectionTime']));

								$annotation->content = $qidansarray;
								$annotation->status = $assignment['AssignmentStatus']; // Submitted | Approved | Rejected
								
								Queue::push('Queues\SaveAnnotation', array('annotation' => serialize($annotation)));

								//$annotation->save();

								//$newannotations[] = $annotation;
								$newannotationscount++;

							}

							/*
								Possibly also:

								HITId				2P3Z6R70G5RC7PEQC857ZSST0J2P9T
								Deadline	
							*/

							if(!$agent = CrowdAgent::id($agentId)->first()){
								$agent = new CrowdAgent;
								$agent->_id= $agentId;
								$agent->softwareAgent_id= 'amt';
								$agent->platformAgentId = $assignment['WorkerId'];		
							}
							
							Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($agent)));


						}

						//if(count($newannotations)>0)
						if($newannotationscount>0)
							Log::debug("Got $newannotationscount new annotations for {$h['HITId']} - total " . count($assignments) . " assignments.");

					} // foreach assignment
				} // if / else				
			} // foreach hit

			//$job->addResults($newannotations);
			$job->platformJobId = $newplatformhitid; 
			Queue::push('Queues\UpdateJob', array('job' => serialize($job)));

			//$job->save();
		} // foreach JOB
	}		


	// todo: change to CrowdAgent::createNewCrowdAgent(...)
	public function createCrowdAgent($data){

		$workerId = $data['WorkerId'];

		if($id = CrowdAgent::where('platformAgentId', $workerId)->where('softwareAgent_id', 'amt')->pluck('_id')) 
			return $id;
		else {
			$agent = new CrowdAgent;
			$agent->_id= "crowdagent/amt/$workerId";
			$agent->softwareAgent_id= 'amt';
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
			array('hitid', null, InputOption::VALUE_OPTIONAL, 'AMT HIT ID.', null)
		);
	}

}
