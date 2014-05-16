<?php
use Sunra\PhpSimple\HtmlDomParser;


/*

This class should be completely rewritten to 
1) Keep much less in the session
2) Have less complicated methods (see postsubmitformpart, which gets called every time the tab changes)
3) Ideally incorporate some smart ajax calls (see also the corresponding views)
4) [but this is bigger] The templating system has to change completely. If we don't do this soon, 
	the visualisation and uploading system could be better (more control), possibly also editing with something like ACE. 



*/

class ProcessController extends BaseController {

	public function getIndex() {
        return Redirect::to('process/batch');
	}

	public function getTemplatebuilder(){
		return View::make('process.tabs.templatebuilder');
	}

	public function getWorkers(){
		$c = 0;
		foreach (\MongoDB\CrowdAgent::where('softwareAgent_id', 'cf')->get() as $w) {
			$w->platformAgentId = (string) $w->platformAgentId;
			$w->save();
			$c++;

		}
		echo $c;
	}

	public function getA(){
		//Queue::push('Queues\UpdateUnits', array("entity/text/medical/twrex-structured-sentence/1078"));
		$ca = \MongoDB\CrowdAgent::where("_id", "crowdagent/cf/14781069")->first();
		Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($ca)));
	}

	public function getGeneraterandomjobdates(){
		foreach (Job::get() as $job) {
			if(empty($job->runningTimeInSeconds))
				dd($job);
		}	
	}

	public function getTestmetrics(){
		$job = Job::id("entity/text/medical/job/1")->first();
		Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
			
	}


/*

	public function getUpdate(){
		foreach (Job::get() as $job) {
			$job->results = null;
			Queue::push('Queues\UpdateJob', array('job' => serialize($job)));

		}
	}*/

	public function getDictionary($entity, $format, $domain, $docType, $incr){

		$id = "$entity/$format/$domain/$docType/$incr";

		foreach(Annotation::where('unit_id', $id)->where('softwareAgent_id', 'cf')->get() as $ann){
			//$ann = Annotation::id('entity/text/medical/annotation/5265')->first();
			echo "\r\n{$ann->_id}\r\n";
			print_r($ann->createDictionary());
			echo "\r\n\r\n---------------------------------\r\n\r\n";
		}
	}


	public function getResults(){
		if (($handle = fopen(storage_path() . '/test2.csv', 'r')) === false) {
		    die('Error opening file');
		}

		$jobresults = array();
		foreach(Job::where('softwareAgent_id', 'cf')->type('FactSpan')->get() as $job){
			$jobresults = array_merge($jobresults, $job->results['withSpam']);
		}	

		$headers = fgetcsv($handle, 1024, ',');
		$count = 0;
		$complete = array();
		$return = array();
		$skip = false;

		while ($row = fgetcsv($handle, 1024, ',')) {
			set_time_limit(30);
/*
			$skip=!$skip;
			if($skip)
				continue;*/

			$count++;
			$c = array_combine($headers, $row);

			$sentence = rtrim($c['sentence'], '.');
			$term1 = $c['term1'];
			$term2 = $c['term2'];

			$found = false;
			//foreach (MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->get() as $unit) {
			$unit = MongoDB\Entity::where('documentType', 'twrex-structured-sentence')
			->where('content.sentence.text', $sentence)
			->where('content.terms.first.text', $term1)
			->where('content.terms.second.text', $term2)->first(); 

			if($unit){	
	
	/*			if($unit['content']['sentence']['text'] == $sentence and
					$unit['content']['terms']['first']['text'] == $term1 and
					$unit['content']['terms']['second']['text'] == $term2){*/
					$found = true;
				//dd($unit->_id);
					// THis can also be used to compare with CF.
					//foreach (Job::type('FactSpan')->where('softwareAgent_id', 'amt')->get() as $job) {
					//$job = Job::where('softwareAgent_id', 'cf')->type('FactSpan')->first();
					
					if(array_key_exists($unit->_id, $jobresults)){
						$vector = $jobresults[$unit->_id];
						$temp = array('Sent id' => $c['Sent id']);
						if(substr($c['Sent id'], -2) == 'T1'){
							if(isset($vector['term1'])){
								//$temp = $this->computeSimilarity($vector['term1'], 1, $unit->_id, $job->softwareAgent_id);
								
								$temp = array_merge($this->computeSimilarity($vector['term1'], 1, $unit->_id, 'cf'), $temp);
								
								$temp['sentence']=$sentence;
								$temp['term1']=$term1;
								$temp['term2']=$term2;
								$temp['vector'] = '{' . implode(',', array_values($vector['term1'])) . '}';
								$result[] = $temp;
								
							}
						} elseif(substr($c['Sent id'], -2) == 'T2'){

							if(isset($vector['term2'])){
								//$temp = $this->computeSimilarity($vector['term2'], 2, $unit->_id, $job->softwareAgent_id);
								$temp = array_merge($this->computeSimilarity($vector['term2'], 2, $unit->_id, 'cf'), $temp);
								$temp['sentence']=$sentence;
								$temp['term1']=$term1;
								$temp['term2']=$term2;
								$temp['vector'] = '{' . implode(',', array_values($vector['term2'])) . '}';
								$result[] = $temp;
							}
						}
						
					}
				} else {
					$result[] = array('','','','','','','','','','','','','','');
				}
				
				//}
				
			//}
			
/*			if(!$found){
				$result[] = array('','','','','','','','','','','','','','');
				$result[] = array('','','','','','','','','','','','','','');
			}*/
			//if($count==5) dd($result);
	/*	
		if($count == 5){
			dd($result);
			$path =storage_path() . '/amt_new_output.csv';
			$out = fopen($path, 'w');

			fputcsv($out, array_keys($result[0]));
			foreach ($result as $row)
				fputcsv($out, $row);	
			
			// Close file
			rewind($out);
			fclose($out);
			dd($path);
		}*/
		}

		$path =storage_path() . '/cf_new_output.csv';
		$out = fopen($path, 'w');

		fputcsv($out, array_keys($result[0]));
		foreach ($result as $row)
			fputcsv($out, $row);	
		
		// Close file
		rewind($out);
		fclose($out);
		dd($path);



	}




//test
	public function getUpdateca(){
		/*$ca = \MongoDB\CrowdAgent::id('crowdagent/cf/19822336')->first();
		$ca->updateStats2();*/

		foreach(MongoDB\CrowdAgent::get() as $ca){
			//$ca->updateStats2();
			$ca->blocked = false;
			//$ca->messagesRecieved = array('count'=>0, 'messages'=>[]);
			$ca->save();
		}
		//$ca->updateStats2();
/*
		//$unitids = array('entity/text/medical/twrex-structured-sentence/1736');
		Queue::push('Queues\UpdateUnits', $unitids);
		//dd($unitids);
		echo count($unitids);*/
	}


	public function getUpdatecfdictionaries(){
		
		foreach(Job::where('softwareAgent_id', 'cf')->type('FactSpan')->get() as $job){
			foreach ($job->annotations as $ann) {
				$ann->dictionary = $ann->createDictionary();
				$ann->save();
			}


			Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
		}
	}



public function getVector(){
	if (($handle = fopen(storage_path() . '/output_AMT_FactSpan_sentences_raw.csv', 'r')) === false) {
		    die('Error opening file');
	}
	$headers = fgetcsv($handle, 1024, ',');
	$count = 0;
	$complete = array();
	$return = array();
	$skip = false;

	while ($row = fgetcsv($handle, 1024, ',')) {
		set_time_limit(30);

		$skip=!$skip;
		if($skip)
			continue;

		$count++;
		$c = array_combine($headers, $row);

		$sentence = rtrim($c['sentence'], '.');
		$term1 = $c['term1'];
		$term2 = $c['term2'];

		$found = false;
		foreach (MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->get() as $unit) {
			if($unit['content']['sentence']['formatted'] == $sentence and
				$unit['content']['terms']['first']['formatted'] == $term1 and
				$unit['content']['terms']['second']['formatted'] == $term2){
				$found = true;
				// THis can also be used to compare with CF.
				//foreach (Job::type('FactSpan')->where('softwareAgent_id', 'amt')->get() as $job) {
				$job = Job::id('entity/text/medical/job/0')->first();
					if(in_array($unit->_id, array_keys($job->results))){
						$vector = $job->results[$unit->_id];

						if(isset($vector['term1'])){
							//$temp = $this->computeSimilarity($vector['term1'], 1, $unit->_id, $job->softwareAgent_id);
							$temp = $vector['term1'];
							$temp['sentence']=$sentence;
							$temp['term1']=$term1;
							$temp['term2']=$term2;
							$result[] = $temp;
						}

						if(isset($vector['term2'])){
							//$temp = $this->computeSimilarity($vector['term2'], 2, $unit->_id, $job->softwareAgent_id);
							$temp = $vector['term2'];
							$temp['sentence']=$sentence;
							$temp['term1']=$term1;
							$temp['term2']=$term2;
							$result[] = $temp;
						}
						
					}
				}
				
			//}
			
		}

		if(!$found){
			$result[] = array('','','','','','','','','','','','','','');
			$result[] = array('','','','','','','','','','','','','','');
		}
		//if($count==5) dd($result);
/*	
	if($count == 5){
		dd($result);
		$path =storage_path() . '/amt_new_output.csv';
		$out = fopen($path, 'w');

		fputcsv($out, array_keys($result[0]));
		foreach ($result as $row)
			fputcsv($out, $row);	
		
		// Close file
		rewind($out);
		fclose($out);
		dd($path);
	}*/
	}

	$path =storage_path() . '/amt_new_output.csv';
	$out = fopen($path, 'w');

	fputcsv($out, array_keys($result[0]));
	foreach ($result as $row)
		fputcsv($out, $row);	
	
	// Close file
	rewind($out);
	fclose($out);
	dd($path);


}


private function computeSimilarity($vector, $num, $uid, $softwareAgent_id = 'amt'){
	$temp = array();
	foreach ($vector as $key=>$val) {
			$arr2 = array(
		        "[WORD_-3]"=>0,
		        "[WORD_-2]"=>0,
		        "[WORD_-1]"=>0,
		        "[WORD_+1]"=>0,
		        "[WORD_+2]"=>0,
		        "[WORD_+3]"=>0,
		        "[WORD_OTHER]"=>0,
		        "[NIL]"=>0,
		        "[CHECK_FAILED]"=>0);

			$arr2[$key] = 1;
			$temp[$key] = $this->similarity($vector, $arr2);

		}
	$temp['maxRelCos'] = max($temp);
	$temp['termno'] = $num;
	$temp['unit_id'] = $uid;
	$temp['numAnnots']= Annotation::where('unit_id', $uid)->where('softwareAgent_id', $softwareAgent_id)->count();
	return $temp;
}
 public function similarity(array $vec1, array $vec2) {
    return $this->_dotProduct($vec1, $vec2) / ($this->_absVector($vec1) * $this->_absVector($vec2));
  }
  
  protected function _dotProduct(array $vec1, array $vec2) {
    $result = 0;
    
    foreach (array_keys($vec1) as $key1) {
      foreach (array_keys($vec2) as $key2) {
	if ($key1 === $key2) $result += $vec1[$key1] * $vec2[$key2];
      }
    }
    
    return $result;
  }
  
  protected function _absVector(array $vec) {
    $result = 0;
    
    foreach (array_values($vec) as $value) {
      $result += $value * $value;
    }
    
    return sqrt($result);
  }

public function getTest($entity, $format, $domain, $docType, $incr){

		$id = "$entity/$format/$domain/$docType/$incr";

		$unit = MongoDB\Entity::id($id)->first();
		echo "<h1>{$unit->_id}</h1>\n";
		echo "-Sentence:{$unit->content['sentence']['formatted']}<br>\n";
		echo "-Term1:{$unit->content['terms']['first']['formatted']}<br>\n";
		echo "-Term2:{$unit->content['terms']['second']['formatted']}<br>\n";
		echo "<hr>\r\n";
		foreach(Annotation::where('unit_id', $unit->_id)->where('softwareAgent_id', 'amt')->get() as $ann){
			$dic = $ann->createDictionary();

			echo "<table>";
			foreach ($ann->content as $key => $value) {
				echo "<tr><td><b>$key</b></td><td>$value</td></tr>\r\n";
			}
			echo "</table>";
			echo "\r\nTERM1\r\n";
			echo "<table>";
			foreach ($dic['term1'] as $key => $value) {
				echo "<tr><td><b>$key</b></td><td>$value</td></tr>\r\n";
			}
			echo "</table>";
			echo "\r\nTERM2\r\n";
			echo "<table>";
			foreach ($dic['term2'] as $key => $value) {
				echo "<tr><td><b>$key</b></td><td>$value</td></tr>\r\n";
			}
			echo "</table>";
			echo "\r\n<hr>\r\n";
		}

}
// Status: Units found / not found
// reldir 360 - 1420
// relex 277 - 1530
// FactSpan 916 - 192
	

	public function getLoop(){
		foreach (Job::get() as $job){
			//if(!isset($job->projectedCost)){
				try{
				$batch = $job->batch;
				$reward = $job->jobConfiguration->content['reward'];
				$annotationsPerUnit = intval($job->jobConfiguration->content['annotationsPerUnit']);
				$unitsPerTask = intval($job->jobConfiguration->content['unitsPerTask']);
				$unitsCount = count($batch->wasDerivedFrom);
	            if(!$unitsPerTask)
	                $unitsPerTask = 1;
				    
				$projectedCost = round(($reward/$unitsPerTask)*($unitsCount*$annotationsPerUnit), 2);

				$count = 0;
				foreach ($job->annotations as $ann) {
					$count++;
				}
				$job->realCost = $count*$reward;

				$job->unitsCount = $unitsCount;
				$job->projectedCost = $projectedCost;
				$job->save;
				} catch (LogicException $e) {
					echo $e->getMessage();
				}
			//}
		}
	}

	public function getUpdatecrowdagent(){
		foreach (MongoDB\CrowdAgent::get() as $worker) {
			set_time_limit(30);
			Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($worker)));
		}
	}

	public function getRegenerateamtfactspan(){
		foreach (Job::type('FactSpan')->where('softwareAgent_id', 'amt')->get() as $job) {
			foreach ($job->annotations as $ann) {
				$ann->dictionary=$ann->createDictionary();
				$ann->save();
				echo "saved";
			}
			Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
		}
	}


	public function getRegenerateamtrelex(){
		$count = $failed = 0;
		foreach (Job::type('RelEx')->where('softwareAgent_id', 'amt')->get() as $job) {
			foreach ($job->annotations as $ann) {
				//dd($ann);
				$ann->dictionary=$ann->createDictionary();
				$ann->save();
				echo "saved";
				if(is_null($ann->dictionary)) $failed++;
				else $count++;
			}
			Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
		}
		echo "\r\nOK: $count. Failed: $failed";
	}



	public function getBatch() {
		$batches = Batch::where('documentType', 'batch')->get(); 
		$batch = unserialize(Session::get('batch'));
		if(!$batch) $selectedbatchid = ''; 
		else $selectedbatchid = $batch->_id;
		return View::make('process.tabs.batch')->with('batches', $batches)->with('selectedbatchid', $selectedbatchid);
	}

	public function getTemplate() {
		// Create array for the tree
		$batch = unserialize(Session::get('batch'));
		if(!$batch){
			Session::flash('flashNotice', 'Please select a batch first.');
			return Redirect::to("process/batch");
		} 

		$currenttemplate = Session::get('template');	
		
		if(empty($currenttemplate)){ 
			if($batch->format=='text')
				$currenttemplate = 'text/RelDir/relation_direction';

			else if($batch->format=="video") 
				$currenttemplate = 'video/SoundAndVision/videosegments';
			else 
				$currenttemplate = 'images/Rijksmuseum/flowers'; // TODO: should be cleaner
		}

		$treejson = $this->makeDirTreeJSON($currenttemplate, $batch->format);

		return View::make('process.tabs.template')
			->with('treejson', $treejson)
			->with('currenttemplate', $currenttemplate)
			->with('format', $batch->format);
	}

	public function getDetails() {
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$batch = unserialize(Session::get('batch'));
		$questiontemplateid = Session::get('questiontemplateid');
		//$j = new Job($batch, $template, $jc, $questiontemplate);
		$questionids = array();
		$goldfields = array();
		$unitscount = count($batch->wasDerivedFrom);

		if(isset($jc->content['unitsPerTask']) and  $jc->content['unitsPerTask'] > $unitscount){
			$jc->setValue('unitsPerTask', $unitscount); 
			Session::flash('flashNotice', 'Adapted units per task to match the batch size.');
		}	

/*		try {
			$questionids = $j->getQuestionIds();
			$goldfields = $j->getGoldFields();	
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		} 

		// Compare QuestionID's and goldfields.
		if($diff = array_diff($goldfields, $questionids))
			if(count($diff) == 1)
				Session::flash('flashNotice', 'Field \'' . array_values($diff)[0] . '\' is in the answerkey but not in the HTML template.');
			elseif(count($diff) > 1)
				Session::flash('flashNotice', 'Fields \'' . implode('\', \'', $diff) . '\' are in the answerkey but not in the HTML template.');
*/
		return View::make('process.tabs.details')
			->with('jobconf', $jc->content)
			->with('goldfields', $goldfields)
			->with('unitscount', $unitscount);
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
		
		return View::make('process.tabs.platform')->with('jobconf', $jc->content)->with('possible', $possibleplatforms);
	}

	public function postUploadTemplate(){
		$files = Input::file('files');
		$batch = unserialize(Session::get('batch'));
		$format = $batch->format;
 		$type = preg_replace("/[^0-9a-zA-Z ]/m", "", Input::get('type'));
		$destinationPath =  public_path() . "/templates/$format/$type";
		$extensions = array();

		try{	
			if(!file_exists($destinationPath))
				mkdir($destinationPath);

			foreach(Config::get('config.platforms') as $platformname)
				$extensions[] = App::make($platformname)->getExtension();

			foreach($files as $file){
				$filename = $file->getClientOriginalName();
				$extension =$file->getClientOriginalExtension(); 
				if(!in_array($extension, array_merge($extensions, array('js', 'css'))))
					throw new Exception("Filetype *.$extension not supported.");
				$file->move($destinationPath, $filename);
			}
			Session::flash('flashSuccess', 'Uploaded template.');
		} catch(Exception $e){
			Session::flash('flashError', $e->getMessage());
		}

		return Redirect::to('process/template');
	}

	public function getSubmit() {
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$batch = unserialize(Session::get('batch'));
		$questiontemplateid = Session::get('questiontemplateid');
		$treejson = $this->makeDirTreeJSON($template, $batch->format, false);
		
		$jc->unsetKey('platformpage');
		// TODO: this here is really bad.
		// The previews should be decoupled form AMT.
		// HTML should be generated based on the QuestionTemplate.
		try {
			$j = new Job;
			$j->batch_id = $batch->_id;
			$j->template = $template;
			$j->questionTemplate_id = $questiontemplateid;
			//$j->jobConf_id = $jobconf->_id;  // BAD
			$amt = App::make('amt');
			$questions = $amt->amtPublish($j, true,true, $jc);//$j->getPreviews();
		} catch (Exception $e) {
			$questions = array('couldn\'t generate previews.');
			Session::flash('flashNotice', $e->getMessage());
			//throw $e; // for debugging: see where it originates
		}

		$toomany = '';
		if($jc->content['unitsPerTask'] > count($batch->wasDerivedFrom)){
			$jc->setValue('unitsPerTask', count($batch->wasDerivedFrom)); 
			Session::flash('flashNotice', 'Adapted units per task to match the batch size.');
		}	

		if(!$jc->validate() or !empty($toomany)){
			$msg = '<ul>';
			foreach ($jc->getErrors()->all() as $message)
				$msg .= "<li>$message</li>";
			Session::flash('flashError', "$msg$toomany</ul>");
		} 

		return View::make('process.tabs.submit')
			->with('treejson', $treejson)
			->with('questions',  $questions)
			->with('table', $jc->toHTML())
			->with('template', '')//$jc->content['template'])
			->with('frameheight', (isset($jc->content['frameheight']) ? $jc->content['frameheight'] : 650))
			->with('jobconf', $jc->content);
	}

	public function getClearTask(){
		Session::forget('jobconf');
		Session::forget('origjobconf');
		Session::forget('template');
		Session::forget('questiontemplateid');
		Session::forget('batch');
		return Redirect::to("process/batch");
	}

	public function getDuplicate($entity, $format, $domain, $docType, $incr){
		Session::forget('jobconf');
		Session::forget('origjobconf');
		Session::forget('template');
		//Session::forget('questiontemplateid');
		Session::forget('batch');

		$job = Job::id("entity/$format/$domain/$docType/$incr")->first();
		if(!is_null($job)){
			//$jc = new JobConfiguration;
			$jc = $job->JobConfiguration->replicate();
			unset($jc->activity_id);
			$jc->parents= array($job->JobConfiguration->_id);
			Session::put('jobconf', serialize($jc));
			Session::put('batch', serialize($job->batch));
			Session::put('template', $job->template);
			// Job->parents = array($job->_id);
			return Redirect::to("process/batch");
		} else {
			Session::flash('flashError',"Job $id not found.");
			return Redirect::back();
		}


	}

	/*
	* Save the jobdetails to the database.
	*/
	public function postSaveDetails(){
		try {
			throw new Exception('Temporarily disabled this.'); // TODO
			$jc = unserialize(Session::get('jobconf'));
			if($jc->save())
				Session::flash('flashSuccess', 'Saved Job configuration to database!');
			else Session::flash('flashNotice', 'This Job configuration already exists.');
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
		$jc = unserialize(Session::get('jobconf', serialize(new JobConfiguration)));
		if(isset($jc->content)) $jcc = $jc->content;
		else $jcc = array();

		$template = Session::get('template');

		if(Input::has('batch')){
			// TODO: CSRF
			$batch = Batch::find(Input::get('batch'));
			Session::put('batch', serialize($batch));
		} else {
			$batch = unserialize(Session::get('batch'));
			if(empty($batch)){
				Session::flash('flashNotice', 'Please select a batch first.');
				return Redirect::to("process/batch");
			}	
		}

		if(Input::has('template')){
			// Create the JobConfiguration object if it doesn't already exist.
			$ntemplate = Input::get('template');
			if (empty($template) or ($template != $ntemplate))	
				$jc = JobConfiguration::fromJSON(Config::get('config.templatedir') . "$ntemplate.json");
			$template = $ntemplate;
			$origjobconf = 'jcid'; // TODO!


			// FOR TESTING -> static questiontemplate. // TODO!
			$filename = Config::get('config.templatedir') . $template . '.questiontemplate.json';
			if(file_exists($filename))
				$testdata = json_decode(file_get_contents($filename), true);
			else $testdata = null;
			/*if($testdata == null) 
				Session::flash('flashNotice', 'JSON not found or incorrectly formatted.');*/
			$qt = new QuestionTemplate;
			$qt->format = $batch->format;
			$qt->domain = $batch->domain;
			$qt->content = $testdata;
			$hash = md5(serialize($qt->content));
            $existing = QuestionTemplate::where('hash', $hash)->pluck('_id');
            
            if($existing) 
                $qtid = $existing;// Stop saving, it already exists.
            else{
	            $qt->hash = $hash;
				$qt->save();
				$qtid = $qt->_id;
			}
			Session::put('questiontemplateid', $qtid);
			//////////////////////////////////////////////////////////


		} else {
			if (empty($jc)){
				// No JobConfiguration and no template selected, not good.
				if($next != 'template')
					Session::flash('flashNotice', 'Please select a template first.');
				return Redirect::to("process/template");
			} else {
				// There already is a JobConfiguration object. Merge it with Input!
				$jcc = array_merge($jcc, Input::get());	

				// If leaving the details page...
				if(Input::has('title')){
					$jcc['answerfields'] = Input::get('answerfields', false);
					if($next == 'nextplatform'){
						if(isset($jcc['platform'][0])){
							$next = $jcc['platform'][0];
						} else {
							Session::flash('flashNotice', 'Please select a platform first');
							return Redirect::to("process/platform");
						}
					}
				}

				// If leaving the Platform page....:
				if(Input::has('platformpage'))
					$jcc['platform'] = Input::get('platform', array());


				// DEFAULT VALUES
				if(!isset($jcc['eventType'])) $jcc['eventType'] = 'HITReviewable'; 
				if(!isset($jcc['frameheight'])) $jcc['frameheight'] = 650;
				unset($jcc['_token']);
				$jc->content = $jcc;	

				// After specific platform tab, call the method and determine which is next.
				$pid = Input::get('platformid', false);
				if($pid){
					$platform = App::make($pid);
					$jc = $platform->updateJobConf($jc);

					if($next == 'nextplatform'){
						$nextindex = array_search($pid, $jc->content['platform']) + 1;
						if(array_key_exists($nextindex, $jc->content['platform']))
							$next = $jc->content['platform'][$nextindex];
						else
							$next = 'submit';	
					}				
				}
			}		
		}

		Session::put('jobconf', serialize($jc));
		Session::put('template', $template);

		try {
			return Redirect::to("process/$next");
		} catch (Exception $e) {
			Session::flash('flashError', $e->getMessage()); // Todo: is this a good way? -> logging out due to timeout
			return Redirect::to("process");
		}
	}

	/*
	* Send it to the platforms.
	*/
	public function postSubmitFinal($ordersandbox = 'order'){
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$batch = unserialize(Session::get('batch'));
		$questiontemplateid = Session::get('questiontemplateid');
		$jobs = array();

		if(!$jc->validate()){
			$msg = '';
			foreach ($jc->getErrors()->all() as $message)
				$msg .= "<li>$message</li>";
			Session::flash('flashError', "<ul>$msg</ul>");
			return Redirect::to("process/submit");
		}

		try{

			// Save activity
			$activity = new MongoDB\Activity;
			$activity->label = "Job is uploaded to crowdsourcing platform.";
			$activity->softwareAgent_id = 'jobcreator'; // JOB softwareAgent_id = $platform. Does this need to be the same?
			$activity->save();

			// Save jobconf if necessary
			$hash = md5(serialize($jc->content));
        	if($existingid = JobConfiguration::where('hash', $hash)->pluck('_id'))
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
			foreach($jc->content['platform'] as $platformstring){
				$j = new Job;
				$j->format = $batch->format;
				$j->domain = $batch->domain;
				$j->type = explode('/', $template)[1];
				$j->template = $template; // TODO: remove
				$j->batch_id = $batch->_id;
				$j->questionTemplate_id = $questiontemplateid;
				$j->jobConf_id = $jcid;
				$j->softwareAgent_id = $platformstring;
				$j->activity_id = $activity->_id;
				$j->publish(($ordersandbox == 'sandbox' ? true : false));
				$jobs[] = $j;
			}

			// Success.
			//Session::flash('flashSuccess', "Created " . ($ordersandbox == 'sandbox' ? 'but didn\'t order' : 'and ordered') . " job(s) on " . 
			//				strtoupper(implode(', ', $jc->content['platform'])) . '.');
			$successmessage = "Created job" . (count($jc->content['platform']) > 1 ? 's' : '') . " on " . 
							strtoupper(implode(', ', $jc->content['platform'])) . '. Order it by pressing the button under \'Actions\'. Demo jobs are published on the sandbox or internal channels only.';

			// TODO: this only takes the first job of potentially two
			if(!empty($jobs[0]->url))
				$successmessage .= ". After that, you can view it <a href='{$jobs[0]->url}' target='blank'>here</a>.";

			Session::flash('flashSuccess', $successmessage);
			return Redirect::to("jobs/");
//(Auth::user()->role == 'demo' ? '. Because this is a demo account, you can not order it. Please take a look at our finished jobs!' : '. Click on \'actions\' on the job to order it.')
		} catch (Exception $e) {

			// Undo creation and delete jobs
			if(isset($jobs))
			foreach($jobs as $j){
				if(isset($j->platformJobId))
					$j->undoCreation($j->platformJobId);
				$j->forceDelete();
			}		

			//delete activity
			if($activity) $activity->forceDelete();
			
			throw $e; //for debugging

			Session::flash('flashError', $e->getMessage());
			return Redirect::to("process/submit");
		}

		
	}


	/*
	* Create the JSON necessary for jstree to use.
	*/
	private function makeDirTreeJSON($currenttemplate, $format, $pretty = true){
		$r = array();
		$path = Config::get('config.templatedir') . $format . '/';

		foreach(File::directories($path) as $dir){
			$dirname = substr($dir, strlen($path));
		   	if($pretty) $displaydir = ucfirst(str_replace('_', ' ', $dirname));
		   	else $displaydir = $dirname;

			$r[] = array('id' => "$format/$dirname", 'parent' => '#', 'text' => $displaydir); 
			$donefilenames = array();
			foreach(File::allFiles($dir) as $file){
				$fullfilename = $file->getFileName();
				if (substr($fullfilename, -5) == '.html')
					$filename = substr($fullfilename, 0, -5);
				if ((substr($fullfilename, -4) == '.cml') or (substr($fullfilename, -4) == '.css'))
					$filename = substr($fullfilename, 0, -4);
				if (substr($fullfilename, -3) == '.js')
					$filename = substr($fullfilename, 0, -3);

				if (isset($filename) and !(in_array($filename, $donefilenames))) {
		   			if($pretty) $displayname = ucfirst(str_replace('_', ' ', $filename));
		   			else $displayname = $filename;
		   			if("$format/$dirname/$filename" == $currenttemplate)
		   				$r[] = array('id' => $filename, 'parent' => "$format/$dirname", 'text' => $displayname, 'state' => array('selected' => 'true'));
		   			else
		   				$r[] = array('id' => $filename, 'parent' => "$format/$dirname", 'text' => $displayname);
		   			
		   			$donefilenames[] = $filename;
		   		}	
			}
		}

//		dd(json_encode($r));
		return json_encode($r);
	}


	/**
	*	Catch all. If the platform exists, go to the platform page. Else, back to batch.
	*/
	public function missingMethod($parameters = array())
	{
	   $jc = unserialize(Session::get('jobconf'));
	   try{
	   		$platform = App::make($parameters[0]);
	   		return $platform->createView()->with('jobconf', $jc->content);
		} catch (ReflectionException $e){
			return Redirect::to("process/batch");
		}
	  // 
	}

}
