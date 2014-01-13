<?php

class ProcessController extends BaseController {

	public function getIndex() {
		// if(!count(Cart::content()) > 0){
		// 	Session::flash('flashNotice', 'You have not added any items to your selection yet');
		// 	return Redirect::to('files/browse');
		// }
        return Redirect::to('process/selectfile');
	}

	public function getSelectfile() {
		return View::make('process.tabs.selectfile');
	}

	public function getDetails() {
		return View::make('process.tabs.details');
	}

	public function getPlatform() {
		return View::make('process.tabs.platform');
	}

	public function getTemplate() {
		return View::make('process.tabs.template');
	}

	public function getAmt($template='default') {
		$hit = new crowdwatson\Hit();
		$questionids= array();
		try {
			$turk = new crowdwatson\MechanicalTurkService(base_path() . '/../amt/res/templates/');
			$hit = $turk->hitFromTemplate($template);
			$questionids = $turk->findQuestionIds($template);
		} catch (crowdwatson\AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		}
		return View::make('process.tabs.amt')->with('hit', $hit)->with('template', $template)->with('questionids', $questionids);
	}

	// public function getCf(){
	// 	return View::make('process.index')->with('page', 'process.cf.index');
	// }

	public function postSubmit(){
		$input = Input::get();
		$hit = new crowdwatson\Hit;
		$turk = new crowdwatson\MechanicalTurkService;

		// Standard fields
		if (!empty($input ['title'])) 			 			$hit->setTitle						  	($input ['title']); 
		if (!empty($input ['description'])) 		 		$hit->setDescription					($input ['description']); 
		if (!empty($input ['maxassignments'])) 				$hit->setMaxAssignments		  			($input ['maxassignments']);
		if (!empty($input ['assignmentdurationinseconds']))	$hit->setAssignmentDurationInSeconds 	($input ['assignmentdurationinseconds']);
		if (!empty($input ['lifetimeinseconds'])) 			$hit->setLifetimeInSeconds		  		($input ['lifetimeinseconds']);
		if (!empty($input ['reward'])) 						$hit->setReward					  		(array('Amount' => $input['reward'], 'CurrencyCode' => 'USD'));
		if (!empty($input ['keywords'])) 					$hit->setKeywords				  		($input ['keywords']);
		if (!empty($input ['autoapprovaldelayinseconds'])) 	$hit->setAutoApprovalDelayInSeconds  	($input ['autoapprovaldelayinseconds']); 

		// QualificationRequirements
		$qarray = array();
		foreach($input['qr'] as $key=>$val){
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
		if(count($qarray)>0) $hit->setQualificationRequirement($qarray);

		//AssignmentReviewPolicy
		$arpanswerkey = array();	
		foreach ($input['answerkey'] as $key=>$val)
			if($val != '') $arpanswerkey[$key]=$val;	

		$arpparams = array();
		foreach ($input['arp'] as $key=>$val)
			if(array_key_exists('checked', $val)) $arpparams[$key]=$val[0];

		if(count($arpanswerkey) > 0)
			$hit->setAssignmentReviewPolicy(array(	'AnswerKey' => $arpanswerkey, 
													'Parameters' => $arpparams));

		// Check if all parameters are set.
		$paramsset = true;
		if	 (!empty($input['params'])) 					$paramsset = false; 
		else foreach($input['params'] as $p) if($p == '') 	$paramsset = false;

		// Create HIT(s)
		try {
			if		(!empty($input['csvfilename'])) Session::flash('flashSuccess', 'Created ' . count($turk->createBatch	($input['template'], $input['csvfilename'], $hit)) . ' HITs.');
			elseif 	($paramsset) 				    Session::flash('flashSuccess', 'Created HIT ' .   $turk->createSingle	($input['template'], $input['params'], $hit) . '.');
			else throw new crowdwatson\AMTException('Provide either a CSV file or parameters.');
		} catch (crowdwatson\AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		}
	}
}