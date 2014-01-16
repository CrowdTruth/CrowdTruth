<?php

//use Jenssegers\Mongodb\Model as Eloquent;

class CrowdTask extends Moloquent {
	//protected $fillable = array('title', 'description', 'keywords', 'template', 'reward', 'maxAssignments', 'assignmentDur');
    protected $fillable = array('title', 'description', 'keywords', 'template', 'autoApprovalDelayInSeconds', 'qualificationRequirement', 'requesterAnnotation' ,'assignmentReviewPolicy');


	public static $rules = array(
	  'title' => 'required',
	  'desciprtion' => 'required',
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
		if(count($qarray)>0) $this->qualificationRequirement = $qarray;
	}

	public function addAssRevPol($answerkey, $arp){
		$arpanswerkey = array();	
		foreach ($answerkey as $key=>$val)
			if($val != '') $arpanswerkey[$key]=$val;	
			
		$arpparams = array();
		foreach ($arp as $key=>$val)
			if(array_key_exists('checked', $val)) $arpparams[$key]=$val[0];
				
		if(count($arpanswerkey) > 0)
			$this->ssignmentReviewPolicy = array(	'AnswerKey' => $arpanswerkey, 
													'Parameters' => $arpparams);
	}


	public static function getFromHit($hit){

		return new CrowdTask(array(
			'title' 		=> $hit->getTitle(),
			'description' 	=> $hit->getDescription(),
			'keywords'		=> $hit->getKeywords(),
			'reward'		=> $hit->getReward()['Amount'],
			'maxAssignments'=> $hit->getMaxAssignments(),
			'assignmentDur'	=> $hit->getAssignmentDurationInSeconds(),
			/* AMT */
			'autoApprovalDelayInSeconds' => $hit->getAutoApprovalDelayInSeconds(),
			'qualificationRequirement'=> $hit->getQualificationRequirement(),
			'assignmentReviewPolicy' => $hit->getAssignmentReviewPolicy()
			));
	}

}

?>