<?php

use \MongoDB\Entity;
use \MongoDB\Activity;

class JobConfiguration extends Entity {
	protected $guarded = array();
	protected $attributes = array(  'format' => 'text', 
                                    'domain' => 'medical', 
                                    'documentType' => 'jobconf', 
                                    'type' => 'todo');

    /**
    *   Override the standard query to include documenttype.
    */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('documentType', 'jobconf');
        return $query;
    }

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
                     Log::debug("Saved JobConfiguration with activity {$jobconf->activity_id}.");
                } catch (Exception $e) {

                    if($activity) $activity->forceDelete();
                    if($jobconf) $jobconf->forceDelete();
                    throw new Exception('Error saving activity for JobConfiguration.');
                }
            }
        });

     } 

    protected $justusedasareferencenow = array(
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

	
	public function toHTML($array = 'no array', $class = "table"){
		if($array=='no array') $array = $this->content;
		
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

}
	

?>
