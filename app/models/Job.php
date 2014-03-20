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
    		throw new Exception('Can\'t handle Job that has not yet been uploaded to a platform.');

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
    }


	/**
	* Find the questionId's in a template.
	* @return string[] The questionId's (name attribute of inputs).
	* @throws AMTException when the file does not exist or is not readable.
	*/
	public function getQuestionIds(){
		return array('todo'); // This will be moved to QuestionTemplate
	}


    /*	 Private functions    */

    /**
    * @return String id of published Job
    */
    private function cfPublish($sandbox){
    	$cfJob = new crowdwatson\Job($this->CFApiKey);

    	$c = $this->jobConfiguration;
		$data = $c->toCFData();
		$gold = $c->answerfields;
		$csv = $this->batch->toCFCSV();
		$template = $this->template;
		$options = array(	"req_ttl_in_seconds" => $c->expirationInMinutes*60, 
							"keywords" => $c->requesterAnnotation, 
							"mail_to" => $c->notificationEmail);
    	try {

    		// TODO: check if all the parameters are in the csv.
			// Read the files
			foreach(array('cml', 'css', 'js') as $ext){
				$filename = "$template.$ext";
				if(file_exists($filename) || is_readable($filename))
					$data[$ext] = file_get_contents($filename);
			}

			if(empty($data['cml']))
				throw new CFExceptions('CML file does not exist or is not readable.');


			/*if(!$sandbox) $data['auto_order'] = true;*/

			// Create the job with the initial data
			$result = $cfJob->createJob($data);
			$id = $result['result']['id'];

			// Add CSV and options
			if(isset($id)) {
				
				// Not in API or problems with API: 
				//  - Channels (we can only order on cf_internal)
				//  - Tags / keywords
				//  - Worker levels (defaults to '1')
				//  - Expiration?

				$csvresult = $cfJob->uploadInputFile($id, $csv);
				unlink($csv); // DELETE temporary CSV.
				if(isset($csvresult['result']['error']))
					throw new CFExceptions("CSV: " . $csvresult['result']['error']['message']);

				$optionsresult = $cfJob->setOptions($id, array('options' => $options));
				if(isset($optionsresult['result']['error']))
					throw new CFExceptions("setOptions: " . $optionsresult['result']['error']['message']);

				$channelsresult = $cfJob->setChannels($id, array('cf_internal'));
				if(isset($channelsresult['result']['error']))
					throw new CFExceptions($channelsresult['result']['error']['message']); 

				if(is_array($gold) and count($gold) > 0){
					// TODO: Foreach? 
					$goldresult = $cfJob->manageGold($id, array('check' => $gold[0]));
					if(isset($goldresult['result']['error']))
						throw new CFExceptions("Gold: " . $goldresult['result']['error']['message']);
				}

				if(is_array($c->countries) and count($c->countries) > 0){
					$countriesresult = $cfJob->setIncludedCountries($id, $c->countries);
					if(isset($countriesresult['result']['error']))
						throw new CFExceptions("Countries: " . $countriesresult['result']['error']['message']);
				}


				if(!$sandbox){
					$orderresult = $cfJob->sendOrder($id, count($this->batch->ancestors), array("cf_internal"));
					if(isset($orderresult['result']['error']))
						throw new CFExceptions("Order: " . $orderresult['result']['error']['message']);
				}

				return $id;

			// Failed to create initial job.
			} else {
				$err = $result['result']['error']['message'];
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

    public function jobConfiguration(){
        return $this->hasOne('JobConfiguration', '_id', 'jobConf_id');
    }

    public function questionTemplate(){
        return $this->hasOne('QuestionTemplate', '_id', 'questionTemplate_id');
    }

    public function batch(){
        return $this->hasOne('Batch', '_id', 'batch_id');
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
