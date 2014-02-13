<?php
/*namespace crowdwatson;*/
use crowdwatson\MechanicalTurk;
use crowdwatson\AMTException;
use crowdwatson\CFExceptions;
use Sunra\PhpSimple\HtmlDomParser;
use \mongo\text\Entity;
use \mongo\text\Activity;

class Job extends Entity { 
    protected $mturk;
    protected $csv;
    protected $template;
    protected $jobConfiguration;
    protected $jcid;
    protected $activityURI;


    public function __construct($csv, $template, $jobConfiguration){
    	$this->csv = Config::get('config.csvdir') . $csv;
    	$this->template = Config::get('config.templatedir') . $template;
    	$this->CFApiKey = Config::get('config.cfapikey');
    	$this->jobConfiguration = $jobConfiguration;
    	$this->mturk = new MechanicalTurk;
    }

   
    public function publish(){
		if(isset($this->jobConfiguration->platform)) $platform = $this->jobConfiguration->platform;
		else $platform = array();
		
		$ids = array();
		try {
			
			// Create a new activity for this action.
			$user = Auth::user();
			$this->activityURI = "/createjob/" . mt_rand(0, 10000); //TODO		
			$activity = new Activity;
			$activity->_id = $this->activityURI;
			$activity->label = "Job is uploaded to crowdsourcing platform(s).";
			$activity->agent_id = $user->_id; // TODO: has to be $user->agentId or something.
			$activity->software_id = URL::to('process');
			$activity->save();

			// Save JobConfiguration (or reference existing). Throws error if not possible.
			// TODO: might have a parent.
			// Problem: on error the activity gets deleted (even if some entities were created...)
			$this->jcid = $this->jobConfiguration->store(null, $this->activityURI);

			if(in_array('amt', $platform)){
				$csvarray = $this->csv_to_array();
				shuffle($csvarray);
				$ids['amt'] = $this->amtPublish($csvarray);
			}

			if(in_array('cf', $platform)){	
				$ids['cf'] = $this->cfPublish();
			}	

			return $ids;
		} catch (AMTException $e) {
			// TODO: what if there's an error halfway through?
			throw new Exception("AMT: {$e->getMessage()}");
		} catch (CFExceptions $e) {
			throw new Exception("CF: {$e->getMessage()}");
		} catch (Exception $e) {
			throw $e; // Error in store().
		}

    }

    /** 
    * @return String[] the HTML for every question.
    */
    public function getPreviews(){
    	return $this->amtPublish($this->csv_to_array(), true);
    }

    /**
    * @return String[] fields from the CSV that have a gold answer.
    */
    public function getGoldFields(){
    	$goldfields = array();
    	foreach (array_keys($this->csv_to_array()[0]) as $key)
			if ($key != '_golden' and $pos = strpos($key, '_gold') and !strpos($key, '_gold_reason'))
				$goldfields[$key] = substr($key, 0, $pos);	
    	return $goldfields;
    }


	/**
	* Find the questionId's in a template.
	* @return string[] The questionId's (name attribute of inputs).
	* @throws AMTException when the file does not exist or is not readable.
	*/
	public function getQuestionIds(){
		$filename = "{$this->template}.html";
		if(!file_exists($filename) || !is_readable($filename))
			throw new AMTException('HTML template file does not exist or is not readable.');
	
		$build = array();
		$ret = array();
		$html = HtmlDomParser::file_get_html($filename); //HTMLDomParser::file_get_html($filename)
		foreach($html->find('input') as $input)
			if(isset($input->name)) $build[] = $input->name;
		foreach($html->find('textarea') as $input)
			if(isset($input->name)) $build[] = $input->name;	
		foreach($html->find('select') as $input)
			if(isset($input->name)) $build[] = $input->name;	

		foreach($build as $id){
			$pos = strpos($id, '{uid}_');
			if($pos !== false) $id = substr($id, $pos+6);
			$ret[]=$id;
		}

		return array_unique($ret); // Unique because checkboxes and radiobuttons have the same name.
	}


    /*	 Private functions    */

    /**
    * @return String id of published Job
    */
    private function cfPublish(){
    	$cfJob = new crowdwatson\Job($this->CFApiKey);

    	$c = $this->jobConfiguration;
		$data = $c->toCFData();
		$gold = $c->answerfields;
		$csv = $this->csv;
		$template = $this->template;
		$options = array(	"req_ttl_in_seconds" => $c->expirationInMinutes*60, 
							"keywords" => $c->requesterAnnotation, 
							"mail_to" => $c->notificationEmail);
    	try {

			// Read the files
			foreach(array('cml', 'css', 'js') as $ext){
				$filename = "$template.$ext";
				if(file_exists($filename) || is_readable($filename))
					$data[$ext] = file_get_contents($filename);
			}

			if(empty($data['cml']))
				throw new CFExceptions('CML file does not exist or is not readable.');

			// Create the job with the initial data
			$result = $cfJob->createJob($data);
			$id = $result['result']['id'];

			// Add CSV and options
			if(isset($id)) {
				// TODO: countries, workerskills, expiration, keywords

				$optionsresult = $cfJob->setOptions($id, array('options' => $options));
				if(isset($optionsresult['result']['errors']))
					throw new CFExceptions($optionsresult['result']['errors'][0]);

				$csvresult = $cfJob->uploadInputFile($id, $csv);
				if(isset($csvresult['result']['errors']))
					throw new CFExceptions($csvresult['result']['errors'][0]);

				$channelsresult = $cfJob->setChannels($id, array('cf_internal'));
				if(isset($channelsresult['result']['errors']))
					throw new CFExceptions($goldresult['result']['errors'][0]);

				if(is_array($gold) and count($gold) > 0){
					// TODO: Foreach? 
					$goldresult = $cfJob->manageGold($id, array('check' => $gold[0]));
					if(isset($goldresult['result']['errors']))
						throw new CFExceptions($goldresult['result']['errors'][0]);

				$this->store('cf', $result['result']);

				return $id;
			}
			// Failed to create initial job.
			} else {
				$err = $result['result']['errors'][0];
				if(isset($err)) $msg = $err;
				else $msg = 'Unknown error.';
				throw new CFExceptions($msg);
			}
		} catch (ErrorException $e) {
			if(isset($id)) $cfJob->deleteJob($id);
			throw new CFExceptions($e->getMessage());
		} catch (CFExceptions $e){
			if(isset($id)) $cfJob->deleteJob($id);
			throw $e;
		} 
    }

    /**
    * @return String[] HIT id's or an array of questions in HTML format (if $preview = true)
    */
    private function amtPublish($csvarray, $preview = false){
    	$htmlfilename = "{$this->template}.html";
    	if(!file_exists($htmlfilename) || !is_readable($htmlfilename))
			throw new AMTException('HTML template file does not exist or is not readable.');



		if(isset($c->frameheight)) $frameheight = $c->frameheight; else $frameheight = 700;
		$questionsbuilder = '';
		$count = 0;
		$return = array();
		$unitids = array('TODO');
		$hittypeid = '';
		$c = $this->jobConfiguration;
		$hit = $c->toHit();
		$upt = $c->unitsPerTask;
		$assRevPol = $hit->getAssignmentReviewPolicy();
		
		$dom = HtmlDomParser::file_get_html($htmlfilename);

		// Do some checks and fill $questiontemplate.
		if($upt > 1){
			try {
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
				if($preview) $questiontemplate = $dom->innertext;
				else throw $e;
			}
		} else {
			$questiontemplate = $dom->innertext;
		}

		foreach ($csvarray as $params) {
			
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

			// Change {uid} to the unit id. Input fields should have name: "{uid}_$answerfield"
			if(!array_key_exists('_unit_id', $params))
				throw new AMTException('CSV file doesn\'t have a \'_unit_id field\'.');
			$tempquestiontemplate = str_replace('{uid}', $params['_unit_id'], $tempquestiontemplate);

			// Temporarily store the AnswerKey
			if(isset($params['_golden']) and $params['_golden'] == true and !empty($c->answerfields)) {
				foreach($c->answerfields as $answerfield)
					$assRevPol['AnswerKey']["{$params['_unit_id']}_$answerfield"] = $params["{$answerfield}_gold"];
			}

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

				if($preview){
					$return[] = strip_tags($questionsbuilder, 
					"<a><abbr><acronym><address><article><aside><b><bdo><big><blockquote><br>
					<caption><cite><code><col><colgroup><dd><del><details><dfn><div><dl><dt>
					<em><figcaption><figure><font><h1><h2><h3><h4><h5><h6><hgroup><hr><i><img>
					<input><ins><li><map><mark><menu><meter><ol><p><pre><q><rp><rt><ruby><s>
					<script><samp><section><select><small><span><strong><style><sub><summary>
					<sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul>
					<var><wbr>");
				} else {
					// Set the questions and optionally the gold answers
				 	$hit->setQuestion($this->amtAddQuestionXML($questionsbuilder, $frameheight));
					if(!empty($assRevPol['AnswerKey']))
						$hit->setAssignmentReviewPolicy($assRevPol);
					else ($hit->setAssignmentReviewPolicy(null));

					// Create
					$created = $this->mturk->createHIT($hit);

					// Save to DB
					$this->store('amt', $created);

					// Add ID to returnarray
					$return[] = $created['HITId'];
					$hittypeid = $created['HITTypeId'];
				}

				$unitids = array();
				unset($assRevPol['AnswerKey']);
				$questionsbuilder = '';
				$count = 0;
			}
		}

		// Notification E-Mail
		if((!$preview) and (!empty($c->notificationEmail)) and (!empty($hittypeid)))
			$this->mturk->setHITTypeNotification($hittypeid, $c->notificationEmail, 'HITReviewable');

		return $return;
    }


    /**
    * Save Job to database
    */
    private function store($platform, $data){

    	// TODO: set tags, domain, format, type, URI, template, etc

    	// Create SoftwareAgent (als in FileUpload.php)

		if($platform == 'amt') {
			$platformJobId = $data['HITId'];
			$platformId = 'todo';
			$status = 'running';
		} elseif ($platform == 'cf') {
			$platformJobId = $data['id'];
			$platformId = 'todo';
			$status = 'unordered';
		}	

		$user = Auth::user();
		$entityURI = "/job/$platform/$platformJobId"; //TODO
		
		try {
			$entity = new Entity;
			$entity->_id = $entityURI;
			$entity->documentType = 'job';
			$entity->activity_id = $this->activityURI;
			$entity->agent_id = $user->_id; // TODO: has to be $user->agentId or something.

			$entity->jobConfigurationId = $this->jcid;
			$entity->templateId = $this->template; // TODO [part of jobconf?]
			$entity->batchId = $this->csv;			// TODO
			$entity->platformId = $platformId; 
			$entity->platformJobId = $platformJobId;
			$entity->status = $status;

			if($platform == 'amt')
				$entity->hitTypeId = $data['HITTypeId'];
			$entity->save();
			return true;
		} catch (Exception $e) {
			// Something went wrong with creating the Entity

			// TODO Delete more? handle this in publish().
			Log::warning("Error saving {$entity->_id} to DB.");
			$entity->forceDelete();
			throw $e;
		}
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


	/**
	* Convert a csv file to an array of associative arrays.
	* @param string $filename Path to the CSV file
	* @param string $delimiter The separator used in the file
	* @return array[][]
	* @throws AMTException if the file is not readable.
	* @author Jay Williams <http://myd3.com/>
	*/
	private function csv_to_array($delimiter=',') {
		$filename = $this->csv;
		if(!file_exists($filename) || !is_readable($filename))
			throw new AMTException('CSV file does not exist or is not readable.');

		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE)
		{
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
			{
				if(!$header)
					$header = $row;
				else
					$data[] = array_combine($header, $row);
			}
			fclose($handle);
		} else throw new AMTException('Failed to open CSV file for reading.');
		return $data;
	}

}
?>