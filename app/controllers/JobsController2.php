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
		$batch = unserialize(Session::get('batch'));
		if(!$batch) $selectedbatchid = ''; 
		else $selectedbatchid = $batch->_id;
		return View::make('job2.tabs.batch')->with('batches', $batches)->with('selectedbatchid', $selectedbatchid);
	}

	public function getBatchd() {
		$batches = Batch::where('documentType', 'batch')->get(); 
		$batch = unserialize(Session::get('batch'));
		if(!$batch) $selectedbatchid = ''; 
		else $selectedbatchid = $batch->_id;
		return View::make('job2.tabs.batch')->with('batches', $batches)->with('selectedbatchid', $selectedbatchid);
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
			Session::put('batch', serialize($job->batch));
			Session::put('format', $job->batch->format);
			if(isset($jc->content['type']))
                           Session::put('templatetype', $jc->content['type']);
			Session::put('title', $jc->content['title']);
			return Redirect::to("jobs2/batchd");
		} else {
			Session::flash('flashError',"Job $id not found.");
			return Redirect::back();
		}
	}

	/*
	* Every time you click a tab or the 'next' button, this function fires. 
	* It combines the Input fields with the JobConfiguration that we already have in the Session.
	*/
	public function postFormPart($next){
		if(Input::has('batch')){
			// TODO: CSRF
			$batch = Batch::find(Input::get('batch'));
			Session::put('batch', serialize($batch));
		} else {
			$batch = unserialize(Session::get('batch'));
			if(empty($batch)){
				Session::flash('flashNotice', 'Please select a batch first.');
				return Redirect::to("jobs2/batch");
			}	
		}
		try {
			return Redirect::to("jobs2/$next");
		} catch (Exception $e) {
			Session::flash('flashError', $e->getMessage()); 
			return Redirect::to("jobs2");
		}
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
		return Redirect::to("jobs");
	}


	public function postSubmitFinal($ordersandbox = 'order'){
		$batch = unserialize(Session::get('batch'));
		if (!$jc = unserialize(Session::get('jobconf'))){
			$jc = new JobConfiguration;
			$jc->documentType = "jobconf";
			$jcco = array();
		}
		else
			$jcco = $jc->content;
		if (Input::has('templateTypeOwn') and strlen(Input::get('templateTypeOwn')) > 0 )
			 		$jcco['type'] = Input::get('templateTypeOwn');
			 	else
			 		$jcco['type'] =  Input::get('templateType');
	    if (Input::has('titleOwn') and strlen(Input::get('titleOwn')) > 0 )
			 		$jcco['title'] = Input::get('titleOwn');
			 	else
			 		$jcco['title'] =  Input::get('title');
	    if ($jcco['title'] == Null or $jcco['type'] == Null) 
	    		return Redirect::back()->with('flashError', "form not filled in.");
	    $jcco['title'] = $jcco['title'] . "  [[ " . $jcco['type'] . " | " . $batch->format . " ]] ";
	    $jcco['platform'] = Array("cf");
	    $jcco['description'] =  Input::get('description');
	    ///////// PUT
	    $jc->content = $jcco;

		try{
			// Save activity
			$activity = new MongoDB\Activity;
			$activity->label = "Job is uploaded to crowdsourcing platform.";
			$activity->softwareAgent_id = 'jobcreator'; // JOB softwareAgent_id = $platform. Does this need to be the same?
			$activity->save();
			// Save jobconf if necessary
			$hash = md5(serialize($jc->content));

        	if($existingid = JobConfiguration::where('hash', $hash)->pluck('_id')) //[qq]
                $jcid = $existingid; // Don't save, it already exists.
            else {
            	$jc->format = $batch->format;
				$jc->domain = $batch->domain;
	            $jc->hash = $hash;
				$jc->activity_id = $activity->_id;
				$jc->save();
				$jcid = $jc->_id;
			}
				// Publish jobs
			 	$j = new Job;
			 	$j->format = $batch->format;
			 	$j->domain = $batch->domain;
			 	$j->type = $jc->content['type'];
			 	$j->batch_id = $batch->_id;
			 	$j->jobConf_id = $jcid;
			 	$j->softwareAgent_id = "cf2"; // $platformstring;
			 	$j->activity_id = $activity->_id;
			 	$j->iamemptyjob = "yes";
			 	$j->save(); //convert to publish later
			 	$j->publish(($ordersandbox == 'sandbox' ? true : false));
			 	$jobs[] = $j;
			$successmessage = "Created job with jobConf :-)"; 
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
			if($activity) $activity->forceDelete();
			throw $e; //for debugging
			Session::flash('flashError', $e->getMessage());
			return Redirect::to("jobs2/submit");
		}
	}
}
