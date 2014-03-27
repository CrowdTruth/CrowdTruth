<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use URL, Session, Exception;

class TwrexStructurer {

	public function process($twrex)
	{
		set_time_limit(1200);
		\DB::connection()->disableQueryLog();
		// fastcgi_finish_request();

		$twrexLines = explode("\n", $twrex->content);

		$twrexStructuredSentences = array();
		$tempTwrexStructuredSentences = array();

		foreach($twrexLines as $twrexLineKey => $twrexLineVal)
		{
			$twrexLineSegments = explode("\t", $twrexLineVal);

			if($twrexLineSegments[0] == "0")
			{
				unset($twrexLineSegments[0]);
				$twrexLineSegments = array_values($twrexLineSegments);
			}

			if(count($twrexLineSegments) < 8)
			{
				continue;
			}

			if($twrexLineKey > 60000)
				continue;

				
			$tempTwrexStructuredSentence = [
				"relation" => [
					"original" => $twrexLineSegments[0],
					"noPrefix" => explode("-", $twrexLineSegments[0])[1]
				],
				"terms" => [
					"first" => [
						"startIndex" => (int) $twrexLineSegments[2],
						"endIndex" => (int) $twrexLineSegments[3],
						"text" => $twrexLineSegments[1]
					],
					"second" => [
						"startIndex" => (int) $twrexLineSegments[5],
						"endIndex" => (int) $twrexLineSegments[6],
						"text" => $twrexLineSegments[4]
					]
				],
				"sentence" => [
					"text" => $twrexLineSegments[7]
				]
			];

			// $tempTwrexStructuredSentence['properties'] = [
			// 	"sentenceWordCount" => str_word_count($tempTwrexStructuredSentence['sentence']['text']),
			// 	"relationInSentence" => $this->relationInSentence($tempTwrexStructuredSentence),
			// 	"relationOutsideTerms" => $this->relationOutsideTerms($tempTwrexStructuredSentence),
			// 	"relationBetweenTerms" => $this->relationBetweenTerms($tempTwrexStructuredSentence),
			// 	"semicolonBetweenTerms" => $this->semicolonBetweenTerms($tempTwrexStructuredSentence),
			// 	"commaSeparatedTerms" => $this->commaSeparatedTerms($tempTwrexStructuredSentence),
			// 	"parenthesisAroundTerms" => $this->parenthesisAroundTerms($tempTwrexStructuredSentence),
			// 	"overlappingTerms" => $this->overlappingTerms($tempTwrexStructuredSentence)
			// ];

			// if($tempTwrexStructuredSentence['properties']['overlappingTerms'] == 1)
			// {
			// 	array_push($twrexStructuredSentences, $tempTwrexStructuredSentence);
			// } else {
			// 	$tempTwrexStructuredSentence = $this->getAllTermCombinations($tempTwrexStructuredSentence);
			// 	array_push($tempTwrexStructuredSentences, $tempTwrexStructuredSentence);				
			// }

			array_push($tempTwrexStructuredSentences, $this->getAllTermCombinations($tempTwrexStructuredSentence));
		}

		$tempTwrexStructuredSentences = array_unique($tempTwrexStructuredSentences, SORT_REGULAR);

		foreach($tempTwrexStructuredSentences as $tempTwrexStructuredSentence)
		{
			foreach($tempTwrexStructuredSentence['terms']['first'] as $firstTerm)
			{
				foreach($tempTwrexStructuredSentence['terms']['second'] as $secondTerm)
				{
					$twrexStructuredSentence = $tempTwrexStructuredSentence;
					$twrexStructuredSentence['terms']['first'] = $firstTerm;
					$twrexStructuredSentence['terms']['second'] = $secondTerm;

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

					$twrexStructuredSentence = $this->formatUppercase($twrexStructuredSentence);

					ksort($twrexStructuredSentence['terms']);
					array_push($twrexStructuredSentences, $twrexStructuredSentence);
				}
			}
		}

		return $twrexStructuredSentences;
	}

	public function formatUppercase($twrexStructuredSentence)
	{
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];

		$firstTermUppercaseWithBrackets = "[" . strtoupper($twrexStructuredSentence['terms']['first']['text']) . "]";
		$secondTermUppercaseWithBrackets = "[" . strtoupper($twrexStructuredSentence['terms']['second']['text']) . "]";

		$twrexStructuredSentence['terms']['first']['formatted'] = $firstTermUppercaseWithBrackets;
		$twrexStructuredSentence['terms']['second']['formatted'] = $secondTermUppercaseWithBrackets;

		if($twrexStructuredSentence['properties']['overlappingTerms'] == "1")
		{
			return $twrexStructuredSentence;
		}		

		$formattedSentence = $twrexStructuredSentence['sentence']['text'];

		$firstTermInSentenceUppercaseWithBrackets = "[" . strtoupper(substr($formattedSentence, $b1, $e1 - $b1)) . "]";
		$secondTermInSentenceUppercaseWithBrackets = "[" . strtoupper(substr($formattedSentence, $b2, $e2 - $b2)) . "]";


		if($b1 < $b2)
		{
			$formattedSentence = substr_replace($formattedSentence, $firstTermInSentenceUppercaseWithBrackets, $b1, $e1 - $b1);
			$formattedSentence = substr_replace($formattedSentence, $secondTermInSentenceUppercaseWithBrackets, $b2 + 2, $e2 - $b2);
		}
		else
		{
			$formattedSentence = substr_replace($formattedSentence, $secondTermInSentenceUppercaseWithBrackets, $b2, $e2 - $b2);			
			$formattedSentence = substr_replace($formattedSentence, $firstTermInSentenceUppercaseWithBrackets, $b1 + 2, $e1 - $b1);
		}


		$twrexStructuredSentence['sentence']['formatted'] = $formattedSentence;

		return $twrexStructuredSentence;
	}

	public function getAllTermCombinations($tempTwrexStructuredSentence)
	{

		if($this->overlappingTerms($tempTwrexStructuredSentence))
		{
			$tempTwrexStructuredSentence['terms']['first'] = array($tempTwrexStructuredSentence['terms']['first']);
			$tempTwrexStructuredSentence['terms']['second'] = array($tempTwrexStructuredSentence['terms']['second']);	
			// dd($tempTwrexStructuredSentence);		
			return $tempTwrexStructuredSentence;
		}


		$firstTerm = strtolower($tempTwrexStructuredSentence['terms']['first']['text']);
		$secondTerm = strtolower($tempTwrexStructuredSentence['terms']['second']['text']);
		$sentenceText = strtolower($tempTwrexStructuredSentence['sentence']['text']);

		// $firstTerm = '/\b' . preg_quote($firstTerm, '/') . '\b/'; Use this for matching on whole words only
		$firstTerm = '/' . preg_quote($firstTerm, '/') . '/';
		preg_match_all($firstTerm, $sentenceText, $firstTermMatch, PREG_OFFSET_CAPTURE);

		// $secondTerm = '/\b' . preg_quote($secondTerm, '/') . '\b/'; Use this for matching on whole words only
		$secondTerm = '/' . preg_quote($secondTerm, '/') . '/';
		preg_match_all($secondTerm, $sentenceText, $secondTermMatch, PREG_OFFSET_CAPTURE);

		if(count($firstTermMatch[0]) > 0)
		{
			unset($tempTwrexStructuredSentence['terms']['first']);

			foreach($firstTermMatch[0] as $firstTermOccurenceKey => $firstTermOccurenceVal)
			{
				$tempTwrexStructuredSentence['terms']['first'][$firstTermOccurenceKey] = [
					"text" => $firstTermOccurenceVal[0],
					"startIndex" => $firstTermOccurenceVal[1],
					"endIndex" => $firstTermOccurenceVal[1] + strlen($firstTermOccurenceVal[0])
				];
			}
		} else {
			$tempTwrexStructuredSentence['terms']['first'] = array($tempTwrexStructuredSentence['terms']['first']);
			// dd($tempTwrexStructuredSentence);
		}

		if(count($secondTermMatch[0]) > 0)
		{
			unset($tempTwrexStructuredSentence['terms']['second']);

			foreach($secondTermMatch[0] as $secondTermOccurenceKey => $secondTermOccurenceVal)
			{
				$tempTwrexStructuredSentence['terms']['second'][$secondTermOccurenceKey] = [
					"text" => $secondTermOccurenceVal[0],
					"startIndex" => $secondTermOccurenceVal[1],
					"endIndex" => $secondTermOccurenceVal[1] + strlen($secondTermOccurenceVal[0])
				];
			}
		} else {
			$tempTwrexStructuredSentence['terms']['second'] = array($tempTwrexStructuredSentence['terms']['second']);
			// dd($tempTwrexStructuredSentence);
		}

		return $tempTwrexStructuredSentence;
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

			if(stripos(substr($sentenceText, $b2), $relationWithoutPrefixStemmed))
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

	public function semicolonBetweenTerms($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];

		if($b1 < $b2)
		{
			if(stripos(substr($sentenceText, $e1, $b2), ';')){
				return 1;
			}
		}
		else
		{
			if(stripos(substr($sentenceText, $e2, $b1), ';')){
				return 1;
			}
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

		// $firstTerms = str_replace(["(", ")"], "", $firstTerms);
		// $secondTerms = str_replace(["(", ")"], "", $secondTerms);


		// $firstTerms = preg_quote($firstTerms, '/');
		// $secondTerms = preg_quote($firstTerms, '/');


		// if(preg_match("/(" . $firstTerms . ")\s+\,\s+( " . $secondTerms . ")/", $sentenceText))
		// {
		// 	return 1;
		// }
			
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
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];	

		if(filter_var(
		    $b2, 
		    FILTER_VALIDATE_INT, 
		    array(
		        'options' => array(
		            'min_range' => $b1, 
		            'max_range' => $e1
		        )
		    )
		) || filter_var(
		    $e2, 
		    FILTER_VALIDATE_INT, 
		    array(
		        'options' => array(
		            'min_range' => $b1, 
		            'max_range' => $e1
		        )
		    )
		))
		{
			// echo "<pre>";
			// echo $b1 . "\n", $e1 . "\n", $b2 . "\n", $e2. "\n";			
			// dd('yes');
			return 1;
		}
		else
		{
			// dd('no');
			return 0;
		}

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
		$tempEntityID = null;
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
				$entity->_id = $tempEntityID;
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

			$tempEntityID = $entity->_id;
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