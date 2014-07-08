<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;
use \MongoDate;

use URL, Session, Exception, File, Input, Redirect, App;

use League\Csv\Reader as Reader;

class AMTCSVresultMapper {

	public function processInputData($csvresult, $preview = false)
	{
		$csvLines = explode("\n", $csvresult['content']);
		$csvLines = array_filter($csvLines, 'strlen');

		$csv = Reader::createFromString(implode("\n", $csvLines));

		// $csvLines = explode("\n", $csvresult['content']);
		// dd($csvLines);


		
		$headers = $csv->fetchOne();

		foreach($headers as $columnIndex => $columnValue)
		{
			if($columnValue == "")
			{
				$headers[$columnIndex] = "empty_" . $columnIndex;
			}
		}

		$data = $csv->fetchAssoc($headers);

		$rowsMappedWithUnits = array();
		$batch = array();

		unset($data[0]); // Unsetting header
		$count = 0;

		foreach ($data as $line_index => $row)
		{
			$count++;
			$entity = Entity::where('documentType', 'relex-structured-sentence');

			if(isset($row['relation-type']))
			{
				$entity = $entity->where('content.relation.original', '=', $row['relation-type']);
			}
			elseif (isset($row['relation_type'])) 
			{
				$entity = $entity->where('content.relation.original', '=', $row['relation_type']);
			}
			else
			{
				continue;
			}

			if(isset($row['term1']))
			{
				if (substr($row['term1'], 0, 1) == '[')
					$entity = $entity->where('content.terms.first.formatted', '=', $row['term1']);
				else
					$entity = $entity->where('content.terms.first.text', '=', $row['term1']);
			}
			else
			{
				continue;
			}

			if(isset($row['term2']))
			{
				if (substr($row['term2'], 0, 1) == '[')
					$entity = $entity->where('content.terms.second.formatted', '=', $row['term2']);
				else
					$entity = $entity->where('content.terms.second.text', '=', $row['term2']);
			}
			else
			{
				continue;
			}
/*echo $row['term2'] . '>';			
echo $entity->first()->content['terms']['first']['startIndex'];
echo ' - ' . $row['b1'] . '. ';*/
			if(isset($row['b1']))
			{
				$entity = $entity->where('content.terms.first.startIndex', '=', (int) $row['b1']);
			}
			else
			{
				continue;
			}
		
			if(isset($row['e1']))
			{
				$entity = $entity->where('content.terms.first.endIndex', '=', (int) $row['e1']);
			}

			if(isset($row['b2']))
			{
				$entity = $entity->where('content.terms.second.startIndex', '=', (int) $row['b2']);
			}
			else
			{
				continue;
			}

			if(isset($row['e2']))
			{
				$entity = $entity->where('content.terms.second.endIndex', '=', (int) $row['e2']);
			}

			if($result = $entity->first())
			{
			    $row['unit'] = $result->toArray();
			    array_push($rowsMappedWithUnits, $row);
			    array_push($batch, $result->toArray()['_id']);	
			} else {
				print_r($row);
				echo "\r\n";
			}
		}
		
		if(count($rowsMappedWithUnits) != $count)
			return count($rowsMappedWithUnits) . " != $count";
		

		if($preview)
		{
			return $rowsMappedWithUnits;
		}

		return $batch;
	}

	public function processRel($csvresult, $workerunitCSVArray, $preview)
	{
		$mappedWorkerunitsWithUnits = array();
		$batchOfUnits = array();

		$notMatched = array();

		foreach ($workerunitCSVArray as $line_index => $row)
		{
			$entity = Entity::where('documentType', 'relex-structured-sentence');

			if(isset($row['sent_id']))
			{
				$entity = $entity->where('sent_id', '=', $row['sent_id']);
			}

			if($result = $entity->first())
			{
			    $row['unit'] = $result->toArray();
			    array_push($mappedWorkerunitsWithUnits, $row);
			    array_push($batchOfUnits, $result->toArray()['_id']);	
			} else {

				if($result = Entity::where('content.sentence.formatted', '=', $row['sentence'])->first())
				{
					$row['unit'] = $result->toArray();
					array_push($mappedWorkerunitsWithUnits, $row);
					array_push($batchOfUnits, $result->toArray()['_id']);	
				}
				else
				{
					array_push($notMatched, $row);
				}				
			}
		}

		// return $notMatched;

		$batchOfUnits = array_values(array_unique($batchOfUnits));
		natsort($batchOfUnits);
		$batchOfUnits = array_values($batchOfUnits);

		if($preview) {
			return $mappedWorkerunitsWithUnits;
		}

		if(strpos($csvresult->title, 'relexcorrected') !== false && strpos($csvresult->title, 'f389000') !== false)
		{
			$status = $this->relexc_f389000($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(strpos($csvresult->title, 'relexcorrected') !== false && strpos($csvresult->title, 'f389001') !== false)
		{
			$status = $this->relexc_f389001($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(strpos($csvresult->title, 'reldirc') !== false && strpos($csvresult->title, 'f391072') !== false)
		{
			$status = $this->reldirc_f391072($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(strpos($csvresult->title, 'reldirc') !== false && strpos($csvresult->title, 'f391073') !== false)
		{
			$status = $this->reldirc_f391073($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(strpos($csvresult->title, 'reldirc') !== false && strpos($csvresult->title, 'f391076') !== false)
		{
			$status = $this->reldirc_f391076($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}

		return $status;
	}

	public function processWorkerunitData($csvresult, $preview = false)
	{
		set_time_limit(6000);
		$csvLines = explode("\n", $csvresult['content']);
		$csvLines = array_filter($csvLines, 'strlen');

		// $csvLines = array_slice($csvLines, 0, 2);

		$csv = Reader::createFromString(implode("\n", $csvLines));

		$delimiter = $csv->detectDelimiter();
		$csv->setDelimiter($delimiter);

		$headers = $csv->fetchOne();

		// dd($headers);

		foreach($headers as $columnIndex => $columnValue)
		{
			if($columnValue == "")
			{
				$headers[$columnIndex] = "empty_" . $columnIndex;
			}
		}

		$rowsMappedWithUnits = array();
		$batch = array();

		// dd($headers);

		// dd($csv->fetchAll());

		$workerunitCSVArray = $csv->fetchAssoc($headers);

		unset($workerunitCSVArray[0]);

		if(strpos($csvresult->title, 'relexcorrected') !== false || strpos($csvresult->title, 'reldir') !== false)
		{
			$result = $this->processRel($csvresult, $workerunitCSVArray, $preview);

			return $result;
		}

		// dd($workerunitCSVArray);

		$inputCSVFiles = \MongoDB\Entity::where('documentType', 'csvresult')->where('title', 'like', '%input%')->get();

		$processedInputCSVFiles = array();

		foreach($inputCSVFiles as $inputCSVFile)
		{
			array_push($processedInputCSVFiles, $this->processInputData($inputCSVFile->toArray(), true));
		}

		$aggregatedProcessedInputCSVFiles = array();

		foreach($processedInputCSVFiles as $processedInputCSVFile)
		{
			foreach($processedInputCSVFile as $processedInputCSVFileMapped)
			{
				array_push($aggregatedProcessedInputCSVFiles, $processedInputCSVFileMapped);
			}
		}

		$mappedWorkerunitsWithUnits = array();
		$batchOfUnits = array();

		foreach($workerunitCSVArray as $workerunitRow)
		{
			foreach($aggregatedProcessedInputCSVFiles as $mappedInputRow)
			{
				if(strpos($csvresult->title, 'annotation_output_cf_factorspan_merged_relex') !== false)
				{
					if(stripos($workerunitRow['Sent_id'], '-'))
					{
						$workerunitRowIndex = explode('-', $workerunitRow['Sent_id'])[0];
					}
				}
				else
				{
					$workerunitRowIndex = $workerunitRow['index'];
				}

				if($workerunitRowIndex == $mappedInputRow['index'])
				{
					$mappedWorkerunitWithUnit = $workerunitRow;
					$mappedWorkerunitWithUnit['unit'] = $mappedInputRow['unit'];

					array_push($batchOfUnits, $mappedInputRow['unit']['_id']);
					array_push($mappedWorkerunitsWithUnits, $mappedWorkerunitWithUnit);
				}
			}
		}

		$batchOfUnits = array_values(array_unique($batchOfUnits));
		natsort($batchOfUnits);

		$batchOfUnits = array_values($batchOfUnits);

		if($preview == true)
		{
			return $mappedWorkerunitsWithUnits;
		}

		if(stripos($csvresult->title, "factorspan") && stripos($csvresult->title, "380140"))
		{
			$status = $this->factorspan_308140($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(stripos($csvresult->title, "factorspan") && stripos($csvresult->title, "380640"))
		{
			$status = $this->factorspan_380640($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(stripos($csvresult->title, "factorspan") && stripos($csvresult->title, "382004"))
		{
			$status = $this->factorspan_382004($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(strpos($csvresult->title, 'annotation_output_cf_factorspan_merged_relex') !== false)
		{
			$status = $this->workerunit_output_cf_factorspan_merged_relex($mappedWorkerunitsWithUnits);
		}
		elseif(stripos($csvresult->title, "relexu") && stripos($csvresult->title, "f387177"))
		{
			$status = $this->relexu_f387177($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(stripos($csvresult->title, "relexu") && stripos($csvresult->title, "f387719"))
		{
			$status = $this->relexu_f387719($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}
		elseif(stripos($csvresult->title, "relexu") && stripos($csvresult->title, "f388572"))
		{
			$status = $this->relexu_f388572($csvresult, $batchOfUnits, $mappedWorkerunitsWithUnits);
		}

		return $status;
	}

	public function factorspan_308140($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch1";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['380140']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "FactSpan";
			$jobconf->content = [
				"jobId" => 380140,
				"type" => "FactSpan",
				"platform" => "CF",
				"dataset" => "WebSci2014-MFactSpan",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 10,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Is the PHRASE complete? (medical sentences)",
		        "description" =>  "N/A",
		        "keywords" =>  "medical terms, text annotation, verify medical term",
		        "instructions" =>  "In the SENTENCE below verify whether the HIGHLIGHTED WORD(s) form a complete medical TERM. \n\n- If it is COMPLETE --> confirm it by clicking on the HIGHLIGHTED WORD(s) in the sentence and use the confirmed WORD(s) in a sentence.\n- If it is NOT COMPLETE --> click ALL words in the sentence that form a complete medical TERM with the HIGHLIGHTED WORD(s). \n\nIf you want to change your selection click again to DE-SELECT the words in the sentence."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();				
			$status['error']['380140']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "FactSpan";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['380140']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id);
		return $status;
	}

	public function factorspan_380640($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch2";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['380640']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "FactSpan";
			$jobconf->content = [
				"jobId" => 380640,
				"type" => "FactSpan",
				"platform" => "CF",
				"dataset" => "WebSci2014-MFactSpan",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 10,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Is the PHRASE complete? (medical sentences)",
		        "description" =>  "N/A",
		        "keywords" =>  "medical terms, text annotation, verify medical term",
		        "instructions" =>  "In the SENTENCE below verify whether the HIGHLIGHTED WORD(s) form a complete medical TERM. \n\n- If it is COMPLETE --> confirm it by clicking on the HIGHLIGHTED WORD(s) in the sentence and use the confirmed WORD(s) in a sentence.\n- If it is NOT COMPLETE --> click ALL words in the sentence that form a complete medical TERM with the HIGHLIGHTED WORD(s). \n\nIf you want to change your selection click again to DE-SELECT the words in the sentence."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['380640']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "FactSpan";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['380140']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id);
		return $status;
	}

	public function factorspan_382004($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch3";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['382004']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "FactSpan";
			$jobconf->content = [
				"jobId" => 382004,
				"type" => "FactSpan",
				"platform" => "CF",
				"dataset" => "WebSci2014-MFactSpan",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 10,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Is the PHRASE complete? (medical sentences)",
		        "description" =>  "N/A",
		        "keywords" =>  "medical terms, text annotation, verify medical term",
		        "instructions" =>  "In the SENTENCE below verify whether the HIGHLIGHTED WORD(s) form a complete medical TERM. \n\n- If it is COMPLETE --> confirm it by clicking on the HIGHLIGHTED WORD(s) in the sentence and use the confirmed WORD(s) in a sentence.\n- If it is NOT COMPLETE --> click ALL words in the sentence that form a complete medical TERM with the HIGHLIGHTED WORD(s). \n\nIf you want to change your selection click again to DE-SELECT the words in the sentence."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['382004']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "FactSpan";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['380140']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id);
		return $status;
	}

	public function relexu_f387177($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch1";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['relexu_f38177']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelEx";
			$jobconf->content = [
				"jobId" => 387177,
				"type" => "RelEx",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelExU",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 15,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Choose the valid RELATION(s) between the TERMS in the SENTENCE",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "STEP 1: Read the SENTENCE below and select all the RELATION TYPE(s) that you think are expressed between the TWO HIGHLIGHTED WORDS in the text.  \n\nNote that if one of the WORDS appears multiple time you will have to consider only the highlighted one.\n\n         Example 1:  \n         for the relation 'PREVENTS' between 'INFLUENZA' and 'VITAMIN C' \n         in the sentence '.... the risk of influenza is reduced by vitamin C...'\n         highlight the words: 'reduced by'\n\n         Example 2: \n         for the relation 'DIAGNOSE' between 'RINNE TEST' and 'HEARING LOSS' \n         in the sentence ' ... RINNE test is used for determining hearing loss ...'\n         highlight the words: 'used for determining'\n\nNOTE: You are not expected to have a domain knowledge in the topic of the sentence. It doesn't matter if you don't know what the highlighted words mean. It is important to understand what the different relation types mean (in STEP 1). HOVER MOUSE over each relation name to see the DEFINITION and an EXAMPLE."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['relexu_f38177']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelEx";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['relexu_f38177']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelEx");
		return $status;
	}

	public function relexu_f387719($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch2";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['relexu_f387719']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelEx";
			$jobconf->content = [
				"jobId" => 387719,
				"type" => "RelEx",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelExU",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 15,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Choose the valid RELATION(s) between the TERMS in the SENTENCE",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "STEP 1: Read the SENTENCE below and select all the RELATION TYPE(s) that you think are expressed between the TWO HIGHLIGHTED WORDS in the text.  \n\nNote that if one of the WORDS appears multiple time you will have to consider only the highlighted one.\n\n         Example 1:  \n         for the relation 'PREVENTS' between 'INFLUENZA' and 'VITAMIN C' \n         in the sentence '.... the risk of influenza is reduced by vitamin C...'\n         highlight the words: 'reduced by'\n\n         Example 2: \n         for the relation 'DIAGNOSE' between 'RINNE TEST' and 'HEARING LOSS' \n         in the sentence ' ... RINNE test is used for determining hearing loss ...'\n         highlight the words: 'used for determining'\n\nNOTE: You are not expected to have a domain knowledge in the topic of the sentence. It doesn't matter if you don't know what the highlighted words mean. It is important to understand what the different relation types mean (in STEP 1). HOVER MOUSE over each relation name to see the DEFINITION and an EXAMPLE."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['relexu_f387719']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelEx";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['relexu_f387719']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelEx");
		return $status;
	}

	public function relexu_f388572($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch3";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['relexu_f388572']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelEx";
			$jobconf->content = [
				"jobId" => 388572,
				"type" => "RelEx",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelExU",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 15,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Choose the valid RELATION(s) between the TERMS in the SENTENCE",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "STEP 1: Read the SENTENCE below and select all the RELATION TYPE(s) that you think are expressed between the TWO HIGHLIGHTED WORDS in the text.  \n\nNote that if one of the WORDS appears multiple time you will have to consider only the highlighted one.\n\n         Example 1:  \n         for the relation 'PREVENTS' between 'INFLUENZA' and 'VITAMIN C' \n         in the sentence '.... the risk of influenza is reduced by vitamin C...'\n         highlight the words: 'reduced by'\n\n         Example 2: \n         for the relation 'DIAGNOSE' between 'RINNE TEST' and 'HEARING LOSS' \n         in the sentence ' ... RINNE test is used for determining hearing loss ...'\n         highlight the words: 'used for determining'\n\nNOTE: You are not expected to have a domain knowledge in the topic of the sentence. It doesn't matter if you don't know what the highlighted words mean. It is important to understand what the different relation types mean (in STEP 1). HOVER MOUSE over each relation name to see the DEFINITION and an EXAMPLE."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['relexu_f388572']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelEx";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['relexu_f388572']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelEx");
		return $status;
	}

	public function relexc_f389000($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch4";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['relexc_f389000']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelEx";
			$jobconf->content = [
				"jobId" => 389000,
				"type" => "RelEx",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelExC",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 15,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Choose the valid RELATION(s) between the TERMS in the SENTENCE",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "STEP 1: Read the SENTENCE below and select all the RELATION TYPE(s) that you think are expressed between the TWO HIGHLIGHTED WORDS in the text.  \n\nNote that if one of the WORDS appears multiple time you will have to consider only the highlighted one.\n\n         Example 1:  \n         for the relation 'PREVENTS' between 'INFLUENZA' and 'VITAMIN C' \n         in the sentence '.... the risk of influenza is reduced by vitamin C...'\n         highlight the words: 'reduced by'\n\n         Example 2: \n         for the relation 'DIAGNOSE' between 'RINNE TEST' and 'HEARING LOSS' \n         in the sentence ' ... RINNE test is used for determining hearing loss ...'\n         highlight the words: 'used for determining'\n\nNOTE: You are not expected to have a domain knowledge in the topic of the sentence. It doesn't matter if you don't know what the highlighted words mean. It is important to understand what the different relation types mean (in STEP 1). HOVER MOUSE over each relation name to see the DEFINITION and an EXAMPLE."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['relexc_f389000']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelEx";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['relexc_f389000']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelEx");
		return $status;
	}

	public function relexc_f389001($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch5";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['relexc_f389001']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelEx";
			$jobconf->content = [
				"jobId" => 389001,
				"type" => "RelEx",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelExC",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  3,
		        "reward" =>  0.02,
		        "workerunitsPerUnit" => 15,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "Choose the valid RELATION(s) between the TERMS in the SENTENCE",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "STEP 1: Read the SENTENCE below and select all the RELATION TYPE(s) that you think are expressed between the TWO HIGHLIGHTED WORDS in the text.  \n\nNote that if one of the WORDS appears multiple time you will have to consider only the highlighted one.\n\n         Example 1:  \n         for the relation 'PREVENTS' between 'INFLUENZA' and 'VITAMIN C' \n         in the sentence '.... the risk of influenza is reduced by vitamin C...'\n         highlight the words: 'reduced by'\n\n         Example 2: \n         for the relation 'DIAGNOSE' between 'RINNE TEST' and 'HEARING LOSS' \n         in the sentence ' ... RINNE test is used for determining hearing loss ...'\n         highlight the words: 'used for determining'\n\nNOTE: You are not expected to have a domain knowledge in the topic of the sentence. It doesn't matter if you don't know what the highlighted words mean. It is important to understand what the different relation types mean (in STEP 1). HOVER MOUSE over each relation name to see the DEFINITION and an EXAMPLE."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['relexc_f389001']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelEx";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['relexc_f389001']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelEx");
		return $status;
	}		

	public function reldirc_f391072($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch6";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['reldirc_f391072']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelDir";
			$jobconf->content = [
				"jobId" => 391072,
				"type" => "RelDir",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelDir",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  1,
		        "reward" =>  0.01,
		        "workerunitsPerUnit" => 12,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "What is the right order of two related WORD PHRASES?",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "In the SENTENCE below there are two highlighted WORD PHRASES that we believe are related. Choose one of the options below that according to you expresses their right order.

Please consider only the capitalized WORD PHRASES (in case one of them appears multiple times in the sentence)."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['reldirc_f391072']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelDir";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['reldirc_f391072']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelDir");
		return $status;
	}		

	public function reldirc_f391073($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch7";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['reldirc_f391073']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelDir";
			$jobconf->content = [
				"jobId" => 391073,
				"type" => "RelDir",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelDir",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  1,
		        "reward" =>  0.01,
		        "workerunitsPerUnit" => 12,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "What is the right order of two related WORD PHRASES?",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "In the SENTENCE below there are two highlighted WORD PHRASES that we believe are related. Choose one of the options below that according to you expresses their right order.

Please consider only the capitalized WORD PHRASES (in case one of them appears multiple times in the sentence)."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['reldirc_f391073']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelDir";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['reldirc_f391073']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelDir");
		return $status;
	}		

	public function reldirc_f391076($csvresult, $batchUnits, $mappedWorkerunitsWithUnits)
	{
		if(!$batch = Entity::where('hash', md5(serialize($batchUnits)))->first())
		{
			$batchCreator = App::make('BatchCreator');
			$batch['batch_title'] = "websci2014_batch8";
			$batch['batch_description'] = "batch used for websci2014";
			$batch['format'] = "text";
			$batch['domain'] = "medical";
			$batch['units'] = $batchUnits;
			$batch = $batchCreator->store($batch);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error']['reldirc_f391076']['activity'] = $e->getMessage();
			return $status;
		}

		try {
			$jobconf = new Entity;
			$jobconf->format = "text";
			$jobconf->domain = "medical";
			$jobconf->documentType = "jobconf";
			$jobconf->type = "RelDir";
			$jobconf->content = [
				"jobId" => 391076,
				"type" => "RelDir",
				"platform" => "CF",
				"dataset" => "WebSci2014-MRelDir",
    			"startedAt" => new MongoDate(strtotime("01/30/2014 22:02:32")),
		        "hitLifetimeInMinutes" =>  "N/A",
		        "autoApprovalDelayInMinutes" =>  "N/A",
		        "expirationInMinutes" =>  1,
		        "reward" =>  0.01,
		        "workerunitsPerUnit" => 12,
		        "unitsPerTask" =>  count($batchUnits),
	//	        "unitsPerTask" =>  34,
		        "title" =>  "What is the right order of two related WORD PHRASES?",
		        "description" =>  "N/A",
		        "keywords" =>  "medical relations, medical texts, relations, relations-annotation",
		        "instructions" =>  "In the SENTENCE below there are two highlighted WORD PHRASES that we believe are related. Choose one of the options below that according to you expresses their right order.

Please consider only the capitalized WORD PHRASES (in case one of them appears multiple times in the sentence)."
			];
			$jobconf->hash = md5(serialize($jobconf->content));
			$jobconf->activity_id = $activity->_id;
			$jobconf->save();
		} catch (Exception $e){
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$status['error']['reldirc_f391076']['jobconf'] = $e->getMessage();
			return $status;
		}

		try {
			$job = new Entity;
			$job->format = "text";
			$job->domain = "medical";
			$job->documentType = "job";
			$job->type = "RelDir";
			$job->jobConf_id = $jobconf->_id;
			$job->activity_id = $activity->_id;
			$job->batch_id = $batch->_id;
			$job->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$jobconf->forceDelete();	
			$job->forceDelete();	
			$status['error']['reldirc_f391076']['job'] = $e->getMessage();
			return $status;
		}

		$status['crowdAgentsAndWorkerunits'] = $this->createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job->_id, "RelDir");
		return $status;
	}		

	public function workerunit_output_cf_factorspan_merged_relex($mappedWorkerunitsWithUnits)
	{
		$tempEntityID = null;
		$status = array();

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "importer";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error']['activity'] = $e->getMessage();
			return $status;
		}

		$relexStructurer = App::make('\preprocess\RelexStructurer');

		$test = array();

		foreach($mappedWorkerunitsWithUnits as $mappedWorkerunitsWithUnitKey => $mappedWorkerunitsWithUnitVal)
		{
			$parentEntity = $mappedWorkerunitsWithUnitVal['unit'];

			$parentEntity['content']['terms']['first']['text'] = strtolower(str_replace(["[", "]"], ["", ""], $mappedWorkerunitsWithUnitVal['term1']));
			$parentEntity['content']['terms']['second']['text'] = strtolower(str_replace(["[", "]"], ["", ""], $mappedWorkerunitsWithUnitVal['term2']));
			$parentEntity['content']['terms']['first']['formatted'] = $mappedWorkerunitsWithUnitVal['term1'];
			$parentEntity['content']['terms']['first']['startIndex'] = (int) $mappedWorkerunitsWithUnitVal['b1'];
			$parentEntity['content']['terms']['first']['endIndex'] = (int) $mappedWorkerunitsWithUnitVal['e1'];
			$parentEntity['content']['terms']['second']['formatted'] = $mappedWorkerunitsWithUnitVal['term2'];
			$parentEntity['content']['terms']['second']['startIndex'] = (int) $mappedWorkerunitsWithUnitVal['b2'];
			$parentEntity['content']['terms']['second']['endIndex'] = (int) $mappedWorkerunitsWithUnitVal['e2'];
			$parentEntity['content']['sentence']['formatted'] = $mappedWorkerunitsWithUnitVal['sentence'];
			$parentEntity['content']['properties']['overlappingTerms'] = $relexStructurer->overlappingTerms($parentEntity['content']);
			$title = $parentEntity['title'] . "_FS_" . $mappedWorkerunitsWithUnitKey;

			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->sent_id = $mappedWorkerunitsWithUnitVal['Sent_id'];
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity['domain'];
				$entity->format = $parentEntity['format'];
				$entity->documentType = "relex-structured-sentence";
				$entity->parents = array($parentEntity['_id']);
				$entity->content = $parentEntity['content'];
				unset($parentEntity['properties']);
				$entity->hash = md5(serialize($parentEntity['content']));
				$entity->activity_id = $activity->_id;
				$entity->save();

				array_push($test, $entity->toArray());

				$status['success'][$title] = $title . " was successfully processed into a relex-structured-sentence. (URI: {$entity->_id} {$entity->hash})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage() . " " . $entity->hash;
			}

			$tempEntityID = $entity->_id;
		}

		return $test;

		return $status;
	}

	public function createWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job_id, $taskType = "FactSpan")

	{
		$status = array();
		$index = 0;

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "cf";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error'][$index]['activity'] = $e->getMessage();
		}		


        // $lastMongoURIUsed = Entity::where('format', 'text')->where('domain', 'medical')->where("documentType", 'workerunit')->get(array("_id"));
    
        // if(count($lastMongoURIUsed) > 0) {
        //     $lastMongoURIUsed = $lastMongoURIUsed->sortBy(function($entity) {
        //         return $entity->_id;
        //     }, SORT_NATURAL)->toArray();

        //     if(end($lastMongoURIUsed)){
        //         $lastMongoIDUsed = explode("/", end($lastMongoURIUsed)['_id']);
        //         $inc = end($lastMongoIDUsed) + 1;                
        //     }
        // }
        // else
        // {
        // 	$inc = 0;
        // }

        $allEntities = array();

		foreach($mappedWorkerunitsWithUnits as $mappedWorkerunitsWithUnit)
		{
			$index++;

			$crowdagent = CrowdAgent::where('platformAgentId', $mappedWorkerunitsWithUnit['_worker_id'])
								->where('softwareAgent_id', 'cf')
								->first();

			if(!$crowdagent)
			{
				try {
					$crowdagent = new CrowdAgent;
					$crowdagent->_id= "crowdagent/cf/" . $mappedWorkerunitsWithUnit['_worker_id'];
					$crowdagent->softwareAgent_id= 'cf';
					$crowdagent->platformAgentId = (int) $mappedWorkerunitsWithUnit['_worker_id'];
					$crowdagent->country = $mappedWorkerunitsWithUnit['_country'];
					$crowdagent->region = $mappedWorkerunitsWithUnit['_region'];
					$crowdagent->city = $mappedWorkerunitsWithUnit['_city'];
					$crowdagent->cfWorkerTrust = (float) $mappedWorkerunitsWithUnit['_trust'];
					$crowdagent->save();	
				} catch(Exception $e) {
					$status['error'][$index]['crowdagent'] = $e->getMessage();
					// continue;
				}				
			}		

			if(!Entity::where('softwareAgent_id', 'cf')
				->where('platformWorkerunitId', $mappedWorkerunitsWithUnit['_id'])
				->first())
			{
				$entity = new Entity;
				$entity->format = "text";
				$entity->domain = "medical";
				$entity->documentType = "workerunit";
				$entity->job_id = $job_id;
				$entity->activity_id = $activity->_id;
				$entity->crowdAgent_id = $crowdagent->_id;
				$entity->softwareAgent_id = "cf";
				$entity->unit_id = $mappedWorkerunitsWithUnit['unit']['_id'];
				$entity->platformWorkerunitId = (int) $mappedWorkerunitsWithUnit['_id'];
				$entity->cfChannel = $mappedWorkerunitsWithUnit['_channel'];
				$entity->acceptTime = new MongoDate(strtotime($mappedWorkerunitsWithUnit['_started_at']));
				$entity->submitTime = new MongoDate(strtotime($mappedWorkerunitsWithUnit['_created_at']));
				$entity->cfTrust = (float) $mappedWorkerunitsWithUnit['_trust'];

				if($taskType == "FactSpan")
				{
					$entity->content = [
						"confirmfirstfactor" => $mappedWorkerunitsWithUnit['confirmfirstfactor'],
						"confirmsecondfactor" => $mappedWorkerunitsWithUnit['confirmsecondfactor'],
						"firstfactor" => $mappedWorkerunitsWithUnit['firstfactor'],
						"secondfactor" => $mappedWorkerunitsWithUnit['secondfactor'],
						"saveselectionids1" => $mappedWorkerunitsWithUnit['saveselectionids1'],
						"saveselectionids2" => $mappedWorkerunitsWithUnit['saveselectionids2'],
						"confirmids1" => $mappedWorkerunitsWithUnit['confirmids1'],
						"confirmids2" => $mappedWorkerunitsWithUnit['confirmids2'],
						"sentencefirstfactor" => $mappedWorkerunitsWithUnit['sentencefirstfactor'],
						"sentencesecondfactor" => $mappedWorkerunitsWithUnit['sentencesecondfactor'],
					];
				}
				elseif($taskType == "RelEx")
				{
					$entity->content = [
						"step_1_select_the_valid_relations" => $mappedWorkerunitsWithUnit['step_1_select_the_valid_relations'],
						"step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1" => $mappedWorkerunitsWithUnit['step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1'],
						"step_2b_if_you_selected_none_in_step_1_explain_why" => $mappedWorkerunitsWithUnit['step_2b_if_you_selected_none_in_step_1_explain_why']
					];
				}
				elseif($taskType == "RelDir")
				{
					$entity->content = [
						"direction" => $mappedWorkerunitsWithUnit['direction']
					];
				}

				try {
					$entity->save();
				}
				catch (Exception $e)
				{
					$status['error'][$index]['entity'] = $e->getMessage();
				}
			}
		}

		return $status;
	}

	public function OldcreateWorkerunitsAndCrowdAgents($mappedWorkerunitsWithUnits, $job_id, $taskType = "FactSpan")
	{
		$status = array();
		$index = 0;

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "cf";
			$activity->save();
		} catch (Exception $e) {
			$activity->forceDelete();	
			$status['error'][$index]['activity'] = $e->getMessage();
		}		

		foreach($mappedWorkerunitsWithUnits as $mappedWorkerunitsWithUnit)
		{
			$index++;

			$crowdagent = CrowdAgent::where('platformAgentId', $mappedWorkerunitsWithUnit['_worker_id'])
								->where('softwareAgent_id', 'cf')
								->first();

			if(!$crowdagent)
			{
				try {
					$crowdagent = new CrowdAgent;
					$crowdagent->_id= "crowdagent/cf/" . $mappedWorkerunitsWithUnit['_worker_id'];
					$crowdagent->softwareAgent_id= 'cf';
					$crowdagent->platformAgentId = (int) $mappedWorkerunitsWithUnit['_worker_id'];
					$crowdagent->country = $mappedWorkerunitsWithUnit['_country'];
					$crowdagent->region = $mappedWorkerunitsWithUnit['_region'];
					$crowdagent->city = $mappedWorkerunitsWithUnit['_city'];
					$crowdagent->cfWorkerTrust = (float) $mappedWorkerunitsWithUnit['_trust'];
					$crowdagent->save();	
				} catch(Exception $e) {
					$status['error'][$index]['crowdagent'] = $e->getMessage();
					// continue;
				}				
			}		

			if(!Entity::where('softwareAgent_id', 'cf')
				->where('platformWorkerunitId', $mappedWorkerunitsWithUnit['_id'])
				->first())
			{
				$entity = new Entity;
				$entity->format = "text";
				$entity->domain = "medical";
				$entity->documentType = "workerunit";
				$entity->job_id = $job_id;
				$entity->activity_id = $activity->_id;
				$entity->crowdAgent_id = $crowdagent->_id;
				$entity->softwareAgent_id = "cf";
				$entity->unit_id = $mappedWorkerunitsWithUnit['unit']['_id'];
				$entity->platformWorkerunitId = (int) $mappedWorkerunitsWithUnit['_id'];
				$entity->cfChannel = $mappedWorkerunitsWithUnit['_channel'];
				$entity->acceptTime = new MongoDate(strtotime($mappedWorkerunitsWithUnit['_started_at']));
				$entity->submitTime = new MongoDate(strtotime($mappedWorkerunitsWithUnit['_created_at']));
				$entity->cfTrust = (float) $mappedWorkerunitsWithUnit['_trust'];

				if($taskType == "FactSpan")
				{
					$entity->content = [
						"confirmfirstfactor" => $mappedWorkerunitsWithUnit['confirmfirstfactor'],
						"confirmsecondfactor" => $mappedWorkerunitsWithUnit['confirmsecondfactor'],
						"firstfactor" => $mappedWorkerunitsWithUnit['firstfactor'],
						"secondfactor" => $mappedWorkerunitsWithUnit['secondfactor'],
						"saveselectionids1" => $mappedWorkerunitsWithUnit['saveselectionids1'],
						"saveselectionids2" => $mappedWorkerunitsWithUnit['saveselectionids2'],
						"confirmids1" => $mappedWorkerunitsWithUnit['confirmids1'],
						"confirmids2" => $mappedWorkerunitsWithUnit['confirmids2'],
						"sentencefirstfactor" => $mappedWorkerunitsWithUnit['sentencefirstfactor'],
						"sentencesecondfactor" => $mappedWorkerunitsWithUnit['sentencesecondfactor'],
					];
				}
				elseif($taskType == "RelEx")
				{
					$entity->content = [
						"step_1_select_the_valid_relations" => $mappedWorkerunitsWithUnit['step_1_select_the_valid_relations'],
						"step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1" => $mappedWorkerunitsWithUnit['step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1'],
						"step_2b_if_you_selected_none_in_step_1_explain_why" => $mappedWorkerunitsWithUnit['step_2b_if_you_selected_none_in_step_1_explain_why']
					];
				}
				elseif($taskType == "RelDir")
				{
					$entity->content = [
						"direction" => $mappedWorkerunitsWithUnit['direction']
					];
				}

				try {
					$entity->save();
				}
				catch (Exception $e)
				{
					$status['error'][$index]['entity'] = $e->getMessage();
				}
			}
		}

		return $status;
	}

}