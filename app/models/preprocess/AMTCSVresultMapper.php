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
<<<<<<< HEAD

		$headers0 = $csv->fetchOne();
		$headers = array();
		$count = 0; $c1 = 0;
		foreach($headers0 as $h){
			if(empty($h)){
				$h = "empty$count";
				$count++;
			} 

			if(in_array($h, $headers)){
				$h = "$h$c1";
				$c1++;
			}

			$headers[] = $h;
		}
		
=======
		$headers = $csv->fetchOne();

		foreach($headers as $columnIndex => $columnValue)
		{
			if($columnValue == "")
			{
				$headers[$columnIndex] = "empty_" . $columnIndex;
			}
		}

>>>>>>> TwrexStructurer - CSVresultmapper - Lukasz Features
		$data = $csv->fetchAssoc($headers);

		$rowsMappedWithUnits = array();
		$batch = array();

		unset($data[0]); // Unsetting header
$count = 0;
		foreach ($data as $line_index => $row)
		{
			$count++;
			$entity = Entity::where('documentType', 'twrex-structured-sentence');

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
<<<<<<< HEAD
			
			if(isset($row['unit_id0'])) unset($row['unit_id0']);
			
=======
			else
			{
				continue;
			}

>>>>>>> TwrexStructurer - CSVresultmapper - Lukasz Features
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

	public function processAnnotationData($csvresult, $preview = false)
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

<<<<<<< HEAD
		$rowsMappedWithUnits = array();
		$batch = array();
=======
		// dd($headers);

		// dd($csv->fetchAll());
>>>>>>> TwrexStructurer - CSVresultmapper - Lukasz Features

		$annotationCSVArray = $csv->fetchAssoc($headers);

		unset($annotationCSVArray[0]);

		// dd($annotationCSVArray);

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

		$mappedAnnotationsWithUnits = array();
		$batchOfUnits = array();

		foreach($annotationCSVArray as $annotationRow)
		{
			foreach($aggregatedProcessedInputCSVFiles as $mappedInputRow)
			{
				if(strpos($csvresult->title, 'annotation_output_cf_factorspan_merged_relex') !== false)
				{
					if(stripos($annotationRow['Sent_id'], '-'))
					{
						$annotationRowIndex = explode('-', $annotationRow['Sent_id'])[0];
					}
				}
				else
				{
					$annotationRowIndex = $annotationRow['index'];					
				}

				if($annotationRowIndex == $mappedInputRow['index'])
				{
					$mappedAnnotationWithUnit = $annotationRow;
					$mappedAnnotationWithUnit['unit'] = $mappedInputRow['unit'];

					array_push($batchOfUnits, $mappedInputRow['unit']['_id']);
					array_push($mappedAnnotationsWithUnits, $mappedAnnotationWithUnit);
				}
			}
		}

		$batchOfUnits = array_values(array_unique($batchOfUnits));
		natsort($batchOfUnits);

		$batchOfUnits = array_values($batchOfUnits);

		if($preview == true)
		{
			return $mappedAnnotationsWithUnits;
		}

		if(stripos($csvresult->title, "factorspan") && stripos($csvresult->title, "380140"))
		{
			$status = $this->factorspan_308140($csvresult, $batchOfUnits, $mappedAnnotationsWithUnits);
		}
		elseif(stripos($csvresult->title, "factorspan") && stripos($csvresult->title, "380640"))
		{
			$status = $this->factorspan_380640($csvresult, $batchOfUnits, $mappedAnnotationsWithUnits);
		}
		elseif(stripos($csvresult->title, "factorspan") && stripos($csvresult->title, "382004"))
		{
			$status = $this->factorspan_382004($csvresult, $batchOfUnits, $mappedAnnotationsWithUnits);
		}
		elseif(strpos($csvresult->title, 'annotation_output_cf_factorspan_merged_relex') !== false)
		{
			$status = $this->annotation_output_cf_factorspan_merged_relex($mappedAnnotationsWithUnits);
		}
		elseif(stripos($csvresult->title, "relexu") && stripos($csvresult->title, "f387177"))
		{
			$status = $this->relexu_f387177($csvresult, $batchOfUnits, $mappedAnnotationsWithUnits);
		}
		elseif(stripos($csvresult->title, "relexu") && stripos($csvresult->title, "f387719"))
		{
			$status = $this->relexu_f387719($csvresult, $batchOfUnits, $mappedAnnotationsWithUnits);
		}
		elseif(stripos($csvresult->title, "relexu") && stripos($csvresult->title, "f388572"))
		{
			$status = $this->relexu_f388572($csvresult, $batchOfUnits, $mappedAnnotationsWithUnits);
		}

		return $status;
	}

	public function factorspan_308140($csvresult, $batchUnits, $mappedAnnotationsWithUnits)
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
		        "annotationsPerUnit" => 10,
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

		$status['crowdAgentsAndAnnotations'] = $this->createAnnotationsAndCrowdAgents($mappedAnnotationsWithUnits, $job->_id);
		return $status;
	}

	public function factorspan_380640($csvresult, $batchUnits, $mappedAnnotationsWithUnits)
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
		        "annotationsPerUnit" => 10,
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

		$status['crowdAgentsAndAnnotations'] = $this->createAnnotationsAndCrowdAgents($mappedAnnotationsWithUnits, $job->_id);
		return $status;
	}

	public function factorspan_382004($csvresult, $batchUnits, $mappedAnnotationsWithUnits)
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
		        "annotationsPerUnit" => 10,
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

		$status['crowdAgentsAndAnnotations'] = $this->createAnnotationsAndCrowdAgents($mappedAnnotationsWithUnits, $job->_id);
		return $status;
	}

	public function relexu_f387177($csvresult, $batchUnits, $mappedAnnotationsWithUnits)
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
		        "annotationsPerUnit" => 15,
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

		$status['crowdAgentsAndAnnotations'] = $this->createAnnotationsAndCrowdAgents($mappedAnnotationsWithUnits, $job->_id, "RelEx");
		return $status;
	}

	public function relexu_f387719($csvresult, $batchUnits, $mappedAnnotationsWithUnits)
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
		        "annotationsPerUnit" => 15,
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

		$status['crowdAgentsAndAnnotations'] = $this->createAnnotationsAndCrowdAgents($mappedAnnotationsWithUnits, $job->_id, "RelEx");
		return $status;
	}

	public function relexu_f388572($csvresult, $batchUnits, $mappedAnnotationsWithUnits)
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
		        "annotationsPerUnit" => 15,
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

		$status['crowdAgentsAndAnnotations'] = $this->createAnnotationsAndCrowdAgents($mappedAnnotationsWithUnits, $job->_id, "RelEx");
		return $status;
	}

	public function annotation_output_cf_factorspan_merged_relex($mappedAnnotationsWithUnits)
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

		$twrexStructurer = App::make('\preprocess\TwrexStructurer');

		$test = array();

		foreach($mappedAnnotationsWithUnits as $mappedAnnotationsWithUnitKey => $mappedAnnotationsWithUnitVal)
		{
			$parentEntity = $mappedAnnotationsWithUnitVal['unit'];

			$parentEntity['content']['terms']['first']['text'] = strtolower(str_replace(["[", "]"], ["", ""], $mappedAnnotationsWithUnitVal['term1']));
			$parentEntity['content']['terms']['second']['text'] = strtolower(str_replace(["[", "]"], ["", ""], $mappedAnnotationsWithUnitVal['term2']));
			$parentEntity['content']['terms']['first']['formatted'] = $mappedAnnotationsWithUnitVal['term1'];
			$parentEntity['content']['terms']['first']['startIndex'] = (int) $mappedAnnotationsWithUnitVal['b1'];
			$parentEntity['content']['terms']['first']['endIndex'] = (int) $mappedAnnotationsWithUnitVal['e1'];
			$parentEntity['content']['terms']['second']['formatted'] = $mappedAnnotationsWithUnitVal['term2'];
			$parentEntity['content']['terms']['second']['startIndex'] = (int) $mappedAnnotationsWithUnitVal['b2'];
			$parentEntity['content']['terms']['second']['endIndex'] = (int) $mappedAnnotationsWithUnitVal['e2'];
			$parentEntity['content']['sentence']['formatted'] = $mappedAnnotationsWithUnitVal['sentence'];
			$parentEntity['content']['properties']['overlappingTerms'] = $twrexStructurer->overlappingTerms($parentEntity['content']);
			$title = $parentEntity['title'] . "_FS_" . $mappedAnnotationsWithUnitKey;

			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity['domain'];
				$entity->format = $parentEntity['format'];
				$entity->documentType = "twrex-structured-sentence";
				$entity->parents = array($parentEntity['_id']);
				$entity->content = $parentEntity['content'];
				unset($parentEntity['properties']);
				$entity->hash = md5(serialize($parentEntity['content']));
				$entity->activity_id = $activity->_id;
				// $entity->save();

				array_push($test, $entity->toArray());

				$status['success'][$title] = $title . " was successfully processed into a twrex-structured-sentence. (URI: {$entity->_id} {$entity->hash})";
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

	protected function overlappingTerms($twrexStructuredSentence)
	{
		$firstTerms = strtolower($twrexStructuredSentence['terms']['first']['text']);
		$secondTerms = strtolower($twrexStructuredSentence['terms']['second']['text']);

		$firstTermsArray = explode(" ", $firstTerms);
		$secondTermsArray = explode(" ", $secondTerms);

		foreach($firstTermsArray as $term){
			if(in_array($term, $secondTermsArray)) {
				return 1;
			}
			
		}

		return 0;	
	}	

	public function createAnnotationsAndCrowdAgents($mappedAnnotationsWithUnits, $job_id, $taskType = "FactSpan")
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

		foreach($mappedAnnotationsWithUnits as $mappedAnnotationsWithUnit)
		{
			$index++;

			$crowdagent = CrowdAgent::where('platformAgentId', $mappedAnnotationsWithUnit['_worker_id'])
								->where('softwareAgent_id', 'cf')
								->first();

			if(!$crowdagent)
			{
				try {
					$crowdagent = new CrowdAgent;
					$crowdagent->_id= "crowdagent/cf/" . $mappedAnnotationsWithUnit['_worker_id'];
					$crowdagent->softwareAgent_id= 'cf';
					$crowdagent->platformAgentId = $mappedAnnotationsWithUnit['_worker_id'];
					$crowdagent->country = $mappedAnnotationsWithUnit['_country'];
					$crowdagent->region = $mappedAnnotationsWithUnit['_region'];
					$crowdagent->city = $mappedAnnotationsWithUnit['_city'];			
					$crowdagent->cfWorkerTrust = (float) $mappedAnnotationsWithUnit['_trust'];	
					$crowdagent->save();	
				} catch(Exception $e) {
					$status['error'][$index]['crowdagent'] = $e->getMessage();
					// continue;
				}				
			}		

			if(!Entity::where('softwareAgent_id', 'cf')
				->where('platformAnnotationId', $mappedAnnotationsWithUnit['_id'])
				->first())
			{
				$entity = new Entity;
				$entity->format = "text";
				$entity->domain = "medical";
				$entity->documentType = "annotation";
				$entity->job_id = $job_id;
				$entity->activity_id = $activity->_id;
				$entity->crowdAgent_id = $crowdagent->_id;
				$entity->softwareAgent_id = "cf";
				$entity->unit_id = $mappedAnnotationsWithUnit['unit']['_id'];
				$entity->platformAnnotationId = $mappedAnnotationsWithUnit['_id'];
				$entity->cfChannel = $mappedAnnotationsWithUnit['_channel'];
				$entity->acceptTime = new MongoDate(strtotime($mappedAnnotationsWithUnit['_started_at']));
				$entity->submitTime = new MongoDate(strtotime($mappedAnnotationsWithUnit['_created_at']));
				$entity->cfTrust = (float) $mappedAnnotationsWithUnit['_trust'];

				if($taskType == "FactSpan")
				{
					$entity->content = [
						"confirmfirstfactor" => $mappedAnnotationsWithUnit['confirmfirstfactor'],
						"confirmsecondfactor" => $mappedAnnotationsWithUnit['confirmsecondfactor'],
						"firstfactor" => $mappedAnnotationsWithUnit['firstfactor'],
						"secondfactor" => $mappedAnnotationsWithUnit['secondfactor'],
						"saveselectionids1" => $mappedAnnotationsWithUnit['saveselectionids1'],
						"saveselectionids2" => $mappedAnnotationsWithUnit['saveselectionids2'],
						"confirmids1" => $mappedAnnotationsWithUnit['confirmids1'],
						"confirmids2" => $mappedAnnotationsWithUnit['confirmids2'],
						"sentencefirstfactor" => $mappedAnnotationsWithUnit['sentencefirstfactor'],
						"sentencesecondfactor" => $mappedAnnotationsWithUnit['sentencesecondfactor'],
					];
				}
				elseif($taskType == "RelEx")
				{
					$entity->content = [
						"step_1_select_the_valid_relations" => $mappedAnnotationsWithUnit['step_1_select_the_valid_relations'],
						"step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1" => $mappedAnnotationsWithUnit['step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1'],
						"step_2b_if_you_selected_none_in_step_1_explain_why" => $mappedAnnotationsWithUnit['step_2b_if_you_selected_none_in_step_1_explain_why']
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