<?php
use \MongoDB\Entity;
use \MongoDB\Activity;
use \MongoDB\SoftwareAgent;

class Job extends Entity { 
    
	protected $attributes = array('documentType' => 'job');

    /**
    *   Override the standard query to include documenttype.
    */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('documentType', 'job');
        return $query;
    }

	public static function boot ()
    {
        parent::boot();

        static::saving(function( $job )
        {
            \Log::debug('Clearing jobCache and mainSearchFilters.');
            \MongoDB\Temp::whereIn('_id', ['mainSearchFilters', 'jobCache', $job->_id])->forceDelete();
        });



        static::creating(function ( $job )
        {

		try {
            if(!SoftwareAgent::find('jobcreator')){
                $softwareAgent = new SoftwareAgent;
                $softwareAgent->_id = 'jobcreator';
                $softwareAgent->label = "Job creation";
            }

			if(!isset($job->projectedCost)){
				$reward = $job->jobConfiguration->content['reward'];
				$annotationsPerUnit = intval($job->jobConfiguration->content['annotationsPerUnit']);
				$unitsPerTask = intval($job->jobConfiguration->content['unitsPerTask']);
				$unitsCount = count($job->batch->wasDerivedFrom);
                if(!$unitsPerTask)
                    $projectedCost = 0;
                else
				    $projectedCost = round(($reward/$unitsPerTask)*($unitsCount*$annotationsPerUnit), 2);

                $job->expectedAnnotationsCount=$unitsCount*$job->jobConfiguration->content['annotationsPerUnit'];
                $job->projectedCost = $projectedCost;
				$job->unitsCount = $unitsCount;
                $job->latestMetrics = 0;
				$job->annotationsCount = 0;
				$job->completion = 0.00; // 0.00-1.00
				
			}

			if(!isset($job->activity_id)){
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
    * @throws Exception
    */
    public function publish($sandbox = false){
    	try {
	    	$response = $this->getPlatform()->publishJob($this, $sandbox);
	    	$this->platformJobId = (string) $response['id']; // NB: mongo is strictly typed and CF has Int jobid's!!!
	    	
            if(isset($response['url']))
                $this->url = $response['url'];

            $this->status = ($sandbox ? 'unordered' : 'running');
	    	$this->update();
    	} catch (Exception $e) {
            Log::debug("Error creating job: {$e->getMessage()}");
    		$this->undoCreation($this->platformJobId, $e);
    		$this->forceDelete();
			throw $e; 
    	}
    }

    public function order(){
    	$this->getPlatform()->orderJob($this);
    	$this->status = 'running';
        if(empty($this->startedAt))
            $this->startedAt = new MongoDate;
    	$this->update();
    }

    public function pause(){
    	$this->getPlatform()->pauseJob($this->platformJobId);
    	$this->status = 'paused';
    	$this->update();
    }

    public function resume(){
    	$this->getPlatform()->resumeJob($this->platformJobId);
    	$this->status = 'running';
    	$this->update();
    }

    public function cancel(){
    	$this->getPlatform()->cancelJob($this->platformJobId);
    	$this->status = 'cancelled';
    	$this->update();
    }

    private function getPlatform(){
    	if(!isset($this->softwareAgent_id)) // and (!isset($this->platformJobId) !!! TODO
    		throw new Exception('Can\'t handle a Job that has not yet been uploaded to a platform.');

    	return App::make($this->softwareAgent_id);
    }

    /** 
    * In case of exception: undo everything.
    * @throws Exception if even the undo isn't working. 
    */
    private function undoCreation($ids, $error = null){
    	// TODO use platformjobid.				
    	Log::debug("Attempting to delete jobs from crowdsourcing platform.");
    	
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
    }


	/**
	* Find the questionId's in a template.
	* @return string[] The questionId's (name attribute of inputs).
	* @throws AMTException when the file does not exist or is not readable.
	*/
	public function getQuestionIds(){
		return array('todo'); // This will be moved to QuestionTemplate
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

    public function annotations(){
        return $this->hasMany('Annotation', 'job_id', '_id');
    }


}
?>
