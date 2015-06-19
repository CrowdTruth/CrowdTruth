<?php
namespace Preprocess;

/**
 * This abstract class defines the methods a TextProcessor must implement in order 
 * to be usable for input preprocessing.
 * 
 * DEVEL NOTE: 
 * 
 * New TextPreprocessor MUST extend AbstractTextPreprocessor AND MUST be registered 
 * in /vendor/composer/autoload_classmap.php for them to be available in the application level.
 * 
 * run php artisan dump-autoload
 * 
 * @author carlosm
 */
abstract class AbstractTextPreprocessor {
	/**
	 * Return the name of this preprocessor.
	 */
	abstract function getName();
	
	/**
	 * Return the name name of the Javascript function used to generate a DIV that can 
	 * collect the parameters for this TextProcessor.
	 */
	abstract function getParameterJSFunctionName();
	
	/**
	 * Return the HTML code writes the Javascript function used by this TextPreprocessor.
	 * Alternatively, this can return the HTML code required to include the Javascript 
	 * function.
	 */
	abstract function getParameterJSFunction();

	/**
	 * Perform the preprocessing for a given item, using the given parameters and data. The entity 
	 * currently being created is also given, in case the preprocessor requires information from 
	 * other fields in the entity.
	 * 
	 * @param $params   Array with name => value of expected parameters, defined by the preprocessor.
	 * @param $data     Array with name => value of data available to the preprocessor.
	 * @param $entities Entity currently being created (READ ONLY).
	 */
	abstract function processItem($params, $data, $entities);
	
	/**
	 * Return the configuration parameters for this Preprocessor, given a list of all available
	 * configuration parameters.
	 * 
	 * @param $params List of all available configuration parameters.
	 */
	abstract function getConfiguration($params);
	
	/**
	 * This function should be overriden if-and-only-if the implementing 
	 * TextPreprocessor is aimed at processing a basic data type (number, text).
	 * 
	 * @return boolean True if basic data type, false otherwise.
	 */
	public function isDataType() {
		return false;
	}
}
