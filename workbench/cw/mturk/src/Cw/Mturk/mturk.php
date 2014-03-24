<?php
namespace Cw\Mturk;
use Cw\Mturk\Turkapi\MechanicalTurk;
use Cw\Mturk\Turkapi\Hit;
use Cw\Mturk\Turkapi\AMTException;
use Sunra\PhpSimple\HtmlDomParser;
use \Exception;
use \Config;
use \View;
use \Input;

class Mturk {
	protected $mechanicalTurk = null;
	public $label = "Crowdsourcing platform: Amazon Mechanical Turk";
	
	public $jobConfValidationRules = array(
		'hitLifetimeInMinutes' => 'required|numeric|min:1',
		'frameheight' => 'numeric|min:300' // not required because we have a default value.
	);

	public function __construct(){
		$this->mechanicalTurk = new MechanicalTurk(Config::get('mturk::rooturl'), false, Config::get('mturk::accesskey'), Config::get('mturk::secretkey'));
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
			$platformjobids = $this->amtpublish($job, $sandbox);
			$fullplatformjobids = array();
			foreach($platformjobids as $id)
				array_push($fullplatformjobids, array('id' => $id, 'status' => $status, 'timestamp' => time()));
			return $fullplatformjobids;
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
	private function amtpublish($job, $sandbox){
		if($sandbox) $this->mechanicalTurk->setRootURL(Config::get('mturk::sandboxurl'));
		else $this->mechanicalTurk->setRootURL(Config::get('mturk::rooturl'));
		$htmlfilename = public_path() . "/templates/{$job->template}.html";
    	if(!file_exists($htmlfilename) || !is_readable($htmlfilename))
			throw new AMTException('HTML template file does not exist or is not readable.');

		$units = $job->batch->wasDerivedFrom;
		shuffle($units);

		$questionsbuilder = '';
		$count = 0;
		$platformids = array();
		$c = $job->jobConfiguration->content;
		$hit = $this->jobConfToHIT($c);
		$upt = $c['unitsPerTask'];
		$assRevPol = $hit->getAssignmentReviewPolicy();
		$dom = HtmlDomParser::file_get_html($htmlfilename);

		// Do some checks and fill $questiontemplate.
		if($upt > 1){

			if(!$div = $dom->find('div[id=wizard]', 0))
				throw new AMTException('Multipage template has no div with id \'wizard\'. View the readme in the templates directory for more info.');
			
			if(!$div->find('h1', 0))
				throw new AMTException('Multipage template has no <h1>. View the readme in the templates directory for more info.');

			$questiontemplate = $div->innertext;
			if(!strpos($questiontemplate, '{x}'))
				throw new AMTException('Multipage template has no \'{x}\'. View the readme in the templates directory for more info.');
			if(!strpos($questiontemplate, '{uid}'))
				throw new AMTException('Multipage template has no \'{uid}\'. View the readme in the templates directory for more info.');
		
		} else {
			$questiontemplate = $dom->innertext;
		}

		foreach ($units as $parameters) {
			$params = array_dot($parameters['content']);

//			$replacerules=array('cause' => 'causes'); // TODO: get these from QUESTIONTEMPLATE
//			$params = str_replace(array_keys($replacerules), $replacerules, $params);

			if($upt>1)	{
				$count++;
				$tempquestiontemplate = str_replace('{x}', $count, $questiontemplate);
			} else {
				$count = '';
				$tempquestiontemplate = $questiontemplate;
			}

			// Insert the parameters

			foreach ($params as $key=>$val)	{	
				$param = '${' . $key . '}';
				$tempquestiontemplate = str_replace($param, $val, $tempquestiontemplate);
			}

			$tempquestiontemplate = str_replace('{uid}', $parameters['_id'], $tempquestiontemplate);
			
			/*if(!strpos($questiontemplate, '{instructions}'))
				throw new AMTException('Template has no {instructions}');*/
			$tempquestiontemplate = str_replace('{instructions}', nl2br($c['instructions']), $tempquestiontemplate);

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
				$created = $this->mechanicalTurk->createHIT($hit);
				
				// Add ID to returnarray
				$platformids[] = $created['HITId'];
				$hittypeid = $created['HITTypeId'];
			
				unset($assRevPol['AnswerKey']);
				$questionsbuilder = '';
				$count = 0;
			}
		}	

					// Notification E-Mail
		if((!empty($c['notificationEmail'])) and (!empty($hittypeid)))
			$this->mechanicalTurk->setHITTypeNotification($hittypeid, $c['notificationEmail'], $c['eventType']);

		return $platformids;
	}


    public function orderJob($id){
    	
	}

	public function pauseJob($id){
		throw new Exception('AMT can\'t pause/resume.');
	}

	public function resumeJob($id){
		throw new Exception('AMT can\'t pause/resume.');
	}

	public function cancelJob($id){
		//$status = $this->mechanicalTurk->getHIT($hitid)->getHITStatus();
		//if($status != 'Disposed')
		foreach($id as $hitid){
		 	$this->mechanicalTurk->forceExpireHIT($hitid);
        }
	}



	private function jobConfToHIT($jc){
		$hit = new Hit();
		if (isset($jc['title'])) 			 		 	$hit->setTitle						  	($jc['title']); 
		if (isset($jc['description'])) 		 			$hit->setDescription					($jc['description']); 
		if (isset($jc['keywords'])) 					$hit->setKeywords				  		($jc['keywords']);
		if (isset($jc['annotationsPerUnit'])) 		 	$hit->setMaxAssignments		  			($jc['annotationsPerUnit']);
		if (isset($jc['expirationInMinutes']))		 	$hit->setAssignmentDurationInSeconds 	($jc['expirationInMinutes']*60);
		if (isset($jc['hitLifetimeInMinutes'])) 		$hit->setLifetimeInSeconds		  		($jc['hitLifetimeInMinutes']*60);
		if (isset($jc['reward'])) 					 	$hit->setReward					  		(array('Amount' => $jc['reward'], 'CurrencyCode' => 'USD'));
		if (isset($jc['autoApprovalDelayInMinutes'])) 	$hit->setAutoApprovalDelayInSeconds  	($jc['autoApprovalDelayInMinutes']*60); 
		if (isset($jc['qualificationRequirement']))	$hit->setQualificationRequirement		($jc['qualificationRequirement']);
		if (isset($jc['requesterAnnotation']))		 	$hit->setRequesterAnnotation			($jc['requesterAnnotation']);
		
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
			'annotationsPerUnit'=> $hit->getMaxAssignments(),
			'expirationInMinutes'	=> intval($hit->getAssignmentDurationInSeconds())/60,
			'hitLifetimeInMinutes' => intval($hit->getLifetimeInSeconds())/60,
			'unitsPerTask' => 1, 
			'autoApprovalDelayInMinutes' => intval($hit->getAutoApprovalDelayInSeconds())/60,
			'qualificationRequirement'=> $hit->getQualificationRequirement(),
			'assignmentReviewPolicy' => $hit->getAssignmentReviewPolicy(),
			'platform' => array('amt')		
			));
	}*/



}

?>