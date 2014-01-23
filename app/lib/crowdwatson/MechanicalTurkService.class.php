<?php
/**
* This ServiceClass uses the MechanicalTurk class, which in turn talks to Amazon's API.
* It contains functions that are used for creating batches and filling out templates.
* The $templatePath should contain an html and an xml file for each $templateName.
* Please refer to the example files in this directory to see the correct format.
* @author Arne Rutjes for the Crowd Watson project.
* @license IPL
*/

namespace crowdwatson;
	
require_once(dirname(__FILE__) . '/php-mturk-api/MechanicalTurk.class.php');
require_once(dirname(__FILE__) . '/simple_html_dom/simple_html_dom.php');

class MechanicalTurkService{

	private $mturk;
	private $templatePath = '../res/templates/';

	public function __construct($templatePath = null){
		$this->mturk = new MechanicalTurk();
		if(isset($templatePath)) $this->templatePath = $templatePath;
	}
	
	
	/**
	* Create a series of HITs from a template (html for the question, xml or Hit for the rest) with parameters from a CSV file.
	* @param string $templateName The name of the html and xml template files in the templates directory.
	* @param string $csvFileName Comma separated parameters to fill the template with.
	* @param Hit $templateHit Optional. Used instead of the xml template. We still need the templateName for the question.
	* @param int $assignmentsPerHit Optional. If set, divides the CSV file up into chunks and posts multipage HIT's
	* @return string[] the HITIds of the created HITs.
	* @throws AMTException when one of the files does not exist, or when the parameters and template don't match. Also when
	* you attempted to create a multipage hit with the wrong parameters.
	*/
	public function createBatch($templateName, $csvFilename, $templateHit = null, $assignmentsPerHit = 1, $answerfield = null){
			$paramsArray = $this->csv_to_array($csvFilename);
			
			// can we shuffle? Would be useful for gold questions, but might be bad for continuity?

			if(isset($templateHit)) $hit = $templateHit;
			else $hit = $this->hitFromTemplate($templateName);
			
			$created = array();	

			if($assignmentsPerHit == 1){		
				foreach($paramsArray as $params){
					$hit->setQuestion($this->questionFromHTML($templateName, $params));
					$id = $this->mturk->createHIT($hit); 
					$created[] = $id;
				}
			} else {
				$chunks = array_chunk($paramsArray, $assignmentsPerHit);
				
				foreach ($chunks as $chunk){
					$hit = $this->addMultipageQuestion($hit, $templateName, $chunk, $answerfield);
					//print_r($hit);
					//echo '\r\n\r\n\r\n';
					//$id = $this->mturk->createHIT($hit); 
					//$created[] = $id;
				}
			}
		dd($hit);
			return $created;
	}
	
	/**
	* Inject the parameters of the CSV files into the HTMLquestion. Just for previewing purposes.
	* @var string $question HTML of the question.
	* @var string $csvFilename
	* @return string[] HTML of the question, with parameters injected.
	*/
	public function createPreviews($question, $csvFilename){
		$paramsArray = $this->csv_to_array($csvFilename);
		$questions = array();
		foreach($paramsArray as $params){
			$basequestion = $question;
			foreach ($params as $key=>$val)	{
				$param = '${' . $key . '}';
				/*if (strpos($question, $param) === false)
					throw new AMTException('Not all given parameters are in the HTML template.');*/
				$basequestion = str_replace($param, $val, $basequestion);
			}
			$questions[] = $basequestion;
			if(preg_match('#\$\{[A-Za-z0-9_.]*\}#', $basequestion) == 1) // ${...}
				throw new AMTException('HTML contains parameters that are not given in the CSV.');
		}
		
		return $questions;
	}


	/**
	* Create a HIT from a template (html for the question, xml or Hit for the rest) with parameters, and upload it to AMT
	* @param string $templateName The name of the html and xml template files in the templates directory (without extension).
	* @param string[] $params the parameters to be injected in the template (key=>val)
	* @param Hit $templateHit Optional. Used instead of the xml template. We still need the templateName for the question.
	* @return string the HITId of the created HIT.
	* @throws AMTException when one of the files does not exist, or when the parameters and template don't match.
	*/
	public function createSingle($templateName, $params, $templateHit = null){
		if(isset($templateHit)) $hit = $templateHit;
		else $hit = $this->hitFromTemplate($templateName);

		$hit->setQuestion($this->questionFromHTML($templateName, $params));
		$id = $this->mturk->createHIT($hit); 
		return $id;
	}
	
	
	/**
	* Take a template and parameters, join them and instantiate a Hit object.
	* @param string $templateName the name of the template files in the templates/ directory.
	* @param string[] $params the parameters to be injected in the template (key=>val) 
	* @return Hit
	* @throws AMTException when one of the files does not exist, or when the parameters and template don't match.
	*/
	public function hitFromTemplate($templateName, $params = null){
		$hit = $this->hitFromXML($templateName);
		if(isset($params)) // If the params aren't set, we don't set the question. Note that the Hit isn't ready to be uploaded this way.
			$hit->setQuestion($this->questionFromHTML($templateName, $params));
	
		return $hit;
	}

	
	/**
	* Find the paramaters in a template.
	* @param string $templateName
	* @return string[] The parameters that have to be set.
	* @throws AMTException when the file does not exist or is not readable.
	*/
	public function findParameters($templateName){
		$filename = "{$this->templatePath}$templateName.html";
		if(!file_exists($filename) || !is_readable($filename))
			throw new AMTException('HTML template file does not exist or is not readable.');
	
		$template = file_get_contents($filename);
		if(preg_match_all('#\$\{[A-Za-z0-9_.]*\}#', $template, $arr) == 0)
			throw new AMTException('No parameters found in HTML template.');
		
		$ret = array();
		foreach($arr[0] as $a) 
			$ret[] = trim($a, '${}');

		return array_unique($ret);		
	}
	
	
	/**
	* Find the questionId's in a template.
	* @param string $templateName
	* @return string[] The questionId's (name attribute of inputs).
	* @throws AMTException when the file does not exist or is not readable.
	*/
	public function findQuestionIds($templateName){
		$filename = "{$this->templatePath}$templateName.html";
		if(!file_exists($filename) || !is_readable($filename))
			throw new AMTException('HTML template file does not exist or is not readable.');
	
		$ret = array();
		$html = file_get_html($filename); //HTMLDomParser::file_get_html($filename)
		foreach($html->find('input') as $input)
			if(isset($input->name)) $ret[] = $input->name;
		foreach($html->find('textarea') as $input)
			if(isset($input->name)) $ret[] = $input->name;	
		foreach($html->find('select') as $input)
			if(isset($input->name)) $ret[] = $input->name;	
		return array_unique($ret); // Unique because checkboxes and radiobuttons have the same name.
	}
	
	
	/**
	* Convert an XML file to a new Hit() (without a question)
	* @param string $templateName The name of the html and xml template files in the templates directory (without extension).
	* @return Hit
	* @throws AMTException when one of the files does not exist or is not readable.
	*/
	private function hitFromXML($templateName){ 
		$filename = "{$this->templatePath}$templateName.xml";
		if(!file_exists($filename) || !is_readable($filename))
			throw new AMTException('XML template file does not exist or is not readable.');
		
		$xml = simplexml_load_file($filename);	
		if(!$xml) throw new AMTException('XML template file not formatted correctly.');
		
		$hitxml = $xml->xpath('/HIT');
		return new Hit($hitxml);
	}
	
	/**
	* Create the Question for an AMT multipage HIT
	*/
	private function addMultipageQuestion($hit, $templateName, $paramsArray, $answerfield, $frameheight = 650){
		$filename = "{$this->templatePath}$templateName.html";
		if(!file_exists($filename) || !is_readable($filename))
			throw new AMTException('HTML template file does not exist or is not readable.');

		// Read the file, extract the juice and check if the format is correct.
		$dom = file_get_html($filename);
		if(!$div = $dom->find('div[id=wizard]', 0))
			throw new AMTException('Multipage template has no div with id \'wizard\'. View the readme in the templates directory for more info.');
		
		if(!$div->find('h1', 0))
			throw new AMTException('Multipage template has no <h1>. View the readme in the templates directory for more info.');
		
		$questiontemplate = $div->innertext;
		if(!strpos($questiontemplate, 'Q{x}'))
			throw new AMTException('Multipage template has no \'Q{x}\'. View the readme in the templates directory for more info.');

		$questionsbuilder = '';
		$count = 0;
		$assRevPol = $hit->getAssignmentReviewPolicy();
		foreach ($paramsArray as $params) {
			$tempquestiontemplate = str_replace('{x}', $count, $questiontemplate);

			// -- IF isset($hit->getAssignmentReviewPolicy['Parameters'])
			// ELSE [issue warning? Throw exception?]

			if(isset($params['_golden']) and $params['_golden'] == true and isset($answerfield)) {
				$assRevPol['AnswerKey']["Q$count"] = $params[$answerfield];
			}

			foreach ($params as $key=>$val)	{	
				$param = '${' . $key . '}';
				$tempquestiontemplate = str_replace($param, $val, $tempquestiontemplate);
			}

			$questionsbuilder .= $tempquestiontemplate;
			$count++;			
		}

		$dom->find('div[id=wizard]', 0)->innertext = $questionsbuilder;
		$html = $dom->save();

		$hit->setQuestion($this->makeQuestion($html, $frameheight));
		if(count($assRevPol)>0)
			$hit->setAssignmentReviewPolicy($assRevPol);

		return $hit;
	}
	

	/**
	* Fill a HTML Question template with an associative array of parameters. 
	* @param string $templateName The name of the html and xml template files in the templates directory (without extension).
	* @param string[] $params An associative array of the parameters that will be replaced in the template.
	* @param int frameheight the height of the questionframe that will be shown to the worker.
	* @return string an HTMLQuestion, ready to be added to a Hit object and sent to AMT.
	* @throws AMTException when the file is not readable or the template and params don't match.
	*/
	private function questionFromHTML($templateName, $params, $frameheight = 650){
		$filename = "{$this->templatePath}$templateName.html";
		if(!file_exists($filename) || !is_readable($filename))
			throw new AMTException('HTML template file does not exist or is not readable.');
	
		$template = file_get_contents($filename);
		foreach ($params as $key=>$val)	{	
			$param = '${' . $key . '}';
			$template = str_replace($param, $val, $template);
		}
		
		if(preg_match('#\$\{[A-Za-z0-9_.]*\}#', $template) == 1) // ${...}
			throw new AMTException('HTML contains parameters that are not in the CSV.');
	
		return $this->makeQuestion($template, $frameheight);
	}


	/**
	* Convert the HTML form a template (with parameters injected) to a proper AMT Question.
	* @param string $html 
	* @return string AMT HTMLQuestion.
	*/
	private function makeQuestion($html, $frameheight = 650){
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
	public function csv_to_array($filename, $delimiter=',')
	{
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

	public function setTemplatePath($templatePath){
		$this->templatePath = $templatePath;
	}
	
	

}


?>