<?php

use crowdwatson\Hit;
use \mongoDB\Entity;
use \mongoDB\Activity;

class JobConfiguration extends Moloquent {
    protected $fillable = array(
    								'title', 
    								'description',
    								'instructions', /* AMT: inject into template */ 
    								'keywords', 
    								'annotationsPerUnit', /* AMT: maxAssignments */
    								'unitsPerTask', /* AMT: not in API. Would be 'tasks per assignment' */
    								'reward', 
    								'expirationInMinutes', /* AMT: assignmentDurationInSeconds */
    								'notificationEmail',
    								'requesterAnnotation',
    								'instructions',

    								/* AMT specific */
    	    						'autoApprovalDelayInMinutes', /* AMT API: AutoApprovalDelayInSeconds */
									'hitLifetimeInMinutes', 
									'qualificationRequirement',
									'assignmentReviewPolicy', 
									'frameheight',
									'eventType',

    	    						/* CF specific */
    	    						'annotationsPerWorker',
    	    						'countries',

    	    						/* for our use */
    	    						'answerfields', /* The fields of the CSV file that contain the gold answers. */
    								'platform',
    								'questionTemplate_id'
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
		'annotationsPerUnit' => 'required|numeric|min:1', /* AMT: defaults to 1 */
		'unitsPerTask' => 'required|numeric|min:1',
		'instructions' => 'required',
		'annotationsPerWorker' => 'required|numeric|min:1'
	);	

	private $amtrules = array(
		'hitLifetimeInMinutes' => 'required|numeric|min:1',
		'frameheight' => 'numeric|min:300' // not required because we have a default value.
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


    public function getDetails(){
    	return array('keywords' => $this->keywords, 'expirationInMinutes' => $this->expirationInMinutes, 'lifetimeInSeconds' => $this->lifetimeInSeconds, 'autoApprovalDelayInSeconds' => $this->autoApprovalDelayInMinutes, 'qualificationRequirement' => $this->qualificationRequirement, 'assignmentReviewPolicy' => $this->assignmentReviewPolicy );
    }

    public function getElapsedTime($created_at){
	    $time = time() - strtotime($created_at); // to get the time since that moment

    	$tokens = array (
        	31536000 => 'yr',
        	2592000 => 'm',
        	604800 => 'w',
        	86400 => 'day',
        	3600 => 'hr',
        	60 => 'min',
        	1 => 'sec'
	    );

	    foreach ($tokens as $unit => $text) {
	        if ($time < $unit) continue;
	        $numberOfUnits = floor($time / $unit);
	        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
	    	}
	}

    //FIELDS IN LARAVEL -_-
    public function totalJudgments(){
    	return $this->annotationsPerUnit*$this->unitsPerTask;
    }

	public function totalCost(){
		$judgments = JobConfiguration::totalJudgments();
		return '$ ' + round($judgments*$this->reward, 2);
	}

	public function progressBar(){
		return round(($this->completedJudgments() / $this->totalJudgments())*100);
	}
		

	public function completedJudgments(){
		return 20;
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

	// Used now, for HITs that don't come from our own platform
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

			/* AMT */
			'autoApprovalDelayInMinutes' => intval($hit->getAutoApprovalDelayInSeconds())/60,
			'qualificationRequirement'=> $hit->getQualificationRequirement(),
			'assignmentReviewPolicy' => $hit->getAssignmentReviewPolicy(),
			'platform' => array('amt')		
			));
	}

	public static function getTemplate(){
		return implode(",", $template);
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
		if (!empty($this->annotationsPerUnit)) 			$hit->setMaxAssignments		  			($this->annotationsPerUnit);
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
		$data = array();

		if (!empty($this->title)) 			 	$data['title']					 	= $this->title; 
		if (!empty($this->instructions)) 		$data['instructions']				= $this->instructions; 
		if (!empty($this->annotationsPerUnit)) 	$data['judgments_per_unit']		  	= $this->annotationsPerUnit;

		if (!empty($this->unitsPerTask))		$data['units_per_assignment']		= $this->unitsPerTask;
		if (!empty($this->annotationsPerWorker))	{
			$data['max_judgments_per_worker']	= $this->annotationsPerWorker;
			$data['max_judgments_per_ip']		= $this->annotationsPerWorker; // We choose to keep this the same.
		}

		// Webhook doesn't work on localhost and we the uri should be set. 
		if((App::environment() != 'local') and (Config::get('config.cfwebhookuri')) != ''){
			
			$data['webhook_uri'] = Config::get('config.cfwebhookuri');
			$data['send_judgments_webhook'] = 'true';
		}
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
	    if (empty($array) || !is_array($array))
	        return false;
	 
	    if (!isset($array[0]) || !is_array($array[0]))
	        $array = array($array);
	 
	    // Header row
	    $table = "<table>\n\t<tr>";
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
	            if (is_object($cell)) $cell = (array) $cell;
	            if ($recursive === true && is_array($cell) && !empty($cell))
	                $table .= "\n" . $this->array2table($cell, true, true) . "\n";
	            else
	                $table .= (strlen($cell) > 0) ? htmlspecialchars((string) $cell) : $null;
	 
	            $table .= '</td>';
	        }
	 
	        $table .= "</tr>\n";
	    }
	 
	    $table .= '</table>';
	    return $table;
	}

	/**
	* Saves the JobConfiguration, along with an activity, to the DB.
	* @return entity id (existing or new one)
	* @throws Exception (deletes the created entities when exception is thrown)
	*/
	public function store($originalEntity = null, $activityURI = null){
		$entity = new Entity;
		$activity = new Activity;

		try {

			$newEntityContent = $this->toArray();
			
			// Return the existing entity URI if it exists already.
			// What if we want to save the same JobConf with different tags / title?
			// Option: make an updateTags() function or something.
			$hash = md5(serialize($newEntityContent));
			$existing = Entity::where('hash', $hash)->pluck('_id');
			if($existing) 
				return $existing;
		 	
		 	// Create a new activity, but only if there isn't one in the parameters.
			if (is_null($activityURI)){
				$activity->label = "JobConfiguration is saved.";
				$activity->software_id = 'jobcreator';

				if(!is_null($originalEntity)) 
					$activity->entity_used_id = $originalEntity->_id;

				$activity->save();
				$activityURI = $activity->_id;
			}

			// Create the entity
			
			// Mandatory
			$entity->domain = "medical";
			$entity->format = "text";
			$entity->documentType = "jobconf";
			$entity->activity_id = $activityURI;
			
			// Further identification
			$entity->type = "factor_span";
			//$entity->tags = array('bla', 'bla', 'bla'); // OR: title

			$entity->content = $newEntityContent;
			$entity->hash = $hash;
			
			// Ancestors 
			// TODO: move this to ENTITY?
			if(!is_null($originalEntity)){
				$ancestors = $originalEntity->ancestors;
				if(is_array($ancestors))
					array_push($ancestors, $originalEntity->_id);
				else
					$ancestors = array($originalEntity->_id);

				$entity->ancestors = $ancestors;
			} 

			$entity->save();
			Log::debug("Saved entity {$entity->_id} and activity $activityURI.");

			return $entity->_id;
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$activity->forceDelete();
			$entity->forceDelete();
			throw $e;
		}
	}

}
	

?>