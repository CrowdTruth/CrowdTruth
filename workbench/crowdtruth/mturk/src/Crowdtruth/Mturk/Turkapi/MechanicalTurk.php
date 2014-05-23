<?php
/**
* PHP Library for communicating with Amazon Mechanical Turk's REST API.
*
* In most cases, the structures from the API are followed. In some cases it was better to change them.
* See Hit.class.php and Assignment.class.php to find out more. Specify your API credentials and settings in config.php.
*
* @author Arne Rutjes for the Crowd Watson project. Inspired by Jack Weeden's PHP wrapper.
* @license IPL
*/


namespace CrowdTruth\Mturk\Turkapi;
require_once(dirname(__FILE__) . '/Hit.class.php');
require_once(dirname(__FILE__) . '/Assignment.class.php');
require_once(dirname(__FILE__) . '/AMTException.class.php');
//include_once(dirname(__FILE__) . '/config.php');

class MechanicalTurk {
	
	protected $accesskey;
	protected $secretkey;
	protected $debug;
	protected $rootURL;

	public function __construct($rootURL = null, $debug = null, $accesskey = null, $secretkey = null){
		if(isset($rootURL)) 	$this->rootURL 	 = $rootURL;
		//else 					$this->rootURL   = AMT_ROOT_URL;
		if(isset($debug)) 		$this->debug 	 = $debug;
		//else 					$this->debug 	 = DEBUG;
		if(isset($accesskey)) 	$this->accesskey = $accesskey;
		//else 					$this->accesskey = AWS_ACCESS_KEY;	
		if(isset($secretkey)) 	$this->secretkey = $secretkey;
		//else 					$this->secretkey = AWS_SECRET_KEY;
	}

	/**
	*	Change the root URL, for switching between sandbox and production.
	*/
	public function setRootURL($rootURL){
		$this->rootURL = $rootURL;
	}

	/**
	* Point this to your own logger.
	* @param string $message
	*/
	private function log($message){
		if($this->debug) \Log::debug($message);
	}
	
	
	/**
	* Upload a HIT to the AMT server. It will be available for workers immediately.
	* @param Hit $hit	
	* @return Array(HITId, HITTypeId).
	* @throws AMTException when the arguments are invalid. Also when the server can not be contacted or the 
	* request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_CreateHITOperation.html
	*/
	public function createHIT($hit) {
		try {
			$data = $hit->toPOSTdata();
			$xml = $this->getAPIResponse('CreateHIT', $data);
			$id = (string) $xml->HIT->HITId;
			$hitTypeid = (string) $xml->HIT->HITTypeId;	
			// The only values we get back from the server are the HITId and the HITTypeId.

			$this->log("Created HIT $id.");
			return array('HITId' => $id, 'HITTypeId' => $hitTypeid);
		} catch (\InvalidArgumentException $e){
			throw new AMTException('Invalid Argument: ' . $e->getMessage(), $e->getCode(), $e);
		}
	
	}

	
	/**
	* Poll the server for your HITs that have been submitted by workers.
	* @param int $pagesize
	* @param int $pagenumber
	* @return Array of HITs.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetReviewableHITsOperation.html
	*/	
	public function getReviewableHITs($pagesize = 50, $pagenumber = 1) {
		$data = array(	'PageSize' 		=> $pagesize, 
						'PageNumber'    => $pagenumber );
		$xml = $this->getAPIResponse('GetReviewableHITs', $data);
		
		$ret = array();
		$hits = $xml->xpath('/GetReviewableHITsResponse/GetReviewableHITsResult/HIT');
		
		foreach ($hits as $hitxml)
			$ret[] = new Hit($hitxml);//(string) $hitxml->HITId;
		
		$this->log("Retrieved " . count($ret) . " reviewable HITs" );
		return $ret;
	}
	
	
	/**
	* @alias searchHITs()
	*/
	public function getAllHITs( $pagesize = 50, $pagenumber = 1, $sortproperty = null, $sortdirection = null){
		return $this->searchHITs($pagesize, $pagenumber, $sortproperty, $sortdirection);
	}

	
	/**
	* Poll the server for ALL your HITs (no queries possible). The name is misleading, I apologize on behalf of the AMT team ;). 
	* @param int $pagesize
	* @param int $pagenumber
	* @param string $sortproperty Title | Reward | Expiration | CreationTime | Enumeration
	* @param string $sortdirection  Ascending | Descending
	* @param string $responsegroup Request, Minimal, HITDetail, HITQuestion, HITAssignmentSummary
	* @return Array of HIT's.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_SearchHITsOperation.html
	*/	
	public function searchHITs($pagesize = 50, $pagenumber = 1, $sortproperty = null, $sortdirection = null, $responsegroup = null) {
		$data = array(	'PageSize' 		=> $pagesize, 
						'PageNumber'    => $pagenumber );

		if(isset($sortproperty)) $data['SortProperty'] = $sortproperty;
		if(isset($sortdirection)) $data['SortDirection'] = $sortdirection;
		if(isset($responsegroup)) $data['ResponseGroup'] = $responsegroup;

		$xml = $this->getAPIResponse('SearchHITs', $data);
	
		$ret = array();
		$hits = $xml->xpath('SearchHITsResult/HIT');
		foreach ($hits as $hitxml)
			$ret[] = new Hit($hitxml);//(string) $hitxml->HITId;
		
		$this->log("Retrieved " . count($ret) . " hits." );
		return $ret;
	}
	
	
	/**
	* Get all the information about a HIT from the API.
	* @param string $hit_id
	* @param boolean $withQuestion Do we want to include the question as well? (has no effect on bandwidth usage, but makes the object less bulky).
	* @param string $responsegroup (Request | Minimal | AssignmentFeedback) Determine how much information we want.
	* @return Hit
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetHITOperation.html
	*/	
	public function getHIT($hit_id, $withQuestion = true, $responsegroup = null) {
			
		$data = array('HITId' => $hit_id);

		$xml = $this->getAPIResponse('GetHIT', $data, $responsegroup);
		$hitxml = $xml->xpath('/GetHITResponse/HIT');
		$hit = new Hit($hitxml);
		if(!$withQuestion) $hit->setQuestion(null);
		
		$this->log("Retrieved $hit_id from server.");
		return $hit;
	}

	
	/**
	* The ForceExpireHIT operation causes a HIT to expire immediately, as if the LifetimeInSeconds parameter of the HIT had elapsed.
	* @param string $hit_id
	* @return true
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_ForceExpireHITOperation.html
	*/
	public function forceExpireHIT($hit_id) {
		$data = array('HITId' => $hit_id);
		$this->getAPIResponse('ForceExpireHIT', $data);
		$this->log("Force expired HIT $hit_id.");
	}
	
	
	/**
	* The DisableHIT operation removes a HIT from the Amazon Mechanical Turk marketplace, approves any submitted assignments pending approval or rejection, 
	* and disposes of the HIT and all assignment data. Assignment results data cannot be retrieved for a HIT that has been disposed.
	* @param string $hit_id
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_DisableHITOperation.html
	*/
	public function disableHIT($hit_id) {	
		$data = array('HITId' => $hit_id);
		$this->getAPIResponse('DisableHIT', $data);
		$this->log("Disabled HIT $hit_id.");
	}
	
	
	/**
	* Remove a HIT from Mechanical Turk.
	* Note: You can only dispose HITs in the Reviewable state, with all submitted assignments approved or rejected.
	* @param string $hit_id
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_DisposeHITOperation.html
	*/	
	public function disposeHIT($hit_id) {
		$data = array('HITId' => $hit_id);
		$this->getAPIResponse('DisposeHIT', $data);
		$this->log("Removed HIT $hit_id.");
	}
	
	
	/**
	* Get the results for a given HIT. as an array of Assignment objects (if $getObject == true) or just the ID's (if $getObject == false).
	* @param int $pagesize
	* @param int $pagenumber
	* @param string $responsegroup (Request | Minimal | AssignmentFeedback) Determine how much information we want.
	* @return Assignment[] or string[] 
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetAssignmentsForHITOperation.html
	*/	
	public function getAssignmentsForHIT($hit_id, $pagesize = 50, $pagenumber = 1, $responsegroup = null) {

		$data = array(	'HITId' 	=> $hit_id, 
						'PageSize' 	=> $pagesize, 
						'PageNumber'=> $pagenumber );
		$xml = $this->getAPIResponse('GetAssignmentsForHIT', $data, $responsegroup);
	
		$ret = array();
		$assignments = $xml->xpath('/GetAssignmentsForHITResponse/GetAssignmentsForHITResult/Assignment');
		
		foreach ($assignments as $assxml)
			$ret[] = new Assignment($assxml);
			//$ret[] = (string) $xml->GetAssignmentsForHITResult->Assignment->AssignmentId;
		
		$this->log("Retrieved " . count($ret) . " assignments for hit $hit_id." );
		return $ret;
	}
	
	
	/**
	* Get a single assignment.
	* @param string $assignment_id
	* @param string $responsegroup (Request | Minimal | AssignmentFeedback) Determine how much information we want. 
	* @return Assignment.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetAssignmentOperation.html
	*/	
	public function getAssignment($assignment_id, $responsegroup = null) {
		$data = array('AssignmentId' => $assignment_id);
		$xml = $this->getAPIResponse('GetAssignment', $data, $responsegroup);	
		$assxml = $xml->xpath('GetAssignmentResult/Assignment');
		$this->log("Retrieved $assignment_id from server.");
		return new Assignment($assxml);
	}

	
	/**
	* Approves an Assignment. This will pay the worker who carried out the HIT and pay AWS their overhead fee.
	* @param string $assignment_id
	* @param string $requesterfeedback Optional. Visible for the worker.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_ApproveAssignmentOperation.html
	*/	
	public function approveAssignment($assignment_id, $requesterfeedback = null) {
		$data = array('AssignmentId' => $assignment_id);	
		if(isset($requesterfeedback)) $data['RequesterFeedback'] = $requesterfeedback;
		
		$this->getAPIResponse('ApproveAssignment', $data);
		$this->log("Approved assignment $assignment_id.");
	}
	
	
	/**
	* Rejects the results of a completed Assignment.
	* @param string $assignment_id
	* @param string $requesterfeedback Optional. Visible for the worker.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_RejectAssignmentOperation.html
	*/	
	public function rejectAssignment($assignment_id, $requesterfeedback = null) {
	
		$data = array(	'AssignmentId' => $assignment_id);
		
		if(isset($requesterfeedback)) $data['RequesterFeedback'] = $requesterfeedback;

		$this->getAPIResponse('RejectAssignment', $data);
		$this->log("Rejected assignment $assignment_id.");
	}
	
	
	/**
	* Retrieve a list of all blocked workers.
	* @param int $pagesize
	* @param int $pagenumber
	* @return array(WorkerId => Reason)
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetBlockedWorkersOperation.html
	*/	
	public function getBlockedWorkers($pagesize = 50, $pagenumber = 1) {
		$data = array(	'PageSize' 	 => $pagesize,
						'PageNumber' => $pagenumber );
						
		$xml = $this->getAPIResponse('GetBlockedWorkers', $data);
		$ret = array();
		$workers = $xml->xpath('/GetBlockedWorkersResult/WorkerBlock');

		foreach ($workers as $w) {
			$ret[(string) $w->WorkerId] = (string) $w->Reason;
		}
		
		$this->log("Retrieved " . count($ret) . " blocked workers.");
		return $ret;
	}

	
	/**
	* Get the statistics of what the worker did for us.
	* @param string $worker_id
	* @param string $statistic (NumberAssignmentsApproved | NumberAssignmentsRejected | PercentAssignmentsApproved | PercentAssignmentsRejected | 
	*							NumberKnownAnswersCorrect | NumberKnownAnswersIncorrect | NumberKnownAnswersEvaluated | PercentKnownAnswersCorrect | 
	*							NumberPluralityAnswersCorrect | NumberPluralityAnswersIncorrect | NumberPluralityAnswersEvaluated | PercentPluralityAnswersCorrect)
	* @param string $timeperiod (OneDay | SevenDays | ThirtyDays | LifeToDate)
	* @param int $count Only when $timeperiod = OneDay. Retrieve multiple datapoints.
	* @return string (or array(array(datetime -> value)) if count > 1).
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())	
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetRequesterWorkerStatisticOperation.html
	*/	
	public function getRequesterWorkerStatistic($worker_id, $statistic = 'PercentAssignmentsApproved', $timeperiod = 'LifeToDate', $count = 1) {
		
		$data = array(	'WorkerId'  	=> $worker_id,
						'Statistic' 	=> $statistic,
						'TimePeriod' 	=> $timeperiod );
		
		if($count > 1) {
			if($timeperiod != 'OneDay')
				throw new AMTException("Count can only be set if the timeperiod is OneDay");
			else
				$data['Count'] = $count;
		}
		
		$xml = $this->getAPIResponse('GetRequesterWorkerStatistic', $data);	
		$statxml = $xml->xpath('GetStatisticResult/DataPoint');
		$this->log("Retrieved RequesterWorkerStatistic ($statistic) for $worker_id");

		if($count > 1)	{
			$ret = array();
			foreach ($statxml as $x){
				foreach ($x as $element=>$value){
					if ($element == 'Date') $date = (string) $value;
					else $ret[$date] = (string) $value;
				}
			}
			return $ret;										// return array.
		} else {				
			foreach ($statxml[0] as $element=>$value)
				if ($element != 'Date') return (string) $value; // return just one value
		}
		
		// This should not be reached.
		throw new AMTException("Invalid response.");
	}
	
	
	/**
	* Block a worker.
	* @param string $worker_id
	* @param string $reason The worker does not see this message.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_BlockWorkerOperation.html
	*/	
	public function blockWorker($worker_id, $reason) {
		$data = array(	'WorkerId' => $worker_id,
						'Reason' => $reason );

		$xml = $this->getAPIResponse('BlockWorker', $data);
		$this->log("Blocked worker $worker_id (Reason: $reason)");
	}
	
	
	/**
	* Unblock a worker.
	* @param string $worker_id
	* @param string $reason The worker does not see this message.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_UnblockWorkerOperation.html
	*/	
	public function unblockWorker($worker_id, $reason) {
		
		$data = array(	'WorkerId' => $worker_id,
						'Reason' => $reason );

		$xml = $this->getAPIResponse('UnblockWorker', $data);
		$this->log("Unblocked worker $worker_id (Reason: $reason)");
	}
	

	/**
	* Pay money from our account to a worker.
	* @param string $worker_id
	* @param string $assignment_id
	* @param string $reason The worker can see this message.
	* @param double $amount The amount in dollars we pay de workers.
	* @param string $currencycode.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_BlockWorkerOperation.html
	*/	
	public function grantBonus($worker_id, $assignment_id, $reason, $amount, $currencyCode = 'USD') {
		$data = array(	'WorkerId' => $worker_id,
						'AssignmentId' => $assignment_id,
						'Reason' => $reason,
						'Reward.1.Amount' => $amount,
						'Reward.1.CurrencyCode' => $currencyCode,
		);

		$xml = $this->getAPIResponse('GrantBonus', $data);
		$this->log("Granted $amount $currencyCode to $worker_id (Reason: $reason)");
	}

	
	/**
	* Send an e-mail message to a maximum of 100 workers.
	* @param string $subject
	* @param string $body
	* @param string[] or string $worker_id
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_BlockWorkerOperation.html
	*/	
	public function notifyWorkers($subject, $messageText, $worker_id) {
		$data = array(	'Subject' => $subject,
						'MessageText' => $messageText,
		);

		$count = 1;
		if(is_array($worker_id)){
			foreach($worker_id as $wid){
				$data["WorkerId.$count"] = $wid;
				$count++;
			}
		} else {
			$data["WorkerId"] = $worker_id;
		}
		
		$xml = $this->getAPIResponse('NotifyWorkers', $data);
		$this->log("Sent message with subject $subject to $count worker(s).");
	}


	/**
	* Get statistics for the Requester.
	* @param string $statistic ( NumberAssignmentsAvailable | NumberAssignmentsAccepted | NumberAssignmentsPending | NumberAssignmentsApproved | NumberAssignmentsRejected | 
	* 							NumberAssignmentsReturned | NumberAssignmentsAbandoned	| PercentAssignmentsApproved | PercentAssignmentsRejected | TotalRewardPayout | 
	* 							AverageRewardAmount | TotalRewardFeePayout | TotalBonusPayout | TotalBonusFeePayout | NumberHITsCreated | NumberHITsCompleted | 
	* 							NumberHITsAssignable | NumberHITsReviewable | EstimatedRewardLiability | EstimatedFeeLiability | EstimatedTotalLiability )
	* @param string TimePeriod (OneDay | SevenDays | ThirtyDays | LifeToDate)
	* @param int $count Only when $timeperiod = OneDay. Retrieve multiple datapoints.
	* @return string (or array(array(datetime -> value)) if count > 1).	
	* @return string
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())	
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetRequesterStatistic.html
	*/	
	public function getRequesterStatistic($statistic = 'NumberAssignmentsAvailable', $timeperiod = 'LifeToDate', $count = 1) {
		
		$data = array(	'Statistic' 	=> $statistic,
						'TimePeriod' 	=> $timeperiod );
		
		if($count > 1) {
			if($timeperiod != 'OneDay')
				throw new AMTException("Count can only be set if the timeperiod is OneDay");
			else
				$data['Count'] = $count;
		}
		
		$xml = $this->getAPIResponse('GetRequesterStatistic', $data);	
		$statxml = $xml->xpath('GetStatisticResult/DataPoint');
		$this->log("Retrieved $statistic.");
	
		if($count > 1)	{
			$ret = array();
			foreach ($statxml as $x){
				foreach ($x as $element=>$value){
					if ($element == 'Date') $date = (string) $value;
					else $ret[$date] = (string) $value;
				}
			}
			return $ret;										// return array.
		} else {				
			foreach ($statxml[0] as $element=>$value)
				if ($element != 'Date') return (string) $value; // return just one value
		}

		// This should not be reached.
		throw new AMTException("Invalid response.");

	}
	

	/**
	* Get the current account balance
	* @param boolean nice Passing 'true' returns a nicely formatted amount (e.g. $1.50), otherwise only the value is returned.
	* @return string
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())	
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_GetAccountBalanceOperation.html
	*/	
	public function getAccountBalance($nice = true) {
		$xml = $this->getAPIResponse('GetAccountBalance');
		if ($nice) 	return (string) $xml->GetAccountBalanceResult->AvailableBalance->FormattedPrice;
		else 		return (string) $xml->GetAccountBalanceResult->AvailableBalance->Amount;
	}
	
	
	/**
	* Set a notification for a given $hitTypeid. An email will be sent to $emailaddress every time $eventtype happens.
	* @param string $hitTypeId
	* @param string $emailaddress
	* @param string $eventType: AssignmentAccepted | AssignmentAbandoned | AssignmentReturned | AssignmentSubmitted | HITReviewable | HITExpired.
	* Note: After you make the call to SetHITTypeNotification, it can take up to five minutes for changes to a HIT type's notification specification to take effect.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_SetHITTypeNotificationOperation.html
	*/
	public function setHITTypeNotification($hitTypeId, $emailaddress, $eventType){
	
		$data = array(	'HITTypeId' 				 => $hitTypeId,
						'Notification.1.Version' 	 => '2006-05-05', //still the newest version (January 2014)
						'Notification.1.Transport' 	 => 'Email',
						'Notification.1.Destination' => $emailaddress,
						'Notification.1.EventType' 	 => $eventType
						);
		
		$this->getAPIResponse('SetHITTypeNotification', $data);
		$this->log("Notification set: e-mail will be sent to $emailaddress when $eventType on HITType: $hitTypeId.");

	}
	
	
	/**
	* Send a testnotification.
	* @param string $emailaddress
	* @param string $eventType: AssignmentAccepted | AssignmentAbandoned | AssignmentReturned | AssignmentSubmitted | HITReviewable | HITExpired.
	* @param boolean $test Set to true if you just want to send a test notification.
	* Note: After you make the call to SetHITTypeNotification, it can take up to five minutes for changes to a HIT type's notification specification to take effect.
	* @throws AMTException when the server can not be contacted or the request or response isn't in the right format. (bubbles up from getAPIResponse())
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_SendTestEventNotificationOperation.html
	*/
	public function SendTestEventNotification($emailaddress, $eventType){
	
		$data = array(	'TestEventType' 			 => $eventType,
						'Notification.1.Version' 	 => '2006-05-05', //still the newest version (december 2013)
						'Notification.1.Transport' 	 => 'Email',
						'Notification.1.Destination' => $emailaddress,
						'Notification.1.EventType' 	 => $eventType
						);

		$this->getAPIResponse('SendTestEventNotification', $data);
		$this->log("Sent test notification ($eventType) to $emailaddress.");

	}
	
	/**
	* Set the notification of a given $hitTypeId to active or inactive.
	* @param string $hitTypeId
	* @param boolean $active Specifies whether notifications are sent for HITs of this HIT type.
	* Note: After you make the call to SetHITTypeNotification, it can take up to five minutes for changes to a HIT type's notification specification to take effect.
	* In the API, this operation is part of setHITTypeNotification.
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_SetHITTypeNotificationOperation.html
	*/
	public function setIfActiveHITTypeNotification($hitTypeId, $active){
		$active ? $activestr = 'True' : $activestr = 'False';
		
		$data = array(	'HITTypeId' => $hitTypeId,
						'Active'	=> $activestr );
	
		$this->getAPIResponse('SetHITTypeNotification', $data);
		$this->log("Notification on HITType: $hitTypeId set to $activestr.");
	}
	
	
	/**
	* Make the API call. 
	* @param string $operation The name of the Operation in the AMT API
	* @param string[] $data An array of POSTdata specific to the operation that wil be sent to the (RESTful) API.
	* @param string $responsegroup Sometimes you can set how much detail you want the server to return. See the APIdocs for more information.
	* @return SimpleXMLElement the XML returned by the server, loaded into SimpleXML.
	* @throws AMTException when the AMT server cannot be contacted or the request isn't valid.
	* @throws UnexpectedValueException when AMT's response is not XML.
	*/	
	private function getAPIResponse($operation, $data = array(), $responsegroup = null){
		// Add the common parameters
		$data['Operation']		= $operation;	
		$data['AWSAccessKeyId']	= $this->accesskey;
		$data['Signature'] 		= $this->generateSignature("AWSMechanicalTurkRequester", $operation, time(), false);
		$data['Timestamp']		= time();
		
		if(isset($responsegroup)) $data['ResponseGroup'] = $responsegroup; 
		 
		$options = array(
				'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
				'ignore_errors' => true  // We let the errors pass to get AMT's more specific messages.
			),
		);
		
		$context  = stream_context_create($options);
		// Get the response and load it into SimpleXML
		@$response = file_get_contents($this->rootURL, false, $context);
		if(!$response) throw new AMTException('Could not contact AMT server.');
		@$xml = simplexml_load_string($response);
		if(!$xml) throw new UnexpectedValueException('Invalid response from AMT server.');
		
		// The XML structure is different for a few operations.
		if (($operation == 'CreateHIT') || ($operation == 'GetHIT')) $path = 'HIT'; 
		else if ($operation == 'GetRequesterWorkerStatistic' || $operation == 'GetRequesterStatistic') $path = 'GetStatisticResult';
		else $path = $operation.'Result';
		
		// Check if AMT says the request is valid.
		if($xml->$path->Request->IsValid == 'False')
			throw new AMTException($xml->$path->Request->Errors->Error->Message);
		
		return $xml;	
	}
	
	
	/**
	* Generates the signature AWS needs for authenticating requests
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMechanicalTurkRequester/MakingRequests_RequestAuthenticationArticle.html
	*/
	private function generateSignature($service, $operation, $timestamp, $urlencode=true) {
		// Generate the signed HMAC signature AWS APIs 
		$hmac = $this->hasher($service.$operation.$timestamp);
		$hmac_b64 = $this->base64($hmac);
		if($urlencode) {
			return urlencode($hmac_b64);;
		} else	{
			return $hmac_b64;
		}	
	}
	
	
	/**
	* Returns the HMAC for generating the signature
	* Algorithm adapted (stolen) from http://pear.php.net/package/Crypt_HMAC/ (via http://code.google.com/p/php-aws/)
	*/
	private function hasher($data) {
		$key = $this->secretkey;
		if(strlen($key) > 64)
			$key = pack('H40', sha1($key));
		if(strlen($key) < 64)
			$key = str_pad($key, 64, chr(0));
		$ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
		$opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));
		return sha1($opad . pack('H40', sha1($ipad . $data)));
	}
       
	 
	private function base64($str) {
		$ret = '';
		for($i = 0; $i < strlen($str); $i += 2)
			$ret .= chr(hexdec(substr($str, $i, 2)));
		return base64_encode($ret);
	}
	
} ?>
