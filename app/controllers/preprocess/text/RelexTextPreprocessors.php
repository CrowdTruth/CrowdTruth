<?php
namespace Preprocess\Relex;

use Preprocess\AbstractTextPreprocessor as AbstractTextPreprocessor;

/**
 * Remove the prefix of a RelEx relation term. For instance:
 * relex-cause yields cause.
 * 
 * @param $relation the RelEx relation.
 */
function removePrefix($relation) {
	return explode("-", $relation)[1];
}

/**
 * Extract the simple stem from a RelEx relation term. For instance:
 * relex-cause yields caus
 * 
 * @param $relation the RelEx relation.
 * @return The short stem form: 'caus', 'locat' or 'diagnos'
 */
function simpleStem($relation){
	$relation = removePrefix($relation);
	
	switch (strtolower($relation)) {
		case 'cause':
			return 'caus';
		case 'location':
			return 'locat';
		case 'diagnose':
			return 'diagnos';
	}

	return $relation;
}

/**
 * This preprocessor is used to determine if a relation term can be 
 * found within a given sentence.
 */
class RelationInSentencePreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Relation in sentence';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'relationInSentence';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		$relation = $data[$params['relation']];
		$sentence = $data[$params['sentence']];
		
		$relation =  simpleStem($relation);
		$sentence = strtolower($sentence);

		if(stripos($sentence, $relation)) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			"relation" => $params["relation"],
			"sentence" => $params["sentence"],
		];
	}
}

/**
 * This preprocessor can be used to determine if a given relation term 
 * is present in a given sentence but it is not located between a given pair
 * of reference terms.
 */
class RelationOutsideTermsPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Relation outside terms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'relationOutsideTerms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		$relation = $data[$params['relation']];
		$sentence = $data[$params['sentence']];
		$idxTerm1 = $data[$params['startTerm1']];
		$idxTerm2 = $data[$params['startTerm2']];
		
		$relation =  simpleStem($relation);
		$sentence = strtolower($sentence);
		
		if($idxTerm1 < $idxTerm2) {
			if(stripos(substr($sentence, 0, $idxTerm1), $relation)) {
				return 1;
			}
			if(stripos(substr($sentence, $idxTerm2), $relation)) {
				return 1;
			}
		} else {
			if(stripos(substr($sentence, $idxTerm1), $relation)) {
				return 1;
			}
			if(stripos(substr($sentence, 0, $idxTerm2), $relation)) {
				return 1;
			}
		}
		return 0;
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			"relation" => $params["relation"],
			"sentence" => $params["sentence"],
			"startTerm1" => $params["startTerm1"],
			"startTerm2" => $params["startTerm2"],
		];
	}
}

/**
 * This preprocessor can be used to determine if a given relation term
 * is present in a given sentence and it is located between a given pair
 * of reference terms.
 */
class RelationBetweenTermsPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Relation between terms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'relationBetweenTerms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		$relation = $data[$params['relation']];
		$sentence = $data[$params['sentence']];
		$startTerm1 = $data[$params['startTerm1']];
		$endTerm1 = $data[$params['endTerm1']];
		$startTerm2 = $data[$params['startTerm2']];
		$endTerm2 = $data[$params['endTerm2']];
		
		$relation =  simpleStem($relation);
		$sentence = strtolower($sentence);
		
		if($startTerm1 < $startTerm2) {
			if(stripos(substr($sentence, $endTerm1, $startTerm2 - $endTerm1), $relation)) {
				return 1;
			}
		} else {
			if(stripos(substr($sentence, $endTerm2, $startTerm1 - $endTerm2), $relation)) {
				return 1;
			}
		}
		return 0;
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			"relation" => $params["relation"],
			"sentence" => $params["sentence"],
			"startTerm1" => $params["startTerm1"],
			"endTerm1" => $params["endTerm1"],
			"startTerm2" => $params["startTerm2"],
			"endTerm2" => $params["endTerm2"],
		];
	}
}

/**
 * This preprocessor can be used to determine if the semicolon ';' character is 
 * located between two given reference terms.
 */
class SemicolonBetweenTermsPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Semicolon between terms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'hasSemicolon';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		$sentence = $data[$params['sentence']];
		$startTerm1 = $data[$params['startTerm1']];
		$endTerm1 = $data[$params['endTerm1']];
		$startTerm2 = $data[$params['startTerm2']];
		$endTerm2 = $data[$params['endTerm2']];
		
		$sentence = strtolower($sentence);
		
		if($startTerm1 < $startTerm2) {
			if(stripos(substr($sentence, $endTerm1, $startTerm2 - $endTerm1), ';')) {
				return 1;
			}
		} else {
			if(stripos(substr($sentence, $endTerm2, $startTerm1 - $endTerm2), ';')) {
				return 1;
			}
		}
		return 0;
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			"sentence" => $params["sentence"],
			"startTerm1" => $params["startTerm1"],
			"endTerm1" => $params["endTerm1"],
			"startTerm2" => $params["startTerm2"],
			"endTerm2" => $params["endTerm2"],
		];
	}
}

/**
 * This preprocessor can be used to determine if the comma ',' character is used as 
 * divider for the given pair of terms.
 */
class CommaSeparatedTermsPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Comma separated terms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'hasComma';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		$relation = $data[$params['relation']];
		$sentence = $data[$params['sentence']];
		$term1 = $data[$params['term1']];
		$startTerm1 = $data[$params['startTerm1']];
		$endTerm1 = $data[$params['endTerm1']];
		$term2 = $data[$params['term2']];
		$startTerm2 = $data[$params['startTerm2']];
		$endTerm2 = $data[$params['endTerm2']];
		
		$relation =  simpleStem($relation);
		$term1 = strtolower($term1);
		$term2 = strtolower($term2);
		$sentence = strtolower($sentence);
		
		if($startTerm1 < $startTerm2) {
			$textWithAndBetweenTerms = substr($sentence, $startTerm1, $endTerm2 - $startTerm1);
		} else {
			$textWithAndBetweenTerms = substr($sentence, $startTerm2, $endTerm1 - $startTerm2);
		}
		
		$numberOfWordsBetweenTerms = str_word_count($textWithAndBetweenTerms);
		$numberOfCommasBetweenTerms = substr_count($textWithAndBetweenTerms, ",");
		
		if($numberOfWordsBetweenTerms < (($numberOfCommasBetweenTerms * 3) + 1)) {
			return 1;
		}

		$pattern = '#' . preg_quote($term1, '#') . '\s*(and|or|,)\s*' . preg_quote($term2, '#') . '#i';
		try {
			if(preg_match_all($pattern, $sentence, $matches, PREG_OFFSET_CAPTURE)) {
				foreach($matches as $match) {
					$filterOptions = [ 'options' => [
											'min_range' => $match[0][1],
											'max_range' => (strlen($match[0][0]) + $match[0][1])
									] ];
					if(filter_var($startTerm1, FILTER_VALIDATE_INT, $filterOptions)) {
						return 1;
					}
				}
			}
		} catch (Exception $e) {
			dd($relexStructuredSentence);
		}
		return 0;
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			"relation" => $params["relation"],
			"sentence" => $params["sentence"],
			"term1" => $params["term1"],
			"startTerm1" => $params["startTerm1"],
			"endTerm1" => $params["endTerm1"],
			"term2" => $params["term2"],
			"startTerm2" => $params["startTerm2"],
			"endTerm2" => $params["endTerm2"],
		];
	}
}

/**
 * This preprocessor is used to determine if a given pair of reference terms
 * are surounded by parenthesis in a given sentence.
 */
class ParenthesisAroundTermsPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Parenthesis around terms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'hasParenthesis';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		$sentence = $data[$params['sentence']];
		$term1 = $data[$params['term1']];
		$startTerm1 = $data[$params['startTerm1']];
		$term2 = $data[$params['term2']];
		$startTerm2 = $data[$params['startTerm2']];
		
		$sentence = strtolower($sentence);
		$term1 = strtolower($term1);
		$term2 = strtolower($term2);
				
		if(stripos($sentence, "(" . $term1 . ")") !== false) {
			return 1;
		}
		if(stripos($sentence, "(" . $term2 . ")") !== false) {
			return 1;
		}
		
		$pattern = '#\([^)]*' . preg_quote($term1, '#') . '[^)]*\)#i';
		
		if(preg_match_all($pattern, $sentence, $matches, PREG_OFFSET_CAPTURE)) {
			foreach($matches as $match) {
				$options = [ 'options' => [
								'min_range' => $match[0][1],
								'max_range' => (strlen($match[0][0]) + $match[0][1])
							] ];
				if(filter_var($startTerm1, FILTER_VALIDATE_INT, $options)) {
					return 1;
				}
			}
		}
		
		$pattern = '#\([^)]*' . preg_quote($term2, '#') . '[^)]*\)#i';
		
		if(preg_match_all($pattern, $sentence, $matches, PREG_OFFSET_CAPTURE)) {
			foreach($matches as $match) {
				$options = [ 'options' => [
							'min_range' => $match[0][1],
							'max_range' => (strlen($match[0][0]) + $match[0][1])
						] ];
				if(filter_var($startTerm2, FILTER_VALIDATE_INT, $options)) {
					return 1;
				}
			}
		}
		return 0;
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			"sentence" => $params["sentence"],
			"term1" => $params["term1"],
			"startTerm1" => $params["startTerm1"],
			"term2" => $params["term2"],
			"startTerm2" => $params["startTerm2"],
		];
	}
}

/**
 * This preprocessor is used to determine whether two given reference terms
 * overlap within a given sentence.
 */
class OverlapingTermsPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Overlapping terms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'hasOverlappingTerms';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		$startTerm1 = $data[$params['startTerm1']];
		$endTerm1 = $data[$params['endTerm1']];
		$startTerm2 = $data[$params['startTerm2']];
		$endTerm2 = $data[$params['endTerm2']];
		
		$opt_11 = [ 'options' => [
		            'min_range' => $startTerm1, 
		            'max_range' => $endTerm1
		        ] ];
		$opt_22 = [ 'options' => [
		            'min_range' => $startTerm2, 
		            'max_range' => $endTerm2
		        ] ];
		if(filter_var( $startTerm2, FILTER_VALIDATE_INT, $opt_11) || 
			filter_var($endTerm2, FILTER_VALIDATE_INT, $opt_11) || 
			filter_var($startTerm1, FILTER_VALIDATE_INT, $opt_22) || 
			filter_var($endTerm1, FILTER_VALIDATE_INT, $opt_22) ) {
			return 1;
		} else {
			return 0;
		}
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			"startTerm1" => $params["startTerm1"],
			"endTerm1" => $params["endTerm1"],
			"startTerm2" => $params["startTerm2"],
			"endTerm2" => $params["endTerm2"],
		];
	}
}
