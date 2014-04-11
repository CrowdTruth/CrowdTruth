<?php
use Sunra\PhpSimple\HtmlDomParser;

class tempImportAndVectorsMethodsController extends BaseController {


	public function getConvertcsv(){
		if (($handle = fopen(storage_path() . '/jobs.csv', 'r')) === false) {
		    die('Error opening file');
		}
		
/*		foreach (\QuestionTemplate::get() as $q)
			$q->forceDelete();

		foreach (\JobConfiguration::get() as $q)
			$q->forceDelete();

		foreach (\Job::get() as $q)
			$q->forceDelete();
*/
		\MongoDB\Activity::truncate();
		$activity = new \MongoDB\Activity;
		$activity->label = "Imported jobs from CSV file.";
		//$activity->used = $job->_id;
		$activity->softwareAgent_id = 'importer';
		$activity->save();

		$headers = fgetcsv($handle, 1024, ',');
		$count = 0;
		$complete = array();
		while ($row = fgetcsv($handle, 1024, ',')) {

			$c = array_combine($headers, $row);
			$c['platform'] = array($c['platform']);

			$j = new JobConfiguration;
			//$j->_id = "entity/text/medical/jobconf/$count";
			$j->type = (isset($row['type']) ? $row['type'] : 'todo');
			$j->content = $c;
			$j->hash = md5(serialize($j->content));
			$j->activity_id = $activity->_id;
			$j->user_id = 'CrowdWatson';
			$j->save();

			$job = new Job;
			$job->jobConf_id = $j->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = "entity/text/medical/batch/$count";
			$job->type = (isset($row['type']) ? $row['type'] : 'todo');
			$job->user_id = 'CrowdWatson';
			$job->status = 'finished';
			$job->save();
			$count++;

		} //new MongoDate(strtotime(

		fclose($handle);

		echo json_encode($complete, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	}


	public function getHit($id){
		$turk = new Cw\Mturk\Turkapi\MechanicalTurk('https://mechanicalturk.amazonaws.com/', false, Config::get('mturk::accesskey'), Config::get('mturk::secretkey'));
		//$hit = $turk->getHIT($id);
		//dd($hit);
		//dd($turk->getAssignmentsForHIT($id));
		//echo json_encode($hit, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
							// Create activity: annotate
							$activity = new \MongoDB\Activity;
							$activity->label = "Imported job from AMT.";
							$activity->softwareAgent_id = 'amt';
							$activity->save();

		$f = fopen(base_path() . '/public/csv/AMT_HITIds.csv', 'r');
		$array = array();
		$keys = fgetcsv($f);
		while (!feof($f)) {
		    $array[] = @array_combine($keys, fgetcsv($f));
		}
		foreach($array as $row){
			set_time_limit (360);
			$id = $row['HITId'];
			$type = $row['Task'];
			
			$existing = \Annotation::where('hitId', $id)->first();
			if(isset($existing))
				continue;

			$hit = $turk->getHIT($id);
			$h = $hit->toArray();
			$jobId = (isset($h['RequesterAnnotation']) ? $h['RequesterAnnotation'] : 'unknown');//$job->_id;
/*
			//Do this once:
			if(empty($job->Expiration)) $job->Expiration = new MongoDate(strtotime($h['Expiration']));
			if(empty($job->HITGroupId)) $job->HITGroupId = $h['HITGroupId'];
			if(empty($job->HITTypeId)) 	$job->HITTypeId  = $h['HITTypeId'];*/

			// Convert status to our language
			if($h['HITStatus'] == 'Assignable' or $h['HITStatus'] == 'Unassignable')
			//and ($job->Expiration->sec > time())) // Not yet expired. TODO: Timezones
				$newstatus = 'running';
			elseif($h['HITStatus'] == 'Reviewable' or $h['HITStatus'] == 'Reviewing')
				$newstatus = 'review';
			elseif($h['HITStatus'] == 'Disposed')
				$newstatus = 'deleted';

			$newplatformhitid[] = array('id' => $id, 
										'status' => $newstatus);
			
			// todo: IF each is disposed, newstatus = deleted.

			// Get Assignments.
	
			$assignments = $turk->getAssignmentsForHIT($id);
			print 'Got ' . count($assignments) . " Assignments for $id\n";
			
			foreach ($assignments as $ass){
				$assignment = $ass->toArray();

				$annotation = Annotation::where('job_id', $jobId)
								->where('platformAnnotationId', $assignment['AssignmentId'])
								->first();
				
				//print_r($annotations); die();
				if($annotation) { 
					$annoldstatus = $annotation['status'];
					$annnewstatus = $assignment['AssignmentStatus'];

					if($annoldstatus != $annnewstatus){
						$annotation->status = $annnewstatus;
						$annotation->update();
						print "Status '$annoldstatus' changed to '$annnewstatus'.";
						Log::debug("Status of Annotation {$annotation->_id} changed from $annoldstatus to $annnewstatus");
					}
				} else { // ASSIGNMENT entity not in DB: create activity, entity and refer to or create agent.

					// Create or retrieve Agent
					$workerId = $assignment['WorkerId'];
					if(!$agentId = MongoDB\CrowdAgent::where('platformAgentId', $workerId)->where('softwareAgent_id', 'amt')->pluck('_id')){ 
						$agent = new MongoDB\CrowdAgent;
						$agent->_id= "crowdagent/amt/$workerId";
						$agent->softwareAgent_id= 'amt';
						$agent->platformAgentId = $workerId;
						$agent->save();		
						$agentId = "crowdagent/amt/$workerId";
					}

					
/*							$groupedbyid = array();
					foreach ($assignment['Answer'] as $q=>$ans){
						// Retrieve the unitID and the QuestionId from the name of the input field.
						$split = strpos($q, "_");
						$unitid = substr($q, 0, $split); 	 // before the first _
						$qid = substr($q, $split+1);		// after the first _
						$groupedbyid[$unitid][$qid] = $ans;// grouped to create an entity for every ID.
					}
					
					// Create entity FOR EACH UNIT
					foreach($groupedbyid as $uid=>$qidansarray){	*/
						$annotation = new Annotation;
						$annotation->activity_id = $activity->_id;
						$annotation->type = $type;
						$annotation->crowdAgent_id = $agentId;
						$annotation->softwareAgent_id = 'amt';
						$annotation->job_id = $jobId;
						$annotation->unit_id = '';
						$annotation->hitId = $id;
						$annotation->question = $h['Question'];
						$annotation->platformAnnotationId = $assignment['AssignmentId'];
						$annotation->acceptTime = new MongoDate(strtotime($assignment['AcceptTime']));
						$annotation->submitTime = new MongoDate(strtotime($assignment['SubmitTime']));
						//
						// Todo: Optionally compute time spent doing the assignment here.
						//
						if(!empty($assignment['AutoApprovalTime']))
							$annotation->autoApprovalTime = new MongoDate(strtotime($assignment['AutoApprovalTime']));
						if(!empty($assignment['ApprovalTime']))
							$annotation->autoApprovalTime = new MongoDate(strtotime($assignment['ApprovalTime']));
						if(!empty($assignment['RejectionTime']))
							$annotation->autoApprovalTime = new MongoDate(strtotime($assignment['RejectionTime']));

						$annotation->content = $assignment['Answer'];
						$annotation->status = $assignment['AssignmentStatus']; // Submitted | Approved | Rejected
						$annotation->user_id = 'CrowdWatson';
						$annotation->save();

						$newannotations[] = $annotation;

					//}

					/*
						Possibly also:

						HITId				2P3Z6R70G5RC7PEQC857ZSST0J2P9T
						Deadline	
					*/
				}

			}

		}
	}

	public function getUnitsreldir(){
		$count = 0;
		foreach(\Annotation::where('type', 'RelDir')->get() as $ann){
			set_time_limit(30);
			if(!isset($ann->content)){
				echo "{$ann->_id} no content\r\n";
				echo "--------------------------------\r\n";
				continue;
			}

/*			if(!empty($ann->unit_id)){
				echo "{$ann->_id} has unitid\r\n";
				continue;
			}*/


			//$xml = simplexml_load_string($ann->question);
			//$url = (string) $xml->ExternalURL;
			$xml = simplexml_load_string($ann->question);
			//$html = $ann->question;//
			$html = (string) $xml->HTMLContent;
		//	dd($html);
			$dom = HtmlDomParser::str_get_html($html);
			$sentence = rtrim($dom->find('span[style=color:#0000CD;]', 0)->innertext, '.'); 
			$input1 = $dom->find('span[class=answertext]', 0)->innertext;
			$term1 = substr($input1, 0, strpos($input1, ']')+1);
			$term2 = substr($input1, strpos(substr($input1, 2), '[') + 2);


			$unit = \MongoDB\Entity::where('content.terms.first.formatted', $term1)
			->where('content.terms.second.formatted', $term2)
			->where('content.sentence.formatted', $sentence)
			->first();

/*			$count++;
			if($count==150)
				die();*/

			if($unit){
				echo "\r\n\YY $sentence\r\n"; 
				echo "== " . $unit->content['sentence']['formatted'];
				echo "\r\n{$ann->_id}->{$unit->_id}\r\n"; 
				$ann->unit_id = $unit->_id;
				$ann->save();
			} else {	

				echo "\r\nNO {$ann->_id}\r\n$term1--$term2--$sentence\r\n"; 
				//echo $punit->content['sentence']['formatted'];
				//echo "\r\n{$ann->unit_id}----------------\r\n"; 
				echo "----------------------------------------";
				continue;
			}



		}
	}


	public function getUnitsfactspan(){
		$count = 0;
		foreach(\Annotation::where('type', 'FactSpan')->get() as $ann){
			
			set_time_limit(300);

			if(!isset($ann->content)){
				echo "{$ann->_id} no content\r\n";
				echo "--------------------------------\r\n";
				continue;
			}

/*			if(!empty($ann->unit_id)){
				echo "{$ann->_id} has unitid\r\n";
				continue;
			}*/

			//dd($ann->question);
			$xml = simplexml_load_string($ann->question);
			$url = (string) $xml->ExternalURL;
			$html = file_get_contents($url);
			$dom = HtmlDomParser::str_get_html($html);
			$sentence = rtrim($dom->find('span[class=senval]', 0)->innertext, '.'); // There appears to be a dot behind it.
			$term1 = $dom->find('input[value=YES]', 0)->next_sibling(0)->children(0)->innertext;
			$term2 = $dom->find('input[value=YES]', 1)->next_sibling(0)->children(0)->innertext;

/*			$sentence = "Poisson regression analysis which included data for multiple measurements of Tme/[TE] over the first year of life and adjusted for age-at-test and maternal smoking during [PREGNANCY] also demonstrated a greater decrease in Tme/Te in female infants who subsequently develop an LRI (P = 0.08";
			$term1 = "[PREGNANCY]";
			$term2 = "[TE]";*/

			$unit = \MongoDB\Entity::where('content.terms.first.formatted', $term1)
			->where('content.terms.second.formatted', $term2)
			->where('content.sentence.formatted', $sentence)
			->first();

			$count++;

			if($unit){
				echo "\r\n\YY $sentence\r\n"; 
				echo "== " . $unit->content['sentence']['formatted'];
				echo "\r\n{$ann->_id}->{$unit->_id}\r\n"; 
				$ann->unit_id = $unit->_id;
				$ann->save();
			} else {	

				echo "\r\nNO {$ann->_id}\r\n $sentence\r\n"; 
				//echo $punit->content['sentence']['formatted'];
				//echo "\r\n{$ann->unit_id}----------------\r\n"; 
				echo "----------------------------------------";
				continue;
			}



		}
	}

	public function getUnitsrelex(){
		$count = 0;
		foreach(\Annotation::where('type', 'RelEx')->get() as $ann){
			set_time_limit(30);
			if(!isset($ann->content)){
				echo "{$ann->_id} no content\r\n";
				echo "--------------------------------\r\n";
				continue;
			}

			if(!empty($ann->unit_id)){
				echo "{$ann->_id} has unitid\r\n";
				continue;
			}

			//dd($ann->question);
			//$xml = simplexml_load_string($ann->question);
			//$url = (string) $xml->ExternalURL;
			$xml = simplexml_load_string($ann->question);
			//$html = $ann->question;//
			$html = (string) $xml->HTMLContent;
			//dd($html);
			$dom = HtmlDomParser::str_get_html($html);
			$sentence = rtrim($dom->find('span[class=senval]', 0)->innertext, '.'); 
			$term1 = $dom->find('span[style=color:#0000CD;]', 1)->innertext;
			$term2 = $dom->find('span[style=color:#0000CD;]', 2)->innertext;

/*			$sentence = "Poisson regression analysis which included data for multiple measurements of Tme/[TE] over the first year of life and adjusted for age-at-test and maternal smoking during [PREGNANCY] also demonstrated a greater decrease in Tme/Te in female infants who subsequently develop an LRI (P = 0.08";
			$term1 = "[PREGNANCY]";
			$term2 = "[TE]";*/

			$unit = \MongoDB\Entity::where('content.terms.first.formatted', $term1)
			->where('content.terms.second.formatted', $term2)
			->where('content.sentence.formatted', $sentence)
			->first();



/*			if(!$unit){
				$hi = 0;
				$units = \MongoDB\Entity::where('content.terms.first.formatted', $term1)
						->where('content.terms.second.formatted', $term2)
						->get();

				foreach($units as $punit){
					try{
						$pct = similar_text($sentence, $punit->content['sentence']['formatted']);
					} catch (ErrorException $e) {
						echo "\r\n\r\n\r\n\r\n{$punit->_id}\r\n\r\n\r\n\r\n";
						$pct = similar_text(strtolower($sentence), strtolower($punit->content['sentence']['text']));
					}	
					if($pct>$hi) {
						$hi=$pct;
						$unit = $punit;
					}
				}

			} */

			if($unit){
				echo "\r\n\YY $sentence\r\n"; 
				echo "== " . $unit->content['sentence']['formatted'];
				echo "\r\n{$ann->_id}->{$unit->_id}\r\n"; 
				$ann->unit_id = $unit->_id;
				$ann->save();
			} else {	

				echo "\r\nNO {$ann->_id}\r\n$term1--$term2--$sentence\r\n"; 
				//echo $punit->content['sentence']['formatted'];
				//echo "\r\n{$ann->unit_id}----------------\r\n"; 
				echo "----------------------------------------";
				continue;
			}



		}
	}




	public function getAddvectors($basevector = null){
		$job = Job::first();

		$seed = array("[WORD_-3]"=>0,
					"[WORD_-2]"=>0,
					"[WORD_-1]"=>0,
					"[WORD_+1]"=>0,
					"[WORD_+2]"=>0,
					"[WORD_+3]"=>0,
					"[WORD_OTHER]"=>0,
					"[NIL]"=>0,
					"[CHECK_FAILED]"=>0);

		$result = array();
		foreach($job->batch->wasDerivedFrom as $unit){
			$a1t1 = $seed; $a1t2 = $seed;
			$annotations = Annotation::where('unit_id', $unit['_id'])->get();
			foreach($annotations as $ann){
				if(!isset($ann->dictionary))
					continue;
				// TODO: smarter? At least more different types.

				// Term 1
				$sums = array();
				$a2 = $ann->dictionary['term1'];
				foreach (array_keys($a1t1 + $a2) as $key) {
				    $sums[$key] = (isset($a1t1[$key]) ? $a1t1[$key] : 0) + (isset($a2[$key]) ? $a2[$key] : 0);
				}
				$a1t1 = $sums;

				// Term 2
				$sums = array();
				$a2 = $ann->dictionary['term2'];
				foreach (array_keys($a1t2 + $a2) as $key) {
				    $sums[$key] = (isset($a1t1[$key]) ? $a1t1[$key] : 0) + (isset($a2[$key]) ? $a2[$key] : 0);
				}
				$a1t2 = $sums;
			}
			$result[$unit['_id']] = array('term1'=>$a1t1, 'term2'=>$a1t2);
		}	

		$job->result = $result;
		$job->save();
	}

	public function getVectorsfactspan(){

		//
		// TODO: Some sentences [probably in 265 annotations] are different: Each time a factor is near "/" and "-", you actually need to the remove the spaces.
		// We still have the Question URL in the annotation so we could use that to doublecheck.
		// Also: check the business rules, should there be more validation?
		//

		$count =0;
		foreach(\Annotation::where('type', 'FactSpan')->get() as $ann){
			if(isset($ann->dictionary))
				continue;

			if(isset($ann->unit->content['sentence']['formatted']))
				$sentence = $ann->unit->content['sentence']['formatted'];
			else 
				$sentence = $ann->unit->content['sentence']['text'];
			
			$term1 = $ann->unit->content['terms']['first']['formatted'];
			$term2 = $ann->unit->content['terms']['second']['formatted'];

			// Set indices1
			$charindex1 = strpos($sentence, $term1);
			$index1start = substr_count(substr($sentence, 0, $charindex1), ' ')+1;
			$indices1 = array($index1start);
			for ($i=0; $i < substr_count($term1, ' '); $i++)
				array_push($indices1, $index1start+$i+1);

			// Set indices2
			$charindex2 = strpos($sentence, $term2);
			$index2start = substr_count(substr($sentence, 0, $charindex2), ' ')+1;
			$indices2 = array($index2start);
			for ($i=0; $i < substr_count($term2, ' '); $i++)
				array_push($indices2, $index2start+$i+1);
			
			$ans = $ann->content;
			if(!isset($ans['Q1'])){
				$ans['Q1'] = 'YES';
			}

			 if(!isset($ans['Q2'])){
			 	$ans['Q2'] = 'YES';
			 }

			/*$ans = array("Q1"=>"YES",
			  "expl1span"=>"29",
			  "expltext1"=>"[PREGNANCY]",
			  "expltext1yesquestion"=>"PREGNANCY blabla",
			  "Q2"=>"NO",
			  "expl2span"=>"11,12,13",
			  "expltext2"=> "multiple measurements of Tme [TE] over the first maternal smoking during [PREGNANCY] ",
			  "expltext2yesquestion"=>"");*/

			$a1indices = explode(',', $ans['expl1span']);
			$a2indices = explode(',', $ans['expl2span']);

			// Q1
			if($ans["Q1"] == 'YES'){
				if((rtrim($ans["expltext1"]) != $term1) or (!$this->isOkYesQuestion($ans['expltext2yesquestion'], $term1, $sentence)))) // [maybe check indices as well?]
					$vector1 = $this->createFactVect(true); // FAILED
				else {
					$vector1 = $this->createFactVect(false, 0, 0); // YES it's the same.
				}	
			} else {
				if((rtrim($ans["expltext1"]) == $term1) or (empty($ans["expl1span"])))
					$vector1 = $this->createFactVect(true); // FAILED
				else {
					$startdiff = $a1indices[0] - $indices1[0];
					$enddiff = end($a1indices) - end($indices1);
					$vector1 = $this->createFactVect(false, $startdiff, $enddiff);		
				}	
			}
			
			// Q2
			if($ans["Q2"] == 'YES'){
				if((rtrim($ans["expltext2"]) != $term2) or (!$this->isOkYesQuestion($ans['expltext2yesquestion'], $term2, $sentence))) // TODO: harsher
					$vector2 = $this->createFactVect(true); // FAILED
				else {
					$vector2 = $this->createFactVect(false, 0, 0); // YES it's the same.
				}	
			} else {
				if((rtrim($ans["expltext2"]) == $term2) or (empty($ans["expl2span"])))
					$vector2 = $this->createFactVect(true); // FAILED
				else {
					$startdiff = $a2indices[0] - $indices2[0];
					$enddiff = end($a2indices) - end($indices2);
					$vector2 = $this->createFactVect(false, $startdiff, $enddiff); 
				}
			}	

			$ann->dictionary = array('term1' => $vector1, 'term2' => $vector2);
			$ann->save();

		}
	}		

	private function isOkYesQuestion($yesquestion, $term, $inputsentence){
		
/*		1. the sentence contains the complete term
		2. the sentence has more than 4 words
		3. the sentence is not equal to the input sentence*/
		return true;

	}

	private function createFactVect($failed = false, $startdiff=null, $enddiff=null){
		$vector = array(
		"[WORD_-3]"=>0,
		"[WORD_-2]"=>0,
		"[WORD_-1]"=>0,
		"[WORD_+1]"=>0,
		"[WORD_+2]"=>0,
		"[WORD_+3]"=>0,
		"[WORD_OTHER]"=>0,
		"[NIL]"=>0,
		"[CHECK_FAILED]"=>0);

		if($failed){
			$vector["[CHECK_FAILED]"] = 1;
			return $vector;
		}

		if($startdiff<-3 or $enddiff > 3){
			$vector["[WORD_OTHER]"]=1;
			return $vector;
		} // TODO; when else?

		if($startdiff == 0 and $enddiff == 0){
			$vector["[NIL]"]=1;
			return $vector;
		} 

		$vector["[WORD_-3]"]= ($startdiff < -2 ? 1 : 0);
		$vector["[WORD_-2]"]= ($startdiff < -1 ? 1 : 0);
		$vector["[WORD_-1]"]= ($startdiff <  0 ? 1 : 0);
		$vector["[WORD_+1]"]= ($enddiff   >  0 ? 1 : 0);
		$vector["[WORD_+2]"]= ($enddiff   >  1 ? 1 : 0);
		$vector["[WORD_+3]"]= ($enddiff   >  2 ? 1 : 0);


		return $vector;
	}

	public function getVectorsrelex(){
		foreach(\Annotation::where('type', 'RelEx')->get() as $ann){

			if(!isset($ann->content))
				dd($ann);

			$ans = str_replace(" ", "_", rtrim($ann->content['Q1text']));			
			$ans = str_replace("[DIAGNOSED_BY_TEST_OR_DRUG]", "[DIAGNOSE_BY_TEST_OR_DRUG]", $ans);
			if($ans == '') dd($ann);//$ans = "[NONE]";

			$ans = str_replace("]_[", "]*[", $ans);
			$ans = explode('*', $ans);

			if(!isset($ans))
				dd($ann);
			
			$dic = array(
			"[TREATS]" => 					(in_array("[TREATS]", 					$ans ) ? 1 : 0),
			"[CAUSES]" => 					(in_array("[CAUSES]", 					$ans ) ? 1 : 0),
			"[PREVENTS]" => 				(in_array("[PREVENTS]", 				$ans ) ? 1 : 0),
			"[IS_A]" => 					(in_array("[IS_A]", 					$ans ) ? 1 : 0),
			"[OTHER]" => 					(in_array("[OTHER]", 					$ans ) ? 1 : 0),
			"[NONE]" => 					(in_array("[NONE]", 					$ans ) ? 1 : 0),
			"[PART_OF]" => 					(in_array("[PART_OF]", 				 	$ans ) ? 1 : 0),
			"[DIAGNOSE_BY_TEST_OR_DRUG]" => (in_array("[DIAGNOSE_BY_TEST_OR_DRUG]",	$ans ) ? 1 : 0),
			"[ASSOCIATED_WITH]" => 			(in_array("[ASSOCIATED_WITH]", 		 	$ans ) ? 1 : 0),
			"[SIDE_EFFECT]" => 				(in_array("[SIDE_EFFECT]",				$ans ) ? 1 : 0),
			"[SYMPTOM]" => 					(in_array("[SYMPTOM]", 				 	$ans ) ? 1 : 0),
			"[LOCATION]" => 				(in_array("[LOCATION]", 				$ans ) ? 1 : 0),
			"[MANIFESTATION]" => 			(in_array("[MANIFESTATION]", 			$ans ) ? 1 : 0),
			"[CONTRAINDICATES]" => 			(in_array("[CONTRAINDICATES]", 		 	$ans ) ? 1 : 0));

			foreach($ans as $a){
				if(!in_array($a, $dic))
					echo "<h1>$a</h1>";
			}

			if(!in_array(1, $dic)){
				echo "<h1>";
				print_r($ans);
				print_r($dic);
				echo "</h1>"; 
			}
/*			$ann->dictionary = $dic;
			$ann->update();*/
		}
	}	

	public function getVectorsreldir(){ //FactSpan
		foreach(\Annotation::where('type', 'RelDir')->get() as $ann){

			if(!isset($ann->content))
				dd($ann);

			$ans = $ann->content['Q1'];
			
			if(!isset($ans))
				dd($ann);
			
			$dic = array(
                "{{terms_first_text}} {{relation_noPrefix}} {{terms_second_text}}" => ($ans == 'Choice1' ? 1 : 0),
                "{{terms_second_text}} {{relation_noPrefix}} {{terms_first_text}}" => ($ans == 'Choice2' ? 1 : 0),
                "no_relation" => ($ans == 'Choice3' ? 1 : 0));

			$ann->dictionary = $dic;
			$ann->update();
		}

	}




	// CF RELDIR
	public function getCfreldirvector(){

		$jobs = Job::type('RelDir')->get();
		$count = 0;
		foreach ($jobs as $job) {
			foreach($job->annotations as $ann){
				$count++;
				$u = $ann->unit['content'];

				$ann->type = $job->type;
				$ans = $ann->content['direction'];
				
				if($ans == 'no_relation')
					$realans = 'Choice3';
				else {
					if (strpos($ans, $u['terms']['first']['formatted']) === 0)
						$realans = 'Choice1';
					elseif (strpos($ans, $u['terms']['first']['formatted']) > 0)
						$realans = 'Choice2';
					else
						$realans = '-------';

				}

				echo "$realans - $ans\r\n";

				$ann->dictionary = array(
		            'Choice1' => (($ans == 'Choice1') ? 1 : 0),
		            'Choice2' => (($ans == 'Choice2') ? 1 : 0),
		            'Choice3' => (($ans == 'Choice3') ? 1 : 0)
		            );

				$ann->save();
			}

			$job = Job::id('entity/text/medical/job/0')->first();
			Queue::push('Queues\UpdateJob', array('job' => serialize($job)));

		}


	
	}

	public function getUpdatebatches(){
		
		foreach(Job::get() as $job){
			$list = array();
			foreach (Annotation::type($job->type)->get() as $ann) {
				
				if(!empty($ann->unit_id))
					$list[] = $ann->unit_id;
			}

			$batch = $job->batch;
			$batch->parents= array_unique($list);
			$batch->save();

			//Queue::push('Queues\UpdateJob', array('job' => serialize($job)));
		}
	}



}

?>	