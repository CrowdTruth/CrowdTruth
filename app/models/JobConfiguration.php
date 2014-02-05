<?php

//use Jenssegers\Mongodb\Model as Eloquent;
use crowdwatson\Hit;

class JobConfiguration extends Moloquent {
    protected $fillable = array(
    								'title', 
    								'description',
    								'instructions', /* AMT: inject into template */ 
    								'keywords', 
    								'judgmentsPerUnit', /* AMT: maxAssignments */
    								'unitsPerTask', /* AMT: not in API. Would be 'tasks per assignment' */
    								'reward', 
    								'expirationInMinutes', /* AMT: assignmentDurationInSeconds */
    								'notificationEmail',
    								'requesterAnnotation',
    								'country', /* TODO: GUI */
    								

    								/* AMT specific */
    	    						'autoApprovalDelayInMinutes', /* AMT API: AutoApprovalDelayInSeconds */
									'hitLifetimeInMinutes', 
									'qualificationRequirement',
									'assignmentReviewPolicy', 
									'frameheight',

    	    						/* CF specific */
    	    						'judgmentsPerWorker',

    	    						/* for our use */
    	    						'answerfields', /* The fields of the CSV file that contain the gold answers. */
    								'platform'
    								);

    private $errors;

    private $commonrules = array(
		'title' => 'required|between:5,128',
		'description' => 'required|between:5,2000',		
		'reward' => 'required|numeric', 
		'expirationInMinutes' => 'required|numeric', /* AMT: assignmentDurationInSeconds */
		'platform' => 'required'
	);

	private $cfrules = array(
		'judgmentsPerUnit' => 'required|numeric|min:1', /* AMT: defaults to 1 */
		'unitsPerTask' => 'required|numeric|min:1',
		'instructions' => 'required',
		'judgmentsPerWorker' => 'required|numeric|min:1'
	);	

	private $amtrules = array(
		'hitLifetimeInMinutes' => 'required|numeric|min:1',
		'frameheight' => 'numeric|min:300'
	);


    public function validate()  {
    	$rules = $this->commonrules;
    	$this->errors = new Illuminate\Support\MessageBag();
	    $return = true;

	    if(is_array($this->platform)){
	    	if(in_array('amt', $this->platform))
	    		$rules = array_merge($rules, $this->amtrules);
	    	if(in_array('cf', $this->platform))
	    		$rules = array_merge($rules, $this->cfrules);
   	 	} else {
   	 		$this->errors->add('platform', 'Please provide at least one platform.');
   	 		$return = false;
   	 	}

        $v = Validator::make($this->toArray(), $rules);
        if ($v->fails()) {
            $this->errors->merge($v->messages()->toArray());
            $return = false;
        }

        // TODO: add some custom validation rules.
        // Note: Job->previewQuestions also does some validation.

        return $return;
    }

    public function getErrors() {
        return $this->errors;
    }

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

		return new JobConfiguration($arr);
	}


	public function toHit(){
		$hit = new Hit();
		if (!empty($this->title)) 			 			$hit->setTitle						  	($this->title); 
		if (!empty($this->description)) 		 		$hit->setDescription					($this->description); 
		if (!empty($this->keywords)) 					$hit->setKeywords				  		($this->keywords);
		if (!empty($this->judgmentsPerUnit)) 			$hit->setMaxAssignments		  			($this->judgmentsPerUnit);
		if (!empty($this->expirationInMinutes))			$hit->setAssignmentDurationInSeconds 	($this->expirationInMinutes*60);
		if (!empty($this->hitLifetimeInMinutes)) 		$hit->setLifetimeInSeconds		  		($this->hitLifetimeInMinutes*60);
		if (!empty($this->reward)) 						$hit->setReward					  		(array('Amount' => $this->reward, 'CurrencyCode' => 'USD'));
		if (!empty($this->autoApprovalDelayInMinutes)) 	$hit->setAutoApprovalDelayInSeconds  	($this->autoApprovalDelayInMinutes*60); 
		if (!empty($this->qualificationRequirement))	$hit->setQualificationRequirement		($this->qualificationRequirement);
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

	
	public function toHTML($array = 'no array', $class = "table"){
		if($array=='no array') $array = $this->toArray();
		
		$ret = "<table class='$class'>\r\n";
		foreach ($array as $key=>$val){
			$rc = '';
			if(is_numeric($key)) $head = ''; else $head = "<th>$key</th>";
			if(is_array($val)) 
				$ret .= "<tr>$head<td>{$this->toHTML($val, 'table table-condensed table-bordered')}</td></tr>\r\n";
			else {
				if(is_object($this->getErrors()) and $this->getErrors()->has($key)) 
					$rc = " class = 'danger'";
				$ret .= "<tr$rc>$head<td>$val</td></tr>\r\n";
			}
		}
		return $ret . "</table>\r\n";
	} 


	/**
	 * Translate a result array into a HTML table
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.3.2
	 * @link        http://aidanlister.com/2004/04/converting-arrays-to-human-readable-tables/
	 * @param       array  $array      The result (numericaly keyed, associative inner) array.
	 * @param       bool   $recursive  Recursively generate tables for multi-dimensional arrays
	 * @param       string $null       String to output for blank cells
	 */
	function array2table($array, $recursive = false, $null = '&nbsp;')
	{
	    // Sanity check
	    if (empty($array) || !is_array($array)) {
	        return false;
	    }
	 
	    if (!isset($array[0]) || !is_array($array[0])) {
	        $array = array($array);
	    }
	 
	    // Start the table
	    $table = "<table>\n";
	 
	    // The header
	    $table .= "\t<tr>";
	    // Take the keys from the first row as the headings
	    foreach (array_keys($array[0]) as $heading) {
	        $table .= '<th>' . $heading . '</th>';
	    }
	    $table .= "</tr>\n";
	 
	    // The body
	    foreach ($array as $row) {
	        $table .= "\t<tr>" ;
	        foreach ($row as $cell) {
	            $table .= '<td>';
	 
	            // Cast objects
	            if (is_object($cell)) { $cell = (array) $cell; }
	             
	            if ($recursive === true && is_array($cell) && !empty($cell)) {
	                // Recursive mode
	                $table .= "\n" . $this->array2table($cell, true, true) . "\n";
	            } else {
	                $table .= (strlen($cell) > 0) ?
	                    htmlspecialchars((string) $cell) :
	                    $null;
	            }
	 
	            $table .= '</td>';
	        }
	 
	        $table .= "</tr>\n";
	    }
	 
	    $table .= '</table>';
	    return $table;
	}

	// Not used (yet?)
	public static function getFromHit($hit){
		return new JobConfiguration(array(
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