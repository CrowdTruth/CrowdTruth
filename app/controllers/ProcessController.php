<?php

use crowdwatson\MechanicalTurkService;
use crowdwatson\AMTException;
use crowdwatson\Hit;

class ProcessController extends BaseController {

	public function getIndex() {
		// if(!count(Cart::content()) > 0){
		// 	Session::flash('flashNotice', 'You have not added any items to your selection yet');
		// 	return Redirect::to('files/browse');
		// }
        return Redirect::to('process/selectfile');
	}

	public function getSelectfile() {
		// Where to forget?
		Session::forget('crowdtask');
		return View::make('process.tabs.selectfile');
	}

	public function getDetails() {
		return View::make('process.tabs.details')->with('crowdtask', unserialize(Session::get('crowdtask')));
	}

	public function getPlatform() {
		return View::make('process.tabs.platform')->with('crowdtask', unserialize(Session::get('crowdtask')));
	}

	public function getTemplate() {
		// Create array for the select
		$crowdtask = unserialize(Session::get('crowdtask'));		
		$templatePath = '/templates/';
		$currenttemplate = (isset($crowdtask->template) ? $crowdtask->template : 'default');

		$filesystempath = base_path() . '/public/' . $templatePath;
		$files = glob($filesystempath . '*.{html}', GLOB_BRACE);
		$templates = array();

		foreach($files as $file) {
			$file = str_replace($filesystempath, '', $file);
			$file = str_replace('.html', '', $file);
			$prettyname = ucfirst(str_replace('_', ' ', $file));
			$templates[$file] = $prettyname;
		}

		return View::make('process.tabs.template')
			->with('templatePath', $templatePath)
			->with('templates', $templates)
			->with('currenttemplate', $currenttemplate)
			->with('crowdtask', $crowdtask);
	}

	public function getFinish() {
		$crowdtask = unserialize(Session::get('crowdtask'));
		$turk = new MechanicalTurkService();
		$questions = array();

		try{
			$question = file_get_contents(base_path() . "/public/templates/{$crowdtask->template}.html");
			$questions = $turk->createPreviews($question, base_path() . '/public/csv/test.csv');
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		} catch (ErrorException $e) {
			Session::flash('flashError', 'Error reading templatefile.');
		}

		return View::make('process.tabs.finish')
			->with('crowdtask', $crowdtask)
			->with('questions',  $questions);
	}

	private function newCTfromTemplate($template){
		try {
			// Currently, the HIT format is used.
			$turk = new MechanicalTurkService(base_path() . '/public/templates/');
			$hit = $turk->hitFromTemplate($template);
			$ct = CrowdTask::getFromHit($hit);
			$ct->template = $template;
			return $ct;
		} catch (AMTException $e){
			Session::flash('flashError', $e->getMessage());
			return new CrowdTask;
		}
	}

	public function postFormPart($next){
		$ct = unserialize(Session::get('crowdtask'));

		if(Input::has('template')){
			$template = Input::get('template');
			if (empty($ct) or ($ct->template != $template))
				$ct = $this->newCTfromTemplate($template);		
		} else {
			if (empty($ct)){
				$ct = new CrowdTask;
				Session::flash('flashWarning', 'No template selected.');
			} else {
				$ct = new CrowdTask(array_merge($ct->toArray(), Input::get()));	
			}		
		}

		Session::put('crowdtask', serialize($ct));
		return Redirect::to("process/$next");

	}

	public function getAmt($template='default') {
		$hit = new Hit();
		$questionids= array();
		try {
			$turk = new MechanicalTurkService(base_path() . '/public/templates/');
			$hit = $turk->hitFromTemplate($template);
			$questionids = $turk->findQuestionIds($template);
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		}
		
		return View::make('process.tabs.amt')
			->with('hit', $hit)
			->with('template', $template)
			->with('questionids', $questionids)
			->with('crowdtask', unserialize(Session::get('crowdtask')));
	}

	// public function getCf(){
	// 	return View::make('process.index')->with('page', 'process.cf.index');
	// }

	public function postSubmit(){
		$input = Input::get();
		$hit = new Hit;
		$turk = new MechanicalTurkService;

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
			else throw new AMTException('Provide either a CSV file or parameters.');
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
			return Redirect::to("process/finish");
		}
	}
}