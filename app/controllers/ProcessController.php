<?php

use crowdwatson\MechanicalTurkService;
use crowdwatson\AMTException;
use crowdwatson\Hit;

class ProcessController extends BaseController {
	protected $templatePath;
	protected $csvPath;
	protected $turk;

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

	public function getDetails() {
		return View::make('process.tabs.details')->with('crowdtask', unserialize(Session::get('crowdtask')));
	}

	public function getPlatform() {
		$ct = unserialize(Session::get('crowdtask'));
		$turk = new MechanicalTurkService($this->templatePath);
		$questionids = array();
		$csvfields = array();
		try {
			$questionids = $turk->findQuestionIds($ct->template);
			if($ct->tasksPerAssignment > 1)
				foreach (array_keys($turk->csv_to_array("{$this->csvPath}{$ct->csv}")[0]) as $key)
					$csvfields[$key] = $key;
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		} 
		
		return View::make('process.tabs.platform')
			->with('crowdtask', $ct)
			->with('questionids', $questionids)
			->with('csvfields', $csvfields);
	}


	private function iterateDirectory($path, $currenttemplate, $pretty = true){
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

	public function getTemplate() {
		// Create array for the tree
		$crowdtask = unserialize(Session::get('crowdtask'));		
		$currenttemplate = (isset($crowdtask->template) ? $crowdtask->template : 'generic/default');	
		$treejson = $this->iterateDirectory($this->templatePath, $currenttemplate);

		return View::make('process.tabs.template')
			->with('treejson', $treejson)
			->with('currenttemplate', $currenttemplate)
			->with('crowdtask', $crowdtask);
	}

	public function getSubmit() {
		$ct = unserialize(Session::get('crowdtask'));
		$turk = new MechanicalTurkService();
		$questions = array();

		// for template saving
		$treejson = $this->iterateDirectory($this->templatePath, $ct->template, false);

		try{
			$question = file_get_contents("{$this->templatePath}{$ct->template}.html");
			$questions = $turk->createPreviews($question, "{$this->csvPath}{$ct->csv}");
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

	private function newCTfromTemplate($template){
		try {
			// Currently, the HIT format is used.
			$turk = new MechanicalTurkService($this->templatePath);
			$hit = $turk->hitFromTemplate($template);
			$ct = CrowdTask::getFromHit($hit);
			$ct->template = $template;
			return $ct;
		} catch (AMTException $e){
			Session::flash('flashError', $e->getMessage());
			return new CrowdTask;
		}
	}

	public function getClearTask(){
		Session::forget('crowdtask');
		return Redirect::to("process/selectfile");
	}

	public function postSaveDetails(){
		$ct = unserialize(Session::get('crowdtask'));
		$json = json_encode($ct->toArray(), JSON_PRETTY_PRINT);
		echo "This would be saved if we would have a function for that: <br><br>\r\n\r\n $json";
	}

	public function postFormPart($next){
		$ct = unserialize(Session::get('crowdtask'));

		if(Input::has('template')){
			$template = Input::get('template');
			if (empty($ct) or ($ct->template != $template))
				//$ct = $this->newCTfromTemplate($template);
				//dd("{$this->templatePath}$template");	
				$ct = CrowdTask::fromJSON("{$this->templatePath}$template.json");
				$ct->template = $template;
			} else {
			// perhaps additional logic here depending on which tab you're on
			if (empty($ct)){
				$ct = new CrowdTask;
				Session::flash('flashWarning', 'No template selected.');
			} else {
				$ct = new CrowdTask(array_merge($ct->toArray(), Input::get()));	
				if(Input::has('qr')) $ct->addQualReq(Input::get('qr'));
				if(Input::has('arp')) $ct->addAssRevPol(Input::get('answerkey'), Input::get('arp'));

			}		
		}

			// TODO: get this from 'selectfile'
			$ct->csv = 'source359444.csv';

		Session::put('crowdtask', serialize($ct));
		return Redirect::to("process/$next");

	}

	public function postSubmitFinal(){
		$ct = unserialize(Session::get('crowdtask'));
		$hit = $ct->toHit();
		$turk = new MechanicalTurkService($this->templatePath);

		// Create HIT(s)
		try {
			if(isset($ct->tasksPerAssignment) and $ct->tasksPerAssignment > 1)
				$created = ($turk->createBatch($ct->template, "{$this->csvPath}{$ct->csv}", $hit, $ct->tasksPerAssignment, $ct->answerfield));
			else
				$created = ($turk->createBatch($ct->template, "{$this->csvPath}{$ct->csv}", $hit));
			Session::flash('flashSuccess', 'Created ' . count($created) . ' HITs.');
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		}

		return Redirect::to("process/submit");
	}
}