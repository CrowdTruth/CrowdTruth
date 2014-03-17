<?php
use Sunra\PhpSimple\HtmlDomParser;
use \MongoDB\Entity;
use \MongoDB\Activity;
use \MongoDB\SoftwareAgent;

class Job  { 
    
    public $batch;
    public $template;
    public $jobConfiguration;
    
    protected $jcid;
    protected $activityURI;
    protected $questionTemplate_id;

    /**
    * @param $batch Batch
    * @param $template string just the name of the template
    * @param $jobConfiguration JobConfiguration
    */
    public function __construct($batch, $template, $jobConfiguration, $questiontemplateid){
    	$this->batch = $batch;
    	$this->template = Config::get('config.templatedir') . $template;
    	$this->CFApiKey = Config::get('config.cfapikey');
    	$this->jobConfiguration = $jobConfiguration;
    	$this->questionTemplate_id = $questiontemplateid;
    }

   
    public function publish($sandbox = false){
		
		if(isset($this->jobConfiguration->platform)) $platforms = $this->jobConfiguration->platform;
		else $platforms = array();
		$ids = array();

		try {

			$this->createSoftwareAgent('jobcreator');
			$this->activityURI = $this->createActivity($platforms);
			$this->jcid = $this->jobConfiguration->store(null, $this->activityURI);
			
			foreach($platforms as $platformstring){
				$platform = App::make($platformstring);
				$ids[$platformstring]['platformjobid'] = $platform->publishJob($this, $sandbox);
				$ids[$platformstring]['entity'] = $this->store($platformstring, $ids[$platformstring]['platformjobid'], $sandbox);
			}

			return $ids;

		} catch (Exception $e) {
			$this->undoCreation($ids, $e);
			throw $e; // TODO this is for debugging
			//throw new Exception($e->getMessage() . " Jobs(s) not created.");
		}

    }

    /** 
    * In case of exception: undo everything.
    * @throws Exception if even the undo isn't working. 
    */
    private function undoCreation($ids, $error){
    					
    	Log::debug("Error in creating jobs. Id's: " . implode(', ', array_dot($ids)) . ". Attempting to delete jobs from crowdsourcing platform(s).");
    	try {
    		
    		foreach($ids as $platformstring=>$id){
/*    			print_r($platformstring);
    			print_r($id);
    			dd('$ids');*/
    			if(isset($id['platformjobid'])){
    				$platform = App::make($platformstring);
    				$platform->undoCreation($id['platformjobid']);
    			}

    			if(isset($id['entity'])){
					$entity = Entity::where('_id', $id['entity'])->first();
					if($entity) $entity->forceDelete();
    			}
    		}
    		
    		$activity = Activity::where('_id', $this->activityURI)->first();
			if($activity) $activity->forceDelete();

		} catch (Exception $e){

			// This is bad.
			$orige = $error->getMessage();
			$newe = $e->getMessage();
			throw new Exception("WARNING. There was an error in uploading the jobs. We could not undo all the steps. 
				Please check the platforms manually and delete any uploaded jobs.
				<br>Initial exception: $orige
				<br>Deletion error: $newe
				<br>Please contact an administrator.");
			Log::warning("Couldn't delete jobs. Please manually check the platforms and database.\r\nInitial exception: $orige
				\r\nDeletion error: $newe\r\nActivity: {$this->activityURI}\r\nJob ID's: " . implode(', ', array_dot($ids)));

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
