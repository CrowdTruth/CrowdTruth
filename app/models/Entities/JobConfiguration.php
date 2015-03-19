<?php
/*
 * Main class for creating and managing job configurations
 * A job configuration is a type of entity
 * Configurations are reusable across jobs
*/

namespace Entities;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

class JobConfiguration extends Entity {
	protected $guarded = array();
	protected $attributes = array('documentType' => 'jobconf');

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

        static::creating(function ( $jobconf )
        {
        	// IFEXISTS CHECK IS NOT HERE.
            try{
                $c = $jobconf->content;
                if(isset($c['reward'])) $c['reward'] = (double) $c['reward'];
                if(isset($c['hitLifetimeInMinutes'])) $c['hitLifetimeInMinutes'] = intval($c['hitLifetimeInMinutes']);
                if(isset($c['autoApprovalDelayInMinutes'])) $c['autoApprovalDelayInMinutes'] = intval($c['autoApprovalDelayInMinutes']);
                if(isset($c['expirationInMinutes'])) $c['expirationInMinutes'] = intval($c['expirationInMinutes']);
                if(isset($c['workerunitsPerUnit'])) $c['workerunitsPerUnit'] = intval($c['workerunitsPerUnit']);
                if(isset($c['unitsPerTask'])){ $c['unitsPerTask'] = intval($c['unitsPerTask']);
                if($c['unitsPerTask'] == 0) $c['unitsPerTask'] = 1;}
                $jobconf->content = $c;
            } catch (Exception $e){
                if($jobconf) $jobconf->forceDelete();
                throw new Exception('Error saving JobConfiguration.');
            }

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

    private $errors;
    private $commonrules = array(
		'title' => 'required|between:5,128',
		'description' => 'required|between:5,2000',		
		'reward' => 'required|numeric', 
		'expirationInMinutes' => 'required|numeric', // AMT: assignmentDurationInSeconds
		'platform' => 'required'
	);

    public function validate()  {
    	$rules = $this->commonrules;
    	$this->errors = new Illuminate\Support\MessageBag();
	    $isok = true;

	    if(isset($this->content['platform']) and count($this->content['platform'])>0){
		    foreach($this->content['platform'] as $platformstring){
		    	$platform = App::make($platformstring);
		    	$rules = array_merge($rules, $platform->getJobConfValidationRules());
		    }	
   	 	} else {
   	 		$this->errors->add('platform', 'Please provide at least one <a href="/job/platform">platform</a>.');
   	 		$isok = false;
   	 	}

        $v = Validator::make($this->content, $rules);
        if ($v->fails()) {
            $this->errors->merge($v->messages()->toArray());
            $isok = false;
        }

        // TODO: add some custom validation rules.

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
		$jc = new JobConfiguration;

        try{
            if(!file_exists($filename) || !is_readable($filename))
    			throw new Exception('JSON template file does not exist or is not readable.');

    		$json = file_get_contents($filename);
    		if(!$arr = json_decode($json, true))
    			throw new Exception('JSON incorrectly formatted');

    		
    		$jc->content = $arr;
    		return $jc;
        } catch(Exception $e){
            Log::debug("$e - using empty JobConf");
            return $jc;
        }    
	}

    public function setValue($key, $value){
        $c = $this->content;
        $c[$key] = $value;
        $this->content = $c;
    }

    public function unsetKey($key){
        $c = $this->content;
        unset($c[$key]);
        $this->content = $c;
    }

}
	

?>
