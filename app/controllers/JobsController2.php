<?php
use Sunra\PhpSimple\HtmlDomParser;

use \Entities\File as File;
use \Entities\Unit as Unit;
use \Entities\Batch as Batch;
use \Entities\JobConfiguration as JobConfiguration;
use \Entities\Job as Job;
use \Template as Template;

use \Activity as Activity;

class JobsController2 extends BaseController {

    public function getIndex(){
        $mainSearchFilters = Temp::getMainSearchFiltersCache()['filters'];
        return View::make('media.search.pages.jobs2', compact('mainSearchFilters'));
    }

	public function getBatch() {
		$batches = Batch::where('type', 'batch')->get(); 
		return View::make('job2.tabs.batch')->with('batches', $batches)->with('selectedbatchid', '');
	}

	public function getTemplateFields($templateType, $jobConfId) {
		if ($templateType != null) {
			//$templateType = Input::get( 'templateType' );
        	$template = Template::where("type", $templateType)->first();
		}
		else {
			$jobConf = \Entity::where("_id", $jobConfId)->first();
			$template = array();
			$template["cml"] = $jobConf["content"]["cml"];
		}

		$simpleRegEx = "/{{([^.}}]*)}}/";

        preg_match_all($simpleRegEx, $template["cml"], $matchesCol, PREG_OFFSET_CAPTURE);
        $matches = array();
        foreach ($matchesCol[1] as $simpleMatch) {
        	if (strpos($simpleMatch[0], "|") === false) {
        		$newMatch = array();
				$newMatch["label"] = str_replace(" ", "", $simpleMatch[0]);
				$newMatch["startLabel"] = $simpleMatch[1];
				$found = false;
				foreach ($matches as $match) {
					if ($match["label"] == $newMatch["label"]) {
						$found = true;
					}
				}
				if ($found == false) {
					array_push($matches, $newMatch);
				}
			}
			else {
				$posLabel = str_replace(" ", "", $simpleMatch[0]);
				$posLabelArray = explode("|", $posLabel);
				$newMatch = array();
				$newMatch["label"] = $posLabelArray[0];
				$newMatch["startLabel"] = $simpleMatch[1];
				$found = false;
				foreach ($matches as $match) {
					if ($match["label"] == $newMatch["label"]) {
						$found = true;
					}
				}
				if ($found == false) {
					array_push($matches, $newMatch);
				}
			}
		}

		// for any other statement between {% %}
		$statements = "/{%\s*.*%}/";
		preg_match_all($statements, $template["cml"], $matchesStat, PREG_OFFSET_CAPTURE);

		$statementsForWithoutData = "/{%.*\([0-9]+\.{2}[0-9]+\).*%}/";
		preg_match_all($statementsForWithoutData, $template["cml"], $matchesStatWOData, PREG_OFFSET_CAPTURE);

		foreach ($matchesStatWOData[0] as $keyWO => $matcheStatWOData) {
			foreach ($matchesStat[0] as $key => $matchStat) {
				if ($matcheStatWOData[0] == $matchStat[0]) {
					unset($matchesStat[0][$key]);
				}
			}
		}

		$unusedArguments = array();

		foreach ($matchesStat[0] as $key => $matchStat) {
			if (strpos($matchStat[0], "endfor") !== false) {
				continue;
			}
			else {
				if (strpos($matchStat[0], "for") !== false) {
					$expl1 = explode("for", $matchStat[0]);
					$explarg1 = explode("in", $expl1[1]);


					$firstArg = str_replace(' ', '', $explarg1[0]);

					$explarg2 = explode("%}", $explarg1[1]);
					$secondArg = str_replace(' ', '', $explarg2[0]);

					
					if (!in_array($firstArg, $unusedArguments)) {
						array_push($unusedArguments, $firstArg);
					}

					// first argument should not be in the results
					// for the second one, we need to check whether it exists in the unusedArguments

					if (in_array($secondArg, $unusedArguments)) {
						continue;
					}

					$found = false;
					foreach ($matches as $match) {
						if ($match["label"] == $secondArg) {
							$found = true;
						}
					}
					if ($found == false) {
						$newMatch = array();
						$newMatch["label"] = $secondArg;
						$newMatch["startLabel"] = $matchStat[1];
						array_push($matches, $newMatch);
					}
				}
				else {
					if (strpos($matchStat[0], "assign") !== false) {
						$expl1 = explode("=", $matchStat[0]);
						$variableArr = explode("{% assign", $expl1[0]);
						$var1 = str_replace(' ', '', $variableArr[1]); 

						$expl2 = explode("}}", $expl1[1]);
						$var2 = str_replace('{', '', $expl2[0]); 
						$var2 = str_replace(' ', '', $var2); 

						if (!in_array($var1, $unusedArguments)) {
							array_push($unusedArguments, $var1);
						}

						// first argument should not be in the results
						// for the second one, we need to check whether it exists in the unusedArguments

						if (in_array($var2, $unusedArguments)) {
							continue;
						}

						$found = false;
						foreach ($matches as $match) {
							if ($match["label"] == $var2) {
								$found = true;
							}
						}
						if ($found == false) {
							$newMatch = array();
							$newMatch["label"] = $var2;
							$newMatch["startLabel"] = $matchStat[1];
							array_push($matches, $newMatch);
						}
					}
				}
			}
		}

		foreach ($matches as $key => $match) {
			if (in_array($match["label"], $unusedArguments)) {
				unset($matches[$key]);
			}
			if (strpos($match["label"], ".") !== false) {
				unset($matches[$key]);
			}
		}

		$result = array();
		foreach ($matches as $key => $match) {
			$result[$key] = $match["label"];
		}
		$result = implode(",", $result);



		$obj = new stdClass();
		$obj->fields = $result;
		$obj->cml = $template["cml"];

		return $obj;

	}

	/**
	 * Extract available fields from CML in the template. Returns a JSON file with 
	 * the CML code and available fields.
	 */
	public function getTemplate() {
        $templateType = Input::get( 'templateType' );
        $template = Template::where("type", $templateType)->first();

        return Response::json($this->getTemplateFields($templateType, null));
	}

	public function getSubmit() {
		return View::make('job2.tabs.submit');
	}

	public function getSave($id) {
		$j = Entity::where("type", "job")->where("platformJobId", $id)->first();
		Session::put('jobconf_id_t', $j->jobConf_id);
		Session::put('job_id_t', $j->_id);
		Session::put('type_t', $j->templateType);

		$jsonObjectTemplFields = $this->getTemplateFields(null, $j->jobConf_id);
		$templateFields = explode(",", $jsonObjectTemplFields->fields);

		Session::put('templateFields', $templateFields);
		return View::make('job2.save');
	}

	public function postSaveover(){
		Session::put('overwrite','yes');
		return $this->postSave();
	}

	public function getSaveover() {
		return View::make('job2.saveover');
	}

	private function findNewestTemplate($type, $format){
		$maxi = Template::where("type", $type)->max('version');
		if ($maxi === null){return null;}
	 	$jcbase = Template::where("type", $type)->where('version', $maxi)->first();

	 	return $jcbase;
	}

	public function postSave() {
		$jc_id = Session::get('jobconf_id_t');
		$j_id = Session::get('job_id_t');
		$jc = Entity::where("_id", $jc_id)->first();
		$jcco = $jc['content'];
		$j = Entity::where("_id", $j_id)->first();
		$type = Input::get('templateType');
		$load = Input::get('load');
		$templateDescription = Input::get('templateDescription');
		$resultFieldName = Input::get('resultFieldName');
		$resultFieldDescription = Input::get('resultFieldDescription');
		$resultFieldType = Input::get('typeResult');
		$resultFieldNoItems = Input::get('noitemsResult');

		$jsonObjectTemplFields = $this->getTemplateFields(null, $jc_id);
		$templateFields = explode(",", $jsonObjectTemplFields->fields);

		foreach ($templateFields as $templateField) {
			$fieldNameType = 'type' . $templateField;
			$fieldNameItems = 'noitems' . $templateField;
			$fieldNameDescription = 'description' . $templateField;
			$fieldNameType = Input::get('type' . $templateField);
			$fieldNameItems = Input::get('noitems' . $templateField);
			$fieldNameDescription = Input::get('description' . $templateField);
		}

		if($type === null){
			$load = Session::get('load');
			$type = Session::get('templateType');
			$templateDescription = Session::get('templateDescription');

			$resultFieldName = Session::get('resultFieldName');
			$resultFieldDescription = Session::get('resultFieldDescription');
			$resultFieldType = Session::get('typeResult');
			$resultFieldNoItems = Session::get('noitemsResult');

			foreach ($templateFields as $templateField) {
				$fieldNameType = 'type' . $templateField;
				$fieldNameType = Session::get('type' . $templateField);
				$fieldNameItems = 'noitems' . $templateField;
				$fieldNameItems = Session::get('noitem' . $templateField);
				$fieldNameDescription = 'description' . $templateField;
				$fieldNameDescription = Session::get('description' . $templateField);
			}
		}
		else
		{
			Session::put('templateType', Input::get('templateType'));
			Session::put('load', $load);
			Session::put('templateDescription', Input::get('templateDescription'));

			Session::put('resultFieldName', $resultFieldName);
			Session::put('resultFieldDescription', $resultFieldDescription);
			Session::put('typeResult', $resultFieldType);
			Session::put('noitemsResult', $resultFieldNoItems);


			foreach ($templateFields as $templateField) {
				$fieldNameType = 'type' . $templateField;
				$fieldNameItems = 'noitems' . $templateField;
				$fieldNameDescription = 'description' . $templateField;
				Session::put('type' . $templateField, Input::get($fieldNameType));
				Session::put('noitems' . $templateField, Input::get($fieldNameItems));
				Session::put('description' . $templateField, Input::get($fieldNameDescription));
			}
		}
		//dd($resultFieldNoItems);
		if($type===null || $type==="")
			return Redirect::back()->with('flashError', "Type name not filled");
		if($templateDescription===null || $templateDescription==="")
			return Redirect::back()->with('flashError', "Template description not filled");

		foreach ($templateFields as $templateField) {
			$fieldNameType = 'type' . $templateField;
			$fieldNameItems = 'noitems' . $templateField;
			$fieldNameDescription = 'description' . $templateField;

			if(Input::get($fieldNameType)===null || Input::get($fieldNameType)==="") {
				return Redirect::back()->with('flashError', "Type for field - ". $templateField . " - not filled");
			}
			if(Input::get($fieldNameItems)===null || Input::get($fieldNameItems)==="") {
				return Redirect::back()->with('flashError', "Number of items for field - ". $templateField . " - not filled");
			}
			if(Input::get($fieldNameDescription)===null || Input::get($fieldNameDescription)==="") {
				return Redirect::back()->with('flashError', "Description for field - ". $templateField . " - not filled");
			}
		}	

		if($resultFieldName===null || $resultFieldName==="")
			return Redirect::back()->with('flashError', "Named for result field not filled");
		if($resultFieldDescription===null || $resultFieldDescription==="")
			return Redirect::back()->with('flashError', "Description for result field not filled");
		if($resultFieldType===null || $resultFieldType==="")
			return Redirect::back()->with('flashError', "Type for result field not filled");
		if($resultFieldNoItems===null || $resultFieldNoItems==="")
			return Redirect::back()->with('flashError', "Number of items for result field not filled");

		$newest = $this->findNewestTemplate($type, $j->format);
		if($newest !== null and !(Session::has('overwrite')) ){
			return Redirect::to("jobs2/saveover");
		}	 	
		if(Session::has('overwrite'))
			Session::forget('overwrite');
		if($newest === null){
			$v = 0;
		}else{
			$v =   Template::where("type", $type)->max('version')+1;
		}	
		//save + increasing version
	    $te = new Template;
	    $te['platform'] = $j->softwareAgent_id;
	    if(isset($jcco['cml']))
	    	$te['cml'] = $jcco['cml'];
	 	if(isset($jcco['css']))
	 		$te['css'] = $jcco['css'];
 		if(isset($jcco['instructions']))
 			$te['instructions'] = $jcco['instructions'];
 		if(isset($jcco['js']))
 			$te['js'] = $jcco['js'];
		$te['version'] = $v;
 		$te['type'] = $type;
 		$parameters = array();
 		$parameters["input"] = array();
 		$parameters["output"] = array();
 		foreach ($templateFields as $templateField) {
			$fieldNameType = 'type' . $templateField;
			$fieldNameItems = 'noitems' . $templateField;
			$fieldNameDescription = 'description' . $templateField;

			$newParameter = array();
			$newParameter["type"] = Input::get($fieldNameType);
			if (Input::get($fieldNameItems) === "multiple")
				$newParameter["type"] .= "[]";

			$newParameter["name"] = $templateField;
			$newParameter["description"] = Input::get($fieldNameDescription);
			
			array_push($parameters["input"] , $newParameter);		
		}
		
		$newParameter = array();
		$newParameter["type"] = Input::get('typeResult');
		if (Input::get('noitemsResult') === "multiple")
			$newParameter["type"] .= "[]";
		$newParameter["name"] = $resultFieldName;
		$newParameter["description"] = $resultFieldDescription;
		array_push($parameters["output"] , $newParameter);		

		$te["parameters"] = $parameters;
		$te["description"] = Input::get('templateDescription');

 		$te->save();
		$load = Input::get('load');
		if($load === 'yes'){
			$this->postLoad();
		}
		Session::flash('flashSuccess', "Template saved! :-)");
		return Redirect::to("jobs");
	}

	public function getLoad($id) {
		$j = Entity::where("type", "job")->where("platformJobId", $id)->first();
		Session::put('jobconf_id_t', $j->jobConf_id);
		Session::put('job_id_t', $j->_id);
		
		return View::make('job2.load');
	}

	public function postLoad() {
		$jc_id = Session::get('jobconf_id_t');
		$j_id = Session::get('job_id_t');
		$jc = Entity::where("_id", $jc_id)->first();
		$j = Entity::where("_id", $j_id)->first();
		$jcco = $jc['content'];
		$jcco['type'] =  Input::get('templateType');

	 	if($jcco['type'] == Null) 
	    	return Redirect::back()->with('flashError', "form not filled in (type).");	 	
	    	// get a selected, newest jcbase
	    $maxi = Template::where("type", $jcco['type'])->max('version');
	 	$jcbase = Template::where("type", $jcco['type'])->where('version', $maxi)->first();

	 	if(!isset($jcbase)){
	 		Session::flash('flashError',"template not found: ". $jcco['type']);
			return Redirect::to("jobs2/submit");
		}
		if(!isset($jcbase['cml'])){
			Session::flash('flashError', "No template details in this template");
			return Redirect::to("jobs2/submit");
		}
	 		$jcco['cml'] = $jcbase['cml'];
	 		if(isset($jcbase['css']))
	 			$jcco['css'] = $jcbase['css'];
	 		if(isset($jcbase['instructions']))
	 			$jcco['instructions'] = $jcbase['instructions'];
	 		if(isset($jcbase['js']))
	 			$jcco['js'] = $jcbase['js'];
	 		$jcco['template_id'] = $jcbase['_id'];
	 		
	 		$jc['content'] = $jcco;

	 		$j['type'] = "job";
	 		$jc->save();
	 		$j->save();
	 		$platform = App::make('CF');

	 		//upadte
			$platform->cfUpdate($j['platformJobId'], $jc);
			$successmessage = "Job loaded."; 
			Session::flash('flashSuccess', $successmessage);
		return Redirect::to("jobs");
	}

	public function getDuplicate($entity, $project, $type, $incr){
		Session::forget('batch');

		$job = Job::id("entity/$project/$type/$incr")->first();

		if(!is_null($job)){
				$jc = $job->JobConfiguration->replicate();
			unset($jc->activity_id);
			$jc->parents= array($job->JobConfiguration->_id);
			Session::put('jobconf', serialize($jc));
			//Session::put('format', $job->batch->format);
			if(isset($jc->content['TVID']))
				Session::put('templateType', $jc->content['TVID']);
			Session::put('title', $jc->content['title']);
			Session::put('templateType', $jc->content['type']);
			return Redirect::to("jobs2/batch");
		} else {
			Session::flash('flashError',"Job $id not found.");
			return Redirect::back();
		}
	}

	/**
	 * Every time you click a tab or the 'next' button, this function fires. 
	 * It combines the Input fields with the JobConfiguration that we already have in the Session.
	 */
	public function postFormPart($next){
		if(Input::has('batch')) {
			// TODO: Validate for CSRF
			$batch = Batch::find(Input::get('batch'));
			// TODO -- is saving batch in the session a good idea ? 	
			Session::put('batch', serialize($batch));
		} else {
			$batch = unserialize(Session::get('batch'));
			if(empty($batch)){
				Session::flash('flashNotice', 'Please select a batch first.');
				return Redirect::to("jobs2/batch");
			}	
		}
		return Redirect::to("jobs2/submit");
	}


	public function getRefresh($entity, $project, $type, $incr){
		$platform = App::make('CF');
		$platform->refreshJob("entity/$project/$type/$incr");
		return Redirect::to("jobs");
	}

	public function getDelete($id){
		$platform = App::make('CF');
		//dd($id);
		$platform->deleteJob($id);
		Temp::truncate();
		return Redirect::to("jobs");
	}

	public function getDeletect($id){
		$platform = App::make('CF');
		//dd($id);
		$platform->deleteJobCT($id);
		Temp::truncate();
		return Redirect::to("jobs");
	}

	public function getDeletepl($id){
		$platform = App::make('CF');
		//dd($id);
		$platform->deleteJobPL($id);
		Temp::truncate();
		return Redirect::to("jobs");
	}

	public function getSavetemplate($id){
		$platform = App::make('CF');
		return Redirect::to("save");
	}

	// TODO: Make job configuration part of job ?
	public function postSubmitFinal($ordersandbox = 'order') {
		$batch = unserialize(Session::get('batch'));

		// switch platform
		$platform = 'CF';


		if(Input::get('platform') == 'game') {
			$platform = 'DrDetectiveGamingPlatform';
		}

		$batchColumnsNewTemplate = array();
		$batchColumnsExtraChosenTemplate = array();
		$associationsTemplBatch = array();
		$ownTemplate = false;
		
		// Use existing job configuration content (if available)
		if (!$jc = unserialize(Session::get('jobconf'))) {
			$jc = new JobConfiguration;
			$jc->type = "jobconf";
			$jcco = array();
		} else {
			$jcco = $jc->content;
		}

		$own = false;
		if (Input::has('templateTypeOwn') && strlen(Input::get('templateTypeOwn')) > 0) {
			$jcco['type'] = Input::get('templateTypeOwn');
			$batchColumns = Input::get('batchColumns');
			$newNamesForBatchColumns = Input::get('newcolnames');
			$newNamesForBatchColumnsArray = explode(",", $newNamesForBatchColumns);
			array_pop($newNamesForBatchColumnsArray);
			
			if($batchColumns == null) {
				return Redirect::back()->with('flashError', "You did not choose the batch columns");
			} else {
				foreach ($newNamesForBatchColumnsArray as $value) {
					$oldNewArray = explode(" - ", $value);
					if (in_array($oldNewArray[0], $batchColumns)) {
						$batchColumnsNewTemplate[$oldNewArray[0]] = $oldNewArray[1];
					}
				}
			}
			$ownTemplate = true;
			$own = true;
		} else {
			$jcco['type'] =  Input::get('templateType');
			if($jcco['type'] == null) {
				return Redirect::back()->with('flashError', "You did not fill in the type of the template");
			}

	 		$jcco['type'] =  Input::get('templateType');
	 		if($jcco['type'] == Null) 
	    		return Redirect::back()->with('flashError', "You did not fill in the type of the template");	 	

	    	// get a selected, newest jcbase
	    	$maxi = Template::where("type", $jcco['type'])->max('version');
	 		$jcbase = Template::where("type", $jcco['type'])->where('version', $maxi)->first();


	 		if(!isset($jcbase)){
	 			Session::flash('flashError',"template not found: ". $jcco['type']);
				return Redirect::to("jobs2/submit");
			}
			if(!isset($jcbase['cml'])){	// Template must have CML field
				Session::flash('flashError', "No template details in this template");
				return Redirect::to("jobs2/submit");
			}

			$batchColumnsExtraChosenTemplate = Input::get('addMoreColumns');

			$fieldsInChosenTemplate = Input::get('tempFields');
			$arrayFields = explode(",", $fieldsInChosenTemplate);

			foreach ($arrayFields as $field) {
				$association =  Input::get($field);
				array_push($associationsTemplBatch, $field . " - " . $association);
				if($association == null || $association == "---") {
					return Redirect::back()->with('flashError', "You did not fill in all the associations for the template fields");
				}
			}

			$jcco['cml'] = $jcbase['cml'];
			if(isset($jcbase['css']))
				$jcco['css'] = $jcbase['css'];
			if(isset($jcbase['instructions']))
				$jcco['instructions'] = $jcbase['instructions'];
			if(isset($jcbase['js']))
				$jcco['js'] = $jcbase['js'];
			$jcco['template_id'] = $jcbase['_id'];
		}

	    if (Input::has('titleOwn') && strlen(Input::get('titleOwn')) > 0 )
			 		$jcco['title'] = Input::get('titleOwn');
			 	else
			 		$jcco['title'] =  Input::get('title');
		if ($jcco['title'] == Null) 
	    		return Redirect::back()->with('flashError', "You did not fill in the title of the template");	 	


	    $jcco['platform'] = $platform; //TODOJORAN
	    $jcco['description'] =  Input::get('description');
	    $jcco['title'] = $jcco['title'];
	    ///////// PUT

	    $jc->content = $jcco;
	    if($own){
		    $_tt = Template::where('type', $jcco['type'])->where("format", $batch->format)->first();
		    if(isset($_tt)){
		    	Session::flash('flashError', "There is already a template of this type. Please rename (or select this template from dropdown list.");
				return Redirect::to("jobs2/submit");
			}
		}

		try{
			// Save activity
			$activity = new Activity;
			$activity->label = "Job is uploaded to crowdsourcing platform.";
			$activity->softwareAgent_id = 'jobcreator'; // JOB softwareAgent_id = $platform. Does this need to be the same?
			$activity->save();
			// Save jobconf if necessary
			$hash = md5(serialize($jc->content));

			if($existingid = JobConfiguration::where('hash', $hash)->pluck('_id')) {//[qq]
				$jcid = $existingid; // Don't save, it already exists.
			} else {
				$jc->project = $batch->project;
				$jc->user_id = $batch->user_id;
				$jc->softwareAgent_id = $activity->softwareAgent_id;
				$jc->hash = $hash;
				$jc->activity_id = $activity->_id;
				$jc->save();
				$jcid = $jc->_id;
			}

			// Software Agent ID which will perform the job
			// TODO: Fix this -- choose one of the possibly many platforms in $jcco
			// at the moment we are picking the first arbitrarily
			$job_sw_agent = $jcco['platform'];
			// Publish jobs
			$j = new Job;
			$j->project = $batch->project;
			$j->user_id = $batch->user_id;
			$j->type = "job";
			$j->batch_id = $batch->_id;
			$j->jobConf_id = $jcid;
			$j->softwareAgent_id = $job_sw_agent;
			$j->activity_id = $activity->_id;
			$j->iamemptyjob = "yes";
			$j->templateType = $jcco['type'];
			$extraInfoBatch = array();
			$extraInfoBatch["batchColumnsNewTemplate"] = $batchColumnsNewTemplate;
			$extraInfoBatch["batchColumnsExtraChosenTemplate"] = $batchColumnsExtraChosenTemplate;
			$extraInfoBatch["associationsTemplBatch"] = $associationsTemplBatch;
			$extraInfoBatch["ownTemplate"] = $ownTemplate;
			$j->extraInfoBatch = $extraInfoBatch;
			$j->save(); //convert to publish later

			$j->publish(($ordersandbox == 'sandbox' ? true : false));
			$jobs[] = $j;

			$successmessage = "Created job with jobConf :-)";

			$platformApp = App::make($platform); //TODOJORAN

			$platformApp->refreshJob($j->_id);
			
			Session::flash('flashSuccess', $successmessage);
			return Redirect::to("jobs");
		} catch (Exception $e) {
			if(isset($j)) $j->forceDelete();
			if(isset($jc)) $jc->forceDelete();
			if($activity) $activity->forceDelete();
			throw $e; //for debugging
			Session::flash('flashError', $e->getMessage());
			return Redirect::to("jobs2/submit");
		}
	}
}
