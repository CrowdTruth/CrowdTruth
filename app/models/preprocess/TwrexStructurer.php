<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use URL, Session, Exception;

class TwrexStructurer {

	public function process($twrex)
	{
		$twrexLines = explode("\n", $twrex->content);

		$twrexStructuredSentences = array();

		foreach($twrexLines as $twrexLine)
		{

			$twrexLineSegments = explode("\t", $twrexLine);

			if(count($twrexLineSegments) < 7)
				continue;

			$twrexStructuredSentence = [
				"relation" => [
					"original" => $twrexLineSegments[1],
					"noPrefix" => explode("-", $twrexLineSegments[1])[1]
				],
				"terms" => [
					"first" => [
						"startIndex" => (int) $twrexLineSegments[3],
						"endIndex" => (int) $twrexLineSegments[4],
						"text" => $twrexLineSegments[2]
					],
					"second" => [
						"startIndex" => (int) $twrexLineSegments[6],
						"endIndex" => (int) $twrexLineSegments[7],
						"text" => $twrexLineSegments[5]
					]
				],
				"sentence" => [
					"text" => $twrexLineSegments[8]
				]
			];

			$twrexStructuredSentence['properties'] = [
				"sentenceWordCount" => str_word_count($twrexStructuredSentence['sentence']['text']),
				"relationInSentence" => $this->relationInSentence($twrexStructuredSentence),
				"relationOutsideTerms" => $this->relationOutsideTerms($twrexStructuredSentence),
				"relationBetweenTerms" => $this->relationBetweenTerms($twrexStructuredSentence),
				"semicolonBetweenTerms" => $this->semicolonBetweenTerms($twrexStructuredSentence),
				"commaSeparatedTerms" => $this->commaSeparatedTerms($twrexStructuredSentence),
				"parenthesisAroundTerms" => $this->parenthesisAroundTerms($twrexStructuredSentence),
				"overlappingTerms" => $this->overlappingTerms($twrexStructuredSentence)
			];

			array_push($twrexStructuredSentences, $twrexStructuredSentence);
		}

		return $twrexStructuredSentences;
	}

	protected function relationInSentence($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

		if(stripos($sentenceText, $relationWithoutPrefixStemmed))
		{
			return 1;
		}

		return 0;
	}

	protected function relationOutsideTerms($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

		if($b1 < $b2)
		{
			if(stripos(substr($sentenceText, 0, $b1), $relationWithoutPrefixStemmed))
			{
				return 1;
			}

			if(stripos(substr($sentenceText, $b1), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}
		else
		{
			if(stripos(substr($sentenceText, $b1), $relationWithoutPrefixStemmed))
			{
				return 1;
			}

			if(stripos(substr($sentenceText, 0, $b2), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}
		
		return 0;		
	}

	public function relationBetweenTerms($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

		if($b1 < $b2)
		{
			if(stripos(substr($sentenceText, $e1, $b2), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}
		else
		{
			if(stripos(substr($sentenceText, $e2, $b1), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}

		return 0;
	}

	protected function semicolonBetweenTerms($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];

		if(stripos(substr($sentenceText, $e1, $b2), ';')){
			return 1;
		}

		return 0;	
	}

	protected function commaSeparatedTerms($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$firstTerms = strtolower($twrexStructuredSentence['terms']['first']['text']);
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];
		$secondTerms = strtolower($twrexStructuredSentence['terms']['second']['text']);				
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

		if($b1 < $b2)
		{
			$textWithAndBetweenTerms = substr($sentenceText, $b1, $e2);
		}
		else
		{
			$textWithAndBetweenTerms = substr($sentenceText, $b2, $e1);
		}


		$numberOfWordsBetweenTerms = str_word_count($textWithAndBetweenTerms);
		$numberOfCommasBetweenTerms = substr_count($textWithAndBetweenTerms, ",");

		if($numberOfWordsBetweenTerms < (($numberOfCommasBetweenTerms * 3) + 1))
		{
			return 1;
		}

		$firstTerms = str_replace(["(", ")"], "", $firstTerms);
		$secondTerms = str_replace(["(", ")"], "", $secondTerms);

		if(preg_match("/(" . $firstTerms . ")\s+\,\s+( " . $secondTerms . ")/", $sentenceText))
		{
			return 1;
		}
			
		return 0;
	}

	protected function parenthesisAroundTerms($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$firstTerms = strtolower($twrexStructuredSentence['terms']['first']['text']);
		$secondTerms = strtolower($twrexStructuredSentence['terms']['second']['text']);				

		if(stripos($sentenceText, "(" . $firstTerms . ")") !== false)
		{
			return 1;
		}

		if(stripos($sentenceText, "(" . $secondTerms . ")") !== false)
		{
			return 1;
		}

		return 0;
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

	public function simpleStem($relationWithoutPrefix){
		switch (strtolower($relationWithoutPrefix)) {
		    case 'cause':
		        return 'caus';
		    case 'location':
		        return 'locat';
		    case 'diagnose':
		        return 'diagnos';
		}

		return $relationWithoutPrefix;
	}

	public function store($parentEntity, $twrexStructuredSentences)
	{

		$status = array();

		try {
			$this->createTwrexStructurerSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['TwrexStructurer'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "twrexstructurer";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		foreach($twrexStructuredSentences as $twrexStructuredSentenceKey => $twrexStructuredSentenceKeyVal){
			$title = $parentEntity->title . "_index_" . $twrexStructuredSentenceKey;

			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = $parentEntity->format;
				$entity->documentType = "twrex-structured-sentence";
				$entity->parents = array($parentEntity->_id);
				$entity->content = $twrexStructuredSentenceKeyVal;

				unset($twrexStructuredSentenceKeyVal['properties']);
				$entity->hash = md5(serialize($twrexStructuredSentenceKeyVal));
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into a twrex-structured-sentence. (URI: {$entity->_id})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}
		}

		return $status;
	}

	public function createTwrexStructurerSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('twrexstructurer'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "twrexstructurer";
			$softwareAgent->label = "This component (pre)processes chang documents into structured twrex documents";
			$softwareAgent->save();
		}
	}	
}