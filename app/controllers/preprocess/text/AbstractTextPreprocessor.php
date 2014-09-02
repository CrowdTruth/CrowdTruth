<?php
namespace Preprocess;

// TODO: Document!
abstract class AbstractTextPreprocessor {
	abstract function getName();
	abstract function getParameterJSFunctionName();
	abstract function getParameterJSFunction();
	/**
	 * 
	 * @param $params   Array with name => value of expected parameters, defined by the preprocessor.
	 * @param $data     Array with name => value of data available to the preprocessor.
	 * @param $entities Entity currently being created (READ ONLY).
	 */
	abstract function processItem($params, $data, $entities);
}