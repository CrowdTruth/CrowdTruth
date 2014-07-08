<?php
/**
* The HIT class mimics the HIT datastructure from the AMT API.
* $Reward and $QualificationRequirement are arrays in the following format: $key->$value.
* $AssignmentReviewPolicy has it's own structure, see below.
* @author Arne Rutjes for the Crowd Watson project.
* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_HITDataStructureArticle.html
* @license IPL
*/

namespace CrowdTruth\Mturk\Turkapi;
class Hit {
	private $HITId; 								// A unique identifier for the HIT. The CreateHIT operation gives a HIT the HIT ID and the HIT retains that ID forever.
	private $HITLayoutId; 							// The ID of the HIT Layout of this HIT
	private $LayoutParameters;						// --- only used for HIT creation --- array(parameter => value).
	private $Title;	 								// The title of the HIT
	private $Description;							// A general description of the HIT
	private $Keywords; 								// One or more words or phrases that describe the HIT, separated by commas.
	private $Reward;				 				// = array('Amount' => 0.01, 'CurrencyCode' => 'USD'); The amount of money the Requester will pay a Worker for successfully completing the HIT.
	private $LifetimeInSeconds;			 			// The amount of time, in seconds, after which the HIT is no longer available for users to accept.
	private $AssignmentDurationInSeconds;			// The length of time, in seconds, that a Worker has to complete the HIT after accepting it.
	private $MaxAssignments; 						// The number of times the HIT can be accepted and completed before the HIT becomes unavailable.
	private $AutoApprovalDelayInSeconds; 			// The amount of time, in seconds after the Worker submits an assignment for the HIT that the results are automatically approved by the Requester.
	private $QualificationRequirement;	 			// Array(array(key=>value)); Conditions that a Worker's Qualifications must meet in order to accept the HIT. 
	private $Question; 								// The data the Worker completing the HIT uses produce the results.
	private $RequesterWorkerunit; 					// An arbitrary data field the Requester who created the HIT can use. This field is visible only to the creator of the HIT.
	private $AssignmentReviewPolicy;				// --- only used for HIT creation --- array('AnswerKey' 	=> array(questionid => answer),
													//											'Parameters' 	=> array(parameter => value). 
													// http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_AssignmentReviewPolicies.html
	
	// Fields that can not be set.
	private $HITTypeId; 							// The ID of the HIT type of this HIT
	private $NumberOfSimilarHITs; 					// The number of HITs with fields identical to this HIT, other than the Question field.
	private $HITReviewStatus; 						// NotReviewed | MarkedForReview | ReviewedAppropriate | ReviewedInappropriate
	private $HITStatus; 							// Assignable | Unassignable | Reviewable | Reviewing | Disposed
	private $HITGroupId; 							// The ID of the HIT Group of this HIT
	private $CreationTime; 							// The date and time the HIT was created
	private $Expiration; 							// The date and time the HIT expires. Type: a dateTime structure in the Coordinated Universal Time (Greenwich Mean Time) time zone, such as 2012-01-31T23:59:59Z.

	// Only filled if the HITAssignmentSummary 
	// response group is specified:
	private $NumberofAssignmentsPending; 			// The number of assignments for this HIT that are being previewed or have been accepted by Workers, but have not yet been submitted, returned, or abandoned.
	private $NumberofAssignmentsAvailable; 			// The number of assignments for this HIT that are available for Workers to accept
	private $NumberofAssignmentsCompleted; 			// The number of assignments for this HIT that have been approved or rejected.
	
	
	public function __construct($hitxml = false){
		if($hitxml) $this->getValuesFromXML($hitxml);
	}
	
	/**
	* Fills itself with information from the server.
	* $Reward and $QualificationRequirement are arrays in the following format: $key->$value.
	* $AssignmentReviewPolicy has an associative array 'AnswerKey' and one 'Parameters'. 
	* All other fields are converted to string.
	* @param SimpleXMLElement $hitxml the HIT-part of an XML response from the server.
	*/
	public function getValuesfromXML($hitxml){
		foreach($hitxml[0] as $key=>$value){
			
			if($key == 'Reward'){
				$this->Reward['Amount'] 		= (string) $hitxml[0]->$key->Amount;
				$this->Reward['CurrencyCode'] 	= (string) $hitxml[0]->$key->CurrencyCode;
				$this->Reward['FormattedPrice'] = (string) $hitxml[0]->$key->FormattedPrice;
				
			} else if($key == 'AssignmentReviewPolicy'){
				
				foreach ($hitxml[0]->$key->Parameter as $p){
					if($p->Key == 'AnswerKey')
						foreach ($p->MapEntry as $m)
							$this->AssignmentReviewPolicy['AnswerKey'] [(string) $m->Key] = (string) $m->Value;
					else    $this->AssignmentReviewPolicy['Parameters'][(string) $p->Key] = (string) $p->Value;
				}
				
			} else {
				//echo "$key => $value<br>";
				if(isset($value) and $key != '') $this->$key = (string) $value;
				
			}
			//echo "\r\n\r\n$key\r\n"; print_r($this); echo "\r\n\r\n";
		}
/*		dd($this);	
		die();*/
		$qualarray = array();
		foreach($hitxml['0']->QualificationRequirement as $q){
			$qbuilder = array();
			if(isset($q->QualificationTypeId)) 	$qbuilder['QualificationTypeId'] = (string) $q->QualificationTypeId;
			if(isset($q->Comparator)) 		   	$qbuilder['Comparator'] 		 = (string) $q->Comparator;
			if(isset($q->IntegerValue)) 		$qbuilder['IntegerValue'] 		 = (string) $q->IntegerValue;
			if(isset($q->RequiredToPreview))   	$qbuilder['RequiredToPreview']   = (string) $q->RequiredToPreview;		
			if(isset($q->LocaleValue->Country)) $qbuilder['LocaleValue'] 		 = (string) $q->LocaleValue->Country; // Currently 'country' is the only field of LocaleValue, so we cut a corner here.
			$qualarray[] = $qbuilder;
		}
		
		if(count($qualarray)>0) $this->QualificationRequirement = $qualarray;
		else 					$this->QualificationRequirement = null;
	}
	
	/**
	* Converts all the properties of the HIT that can be used for HIT creation to an array that can be sent as POST data to the API.
	* @return string[] All the POST key/value pairs related to HIT creation, ready to be sent to the API. 
	* @throws InvalidArgumentException
	*/	
	public function toPOSTdata() {
	
		// Check if the mandatory fields are filled out. (note: these would not be mandatory if you would be using a HITTypeId, but this functionality is not yet implemented).
		if($this->Reward['Amount'] == 0) throw new \InvalidArgumentException('Reward should be larger than 0.');
		
		if (!$this->Title || !$this->Description || !$this->Reward['Amount'] || !$this->Reward['CurrencyCode'] || !$this->LifetimeInSeconds || !$this->AssignmentDurationInSeconds)
			throw new \InvalidArgumentException("Fill out the mandatory fields: Title, Description, Reward['Amount'], Reward['CurrencyCode'], LifetimeInSeconds, AssignmentDurationInSeconds.");

		$data = array(
			'Title'							=> $this->Title,
			'Description'					=> $this->Description,
			'Reward.1.Amount'				=> $this->Reward['Amount'],
			'Reward.1.CurrencyCode'			=> $this->Reward['CurrencyCode'],
			'LifetimeInSeconds' 			=> $this->LifetimeInSeconds, 
			'AssignmentDurationInSeconds'	=> $this->AssignmentDurationInSeconds
			);
	
		// Add either a Question... 
		if 		(isset($this->Question)) 	$data['Question'] = $this->Question;
		
		// ...or a HITLayoutId and HITLayoutParameters...
		else if (isset($this->HITLayoutId) 	&& is_array($this->LayoutParameters)) {
			
			$data['HITLayoutId'] =  $this->HITLayoutId;
			
			$x = 1;
			while ($lp = current($this->LayoutParameters)) {
				$data["HITLayoutParameter.$x.Name"] = key($this->LayoutParameters);
				$data["HITLayoutParameter.$x.Value"] = $lp;
				$x++;
				next($this->LayoutParameters);
			}	
		} 
		// ...or throw an exception.
		else throw new InvalidArgumentException("Provide either a Question or a HITLayoutId and HITLayoutParameters.");
		
	
		// General optional fields
		if(isset($this->Keywords)) 					 	$data['Keywords'] 						= $this->Keywords;	
		if(isset($this->AutoApprovalDelayInSeconds))  	$data['AutoApprovalDelayInSeconds']		= $this->AutoApprovalDelayInSeconds;
		if(isset($this->MaxAssignments))			 	$data['MaxAssignments']					= $this->MaxAssignments;
		
		
		$x=1; // QualificationRequirements
		if(isset($this->QualificationRequirement)){
			foreach($this->QualificationRequirement as $val){		
				if(isset($val['QualificationTypeId'])) 	$data["QualificationRequirement.$x.QualificationTypeId"]	= $val['QualificationTypeId'];
				if(isset($val['Comparator'])) 			$data["QualificationRequirement.$x.Comparator"]				= $val['Comparator'];
				if(isset($val['IntegerValue']))			$data["QualificationRequirement.$x.IntegerValue"]			= $val['IntegerValue'];
				if(isset($val['RequiredToPreview']))	$data["QualificationRequirement.$x.RequiredToPreview"]		= $val['RequiredToPreview'];
				if(isset($val['LocaleValue'])) 			$data["QualificationRequirement.$x.LocaleValue.Country"]	= $val['LocaleValue'];
				$x++;	
			}
		}
		
		$x = 1; // AssignmentReviewPolicy
		if(isset($this->AssignmentReviewPolicy['AnswerKey'])){
			
			$data['AssignmentReviewPolicy.1.PolicyName'] = 'ScoreMyKnownAnswers/2011-09-01';
			$data['AssignmentReviewPolicy.1.Parameter.1.Key'] = 'AnswerKey';
			
			$arp = $this->AssignmentReviewPolicy;
			
			while ($ak = current($arp['AnswerKey'])) {
				$data["AssignmentReviewPolicy.1.Parameter.1.MapEntry.$x.Key"] = key($arp['AnswerKey']);
				$data["AssignmentReviewPolicy.1.Parameter.1.MapEntry.$x.Value"] = $ak;
				$x++;
				next($arp['AnswerKey']);
			}
		
			if(isset($arp['Parameters'])){
				$pcnt = 2;
				while ($p = current($arp['Parameters'])) {
					$data["AssignmentReviewPolicy.1.Parameter.$pcnt.Key"] = key($arp['Parameters']);
					$data["AssignmentReviewPolicy.1.Parameter.$pcnt.Value"] = $p;
					$pcnt++;
					next($arp['Parameters']);
				}
			}	
		}

		return $data;
	}
	
	public function toArray(){
		return array(	'HITId' => $this->HITId, 						
						'HITLayoutId' => $this->HITLayoutId,
						'LayoutParameters' => $this->LayoutParameters,				
						'Title' => $this->Title,
						'Description' => $this->Description,
						'Keywords' => $this->Keywords, 	
						'Reward' => $this->Reward,				 	
						'LifetimeInSeconds' => $this->LifetimeInSeconds,		
						'AssignmentDurationInSeconds' => $this->AssignmentDurationInSeconds,
						'MaxAssignments' => $this->MaxAssignments, 
						'AutoApprovalDelayInSeconds' => $this->AutoApprovalDelayInSeconds,
						'QualificationRequirement' => $this->QualificationRequirement,
						'Question' => $this->Question, 
						'RequesterWorkerunit' => $this->RequesterWorkerunit,
						'AssignmentReviewPolicy' => $this->AssignmentReviewPolicy,
						
						// Fields that can not be set.
						'HITTypeId' => $this->HITTypeId, 							
						'NumberOfSimilarHITs' => $this->NumberOfSimilarHITs, 
						'HITReviewStatus' => $this->HITReviewStatus, 				
						'HITStatus' => $this->HITStatus, 				
						'HITGroupId' => $this->HITGroupId, 	
						'CreationTime' => $this->CreationTime, 
						'Expiration' => $this->Expiration, 							

						// Only filled if the HITAssignmentSummary 
						// response group is specified:
						'NumberofAssignmentsPending' => $this->NumberofAssignmentsPending, 
						'NumberofAssignmentsAvailable' => $this->NumberofAssignmentsAvailable,
						'NumberofAssignmentsCompleted' => $this->NumberofAssignmentsCompleted);	
	}
	
	public function getHITId(){
		return $this->HITId;
	}

	public function getHITTypeId(){
		return $this->HITTypeId;
	}

	public function setHITTypeId($HITTypeId){
		$this->HITTypeId = $HITTypeId;
	}

	public function getHITGroupId(){
		return $this->HITGroupId;
	} 
	
	public function getHITLayoutId(){
		return $this->HITLayoutId;
	}

	public function setHITLayoutId($HITLayoutId){
		$this->HITLayoutId = $HITLayoutId;
	}

	public function setLayoutParameters($LayoutParameters){
		$this->LayoutParameters = $LayoutParameters;
	}
	
	public function setAssignmentReviewPolicy($AssignmentReviewPolicy){
		$this->AssignmentReviewPolicy = $AssignmentReviewPolicy;
	}
	
	public function getCreationTime(){
		return $this->CreationTime;
	}

	public function getTitle(){
		return $this->Title;
	}

	public function setTitle($Title){
		$this->Title = $Title;
	}

	public function getDescription(){
		return $this->Description;
	}

	public function setDescription($Description){
		$this->Description = $Description;
	}

	public function getKeywords(){
		return $this->Keywords;
	}

	public function setKeywords($Keywords){
		$this->Keywords = $Keywords;
	}

	public function getHITStatus(){
		return $this->HITStatus;
	}

	public function getReward(){
		return $this->Reward;
	}

	public function setReward($Reward){
		$this->Reward = $Reward;
	}

	public function getLifetimeInSeconds(){
		return $this->LifetimeInSeconds;
	}

	public function setLifetimeInSeconds($LifetimeInSeconds){
		$this->LifetimeInSeconds = $LifetimeInSeconds;
	}

	public function getAssignmentDurationInSeconds(){
		return $this->AssignmentDurationInSeconds;
	}

	public function setAssignmentDurationInSeconds($AssignmentDurationInSeconds){
		$this->AssignmentDurationInSeconds = $AssignmentDurationInSeconds;
	}

	public function getMaxAssignments(){
		return $this->MaxAssignments;
	}

	public function setMaxAssignments($MaxAssignments){
		$this->MaxAssignments = $MaxAssignments;
	}

	public function getAutoApprovalDelayInSeconds(){
		return $this->AutoApprovalDelayInSeconds;
	}

	public function setAutoApprovalDelayInSeconds($AutoApprovalDelayInSeconds){
		$this->AutoApprovalDelayInSeconds = $AutoApprovalDelayInSeconds;
	}

	public function getExpiration(){
		return $this->Expiration;
	}

	public function getQualificationRequirement(){
		return $this->QualificationRequirement;
	}

	public function setQualificationRequirement($QualificationRequirement){
		$this->QualificationRequirement = $QualificationRequirement;
	}

	public function getQuestion(){
		return $this->Question;
	}

	public function setQuestion($Question){
		$this->Question = $Question;
	}

	public function getRequesterWorkerunit(){
		return $this->RequesterWorkerunit;
	}

	public function setRequesterWorkerunit($RequesterWorkerunit){
		$this->RequesterWorkerunit = $RequesterWorkerunit;
	}

	public function getNumberOfSimilarHITs(){
		return $this->NumberOfSimilarHITs;
	}


	public function getHITReviewStatus(){
		return $this->HITReviewStatus;
	}


	public function getNumberofAssignmentsPending(){
		return $this->NumberofAssignmentsPending;
	}

	public function getNumberofAssignmentsAvailable(){
		return $this->NumberofAssignmentsAvailable;
	}


	public function getNumberofAssignmentsCompleted(){
		return $this->NumberofAssignmentsCompleted;
	}
	
	public function getAssignmentReviewPolicy(){
		return $this->AssignmentReviewPolicy;
	}
	
}
?>