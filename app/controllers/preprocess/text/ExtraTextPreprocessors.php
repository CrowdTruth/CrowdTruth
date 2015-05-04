<?php
namespace Preprocess\Extra;

use Preprocess\AbstractTextPreprocessor as AbstractTextPreprocessor;

/**
 * This TextPreprocessor is used to convert the input in column 'usecol' to plain text.
 */
class JsonTextPreprocessor extends AbstractTextPreprocessor {
	/**
	 * See AbstractTextPreprocessor.
	 */
	function getName() {
		return 'Decode JSON document';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunctionName() {
		return 'jsonPreprocessor';
	}

	/**
	 * See AbstractTextPreprocessor.
	 */
	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/extratextpreprocessors.js"></script>';
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

		return  json_decode ($data[$colName]);
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
