<?php
use Sunra\PhpSimple\HtmlDomParser;
use \MongoDB\Entity;
use \MongoDB\Activity;
use \MongoDB\SoftwareAgent;

class Job extends Entity { 
    
   // public $batch;
  //  public $template;
    //public $jobConfiguration;
/*    
    protected $jcid;
    protected $activityURI;
    protected $questionTemplate_id;*/
	protected $attributes = array(  'format' => 'text', 
                                    'domain' => 'medical', 
                                    'documentType' => 'job', 
                                    'type' => 'todo');

	public static function boot ()
    {
        parent::boot();

        static::saving(function ( $job )
        {

		try {
			$job->createSoftwareAgent('jobcreator');
			
			if(!isset($job->projectedCost)){
				//$jobConfiguration = JobConfiguration::where('_id', $job->jobConf_id)->first(); 

				$reward = $job->jobConfiguration->content['reward'];
				$annotationsPerUnit = intval($job->jobConfiguration->content['annotationsPerUnit']);
				$unitsPerTask = intval($job->jobConfiguration->content['unitsPerTask']);
				$unitsCount = count($job->batch->wasDerivedFrom);
				$projectedCost = round(($reward/$unitsPerTask)*($unitsCount*$annotationsPerUnit), 2);

				$job->unitsCount = $unitsCount;
				$job->annotationsCount = 0;
				$job->completion = 0.00; // 0.00-1.00
				$job->projectedCost = $projectedCost;
			}

			if(empty($job->activity_id)){
		    	$activity = new Activity;
				$activity->label = "Job is uploaded to crowdsourcing platform.";
				$activity->softwareAgent_id = 'jobcreator'; // TODO: JOB softwareAgent_id = $platform. Does this need to be the same?
				$activity->save();
				$job->activity_id = $activity->_id;
			}
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$job->forceDelete();
			throw $e;
		}
             Log::debug("Saved entity {$job->_id} with activity {$job->activity_id}.");
        });

     } 

   
    /**
    * @return ids
    * @throws Exception
    */
    public function publish($sandbox = false){
    	try {
	    	$platformJobId = $this->getPlatform()->publishJob($this, $sandbox);
	    	$this->platformJobId = $platformJobId; // NB: mongo is strictly typed and CF has Int jobid's!!!
	    	$this->status = ($sandbox ? 'unordered' : 'running');
	    	$this->save();
    	} catch (Exception $e) {
    		$this->undoCreation($this->platformJobId, $e);
    		$this->forceDelete();
			throw $e; 
    	}
    }

    public function order(){
    	$this->getPlatform()->orderJob($this->platformJobId);
    	$this->status = 'running';
    	$this->save();
    }

    public function pause(){
    	$this->getPlatform()->pauseJob($this->platformJobId);
    	$this->status = 'paused';
    	$this->save();
    }

    public function resume(){
    	$this->getPlatform()->resumeJob($this->platformJobId);
    	$this->status = 'running';
    	$this->save();
    }

    public function cancel(){
    	$this->getPlatform()->cancelJob($this->platformJobId);
    	$this->status = 'cancelled';
    	$this->save();
    }

    private function getPlatform(){
    	if(!isset($this->softwareAgent_id)) // and (!isset($this->platformJobId) !!! TODO
    		throw new Exception('Can\'t order Job or cthat has not yet been uploaded to a platform.');

    	return App::make($this->softwareAgent_id);
    }

    /** 
    * In case of exception: undo everything.
    * @throws Exception if even the undo isn't working. 
    */
    private function undoCreation($ids, $error = null){
    	// TODO use platformjobid.				
    	Log::debug("Error in creating jobs. Id's: " . json_encode($ids) . ". Attempting to delete jobs from crowdsourcing platform(s).");
    	
    	try {
    		$this->getPlatform()->undoCreation($ids);
    	} catch (Exception $e){

			// This is bad.
			if($error) $orige = $error->getMessage();
			else $orige = 'None.';
			$newe = $e->getMessage();
			throw new Exception("WARNING. There was an error in uploading the jobs. We could not undo all the steps. 
				Please check the platforms manually and delete any uploaded jobs.
				<br>Initial exception: $orige
				<br>Deletion error: $newe
				<br>Please contact an administrator.");
			Log::warning("Couldn't delete jobs. Please manually check the platforms and database.\r\nInitial exception: $orige
				\r\nDeletion error: $newe\r\nActivity: {$this->activityURI}\r\nJob ID's: " . json_encode($ids));

		}
    }

    /** 
    * @return String[] the HTML for every question.
    */
    public function getPreviews(){
    	return array('todo');
    	//throw new AMTException('b'); // TODO
    	//return $this->amtPublish(true);
    }

    /**
    * @return String[] fields from the CSV that have a gold answer.
    */
    public function getGoldFields(){
    	return array('todo');
/*
    	// TODO //
    	$goldfields = array();
    	foreach (array_keys($this->batch->toCFArray()[0]) as $key)
			if ($key != '_golden' and $pos = strpos($key, '_gold') and !strpos($key, '_gold_reason'))
				$goldfields[$key] = substr($key, 0, $pos);	
    	return $goldfields;*/
    }


	/**
	* Find the questionId's in a template.
	* @return string[] The questionId's (name attribute of inputs).
	* @throws AMTException when the file does not exist or is not readable.
	*/
	public function getQuestionIds(){
		return array('todo');
/*		
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

		return array_unique($ret); // Unique because checkboxes and radiobuttons have the same name.*/
	}


    /**
    * Save Job to database
    * @param $platform string amt | cf
    * @param $platformjobid array if $platform = amt, int if $platform = cf.
    * @param $preview boolean sets the status to 'unordered'
    * @return entity id
    */
    public function store($platform, $platformJobId, $preview = false){

    	//$this->createSoftwareAgent($platform);

		if($preview) $status = 'unordered';
		else $status = 'running';

		try {
			$reward = $this->jobConfiguration->reward;
			$annotationsPerUnit = intval($this->jobConfiguration->annotationsPerUnit);
			$unitsPerTask = intval($this->jobConfiguration->unitsPerTask);
			$unitsCount = count($this->batch->wasDerivedFrom);
			$projectedCost = round(($reward/$unitsPerTask)*($unitsCount*$annotationsPerUnit), 2);

			$entity = new Entity;
			$entity->domain = 'medical';
			$entity->format = 'text';
			$entity->type = 'todo'; // TODO: need to set this somewhere
			$entity->documentType = 'job';
			$entity->activity_id = $this->activityURI;
			
			$entity->jobConf_id = $this->jcid;
			//$entity->template_id = $this->template; // Will probably be part of jobconf
			$entity->batch_id = $this->batch->_id;
			$entity->softwareAgent_id = $platform;
			$entity->platformJobId = $platformJobId; // NB: mongo is strictly typed and CF has Int jobid's!!!
			$entity->questionTemplate_id = $this->questionTemplate_id;

			$entity->unitsCount = $unitsCount;
			$entity->annotationsCount = 0;
			$entity->completion = 0.00; // 0.00-1.00
			$entity->projectedCost = $projectedCost;

			$entity->status = $status;

			$entity->save();
			return $entity->_id;
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$entity->forceDelete();
			throw $e;
		}
    }

    public function jobConfiguration(){
        return $this->hasOne('JobConfiguration', '_id', 'jobConf_id');
    }

    public function questionTemplate(){
        return $this->hasOne('QuestionTemplate', '_id', 'questionTemplate_id');
    }

    public function batch(){
        return $this->hasOne('Batch', '_id', 'batch_id');
    }

    private function createActivity($platforms){
    	$activity = new Activity;
		$activity->label = "Job is uploaded to crowdsourcing platform(s): " . implode(', ', $platforms) . ".";
		$activity->softwareAgent_id = 'jobcreator'; // TODO: JOB softwareAgent_id = $platform. Does this need to be the same?
		$activity->save();
		return $activity->_id;
    }

    private function createSoftwareAgent($agentid){
		if(!SoftwareAgent::find($agentid))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = $agentid;

			if($agentid == 'amt')
				$softwareAgent->label = "Crowdsourcing platform: Amazon Mechanical Turk";
			elseif ($agentid == 'cf')
				$softwareAgent->label = "Crowdsourcing platform: CrowdFlower";
			elseif ($agentid = 'jobcreator')
				$softwareAgent->label = "Job creation";
	
			$softwareAgent->save();
		}
	}




}
?>
