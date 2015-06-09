<?php
use Sunra\PhpSimple\HtmlDomParser;

class JobsController2 extends BaseController {

	public function getIndex(){
		$mainSearchFilters = \MongoDB\Temp::getMainSearchFiltersCache()['filters'];
		return View::make('media.search.pages.jobs2', compact('mainSearchFilters'));
	}
	
	public function getBatch() {
		$this->getClearTask();
		$batches = Batch::where('documentType', 'batch')->get(); 
		//$batch = unserialize(Session::get('batch'));
		//if(!$batch) $selectedbatchid = ''; 
		//else $selectedbatchid = $batch->_id;
		return View::make('job2.tabs.batch')->with('batches', $batches)->with('selectedbatchid', '');
	}

	public function getBatchd() {
		$batches = Batch::where('documentType', 'batch')->get(); 
		//$batch = unserialize(Session::get('batch'));
		//dd($batch);
		//if(!isset($batch)) $selectedbatchid = ''; 
		//else $selectedbatchid = $batch->_id;
		//dd($selectedbatchid);
		return View::make('job2.tabs.batch')->with('batches', $batches)->with('selectedbatchid', '');
	}

	/**
	 * Extract available fields from CML in the template. Returns a JSON file with 
	 * the CML code and available fields.
	 */
	public function getTemplate() {
		$templateType = Input::get( 'templateType' );
		$template = \MongoDB\Template::where("type", $templateType)->first();
		// everything that is between {{ }}
		$simpleRegEx = "/{{([^.}}]*)}}/";

		preg_match_all($simpleRegEx, $template["cml"], $matchesCol, PREG_OFFSET_CAPTURE);
		$matches = array();
		foreach ($matchesCol[1] as $simpleMatch) {
			//print_r($simpleMatch);
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

		return Response::json( $obj );
	}

	public function getPlatform() {
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$extensions = array();
		$possibleplatforms = array();

		$pl = Config::get('config.platforms');
		if(empty($pl)){
			Session::flash('flashError', 'Please include any installed platforms in the config file.');
		} else {
			foreach($pl as $platformname){
				$platform = App::make($platformname);
				$ext = $platform->getExtension();
				$extensions[] = $ext;
				$filename = public_path() . "/templates/$template.$ext";
				if(file_exists($filename) && is_readable($filename)){
					$possibleplatforms[] = array('short' => $platformname, 'long' => $platform->getName());
				}
			}
		}
		if(count($possibleplatforms)==0){
			Session::flash('flashError', 'No usable templates found. Please upload a template with one of these extensions: ' . implode(', ', $extensions) . '.');
		}
		return View::make('job2.tabs.platform')->with('jobconf', $jc->content)->with('possible', $possibleplatforms);
	}


	public function getSubmit() {
		return View::make('job2.tabs.submit');
	}

	public function getSave($id) {
		$j = \MongoDB\Entity::where("documentType", "job")->where("platformJobId", $id)->first();
		Session::put('format_t', $j->format);
		Session::put('jobconf_id_t', $j->jobConf_id);
		Session::put('job_id_t', $j->_id);
		Session::put('type_t', $j->type);
		return View::make('job2.save');
	}

	public function postSaveovert(){
		Session::put('overwrite','yes');
		return $this->postSavet();
	}


	public function getSaveover() {
		
		return View::make('job2.saveover');
	}

	private function findNewestTemplate($type, $format) {
		$maxi = \MongoDB\Template::where("type", $type)->where("format", $format)->max('version');
		if ($maxi === null){
			return null;
		}
		$jcbase = \MongoDB\Template::where("type", $type)->where("format", $format)->where('version', $maxi)->first();
		return $jcbase;
	}

	public function postSavet() {
		$jc_id = Session::get('jobconf_id_t');
		$j_id = Session::get('job_id_t');
		$jc = \MongoDB\Entity::where("_id", $jc_id)->first();
		$jcco = $jc['content'];
		$j = \MongoDB\Entity::where("_id", $j_id)->first();
		$type = Input::get('templateType');
		$load = Input::get('load');
		if($type === null){
			$load = Session::get('load');
			$type = Session::get('templateType');
		}
		else
		{
		Session::put('templateType', $type);
		Session::put('load', $load);
		}
		if($type===null or $type==="")
			return Redirect::back()->with('flashError', "Type name not filled");	
		$newest = $this->findNewestTemplate($type, $j->format);
		if($newest !== null and !(Session::has('overwrite')) ){
			return Redirect::to("jobs2/saveover");
			}	 	
		if(Session::has('overwrite'))
			Session::forget('overwrite');
		if($newest === null){
				$v = 0;
			}else{
				$v =   \MongoDB\Template::where("type", $type)->where("format", $j->format)->max('version')+1;
			}	
		//save + increasing version
		$te = new \MongoDB\Template;
		$te['cml'] = $jcco['cml'];
		$te['format'] = $j->format;
	 	if(isset($jcco['css']))
	 			$te['css'] = $jcco['css'];
 		if(isset($jcco['instructions']))
 			$te['instructions'] = $jcco['instructions'];
 		if(isset($jcco['js']))
 			$te['js'] = $jcco['js'];
		$te['version'] = $v;
 		$te['type'] = $type;
 		$te->save();


		$load = Input::get('load');
		if($load === 'yes'){
			$this->postLoadt();
		}
		Session::flash('flashSuccess', "Template saved! :-)");
		return Redirect::to("jobs");
	}

	public function getLoad($id) {
		$j = \MongoDB\Entity::where("documentType", "job")->where("platformJobId", $id)->first();
		Session::put('format_t', $j->format);
		Session::put('jobconf_id_t', $j->jobConf_id);
		Session::put('job_id_t', $j->_id);
		
		return View::make('job2.load');
	}

	public function postLoadt() {
			$jc_id = Session::get('jobconf_id_t');
			$j_id = Session::get('job_id_t');
			$jc = \MongoDB\Entity::where("_id", $jc_id)->first();
			$j = \MongoDB\Entity::where("_id", $j_id)->first();
			$jcco = $jc['content'];
			$jcco['type'] =  Input::get('templateType');
	 		if($jcco['type'] == null) 
				return Redirect::back()->with('flashError', "form not filled in (type).");	 	
			// get a selected, newest jcbase
			$maxi = \MongoDB\Template::where("type", $jcco['type'])->where("format", Session::get('format_t'))->max('version');
	 		$jcbase = \MongoDB\Template::where("type", $jcco['type'])->where("format", Session::get('format_t'))->where('version', $maxi)->first();
	 		if(!isset($jcbase)){
	 			Session::flash('flashError',"template not found");
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
	 		$pos = strpos($jcco['title'], '[[');
		 	$title = substr($jcco['title'], 0, $pos);
		 	$rest = substr($jcco['title'], strpos($jcco['title'], '(entity/' ));
	 		$jcco['title'] = $title . "[[" . $jcco['type'] . $rest;
	 		$jc['content'] = $jcco;
	 		$j['type'] = $jcco['type'];
	 		$jc->save();
	 		$j->save();
	 		$platform = App::make('cf2');

	 		//upadte
			$platform->cfUpdate($j['platformJobId'], $jc);
			$successmessage = "Job loaded."; 
			Session::flash('flashSuccess', $successmessage);
		return Redirect::to("jobs");
	}

	public function getClearTask(){
		Session::forget('jobconf');
		Session::forget('format');
		Session::forget('origjobconf');
		Session::forget('template');
		Session::forget('questiontemplateid');
		Session::forget('batch');
		return Redirect::to("jobs2/batch");
	}

	public function getDuplicate($entity, $format, $domain, $docType, $incr){
		Session::forget('batch');

		$job = Job::id("entity/$format/$domain/$docType/$incr")->first();

		if(!is_null($job)){
				$jc = $job->JobConfiguration->replicate();
			unset($jc->activity_id);
			$jc->parents= array($job->JobConfiguration->_id);
			Session::put('jobconf', serialize($jc));
			Session::put('format', $job->batch->format);
			if(isset($jc->content['TVID']))
				Session::put('templateType', $jc->content['TVID']);
			Session::put('title', $jc->content['title']);
			Session::put('templateType', $jc->content['type']);
			return Redirect::to("jobs2/batchd");
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


	public function getRefresh($entity, $format, $domain, $docType, $incr){
		$platform = App::make('cf2');
		$platform->refreshJob("entity/$format/$domain/$docType/$incr");
		return Redirect::to("jobs");
	}

	public function getDelete($id){
		$platform = App::make('cf2');
		//dd($id);
		$platform->deleteJob($id);
		\MongoDB\Temp::truncate();
		return Redirect::to("jobs");
	}

	public function getDeletect($id){
		$platform = App::make('cf2');
		//dd($id);
		$platform->deleteJobCT($id);
		\MongoDB\Temp::truncate();
		return Redirect::to("jobs");
	}

	public function getDeletepl($id){
		$platform = App::make('cf2');
		//dd($id);
		$platform->deleteJobPL($id);
		\MongoDB\Temp::truncate();
		return Redirect::to("jobs");
	}

	public function getSavetemplate($id){
		$platform = App::make('cf2');
		return Redirect::to("save");
	}

	public function postSubmitFinal($ordersandbox = 'order') {
		$batch = unserialize(Session::get('batch'));

		$batchColumnsNewTemplate = array();
		$batchColumnsExtraChosenTemplate = array();
		$associationsTemplBatch = array();
		$ownTemplate = false;
		
		// Use existing job configuration content (if available)
		if (!$jc = unserialize(Session::get('jobconf'))) {
			$jc = new JobConfiguration;
			$jc->documentType = "jobconf";
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

			// Get selected, newest template
			$jcbase = $this->findNewestTemplate($jcco['type'], $batch->format);
			if($jcbase==null) {
				Session::flash('flashError',"template not found");
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

		if (Input::has('titleOwn') && strlen(Input::get('titleOwn')) > 0 ) {
			$jcco['title'] = Input::get('titleOwn');
		} else {
			$jcco['title'] =  Input::get('title');
		}
		if ($jcco['title'] == null) { 
			return Redirect::back()->with('flashError', "You did not fill in the title of the template");
		}

		// TODO: dynamically load available platforms (in case more than cf are available).
		// At the moment this will work for CF2 and DrDetective but it is not flexible
		if(isset($jcbase['platform'])) {
			$jcco['platform'] = [ $jcbase['platform'] ];
		} else {
			$jcco['platform'] = [ "cf2" ];
			$jcco['title'] = $jcco['title'] . "[[" . $jcco['type'] . "(" . $batch->_id . ", " . $batch->domain .", " . $batch->format . ")]]";
		}

		$jcco['description'] =  Input::get('description');
		///////// PUT
		$jc->content = $jcco;
		if($own){
			$_tt = \MongoDB\Template::where('type', $jcco['type'])->where("format", $batch->format)->first();
			if(isset($_tt)) {
				Session::flash('flashError', "There is already a template of this type. Please rename (or select this template from dropdown list.");
				return Redirect::to("jobs2/submit");
			}
		}

		try{
			// Save activity
			$activity = new MongoDB\Activity;
			$activity->label = "Job is uploaded to crowdsourcing platform.";
			$activity->softwareAgent_id = 'jobcreator'; // JOB softwareAgent_id = $platform. Does this need to be the same?
			$activity->save();
			// Save jobconf if necessary
			$hash = md5(serialize($jc->content));

			if($existingid = JobConfiguration::where('hash', $hash)->pluck('_id')) {//[qq]
				$jcid = $existingid; // Don't save, it already exists.
			} else {
				$jc->format = $batch->format;
				$jc->domain = $batch->domain;
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
			$job_sw_agent = $jcco['platform'][0];
			// Publish jobs
			$j = new Job;
			$j->format = $batch->format;
			$j->project = $batch->project;
			$j->domain = $batch->domain;
			$j->user_id = $batch->user_id;
			$j->type = $jc->content['type'];
			$j->batch_id = $batch->_id;
			$j->jobConf_id = $jcid;
			$j->softwareAgent_id = $job_sw_agent;
			$j->activity_id = $activity->_id;
			$j->iamemptyjob = "yes";
			$extraInfoBatch = array();
			$extraInfoBatch["batchColumnsNewTemplate"] = $batchColumnsNewTemplate;
			$extraInfoBatch["batchColumnsExtraChosenTemplate"] = $batchColumnsExtraChosenTemplate;
			$extraInfoBatch["associationsTemplBatch"] = $associationsTemplBatch;
			$extraInfoBatch["ownTemplate"] = $ownTemplate;
			$j->extraInfoBatch = $extraInfoBatch;
			$j->save(); //convert to publish later

			/*return [
					"WARNING"	=>	"Job creation will crash at this point",
					"cause"		=>	"There is no DrDetective class, and it does not implement publish (or refreshJob) method",
					"fix"		=>	"Add refreshJob to the GameController (however it is called) and use that class."
			];*/
			$j->publish(($ordersandbox == 'sandbox' ? true : false));
			$jobs[] = $j;

			$successmessage = "Created job with jobConf :-)";

			$platform = App::make($job_sw_agent);
			$platform->refreshJob($j->_id);
			
			Session::flash('flashSuccess', $successmessage);
			return Redirect::to("jobs");
		} catch (Exception $e) {
			// Undo creation and delete jobs
			// if(isset($jobs))
			// foreach($jobs as $j){
			// 	if(isset($j->platformJobId))
			// 		$j->undoCreation($j->platformJobId);
			// 	$j->forceDelete();
			// }
			//delete activity
			if(isset($j)) $j->forceDelete();
			if(isset($jc)) $jc->forceDelete();
			if($activity) $activity->forceDelete();
			throw $e; //for debugging
			Session::flash('flashError', $e->getMessage());
			return Redirect::to("jobs2/submit");
		}
	}
}
