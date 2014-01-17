<?php

namespace preprocess;

use Moloquent, Auth, URL, Session, Exception;

class Chang extends Moloquent {

	protected $connection = 'mongodb_text';
	protected $collection = 'entities';
	protected $softDelete = true;
	protected static $unguarded = true;

	public static function process($originalDocument){
		$documentSeparatedByNewline = explode("\n", $originalDocument['content']);

		// print_r($documentSeparatedByNewline);
		// exit;

		$changDocument = array();

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
				$sentenceOffset = strpos($lineValue, $matches[0][1]) + strlen($matches[0][1]);
			}
				$sentenceText = ltrim(substr($lineValue, $sentenceOffset));
				$firstTerms = substr($sentenceText, $b1, $e1 - $b1);
				$secondTerms = substr($sentenceText, $b2, $e2 - $b2);


				$changDocument[$lineNumber]['relation']['original'] = strtolower($TWrexRelation);
				$changDocument[$lineNumber]['relation']['noPrefix'] = strtolower($relationWithoutPrefix);
				$changDocument[$lineNumber]['terms']['first']['startIndex'] = $b1;
				$changDocument[$lineNumber]['terms']['first']['endIndex'] = $e1;
				$changDocument[$lineNumber]['terms']['first']['text'] = $firstTerms;
				$changDocument[$lineNumber]['terms']['second']['startIndex'] = $b2;
				$changDocument[$lineNumber]['terms']['second']['endIndex'] = $e2;
				$changDocument[$lineNumber]['terms']['second']['text'] = $secondTerms;

			//	$changDocument[$lineNumber]['Terms'][1] = substr($sentenceText, $offsets['b1'], $offsets['e1']);
			//	$changDocument[$lineNumber]['Terms'][2] = substr($sentenceText, $offsets['e1'], $offsets['e2']);
				$changDocument[$lineNumber]['sentence']['startIndex'] = $sentenceOffset;
				$changDocument[$lineNumber]['sentence']['text'] = $sentenceText;
				$changDocument[$lineNumber]['filters']['sentenceWordCount'] = str_word_count($sentenceText);

				$relationWithoutPrefixStemmed = static::simpleStem($relationWithoutPrefix);

				$changDocument[$lineNumber]['filters']['relationInSentence'] = 
				strpos($sentenceText, $relationWithoutPrefixStemmed) ? 1 : 0;

				if($b1 < $b2){
					$changDocument[$lineNumber]['filters']['relationOutsideTerms'] = 
					(strpos(substr($sentenceText, 0, $b1), $relationWithoutPrefixStemmed) ||
					strpos(substr($sentenceText, $b2), $relationWithoutPrefixStemmed)) ? 1 : 0;

					$changDocument[$lineNumber]['filters']['relationBetweenTerms'] = 
					strpos(substr($sentenceText, $e1, $b2), $relationWithoutPrefixStemmed) ? 1 : 0;

					$changDocument[$lineNumber]['filters']['semicolonBetweenTerms'] =	
					strpos(substr($sentenceText, $e1, $b2), ';') ? 1 : 0;

					$textWithAndBetweenTerms = substr($sentenceText, $b1, $e2);
				} else {
					$changDocument[$lineNumber]['filters']['relationOutsideTerms'] = 
					(strpos(substr($sentenceText, $b1), $relationWithoutPrefixStemmed) ||
					strpos(substr($sentenceText, 0, $b2), $relationWithoutPrefixStemmed)) ? 1 : 0;

					$changDocument[$lineNumber]['filters']['relationBetweenTerms'] = 
					strpos(substr($sentenceText, $e2, $b1), $relationWithoutPrefixStemmed) ? 1 : 0;	

					$changDocument[$lineNumber]['filters']['semicolonBetweenTerms'] =	
					strpos(substr($sentenceText, $e2, $b1), ';') ? 1 : 0;

					$textWithAndBetweenTerms = substr($sentenceText, $b2, $e1);		
				}

				$numberOfWordsBetweenTerms = str_word_count($textWithAndBetweenTerms);
				$numberOfCommasBetweenTerms = substr_count($textWithAndBetweenTerms, ",");

				$commaSeparatedTerms = 0;
				if($numberOfWordsBetweenTerms < (($numberOfCommasBetweenTerms * 3) + 1))
					$commaSeparatedTerms = 1;

				if(preg_match("/(" . $firstTerms . ")\s+\,\s+( " . $secondTerms . ")/", $sentenceText))
					$commaSeparatedTerms = 1;

				$changDocument[$lineNumber]['filters']['commaSeparatedTerms'] =	$commaSeparatedTerms;


				$changDocument[$lineNumber]['filters']['parenthesisBetweenTerms'] =
				((strpos($sentenceText, "(" . $firstTerms . ")") !== false) || 
				 (strpos($sentenceText, "(" . $firstTerms . ")") !== false)) ? 1: 0;

				$firstTermsArray = explode(" ", $firstTerms);
				$secondTermsArray = explode(" ", $secondTerms);

				foreach($firstTermsArray as $term){
					if(in_array($term, $secondTermsArray)) {
						$changDocument[$lineNumber]['filters']['overlappingTerms'] = 1;
					} else {
						$changDocument[$lineNumber]['filters']['overlappingTerms'] = 0;
					}
				}
		}

		
		// print_r($changDocument);
		// exit;

		return $changDocument;
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

	public static function store($originalEntity, $newEntityContent){
		$user = Auth::user();

		$activityURI = $originalEntity->wasGeneratedBy->_id . '/chang';

		try {
			$activity = new \mongo\text\Activity;
			$activity->_id = strtolower($activityURI);
			$activity->type = "chang";
			$activity->label = '"' . $originalEntity->title . '" was converted to a chang document';
			$activity->entity_used_id = $originalEntity->_id;
			$activity->user_id = $user->_id;
			$activity->software_id = URL::to('preprocess/chang');
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			Session::flash('flashError', $e->getMessage());
			return;
		}

		$entityURI = $originalEntity->_id . '/chang';

		try {
			$entity = new \mongo\text\Entity;
			$entity->_id = strtolower($entityURI);
			$entity->title = $originalEntity->title . '/chang';
			$entity->domain = $originalEntity->domain;
			$entity->fileType = "text";
			$entity->documentType = "chang";
			$entity->parent_id = $originalEntity->_id;
			$entity->ancestors = array($originalEntity->_id);
			$entity->activity_id = strtolower($activityURI);
			$entity->user_id = $user->_id;
			$entity->content = $newEntityContent;
			$entity->save();

			Session::flash('flashSuccess', '"' . $originalEntity->title . '" was successfully converted to a chang document. URI: ' . $entityURI);
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$activity->forceDelete();
			$entity->forceDelete();
			Session::flash('flashError', $e->getMessage());
		}
	}

	public static function createAndStoreChangChild($originalEntity, array $appliedFilters){
		if($originalEntity->documentType !== 'chang')
			return false;

		$appliedFiltersWithValues = array();

		foreach($originalEntity['content'][0]['filters'] as $filterKey => $filterValue){
			foreach($appliedFilters as $appliedFilterKey => $appliedFilterValue){
				if($appliedFilterKey == $filterKey){
					$appliedFiltersWithValues[$appliedFilterKey] = $appliedFilterValue;
				}
			}
		}

		$newEntity['appliedFilters'] = $appliedFiltersWithValues;

		foreach($appliedFilters as $appliedFilterKey => $appliedFilterValue){
			if(strpos($appliedFilterKey, 'line') !== FALSE){
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
			$activity->type = "chang";
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
			$entity->fileType = "text";
			$entity->documentType = "chang";
			$entity->parent_id = $originalEntity->_id;
			$entity->ancestors = array_merge($originalEntity->ancestors, array($originalEntity->_id));
			$entity->activity_id = strtolower($activityURI);
			$entity->user_id = $user->_id;
			$entity->content = $newEntity['content'];
			$entity->save();

			Session::flash('flashSuccess', '"' . $originalEntity->title . '" was successfully converted to a chang document. URI: ' . $entityURI);
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$activity->forceDelete();
			$entity->forceDelete();
			Session::flash('flashError', $e->getMessage());
		}

		return $entityURI;
	}
}