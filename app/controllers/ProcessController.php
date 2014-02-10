<?php

use crowdwatson\AMTException;

class ProcessController extends BaseController {
	protected $templatePath;
	protected $csvPath;

	public function __construct(){
		$this->templatePath = base_path() . '/public/templates/';
		$this->csvPath = base_path() . '/public/csv/';
	}

	public function getIndex() {
		// if(!count(Cart::content()) > 0){
		// 	Session::flash('flashNotice', 'You have not added any items to your selection yet');
		// 	return Redirect::to('files/browse');
		// }
        return Redirect::to('process/selectfile');
	}

	public function getSelectfile() {
		$jc = unserialize(Session::get('jobconf'));
		
		//$turk = new crowdwatson\MechanicalTurk;
		//$jc = JobConfiguration::fromJSON("{$this->templatePath}relation_direction/relation_direction_1.json");

/*		$arr = array();
		$hits = $turk->searchHITs(2, 1, null, 'Descending');
		foreach ($hits as $hit){
			$arr[] = $hit->toArray();
		}*/
		
		//dd($turk->getAssignmentsForHIT('2P3Z6R70G5RC7PEQC857ZSST0J2P9T'));

		//$cf = new crowdwatson\Job("c6b735ba497e64428c6c61b488759583298c2cf3");
		//$job = $cf->readJob('382004');
		//$judg = $cf->getUnitJudgments('380640', '406870707');
		//dd($ass->getHITId());
		//$temp = "<h1>JobConfiguration</h1><br>" . $jc->toHTML($jc->toArray());
		//$temp .= "<h1>Assignment</h1>" . $jc->toHTML($ass->toArray());
		$temp = '';
		return View::make('process.tabs.selectfile')->with('jobconf', $jc)->with('temp', $temp);
	}

	public function getTemplate() {
		// Create array for the tree
		$jc = unserialize(Session::get('jobconf'));	
		$currenttemplate = Session::get('template');
		if(empty($currenttemplate)) $currenttemplate = 'generic/default';
		$treejson = $this->makeDirTreeJSON($this->templatePath, $currenttemplate);

		return View::make('process.tabs.template')
			->with('treejson', $treejson)
			->with('currenttemplate', $currenttemplate)
			->with('jobconf', $jc);
	}

	public function getDetails() {
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$csv = Session::get('csv');

		$j = new Job($csv, $template, $jc);
		$questionids = array();
		$goldfields = array();

		try {
			$questionids = $j->getQuestionIds();
			$goldfields = $j->getGoldFields();	
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		} 
		// Compare QuestionID's and goldfields.
		//if (count($goldfields)>0)
		if($diff = array_diff($goldfields, $questionids))
			if(count($diff) == 1)
				Session::flash('flashNotice', 'Field \'' . array_values($diff)[0] . '\' is in the answerkey but not in the HTML template.');
			elseif(count($diff) > 1)
				Session::flash('flashNotice', 'Fields \'' . implode('\', \'', $diff) . '\' are in the answerkey but not in the HTML template.');

		return View::make('process.tabs.details')
			->with('jobconf', $jc)
			->with('goldfields', $goldfields);
	}

	public function getPlatform() {
		$jc = unserialize(Session::get('jobconf'));
		return View::make('process.tabs.platform')->with('jobconf', $jc);
	}

	public function getSubmit() {
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$csv = Session::get('csv');
		
		$treejson = $this->makeDirTreeJSON($this->templatePath, $template, false);

		try {
			$j = new Job($csv, $template, $jc);
			$questions = $j->getPreviews();
		} catch (AMTException $e) {
			$questions = array('couldn\'t generate previews.');
			Session::flash('flashNotice', $e->getMessage());
		}

		if(!$jc->validate()){
			$msg = '<ul>';
			foreach ($jc->getErrors()->all() as $message)
				$msg .= "<li>$message</li>";
			$msg .= '</ul>';

			Session::flash('flashError', $msg);
		}

		return View::make('process.tabs.submit')
			->with('treejson', $treejson)
			->with('questions',  $questions)
			->with('table', $jc->toHTML())
			->with('template', $jc->template);
	}

	public function getClearTask(){
		Session::forget('jobconf');
		Session::forget('template');
		Session::forget('csv');
		return Redirect::to("process/selectfile");
	}

	/*
	* Save the jobdetails to a JSON file (from the button in the Submit tab)
	*/
	public function postSaveDetails(){
		$jc = unserialize(Session::get('jobconf'));
		$arr = $jc->toArray();
		$json = json_encode($arr, JSON_PRETTY_PRINT);

		// Allow only a-z, 0-9, /, _. The rest will be removed.
		$filename = preg_replace("/[^a-z0-9\/_]+/", "", 
			strtolower(str_replace(' ', '_', Input::get('template'))));

		try {
			file_put_contents("{$this->templatePath}{$filename}.json", $json);
			Session::flash('flashSuccess', "Saved jobdetails on server as $filename.json. Remember to provide an HTML questionfile.");
		} catch (Exception $e) {
			Session::flash('flashError', $e->getMessage());
		}

		return Redirect::to("process/submit");
	}


	/*
	* Every time you click a tab or the 'next' button, this function fires. 
	* It combines the Input fields with the JobConfiguration that we already have in the Session.
	*/
	public function postFormPart($next){
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		if(Input::has('template')){
			// Create the JobConfiguration object if it doesn't already exist.
			$ntemplate = Input::get('template');
			if (empty($template) or ($template != $ntemplate))	
				$jc = JobConfiguration::fromJSON("{$this->templatePath}$ntemplate.json");
			$template = $ntemplate;
		} else {
			if (empty($jc)){
				// No JobConfiguration and no template selected, not good.
				// (Unfortunately we can't flash a warning when redirecting.)
				return Redirect::to("process/template");
			} else {
				// There already is a JobConfiguration object. Merge it with Input!
				$jc = new JobConfiguration(array_merge($jc->toArray(), Input::get()));	

				// If leaving the details page...
				If(Input::has('title')){
					$jc->answerfields = Input::get('answerfields', false);
				}

				// If leaving the Platform page....:
				if(Input::has('qr')) {
					$jc->platform = Input::get('platform', false);
					$jc->addQualReq(Input::get('qr'));
					if(Input::has('arp'))
						$jc->addAssRevPol(Input::get('arp'));
				}
			}		
		}

		// TODO: get this from 'selectfile'
		$csv = 'source359444.csv';

		Session::put('jobconf', serialize($jc));
		Session::put('template', $template);
		Session::put('csv', $csv);
		return Redirect::to("process/$next");

	}

	/*
	* Send it to the platforms.
	*/
	public function postSubmitFinal(){
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$csv = Session::get('csv');
		
		try {
			$j = new Job($csv, $template, $jc);
			$ids = $j->publish();
			$msg = 'Created ' .
			(isset($ids['amt']) ? count($ids['amt']) : 0) .
			 ' jobs on AMT and ' .
			(isset($ids['cf']) ? count($ids['cf']) : 0) .
			 ' on CF.';
			Session::flash('flashSuccess', $msg);
		} catch (Exception $e) {
			Session::flash('flashError', $e->getMessage());
		}

		return Redirect::to("process/submit");
		
	}


	/*
	* Create the JSON necessary for jstree to use.
	*/
	private function makeDirTreeJSON($path, $currenttemplate, $pretty = true){
		$r = array();
		foreach(File::directories($path) as $dir){
			$dirname = substr($dir, strlen($path));
		   	if($pretty) $displaydir = ucfirst(str_replace('_', ' ', $dirname));
		   	else $displaydir = $dirname;

			$r[] = array('id' => $dirname, 'parent' => '#', 'text' => $displaydir); 

			foreach(File::allFiles($dir) as $file){
				$filename = $file->getFileName();
				if (substr($filename, -5) == '.json') {
		   			$filename = substr($filename, 0, -5);
		   			if($pretty) $displayname = ucfirst(str_replace('_', ' ', $filename));
		   			else $displayname = $filename;
		   			if("$dirname/$filename" == $currenttemplate)
		   				$r[] = array('id' => $filename, 'parent' => $dirname, 'text' => $displayname, 'state' => array('selected' => 'true'));
		   			else
		   				$r[] = array('id' => $filename, 'parent' => $dirname, 'text' => $displayname);
		   		}	
			}
		}
		return json_encode($r);
	}

}