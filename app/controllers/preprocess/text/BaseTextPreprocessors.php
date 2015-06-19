<?php
namespace Preprocess;

/**
 * This TextPreprocessor is used to convert the input in column 'usecol' to plain text.
 */
class TextPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'text';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'textPreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/basetextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		// Validate required parameters are present
		$colName = $params['usecol'];

		// Validate required columns are present
		if(! array_key_exists($colName, $data)) {
			return 'N/A';
		}

		return $data[$colName];
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			'usecol' => $params['usecol'],
		];
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	public function isDataType() {
		return true;
	}
}

/**
 * This preprocessor converts the input in column 'usecol' to a numeric value.
 */
class NumberPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'number';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'numberPreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/basetextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		// Validate required parameters are present
		$colName = $params['usecol'];

		// Validate required columns are present
		if(! array_key_exists($colName, $data)) {
			return 'N/A';
		}

		$value = $data[$colName];
		if(str_contains($value, '.')) {
			$item = (float) $value;
		} else {
			$item = (int) $value;
		}

		return $item;
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			'usecol' => $params['usecol'],
		];
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	public function isDataType() {
		return true;
	}
}

/**
 * This preprocessor converts the input in column 'usecol' by applying 
 * the regular expression in column 'regex' and replacing it by the 
 * text in column 'replace'.
 * Boolean input 'regex_split' allows for multiple regular expressions to be used
 * Boolean input 'replace_split' allows for multiple replace values to be used.
 * Boolean input 'uppercase' converts input sentence to upper case.
 * Boolean input 'lowercase' converts input sentence to lower case.
 */
class RegExpPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Regular expression';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'regexPreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/basetextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		// Validate required parameters are present
		if(!array_key_exists('usecol', $params) || 
			!array_key_exists('regex', $params) || 
			!array_key_exists('replace', $params) ) {
			return 'N/A';
		}
		$colName = $params['usecol'];
		$regex = $params['regex'];
		$replace = $params['replace'];

		// Apply split if required.
		if(array_key_exists('regex_split', $params)) {
			$regex = explode(',',$regex);
		}
		if(array_key_exists('regex_split', $params) && array_key_exists('replace_split', $params)) {
			$replace = explode(',',$replace);
		}

		// Validate required columns are present
		if(! array_key_exists($colName, $data)) {
			return 'N/A';
		}
		$value = $data[$colName];

		// Apply upper / lower if required.	
		if(array_key_exists('uppercase', $params)) {
			$value = strtoupper($value);
		}
		if(array_key_exists('lowercase', $params)) {
			$value = strtolower($value);
		}
		
		try {
			$item = preg_replace ( $regex , $replace , $value);
		} catch(\ErrorException $e) {
			$item = '-- Invalid regular expression --';
		}
		
		return $item;
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			'usecol' 		=> $params['usecol'],
			'regex' 		=> $params['regex'],
			'replace' 		=> $params['replace'],
			'regex_split' 	=> array_key_exists('regex_split', $params) ? 'on' : 'off',
			'replace_split' => array_key_exists('replace_split', $params) ? 'on' : 'off',
			'uppercase' 	=> array_key_exists('uppercase', $params) ? 'on' : 'off',
			'lowercase' 	=> array_key_exists('lowercase', $params) ? 'on' : 'off'
		];
	}
}

/**
 * This Preprocessor counts the number of words (separated by blank space) 
 * observed in column 'usecol'.
 */
class WordCountPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Word count';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'wordcountPreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/basetextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		// Validate required parameters are present
		if(!array_key_exists('usecol', $params)) {
			return 'N/A';
		}
		$colName = $params['usecol'];
		
		// Validate required columns are present
		if(! array_key_exists($colName, $data)) {
			return 'N/A';
		}
		$value = $data[$colName];

		$words = explode(' ', $value);
		$item = count($words);
		return $item;
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			'usecol' => $params['usecol'],
		];
	}
}

/**
 * This preprocessor counts the length of the string in column 'usecol'.
 */
class StringLengthPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'String length';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'stringlengthPreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/basetextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		// Validate required parameters are present
		if(!array_key_exists('usecol', $params)) {
			return 'N/A';
		}
		$colName = $params['usecol'];
		
		// Validate required columns are present
		if(! array_key_exists($colName, $data)) {
			return 'N/A';
		}
		$value = $data[$colName];

		return strlen($value);
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			'usecol' => $params['usecol'],
		];
	}
}

/**
 * This Preprocessor calculates the levenshtein distance between terms 
 * in columns 'col1' and 'col2'.
 */
class TermDifferencePreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Term difference';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'termdifferencePreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/basetextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		// Validate required parameters are present
		if(!array_key_exists('col1', $params) || 
			!array_key_exists('col2', $params)) {
			return 'N/A';
		}
		$col1 = $params['col1'];
		$col2 = $params['col2'];
		
		// Validate required columns are present
		if(! array_key_exists($col1, $data) || ! array_key_exists($col2, $data)) {
			return 'N/A';
		}

		try {
			$distance = levenshtein($data[$col1], $data[$col2]);
		} catch(\ErrorException $e) {
			$distance = -1;
		}
			
		return $distance;
	}
	
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			'col1' => $params['col1'],
			'col2' => $params['col2'],
		];
	}
}

/**
 * This preporcessor replaces the term in column 'repFrom' for the term in column 'repBy'.
 */
class TermReplacePreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Replace term';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'replaceTermPreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/basetextpreprocessors.js"></script>';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function processItem($params, $data, $entities) {
		// Validate required parameters are present
		if(!array_key_exists('repFrom', $params) || 
			!array_key_exists('repBy', $params)) {
			return 'N/A';
		}
		$repFrom = $params['repFrom'];
		$repBy   = $params['repBy'];

		try {
			// RepBy MUST be a group which contains a text & formatted properties
			$repFrom = $this->fetchValue($repFrom, $entities);
			$repBy   = $this->fetchValue($repBy, $entities);
			
			$text = $repBy['text'];
			$formatted = $repBy['formatted'];
			$item = str_replace($text, $formatted, $repFrom);
		} catch(\ErrorException $e) {
			$item = '--Replacement failed--';
		}

		return $item;
	}

	/**
	 * Retrieve the value of an entity nested in arrays with keys 
	 * in multiple levels. Given a key in the format format x_y_z,
	 * retrieves the entity in position entities[x][x_y][x_y_z].
	 * 
	 * @param $valName   Key in format x_y_z
	 * @param $entities  Array with keys in format entities[x][x_y][x_y_z]
	 */
	function fetchValue($valName, $entities) {
		$valKeys = explode('_', $valName);

		$value = $entities;
		$fullKey = '';
		foreach ($valKeys as $key) {
			$value = $value[$key];
		}
		return $value;
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getConfiguration($params) {
		return [
			'repFrom' => $params['repFrom'],
			'repBy' => $params['repBy'],
		];
	}
}
