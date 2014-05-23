<?php
/**
* The Assignment class mimics the Assignment datastructure from the AMT API.
* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_AssignmentDataStructureArticle.html
* @author Arne Rutjes for the Crowd Watson project.
* @license IPL
*/
	
namespace CrowdTruth\Mturk\Turkapi;
class Assignment {
								// all [...]Time and the Deadline fields are set to the Coordinated Universal Time (Greenwich Mean Time) time zone, such as 2005-01-31T23:59:59Z.	
	private $AssignmentId; 		// A unique identifier for the assignment
	private $WorkerId; 			//The ID of the Worker who accepted the HIT.
	private $HITId;				//The ID of the HIT
	private $AssignmentStatus;	//The status of the assignment. Submitted | Approved | Rejected
	private $AutoApprovalTime;	// If results have been submitted, AutoApprovalTime is the date and time the results of the assignment results are considered Approved automatically if they have not already been explicitly approved or rejected by the Requester.
	private $AcceptTime;		//The date and time the Worker accepted the assignment.
	private $SubmitTime;		//If the Worker has submitted results, SubmitTime is the date and time the assignment was submitted.
	private $ApprovalTime;		//If the Worker has submitted results and the Requester has approved the results, ApprovalTime is the date and time the Requester approved the results.
	private $RejectionTime;		//If the Worker has submitted results and the Requester has rejected the results, RejectionTime is the date and time the Requester rejected the results.
	private $Deadline;			// The date and time of the deadline for the assignment. This value is derived from the deadline specification for the HIT and the date and time the Worker accepted the HIT.
	private $Answer;			//The Worker's answers submitted for the HIT contained in a QuestionFormAnswers document, if the Worker provides an answer. If the Worker does not provide any answers, Answer may contain a QuestionFormAnswers document, or Answer may be empty.						
	private $RequesterFeedback; //The feedback string included with the call to the ApproveAssignment operation or the RejectAssignment operation.
								// Only returned with ResponseGroup AssignmentFeedback.

	public function __construct($assxml = false){
		if ($assxml) $this->getValuesFromXML($assxml);
	}						
	
	
	/**
	* Fills itself with information from the server.
	* @param SimpleXMLElement $assxml the Assignment-part of an XML response from the server.
	*/
	private function getValuesFromXML($assxml){
		foreach($assxml[0] as $key=>$value){
			if($key == 'Answer'){
				$this->Answer = $this->processAnswer(urldecode((string) $value));
			} else {
				if(isset($value)) $this->$key = (string) $value;
			}
		}	
	}
	
	
	/**
	* Convert QuestionFormAnswers (an AMT DataStructure) to an associative array(qid => answer).
	* This function is designed to be agnostic to the type of QuestionFormElement that is used (FreeText, Selection, whatever).
	* @param string $xmlstring the QuestionFormAnswers in XML form.
	* @return string[] Format: qid -> answer.
	* @link http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_QuestionFormAnswersDataStructureArticle.html
	*/
	private function processAnswer($xmlstring){

		$xml = simplexml_load_string($xmlstring);
		$ret = array();
		
		foreach ($xml as $ans) {
			foreach ($ans as $element=>$value){
				if ($element == "QuestionIdentifier") $qid = (string) $value;
				else { $val = (string) $value; break; } //the answer
			}
			$ret[$qid] = $val;
		}
		
		return $ret;
	}

	public function toArray(){
		return array(	'AssignmentId' => $this->AssignmentId,
						'WorkerId' => $this->WorkerId,
						'HITId' => $this->HITId,
						'AssignmentStatus' => $this->AssignmentStatus,
						'AutoApprovalTime' => $this->AutoApprovalTime,
						'AcceptTime' => $this->AcceptTime,
						'SubmitTime' => $this->SubmitTime,
						'ApprovalTime' => $this->ApprovalTime,
						'RejectionTime'  => $this->RejectionTime,
						'Deadline' => $this->Deadline,
						'Answer' => $this->Answer,						
						'RequesterFeedback' => $this->RequesterFeedback);
	}
	
	public function getAssignmentId(){
		return $this->AssignmentId;
	}

	public function getWorkerId(){
		return $this->WorkerId;
	}


	public function getHITId(){
		return $this->HITId;
	}

	public function getAssignmentStatus(){
		return $this->AssignmentStatus;
	}


	public function getAutoApprovalTime(){
		return $this->AutoApprovalTime;
	}


	public function getAcceptTime(){
		return $this->AcceptTime;
	}


	public function getSubmitTime(){
		return $this->SubmitTime;
	}

	public function getApprovalTime(){
		return $this->ApprovalTime;
	}


	public function getRejectionTime(){
		return $this->RejectionTime;
	}


	public function getDeadline(){
		return $this->Deadline;
	}


	public function getAnswer(){
		return $this->Answer;
	}

	
}
?>