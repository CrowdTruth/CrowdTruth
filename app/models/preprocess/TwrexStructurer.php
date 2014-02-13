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
				$twrexDocument[$lineNumber]['terms']['first']['startIndex'] = $b1;
				$twrexDocument[$lineNumber]['terms']['first']['endIndex'] = $e1;
				$twrexDocument[$lineNumber]['terms']['first']['text'] = $firstTerms;
				$twrexDocument[$lineNumber]['terms']['second']['startIndex'] = $b2;
				$twrexDocument[$lineNumber]['terms']['second']['endIndex'] = $e2;
				$twrexDocument[$lineNumber]['terms']['second']['text'] = $secondTerms;

			//	$twrexDocument[$lineNumber]['Terms'][1] = substr($sentenceText, $offsets['b1'], $offsets['e1']);
			//	$twrexDocument[$lineNumber]['Terms'][2] = substr($sentenceText, $offsets['e1'], $offsets['e2']);
				$twrexDocument[$lineNumber]['sentence']['startIndex'] = $sentenceOffset;
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

		$activity_id = null;

		foreach($twrexStructuredSentences as $twrexStructuredSentenceKey => $twrexStructuredSentenceKeyVal){
			$title = $parentEntity->title . "_index_" . $twrexStructuredSentenceKey;

			try {
				$entity = new Entity;
				$entity->title = strtolower($title);
				$entity->domain = strtolower($parentEntity->domain);
				$entity->format = strtolower($parentEntity->format);
				$entity->documentType = "twrex-structured-sentence";
				$entity->parent_id = $parentEntity->_id;
				$entity->ancestors = array($parentEntity->_id);
				$entity->content = $twrexStructuredSentenceKeyVal;
				$entity->activity_id = $activity_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into a twrex-structured-sentence. (URI: {$entity->_id})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
				continue;
			}

			$activity_id = $entity->activity_id; // Get activity_id from entity saving event.
		}

		try {
			$activity = new Activity;
			$activity->_id = $activity_id;
			$activity->softwareAgent_id = "twrexstructurer";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$entity->forceDelete();
			$status['error'][$title] = $e->getMessage();
		}

		return $status;

	}

	public static function store2($originalEntity, $newEntityContent){
		$user = Auth::user();

		$activityURI = $originalEntity->wasGeneratedBy->_id . '/twrex';

		try {
			$activity = new \mongo\text\Activity;
			$activity->_id = strtolower($activityURI);
			$activity->type = "twrex";
			$activity->label = '"' . $originalEntity->title . '" was converted to a twrex document';
			$activity->entity_used_id = $originalEntity->_id;
			$activity->user_id = $user->_id;
			$activity->software_id = URL::to('preprocess/twrex');
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			Session::flash('flashError', $e->getMessage());
			return;
		}

		$entityURI = $originalEntity->_id . '/twrex';

		try {
			$entity = new \mongo\text\Entity;
			$entity->_id = strtolower($entityURI);
			$entity->title = $originalEntity->title . '/twrex';
			$entity->domain = $originalEntity->domain;
			$entity->type = "text";
			$entity->documentType = "twrex";
			$entity->parent_id = $originalEntity->_id;
			$entity->ancestors = array($originalEntity->_id);
			$entity->activity_id = strtolower($activityURI);
			$entity->user_id = $user->_id;
			$entity->content = $newEntityContent;
			$entity->save();

			Session::flash('flashSuccess', '"' . $originalEntity->title . '" was successfully converted to a twrex document. URI: ' . $entityURI);
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$activity->forceDelete();
			$entity->forceDelete();
			Session::flash('flashError', $e->getMessage());
		}
	}

	public static function createAndStoretwrexChild($originalEntity, array $appliedFilters){
		if($originalEntity->documentType !== 'twrex')
			return false;

		$appliedFiltersWithValues = array();

		foreach($originalEntity['content'][0]['properties'] as $filterKey => $filterValue){
			foreach($appliedFilters as $appliedFilterKey => $appliedFilterValue){
				if($appliedFilterKey == $filterKey){
					$appliedFiltersWithValues[$appliedFilterKey] = $appliedFilterValue;
				}
			}
		}

		$newEntity['appliedFilters'] = $appliedFiltersWithValues;

		foreach($appliedFilters as $appliedFilterKey => $appliedFilterValue){
			if(stripos($appliedFilterKey, 'line') !== FALSE){
				$lineNumber = explode("_", $appliedFilterKey)[1];
				$lineValue = $originalEntity['content'][$lineNumber];
				$newEntity['content'][$lineNumber] = $lineValue;
			}
		}

		$URI_prefix = "";
		foreach($appliedFiltersWithValues as $val){
			$URI_prefix .=  $val;
		}

		$user = Auth::user();

		$activityURI = $originalEntity->wasGeneratedBy->_id . '_' . $URI_prefix;

		try {
			$activity = new \mongo\text\Activity;
			$activity->_id = strtolower($activityURI);
			$activity->type = "twrex";
			$activity->label = 'Created filtered subdocument based on "' . $originalEntity->title . '"';
			$activity->entity_used_id = $originalEntity->_id;
			$activity->user_id = $user->_id;
			$activity->software_id = URL::to('files/create');
			$activity->configuration = $appliedFiltersWithValues;
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			Session::flash('flashError', $e->getMessage());
			return;
		}

		$entityURI = $originalEntity->_id . '_' . $URI_prefix;

		try {
			$entity = new \mongo\text\Entity;
			$entity->_id = strtolower($entityURI);
			$entity->title = $originalEntity->title . '_' . $URI_prefix;
			$entity->domain = $originalEntity->domain;
			$entity->type = "text";
			$entity->documentType = "twrex";
			$entity->parent_id = $originalEntity->_id;
			$entity->ancestors = array_merge($originalEntity->ancestors, array($originalEntity->_id));
			$entity->activity_id = strtolower($activityURI);
			$entity->user_id = $user->_id;
			$entity->content = $newEntity['content'];
			$entity->save();

			Session::flash('flashSuccess', '"' . $originalEntity->title . '" was successfully converted to a twrex document. URI: ' . $entityURI);
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$activity->forceDelete();
			$entity->forceDelete();
			Session::flash('flashError', $e->getMessage());
		}

		return $entityURI;
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