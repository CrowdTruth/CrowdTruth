<?php
namespace CrowdTruth\Mturk;
use CrowdTruth\Mturk\Turkapi\MechanicalTurk;
use CrowdTruth\Mturk\Turkapi\Hit;
use CrowdTruth\Mturk\Turkapi\AMTException;
use Sunra\PhpSimple\HtmlDomParser;
use \Exception;
use \Config;
use \View;
use \Input;

class Mturk extends \FrameWork {
	protected $mechanicalTurk = null;

	public function __construct(){
		$this->mechanicalTurk = new MechanicalTurk(Config::get('mturk::rooturl'), false, Config::get('mturk::accesskey'), Config::get('mturk::secretkey'));
	}
	
	public function getLabel(){
		return "Crowdsourcing platform: Amazon Mechanical Turk";
	} 

	public function getName(){
		return "Mechanical Turk";
	} 

	public function getExtension(){
		return 'html';
	}
	
	public function getJobConfValidationRules(){
		return array(
		'hitLifetimeInMinutes' => 'required|numeric|min:1',
		'frameheight' => 'numeric|min:300'); // not required because we have a default value.
	}

	public function createView(){
		return View::make('mturk::create');
	}

	public function updateJobConf($jc){
		// Qualification Requirements
		$qr = Input::get('qr', false);
		$jcc = $jc->content;
		if($qr){
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
				$jcc['qualificationRequirement'] = $qarray;
			else $jcc['qualificationRequirement'] = null;
		}
		
		// Assignment Review Policy
		$arp = Input::get('arp', false);
		if($arp){	
			
			$arpparams = array();
			foreach ($arp as $key=>$val)
				if(array_key_exists('checked', $val)) $arpparams[$key]=$val[0];
			
			// If there are no params, ARP = empty.
			if(count($arpparams)>0)		
				$jcc['assignmentReviewPolicy'] = array(	'AnswerKey' => null, 
														'Parameters' => $arpparams);
			else $jcc['assignmentReviewPolicy'] = null;
		}

		$jc->content = $jcc;
		return $jc;

	}

	/**
	* @return 
	*/
	public function publishJob($job, $sandbox){
		try {
			if($sandbox) $status = 'unordered';
			else $status = 'running';
			$ids = array();
			$published = $this->amtpublish($job, $sandbox);
			$fullplatformjobids = array();
			foreach($published['id'] as $id)
				array_push($fullplatformjobids, array('id' => $id, 'status' => $status));
			
			$response = array('id' => $fullplatformjobids);
			return $response;
		} catch (AMTException $e) {
			if(isset($fullplatformjobids)) $this->undoCreation($fullplatformjobids);
			elseif(isset($platformjobids)) $this->undoCreation($platformjobids);
			throw new Exception($e->getMessage());
		}	
	}

	/**
	* @throws Exception
	*/
	public function undoCreation($ids){
		
		try {
			// Platform
			if($ids)
				foreach($ids as $id){
					if(is_array($id) && isset($id['id'])) // This should be the case, since we created it this way.
						$id = $id['id'];
					$this->mechanicalTurk->disableHIT($id); // This fully deletes the HIT.
					print_r($id);
				}	
		} catch (AMTException $e) {
			throw new Exception($e->getMessage()); // Let Job take care of this
		} 	

	}

	/**
	* @throws AMTException
	* @return string platformid's
	*/
	public function amtpublish($job, $sandbox, $justpreview = false, $jc = null){ //todo: private, remove justpreview and jc.
		if($sandbox) $this->mechanicalTurk->setRootURL(Config::get('mturk::sandboxurl'));
		else $this->mechanicalTurk->setRootURL(Config::get('mturk::rooturl'));
		$htmlfilename = public_path() . "/templates/{$job->template}.html";//dd($htmlfilename);
    	if(!file_exists($htmlfilename) || !is_readable($htmlfilename))
			throw new AMTException('HTML template file does not exist or is not readable.');

		$units = $job->batch->wasDerivedFrom;
		shuffle($units);

		$questionsbuilder = '';
		$count = 0;
		$platformids = array();
		

		if($jc) $c = $jc->content;
		else $c = $job->jobConfiguration->content;



		if(!isset($c['frameheight'])) $c['frameheight'] = 450;
		$hit = $this->jobConfToHIT($c);
		$upt = $c['unitsPerTask'];
		$assRevPol = $hit->getAssignmentReviewPolicy();
		// The instantiation below looks ugly, but is used to preserve line breaks.
		//file_get_html($url, $use_include_path = false, $context=null, $offset = -1, $maxLen=-1, $lowercase = true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT)
		$dom = HtmlDomParser::file_get_html($htmlfilename, false, null, -1, -1, true, true, DEFAULT_TARGET_CHARSET, false);

		// Do some checks and fill $questiontemplate.
		if($upt > 1){
			try{
				if(!$div = $dom->find('div[id=wizard]', 0))
					throw new AMTException('Multipage template has no div with id \'wizard\'. View the readme in the templates directory for more info.');
				
				if(!$div->find('h1', 0))
					throw new AMTException('Multipage template has no <h1>. View the readme in the templates directory for more info.');

				$questiontemplate = $div->innertext;
				if(!strpos($questiontemplate, '{x}'))
					throw new AMTException('Multipage template has no \'{x}\'. View the readme in the templates directory for more info.');
				if(!strpos($questiontemplate, '{uid}'))
					throw new AMTException('Multipage template has no \'{uid}\'. View the readme in the templates directory for more info.');
			} catch (AMTException $e){
				// Catch when the template is unable to present multiple HIT's on one page.
				\Log::debug('Attempted to create multipage job with singlepage template. Changed to singlepage for AMT.');
				$upt = 1;
				$questiontemplate = $dom->innertext;
			}
		} else {
			$questiontemplate = $dom->innertext;
		}

		foreach ($units as $unit) {

			$params = $job->questionTemplate->flattenAndReplace($unit['content']);

			//$replacerules = array('cause' => 'causes'); // TODO: get these from QUESTIONTEMPLATE
			//$params = str_replace(array_keys($replacerules), $replacerules, $params);

			$count++;
			$tempquestiontemplate = str_replace('{x}', $count, $questiontemplate);
			// Insert the parameters

			foreach ($params as $key=>$val)	{
				$tempquestiontemplate = str_replace('{{' . $key . '}}', $val, $tempquestiontemplate);
			}

			$tempquestiontemplate = str_replace('{uid}', $unit['_id'], $tempquestiontemplate);
			
			/*if(!strpos($questiontemplate, '{instructions}'))
				throw new AMTException('Template has no {instructions}');*/
			$tempquestiontemplate = str_replace('{{instructions}}', nl2br($c['instructions']), $tempquestiontemplate);

			// Temporarily store the AnswerKey

			// TODO!
/*			if(isset($params['_golden']) and $params['_golden'] == true and isset($c['answerfields'])) {
				foreach($c['answerfields'] as $answerfield)
					$assRevPol['AnswerKey']["{$params['_unit_id']}_$answerfield"] = $params["{$answerfield}_gold"];
			}*/

			// Check if all parameters have been replaced
			if(preg_match('#\$\{[A-Za-z0-9_.]*\}#', $tempquestiontemplate) == 1) // ${...}
				throw new AMTException('HTML contains parameters that are not in the CSV.');

			// Add the current question
			$questionsbuilder .= $tempquestiontemplate;
			$url = '';
			// Create a hit every ($upt)
			if($count % $upt == 0){
				if($upt>1){
					$dom->find('div[id=wizard]', 0)->innertext = $questionsbuilder;
					$questionsbuilder = $dom->save();
				}

				// Set the questions and optionally the gold answers
			 	$hit->setQuestion($this->amtAddQuestionXML($questionsbuilder, $c['frameheight']));
				if(!empty($assRevPol['AnswerKey']))
					$hit->setAssignmentReviewPolicy($assRevPol);
				else ($hit->setAssignmentReviewPolicy(null));

				// Create
				if($justpreview) $platformids[] = $questionsbuilder;
				else {
					$created = $this->mechanicalTurk->createHIT($hit);
				
					// Add ID to returnarray
					$platformids[] = $created['HITId'];
					$hittypeid = $created['HITTypeId'];
					
					// URL
					// TODO: AMT doesn't return the HIT group id. Do this in the COMMAND? Or perform 1 call after creating.
/*					if(isset($created['HITGroupId'])){
						// SANDBOX is hardcoded, because the job is always ordered on sandbox first.
						$url = "https://workersandbox.mturk.com/mturk/preview?groupId={$created['HITGroupId']}";
					}
*/
				}
				unset($assRevPol['AnswerKey']);
				$questionsbuilder = '';
				$count = 0;
			}
		}	

		if($justpreview) 
			return $platformids;

		// Notification E-Mail
		if((!empty($c['notificationEmail'])) and (!empty($hittypeid)))
			$this->mechanicalTurk->setHITTypeNotification($hittypeid, $c['notificationEmail'], $c['eventType']);

		$response = array('id'=>$platformids);
		if(!empty($url))
			$response['url'] = $url;

		return $response;
	}


	private function jobConfToHIT($jc){
		$hit = new Hit();
		if (isset($jc['title'])) 			 		 	$hit->setTitle						  	($jc['title']); 
		if (isset($jc['description'])) 		 			$hit->setDescription					($jc['description']); 
		if (isset($jc['keywords'])) 					$hit->setKeywords				  		($jc['keywords']);
		if (isset($jc['workerunitsPerUnit'])) 		 	$hit->setMaxAssignments		  			($jc['workerunitsPerUnit']);
		if (isset($jc['expirationInMinutes']))		 	$hit->setAssignmentDurationInSeconds 	($jc['expirationInMinutes']*60);
		if (isset($jc['hitLifetimeInMinutes'])) 		$hit->setLifetimeInSeconds		  		($jc['hitLifetimeInMinutes']*60);
		if (isset($jc['reward'])) 					 	$hit->setReward					  		(array('Amount' => $jc['reward'], 'CurrencyCode' => 'USD'));
		if (isset($jc['autoApprovalDelayInMinutes'])) 	$hit->setAutoApprovalDelayInSeconds  	($jc['autoApprovalDelayInMinutes']*60); 
		if (isset($jc['qualificationRequirement']))	$hit->setQualificationRequirement		($jc['qualificationRequirement']);
		if (isset($jc['requesterWorkerunit']))		 	$hit->setRequesterWorkerunit			($jc['requesterWorkerunit']);
		
		if (isset($jc['assignmentReviewPolicy']) and
			isset($jc['assignmentReviewPolicy']['Parameters']) and
			count($jc['assignmentReviewPolicy']['Parameters']) > 0 ) 		
														$hit->setAssignmentReviewPolicy			($jc['assignmentReviewPolicy']);
		
		return $hit;
	}

	/**
	* Convert the HTML from a template (with parameters injected) to a proper AMT Question.
	* @param string $html 
	* @return string AMT HTMLQuestion.
	*/
	private function amtAddQuestionXML($html, $frameheight = 650){
		return "<?xml version='1.0' ?>
			<HTMLQuestion xmlns='http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2011-11-11/HTMLQuestion.xsd'>
			  <HTMLContent><![CDATA[
				<!DOCTYPE html>
				<html>
				 <head>
				  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
				  <script type='text/javascript' src='https://s3.amazonaws.com/mturk-public/externalHIT_v1.js'></script>
				 </head>
				 <body>
				  <form name='mturk_form' method='post' id='mturk_form' action='https://www.mturk.com/mturk/externalSubmit'>
				  <input type='hidden' value='' name='assignmentId' id='assignmentId'/>
					$html
				  <p><input type='submit' id='submitButton' value='Submit' /></p></form>
				  <script language='Javascript'>turkSetAssignmentID();</script>
				 </body>
				</html>
			]]>
			  </HTMLContent>
			  <FrameHeight>$frameheight</FrameHeight>
			</HTMLQuestion>
		";
	}
/*
	// Not used. Could be handy for importing
	public static function getFromHit($hit){
		return new JobConfiguration(array(
			'title' 		=> $hit->getTitle(),
			'description' 	=> $hit->getDescription(),
			'keywords'		=> $hit->getKeywords(),
			'reward'		=> $hit->getReward()['Amount'],
			'workerunitsPerUnit'=> $hit->getMaxAssignments(),
			'expirationInMinutes'	=> intval($hit->getAssignmentDurationInSeconds())/60,
			'hitLifetimeInMinutes' => intval($hit->getLifetimeInSeconds())/60,
			'unitsPerTask' => 1, 
			'autoApprovalDelayInMinutes' => intval($hit->getAutoApprovalDelayInSeconds())/60,
			'qualificationRequirement'=> $hit->getQualificationRequirement(),
			'assignmentReviewPolicy' => $hit->getAssignmentReviewPolicy(),
			'platform' => array('amt')		
			));
	}*/



    public function orderJob($job){
    	try {
			$platformjobids = $this->amtpublish($job, false);
			// TODO: (possibly): delete existing results?
			/*$fullplatformjobids = array();
			/*foreach($platformjobids as $id)
				array_push($fullplatformjobids, array('id' => $id, 'status' => 'running'));*/
			$job->platformJobId = $platformjobids;
			$job->save();
		} catch (AMTException $e) {
			if(isset($fullplatformjobids)) $this->undoCreation($fullplatformjobids);
			elseif(isset($platformjobids)) $this->undoCreation($platformjobids);
			throw new Exception($e->getMessage());
		}	
	}

	public function pauseJob($id){
		throw new Exception('AMT can\'t pause/resume.');
	}

	public function resumeJob($id){
		throw new Exception('AMT can\'t pause/resume.');
	}

	public function cancelJob($id){
		if(empty($id))
			throw new Exception('Platform Job ID\'s not found. Is this an imported job?');

		foreach($id as $hitid)
		 	$this->mechanicalTurk->forceExpireHIT($hitid);
        
	}

	public function blockWorker($id, $message){
		try {
			$this->mechanicalTurk->blockWorker($id, $message);
		} catch (AMTException $e){
			throw new Exception($e->getMessage());
		} 
	}

	public function unblockWorker($id, $message){
		try {
			$this->mechanicalTurk->unBlockWorker($id, $message);
		} catch (AMTException $e){
			throw new Exception($e->getMessage());
		} 
	}

	public function sendMessage($subject, $body, $workerids){
		try {
			$this->mechanicalTurk->notifyWorkers($subject, $body, $workerids);
		} catch(AMTException $e){
			throw new Exception($e->getMessage());
		}
	}





}

?>
