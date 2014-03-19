<?php

use \mongoDB\Entity;
use \mongoDB\Activity;

class JobConfiguration extends Entity {
	protected $guarded = array();

	protected $attributes = array(  'format' => 'text', 
                                    'domain' => 'medical', 
                                    'documentType' => 'jobconf', 
                                    'type' => 'todo');

    public static function boot ()
    {
        parent::boot();

        static::saving(function ( $jobconf )
        {
        	// IFEXISTS CHECK IS NOT HERE.

            if(empty($jobconf->activity_id)){
                try {
                    $activity = new Activity;
                    $activity->label = "JobConfiguration is saved.";
                    $activity->softwareAgent_id = 'jobcreator';
                    $activity->save();
                    $jobconf->activity_id = $activity->_id;

                } catch (Exception $e) {

                    if($activity) $activity->forceDelete();
                    if($jobconf) $jobconf->forceDelete();
                    throw new Exception('Error saving activity for JobConfiguration.');
                }
            }

             Log::debug("Saved entity {$jobconf->_id} with activity {$jobconf->activity_id}.");
        });

     } 

    protected $thisusedtobefillablebutisjustusedasareferencenow = array(
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

/*    public addFields($array){
    	$this->fillable = array_merge($this->fillable, $array);
    }*/

    private $errors;

    private $commonrules = array(
		'title' => 'required|between:5,128',
		'description' => 'required|between:5,2000',		
		'reward' => 'required|numeric', 
		'expirationInMinutes' => 'required|numeric', /* AMT: assignmentDurationInSeconds */
		'platform' => 'required'
	);

    public function validate()  {
    	$rules = $this->commonrules;
    	$this->errors = new Illuminate\Support\MessageBag();
	    $isok = true;

	    if(isset($this->content['platform'])){
		    foreach($this->content['platform'] as $platformstring){
		    	$platform = App::make($platformstring);
		    	$rules = array_merge($rules, $platform->jobConfValidationRules);
		    }	
   	 	} else {
   	 		$this->errors->add('platform', 'Please provide at least one platform.');
   	 		$isok = false;
   	 	}

        $v = Validator::make($this->content, $rules);
        if ($v->fails()) {
            $this->errors->merge($v->messages()->toArray());
            $isok = false;
        }

        // TODO: add some custom validation rules.
        // Note: Job->previewQuestions also does some validation.

        return $isok;
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
		$jc = new JobConfiguration;
		$jc->content = $arr;

		return $jc;
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
				$activity->softwareAgent_id = 'jobcreator';

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
			$entity->type = "todo";
			//$entity->tags = array('bla', 'bla', 'bla'); // OR: title

			$entity->content = $newEntityContent;
			$entity->hash = $hash;
			
			// Ancestors 
			// TODO: move this to ENTITY?
			if(!is_null($originalEntity)){
				$this->parents = array($originalEntity->_id);
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