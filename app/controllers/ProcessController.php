<?php

use crowdwatson\MechanicalTurkService;
use crowdwatson\AMTException;
use crowdwatson\Hit;
use crowdwatson\CFService;

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
		$ct = unserialize(Session::get('crowdtask'));
		return View::make('process.tabs.selectfile')->with('crowdtask', $ct);
	}

	public function getTemplate() {
		// Create array for the tree
		$crowdtask = unserialize(Session::get('crowdtask'));		
		$currenttemplate = (isset($crowdtask->template) ? $crowdtask->template : 'generic/default');	
		$treejson = $this->makeDirTreeJSON($this->templatePath, $currenttemplate);

		return View::make('process.tabs.template')
			->with('treejson', $treejson)
			->with('currenttemplate', $currenttemplate)
			->with('crowdtask', $crowdtask);
	}

	public function getDetails() {
		return View::make('process.tabs.details')->with('crowdtask', unserialize(Session::get('crowdtask')));
	}

	public function getPlatform() {
		$ct = unserialize(Session::get('crowdtask'));
		$turk = new MechanicalTurkService($this->templatePath);
		$questionids = array();
		$goldfields = array();
		try {
			$questionids = $turk->findQuestionIds($ct->template);
			if($ct->unitsPerTask > 1) // Admittedly a strange place for this. Should be refactored. (todo)
				foreach (array_keys($turk->csv_to_array("{$this->csvPath}{$ct->csv}")[0]) as $key)
					if ($key != '_golden' and $pos = strpos($key, '_gold') and !strpos($key, '_gold_reason'))
						$goldfields[$key] = substr($key, 0, $pos);		
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		} 
		
		// Compare QuestionID's and goldfields.
		if (count($goldfields)>0)
			if($diff = array_diff($goldfields, $questionids))
				if(count($diff) == 1)
					Session::flash('flashNotice', 'Field \'' . array_values($diff)[0] . '\' is in the answerkey but not in the HTML template.');
				elseif(count($diff) > 1)
					Session::flash('flashNotice', 'Fields \'' . implode(', ', $diff) . '\' are in the answerkey but not in the HTML template.');

		return View::make('process.tabs.platform')
			->with('crowdtask', $ct)
			->with('questionids', $questionids)
			->with('goldfields', $goldfields);
	}

	public function getSubmit() {
		$ct = unserialize(Session::get('crowdtask'));
		$turk = new MechanicalTurkService();
		$questions = array();

		// for template saving
		$treejson = $this->makeDirTreeJSON($this->templatePath, $ct->template, false);

		try{
			$question = file_get_contents("{$this->templatePath}{$ct->template}.html");
			$questionsdirty = $turk->createPreviews($question, "{$this->csvPath}{$ct->csv}");

			// TODO: can probably be done in a better way.
			foreach($questionsdirty as $q) {
				$questions[] = strip_tags($q, 
					"<a><abbr><acronym><address><article><aside><b>
					<bdo><big><blockquote><br><caption><cite><code>
					<col><colgroup><dd><del><details><dfn><div>
					<dl><dt><em><figcaption><figure><font>
					<h1><h2><h3><h4><h5><h6><hgroup>
					<hr><i><img><ins><li><map><mark><menu>
					<meter><ol><p><pre><q><rp><rt><ruby><s><samp>
					<section><small><span><strong><style><sub>
					<summary><sup><table><tbody><td><tfoot><th><thead>
					<time><tr><tt><u><ul><var><wbr>");
			}

		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		} catch (ErrorException $e) {
			Session::flash('flashError', 'Error reading templatefile.');
		}

		return View::make('process.tabs.submit')
			->with('crowdtask', $ct)
			->with('treejson', $treejson)
			->with('questions',  $questions);
	}

	public function getClearTask(){
		Session::forget('crowdtask');
		return Redirect::to("process/selectfile");
	}

	/*
	* Save the jobdetails to a JSON file (from the button in the Submit tab)
	*/
	public function postSaveDetails(){
		$ct = unserialize(Session::get('crowdtask'));
		$arr = $ct->toArray();
		unset($arr['csv']);
		unset($arr['template']);
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
	* It combines the Input fields with the CrowdTask that we already have in the Session.
	*/
	public function postFormPart($next){
		$ct = unserialize(Session::get('crowdtask'));

		if(Input::has('template')){
			// Create the CrowdTask object if it doesn't already exist.
			$template = Input::get('template');
			if (empty($ct) or ($ct->template != $template))	
				$ct = CrowdTask::fromJSON("{$this->templatePath}$template.json");
			$ct->template = $template;
		} else {
			if (empty($ct)){
				// No CrowdTask and no template selected, not good.
				// (Unfortunately we can't flash a warning when redirecting.)
				return Redirect::to("process/template");
			} else {
				// There already is a CrowdTask object. Merge it with Input!
				$ct = new CrowdTask(array_merge($ct->toArray(), Input::get()));	

				// If leaving the Platform page....:
				if(Input::has('qr')) {
					$ct->platform = Input::get('platform', false);
					$ct->answerfields = Input::get('answerfields', false);
					$ct->addQualReq(Input::get('qr'));
					$ct->addAssRevPol(Input::get('answerkey'), Input::get('arp'));
				}
			}		
		}

		// TODO: get this from 'selectfile'
		$ct->csv = 'source359444.csv';

		Session::put('crowdtask', serialize($ct));
		return Redirect::to("process/$next");

	}

	/*
	* Send it to the platforms.
	*/
	public function postSubmitFinal(){
		$ct = unserialize(Session::get('crowdtask'));
		$flash = '';

		// TODO: maybe a result page?

		try {

			// CrowdFlower
			if(in_array('cf', $ct->platform)){
				$cf = new CFService;
				dd($cf->readJob('379391'));
				$cf->createJob($ct->toCFData(), "{$this->csvPath}source359444.csv", 
					"{$this->templatePath}{$ct->template}", $ct->answerfields, 
					array('req_ttl_in_seconds' => $ct->expirationInMinutes*60)); 
				$flash = 'Created CrowdFlower job.<br>';
			}

			// Mechanical Turk
			if(in_array('amt', $ct->platform)){
				$hit = $ct->toHit();
				$turk = new MechanicalTurkService($this->templatePath);
				$upt = $ct->unitsPerTask;

				if(isset($upt) and $upt > 1)
					$created = $turk->createBatch($ct->template, "{$this->csvPath}{$ct->csv}", $hit, $upt, $ct->answerfields);
				else
					$created = $turk->createBatch($ct->template, "{$this->csvPath}{$ct->csv}", $hit);
				
				$flash .= 'Created ' . count($created) . ' HITs.<br>';
			}

			if(!empty($flash))
				Session::flash('flashSuccess', $flash);

		} catch (AMTException $e) {
			Session::flash('flashError', "AMT: {$e->getMessage()}");
		} catch (CFException $e) {
			Session::flash('flashError', "CF: {$e->getMessage()}");
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