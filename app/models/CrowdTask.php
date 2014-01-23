<?php

//use Jenssegers\Mongodb\Model as Eloquent;
use crowdwatson\Hit;

class CrowdTask extends Moloquent {
	//protected $fillable = array('title', 'description', 'keywords', 'template', 'reward', 'maxAssignments', 'assignmentDur');
    protected $fillable = array('title', 'description', 'keywords', 'template', 'reward', 'maxAssignments', 'assignmentDur', 
    	'autoApprovalDelayInSeconds', 'qualificationRequirement', 'requesterAnnotation' ,'assignmentReviewPolicy', 'answerfield',
    	'lifetimeInSeconds', 'tasksPerAssignment', 'csv');

    public static $rules = array(
	  'title' => 'required',
	  'description' => 'required',
	  'reward' => 'required|numeric',
	  'maxAssignments' => 'required|numeric'
	);

	public function addQualReq($qr){
		$qarray = array();
		foreach($qr as $key=>$val){
			if(array_key_exists('checked', $val)){
				$qbuilder = array();
				$qbuilder['QualificationTypeId'] 	= $key;
				$qbuilder['Comparator'] 			= $val['comparator'];
				if	($key=="00000000000000000071")  
					$qbuilder['LocaleValue'] 		= $val['value'];
				else							
					$qbuilder['IntegerValue'] 		= $val['value'];
		
				$qarray[]=$qbuilder;
			}
		}
		if(count($qarray)>0)
			$this->qualificationRequirement = $qarray;
		else $this->qualificationRequirement = null;
	}

	public function addAssRevPol($answerkey, $arp){
		// Answerkey is not mandatory, because we can use a CSV column.
		$arpanswerkey = array();
		if(count($answerkey)>0) {
			foreach ($answerkey as $key=>$val)
				if($val != '') $arpanswerkey[$key]=$val;	
		}	

		$arpparams = array();
		foreach ($arp as $key=>$val)
			if(array_key_exists('checked', $val)) $arpparams[$key]=$val[0];
		
		// If there are no params, ARP = empty.
		if(count($arpparams)>0)		
			$this->assignmentReviewPolicy = array(	'AnswerKey' => $arpanswerkey, 
													'Parameters' => $arpparams);
		else $this->assignmentReviewPolicy = null;
	}

	// TODO: now we use the hitxml format for templating. There should be a more generic system.
	public static function getFromHit($hit){
		return new CrowdTask(array(
			'title' 		=> $hit->getTitle(),
			'description' 	=> $hit->getDescription(),
			'keywords'		=> $hit->getKeywords(),
			'reward'		=> $hit->getReward()['Amount'],
			'maxAssignments'=> $hit->getMaxAssignments(),
			'assignmentDur'	=> $hit->getAssignmentDurationInSeconds(),
			'lifetimeInSeconds' => $hit->getLifetimeInSeconds(),
			'tasksPerAssignment' => 1, // TODO add this to templating system

			/* AMT */
			'autoApprovalDelayInSeconds' => $hit->getAutoApprovalDelayInSeconds(),
			'qualificationRequirement'=> $hit->getQualificationRequirement(),
			'assignmentReviewPolicy' => $hit->getAssignmentReviewPolicy()
			));
	}

	public function toHit(){
		$hit = new Hit();
		if (isset($this->title)) 			 			$hit->setTitle						  	($this->title); 
		if (isset($this->description)) 		 			$hit->setDescription					($this->description); 
		if (isset($this->keywords)) 					$hit->setKeywords				  		($this->keywords);
		if (isset($this->maxassignments)) 				$hit->setMaxAssignments		  			($this->maxassignments);
		if (isset($this->assignmentDur))				$hit->setAssignmentDurationInSeconds 	($this->assignmentDur);
		if (isset($this->lifetimeInSeconds)) 			$hit->setLifetimeInSeconds		  		($this->lifetimeInSeconds);
		if (isset($this->reward)) 						$hit->setReward					  		(array('Amount' => $this->reward, 'CurrencyCode' => 'USD'));
		if (isset($this->autoapprovaldelayinseconds)) 	$hit->setAutoApprovalDelayInSeconds  	($this->autoapprovaldelayinseconds); 
		if (isset($this->qualificationRequirement))		$hit->setQualificationRequirement		($this->qualificationRequirement);
		if (isset($this->assignmentReviewPolicy))		$hit->setAssignmentReviewPolicy			($this->assignmentReviewPolicy);
		if (isset($this->requesterAnnotation))			$hit->setRequesterAnnotation			($this->requesterAnnotation);

		return $hit;
	}

}

?>