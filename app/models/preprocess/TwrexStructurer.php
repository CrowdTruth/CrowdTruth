<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use URL, Session, Exception;

class TwrexStructurer {

	public static function process($originalDocument){
		$documentSeparatedByNewline = explode("\n", $originalDocument['content']);

		// print_r($documentSeparatedByNewline);
		// exit;

		$twrexDocument = array();

		foreach($documentSeparatedByNewline as $lineNumber => $lineValue){
			if($lineValue == "")
				continue;

			if(preg_match("/(TWrex)\-[a-zA-Z-]+/", $lineValue, $matches)) {
				$TWrexRelation = $matches[0];
				$relationWithoutPrefix = explode("-", $TWrexRelation)[1];
			}

			if(preg_match_all("/\t+\d+\t\d+\t+/", $lineValue, $matches)){
				$b1 = preg_split("/\s+/", trim($matches[0][0]))[0];
				$e1 = preg_split("/\s+/", trim($matches[0][0]))[1];
				$b2 = preg_split("/\s+/", trim($matches[0][1]))[0];
				$e2 = preg_split("/\s+/", trim($matches[0][1]))[1];
				$sentenceOffset = stripos($lineValue, $matches[0][1]) + strlen($matches[0][1]);
			}
				$sentenceText = ltrim(substr($lineValue, $sentenceOffset));
				$firstTerms = substr($sentenceText, $b1, $e1 - $b1);
				$secondTerms = substr($sentenceText, $b2, $e2 - $b2);


				$twrexDocument[$lineNumber]['relation']['original'] = strtolower($TWrexRelation);
				$twrexDocument[$lineNumber]['relation']['noPrefix'] = strtolower($relationWithoutPrefix);
				$twrexDocument[$lineNumber]['terms']['first']['startIndex'] = (int) $b1;
				$twrexDocument[$lineNumber]['terms']['first']['endIndex'] = (int) $e1;
				$twrexDocument[$lineNumber]['terms']['first']['text'] = $firstTerms;
				$twrexDocument[$lineNumber]['terms']['second']['startIndex'] = (int) $b2;
				$twrexDocument[$lineNumber]['terms']['second']['endIndex'] = (int) $e2;
				$twrexDocument[$lineNumber]['terms']['second']['text'] = $secondTerms;

			//	$twrexDocument[$lineNumber]['Terms'][1] = substr($sentenceText, $offsets['b1'], $offsets['e1']);
			//	$twrexDocument[$lineNumber]['Terms'][2] = substr($sentenceText, $offsets['e1'], $offsets['e2']);
				$twrexDocument[$lineNumber]['sentence']['startIndex'] = (int) $sentenceOffset;
				$twrexDocument[$lineNumber]['sentence']['text'] = $sentenceText;
				$twrexDocument[$lineNumber]['properties']['sentenceWordCount'] = str_word_count($sentenceText);

				$relationWithoutPrefixStemmed = static::simpleStem($relationWithoutPrefix);

				$twrexDocument[$lineNumber]['properties']['relationInSentence'] = 
				stripos($sentenceText, $relationWithoutPrefixStemmed) ? 1 : 0;

				if($b1 < $b2){
					$twrexDocument[$lineNumber]['properties']['relationOutsideTerms'] = 
					(stripos(substr($sentenceText, 0, $b1), $relationWithoutPrefixStemmed) ||
					stripos(substr($sentenceText, $b2), $relationWithoutPrefixStemmed)) ? 1 : 0;

					$twrexDocument[$lineNumber]['properties']['relationBetweenTerms'] = 
					stripos(substr($sentenceText, $e1, $b2), $relationWithoutPrefixStemmed) ? 1 : 0;

					$twrexDocument[$lineNumber]['properties']['semicolonBetweenTerms'] =	
					stripos(substr($sentenceText, $e1, $b2), ';') ? 1 : 0;

					$textWithAndBetweenTerms = substr($sentenceText, $b1, $e2);
				} else {
					$twrexDocument[$lineNumber]['properties']['relationOutsideTerms'] = 
					(stripos(substr($sentenceText, $b1), $relationWithoutPrefixStemmed) ||
					stripos(substr($sentenceText, 0, $b2), $relationWithoutPrefixStemmed)) ? 1 : 0;

					$twrexDocument[$lineNumber]['properties']['relationBetweenTerms'] = 
					stripos(substr($sentenceText, $e2, $b1), $relationWithoutPrefixStemmed) ? 1 : 0;	

					$twrexDocument[$lineNumber]['properties']['semicolonBetweenTerms'] =	
					stripos(substr($sentenceText, $e2, $b1), ';') ? 1 : 0;

					$textWithAndBetweenTerms = substr($sentenceText, $b2, $e1);		
				}

				$numberOfWordsBetweenTerms = str_word_count($textWithAndBetweenTerms);
				$numberOfCommasBetweenTerms = substr_count($textWithAndBetweenTerms, ",");

				$commaSeparatedTerms = 0;
				if($numberOfWordsBetweenTerms < (($numberOfCommasBetweenTerms * 3) + 1))
					$commaSeparatedTerms = 1;

				if(preg_match("/(" . $firstTerms . ")\s+\,\s+( " . $secondTerms . ")/", $sentenceText))
					$commaSeparatedTerms = 1;

				$twrexDocument[$lineNumber]['properties']['commaSeparatedTerms'] =	$commaSeparatedTerms;


				$twrexDocument[$lineNumber]['properties']['parenthesisAroundTerms'] =
				((stripos($sentenceText, "(" . $firstTerms . ")") !== false) || 
				 (stripos($sentenceText, "(" . $firstTerms . ")") !== false)) ? 1: 0;

				$firstTermsArray = explode(" ", $firstTerms);
				$secondTermsArray = explode(" ", $secondTerms);

				foreach($firstTermsArray as $term){
					if(in_array($term, $secondTermsArray)) {
						$twrexDocument[$lineNumber]['properties']['overlappingTerms'] = 1;
					} else {
						$twrexDocument[$lineNumber]['properties']['overlappingTerms'] = 0;
					}
				}
		}

		
		// print_r($twrexDocument);
		// exit;

		return $twrexDocument;
	}

	public static function simpleStem($relationWithoutPrefix){
		switch ($relationWithoutPrefix) {
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
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = $parentEntity->format;
				$entity->documentType = "twrex-structured-sentence";
				$entity->ancestors = array($parentEntity->_id);
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

		// Session::forget('lastMongoIDUsed');

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