<?php

//use Jenssegers\Mongodb\Model as Eloquent;
use crowdwatson\Hit;

class CrowdTask extends Moloquent {
    protected $fillable = array(
    								'title', 
    								'description', 
    								'keywords', 
    								'judgmentsPerUnit', /* AMT: maxAssignments */
    								'unitsPerTask', /* AMT: not in API. Would be 'tasks per assignment' */
    								'reward', 
    								'expirationInMinutes', /* AMT: assignmentDurationInSeconds */
    								'notificationEmail',
    								'requesterAnnotation',
    								'country', /* TODO: GUI

    								/* Undecided */
    								'instructions',

    								/* AMT specific */
    	    						'autoApprovalDelayInMinutes', /* AMT API: AutoApprovalDelayInSeconds */
									'hitLifetimeInMinutes', 
									'qualificationRequirement',
									'assignmentReviewPolicy', 

    	    						/* CF specific */
    	    						'mandatory',
    	    						'judgmentsPerWorker',

    	    						/* for our use */
    	    						'answerfields', /* The fields of the CSV file that contain the gold answers. */
    								'template',
    								'csv',
    								'platform'
    								);

    // TODO: we do nothing with the rules yet.
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

	public function addAssRevPol($arp){
		$arpparams = array();
		foreach ($arp as $key=>$val)
			if(array_key_exists('checked', $val)) $arpparams[$key]=$val[0];
		
		// If there are no params, ARP = empty.
		if(count($arpparams)>0)		
			$this->assignmentReviewPolicy = array(	'AnswerKey' => null, 
													'Parameters' => $arpparams);
		else $this->assignmentReviewPolicy = null;
	}

	public static function fromJSON($filename){
		if(!file_exists($filename) || !is_readable($filename))
			throw new Exception('JSON template file does not exist or is not readable.');
	
		$json = file_get_contents($filename);
		if(!$arr = json_decode($json, true))
			throw new Exception('JSON incorrectly formatted');
		return new CrowdTask($arr);
	}


	public function toHit(){
		$hit = new Hit();
		if (!empty($this->title)) 			 			$hit->setTitle						  	($this->title); 
		if (!empty($this->description)) 		 			$hit->setDescription					($this->description); 
		if (!empty($this->keywords)) 					$hit->setKeywords				  		($this->keywords);
		if (!empty($this->judgmentsPerUnit)) 			$hit->setMaxAssignments		  			($this->judgmentsPerUnit);
		if (!empty($this->expirationInMinutes))			$hit->setAssignmentDurationInSeconds 	($this->expirationInMinutes*60);
		if (!empty($this->hitLifetimeInMinutes)) 		$hit->setLifetimeInSeconds		  		($this->hitLifetimeInMinutes*60);
		if (!empty($this->reward)) 						$hit->setReward					  		(array('Amount' => $this->reward, 'CurrencyCode' => 'USD'));
		if (!empty($this->autoApprovalDelayInMinutes)) 	$hit->setAutoApprovalDelayInSeconds  	($this->autoApprovalDelayInMinutes*60); 
		if (!empty($this->qualificationRequirement))		$hit->setQualificationRequirement		($this->qualificationRequirement);
		if (!empty($this->requesterAnnotation))			$hit->setRequesterAnnotation			($this->requesterAnnotation);
		
		if (/* isset($this->assignmentReviewPolicy['AnswerKey']) and 
			count($this->assignmentReviewPolicy['AnswerKey']) > 0 and */
			isset($this->assignmentReviewPolicy['Parameters']) and
			count($this->assignmentReviewPolicy['Parameters']) > 0 ) 		
														$hit->setAssignmentReviewPolicy			($this->assignmentReviewPolicy);
		
		return $hit;
	}


	public function toCFData(){
		// not yet implemented: max_judgments_per_ip, webhook_uri, send_judgments_webhook => true, instructions, css, js, cml
		$data = array();

		if (!empty($this->title)) 			 	$data['title']					 	= $this->title; 
		if (!empty($this->instructions)) 		$data['instructions']				= $this->instructions; 
		//if (!empty($this->keywords)) 			$data['Keywords']				  		($this->keywords);
		if (!empty($this->judgmentsPerUnit)) 	$data['judgments_per_unit']		  	= $this->judgmentsPerUnit;
		//if (!empty($this->expirationInMinutes))$data['AssignmentDurationInSeconds'] 	($this->expirationInMinutes*60);
		if (!empty($this->reward)) 				$data['payment_cents']				= $this->reward*100;
		if (!empty($this->unitsPerTask))		$data['units_per_assignment']		= $this->unitsPerTask;
		if (!empty($this->judgmentsPerWorker))	$data['max_judgments_per_worker']	= $this->judgmentsPerWorker;
		return $data;
	}


	// Not used (yet?)
	public static function getFromHit($hit){
		return new CrowdTask(array(
			'title' 				=> $hit->getTitle(),
			'description' 			=> $hit->getDescription(),
			'keywords'				=> $hit->getKeywords(),
			'reward'				=> $hit->getReward()['Amount'],
			'judgmentsPerUnit'		=> $hit->getMaxAssignments(),
			'expirationInMinutes'	=> $hit->getAssignmentDurationInSeconds(),
			'hitLifetimeInMinutes' 	=> $hit->getLifetimeInSeconds() / 60,
			'unitsPerTask' 			=> 1, /* This is not in the AMT API */

			/* AMT */
			'autoApprovalDelayInSeconds' 	=> $hit->getAutoApprovalDelayInSeconds(),
			'qualificationRequirement'		=> $hit->getQualificationRequirement(),
			'assignmentReviewPolicy' 		=> $hit->getAssignmentReviewPolicy()
			));
	}
}

?>