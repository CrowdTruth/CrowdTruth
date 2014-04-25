<?php

/**
   Copyright 2013 AlchemyAPI

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

namespace AlchemyAPI;

class AlchemyAPI {
	
	private $_api_key;
	private $_ENDPOINTS;
    private $_BASE_URL = 'http://access.alchemyapi.com/calls';


	/** 
	  *	Initializes the SDK so it can send requests to AlchemyAPI for analysis.
	  *	It loads the API key from api_key.txt and configures the endpoints.
	  *	This function will be called automatically when needed the AlchemyAPI object is created.
	  *
	  * Note: if you don't have an API key, register for one at: http://www.alchemyapi.com/api/register.html
	  *
	  * INPUT:
	  * none
	  *
	  * OUTPUT:
	  * none
	*/ 
	public function AlchemyAPI() {
		//Load the API Key from api_key.txt
		$key = trim(file_get_contents("api_key.txt"));
	
		if (!$key) {
			//Keys should not be blank
			echo 'The api_key.txt file appears to be blank, please copy/paste your API key in the file: api_key.txt', PHP_EOL;
			echo 'If you do not have an API Key from AlchemyAPI, please register for one at: http://www.alchemyapi.com/api/register.html', PHP_EOL;
			file_put_contents('api_key.txt','');
			exit(1);
		}

		if (strlen($key) != 40) {
			echo strlen($key), PHP_EOL;
			//Keys should be 40 characters long
			echo 'It appears that the key in api_key.txt is invalid. Please make sure the file only includes the API key, and it is the correct one.', PHP_EOL;
			exit(1);
		}

		$this->_api_key = $key;


		//Initialize the API Endpoints
		$this->_ENDPOINTS['sentiment']['url'] = '/url/URLGetTextSentiment';
		$this->_ENDPOINTS['sentiment']['text'] = '/text/TextGetTextSentiment';
		$this->_ENDPOINTS['sentiment']['html'] = '/html/HTMLGetTextSentiment';
		$this->_ENDPOINTS['sentiment_targeted']['url'] = '/url/URLGetTargetedSentiment';
		$this->_ENDPOINTS['sentiment_targeted']['text'] = '/text/TextGetTargetedSentiment';
		$this->_ENDPOINTS['sentiment_targeted']['html'] = '/html/HTMLGetTargetedSentiment';
		$this->_ENDPOINTS['author']['url'] = '/url/URLGetAuthor';
		$this->_ENDPOINTS['author']['html'] = '/html/HTMLGetAuthor';
		$this->_ENDPOINTS['keywords']['url'] = '/url/URLGetRankedKeywords';
		$this->_ENDPOINTS['keywords']['text'] = '/text/TextGetRankedKeywords';
		$this->_ENDPOINTS['keywords']['html'] = '/html/HTMLGetRankedKeywords';
		$this->_ENDPOINTS['concepts']['url'] = '/url/URLGetRankedConcepts';
		$this->_ENDPOINTS['concepts']['text'] = '/text/TextGetRankedConcepts';
		$this->_ENDPOINTS['concepts']['html'] = '/html/HTMLGetRankedConcepts';
		$this->_ENDPOINTS['entities']['url'] = '/url/URLGetRankedNamedEntities';
		$this->_ENDPOINTS['entities']['text'] = '/text/TextGetRankedNamedEntities';
		$this->_ENDPOINTS['entities']['html'] = '/html/HTMLGetRankedNamedEntities';
		$this->_ENDPOINTS['category']['url']  = '/url/URLGetCategory';
		$this->_ENDPOINTS['category']['text'] = '/text/TextGetCategory';
		$this->_ENDPOINTS['category']['html'] = '/html/HTMLGetCategory';
		$this->_ENDPOINTS['relations']['url']  = '/url/URLGetRelations';
		$this->_ENDPOINTS['relations']['text'] = '/text/TextGetRelations';
		$this->_ENDPOINTS['relations']['html'] = '/html/HTMLGetRelations';
		$this->_ENDPOINTS['language']['url']  = '/url/URLGetLanguage';
		$this->_ENDPOINTS['language']['text'] = '/text/TextGetLanguage';
		$this->_ENDPOINTS['language']['html'] = '/html/HTMLGetLanguage';
		$this->_ENDPOINTS['text']['url']  = '/url/URLGetText';
		$this->_ENDPOINTS['text']['html'] = '/html/HTMLGetText';
		$this->_ENDPOINTS['text_raw']['url']  = '/url/URLGetRawText';
		$this->_ENDPOINTS['text_raw']['html'] = '/html/HTMLGetRawText';
		$this->_ENDPOINTS['title']['url']  = '/url/URLGetTitle';
		$this->_ENDPOINTS['title']['html'] = '/html/HTMLGetTitle';
		$this->_ENDPOINTS['feeds']['url']  = '/url/URLGetFeedLinks';
		$this->_ENDPOINTS['feeds']['html'] = '/html/HTMLGetFeedLinks';
		$this->_ENDPOINTS['microformats']['url']  = '/url/URLGetMicroformatData';
		$this->_ENDPOINTS['microformats']['html'] = '/html/HTMLGetMicroformatData';
	}


	/**
	  *	Extracts the entities for text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/entity-extraction/ 
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/entity-extraction/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	disambiguate -> disambiguate entities (i.e. Apple the company vs. apple the fruit). 0: disabled, 1: enabled (default)
	  *	linkedData -> include linked data on disambiguated entities. 0: disabled, 1: enabled (default) 
	  *	coreference -> resolve coreferences (i.e. the pronouns that correspond to named entities). 0: disabled, 1: enabled (default)
	  *	quotations -> extract quotations by entities. 0: disabled (default), 1: enabled.
	  *	sentiment -> analyze sentiment for each entity. 0: disabled (default), 1: enabled. Requires 1 additional API transction if enabled.
	  *	showSourceText -> 0: disabled (default), 1: enabled 
	  *	maxRetrieve -> the maximum number of entities to retrieve (default: 50)
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function entities($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['entities'])) {
			return array('status'=>'ERROR','statusInfo'=>'Entity extraction for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['entities'][$flavor], $options);
	}


	/**
	  *	Extracts the keywords from text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/keyword-extraction/
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/keyword-extraction/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *			
	  *	Available Options:
	  *	keywordExtractMode -> normal (default), strict
	  *	sentiment -> analyze sentiment for each keyword. 0: disabled (default), 1: enabled. Requires 1 additional API transaction if enabled.
	  *	showSourceText -> 0: disabled (default), 1: enabled.
	  *	maxRetrieve -> the max number of keywords returned (default: 50)
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function keywords($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['keywords'])) {
			return array('status'=>'ERROR','statusInfo'=>'Keyword extraction for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['keywords'][$flavor], $options);
	}
	
	
	/**
	  *	Tags the concepts for text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/concept-tagging/
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/concept-tagging/ 
	  *	
	  *	Available Options:
	  *	maxRetrieve -> the maximum number of concepts to retrieve (default: 8)
	  *	linkedData -> include linked data, 0: disabled, 1: enabled (default)
	  *	showSourceText -> 0:disabled (default), 1: enabled
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function concepts($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['concepts'])) {
			return array('status'=>'ERROR','statusInfo'=>'Concept tagging for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['concepts'][$flavor], $options);
	}


	/**
	  * Calculates the sentiment for text, a URL or HTML.
	  * For an overview, please refer to: http://www.alchemyapi.com/products/features/sentiment-analysis/
	  * For the docs, please refer to: http://www.alchemyapi.com/api/sentiment-analysis/
	  *        
	  * INPUT:
	  * flavor -> which version of the call, i.e. text, url or html.
	  * data -> the data to analyze, either the text, the url or html code.
	  * options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *        
	  * Available Options:
	  * showSourceText -> 0: disabled (default), 1: enabled
	  *
	  * OUTPUT:
	  * The response, already converted from JSON to a PHP object. 
	*/
	public function sentiment($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['sentiment'])) {
			return array('status'=>'ERROR','statusInfo'=>'Sentiment analysis for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['sentiment'][$flavor], $options);
	}


	/**
	  *	Calculates the targeted sentiment for text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/sentiment-analysis/
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/sentiment-analysis/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	target -> the word or phrase to run sentiment analysis on.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	showSourceText	-> 0: disabled, 1: enabled
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function sentiment_targeted($flavor, $data, $target, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['sentiment_targeted'])) {
			return array('status'=>'ERROR','statusInfo'=>'Targeted sentiment analysis for ' . $flavor . ' not available');
		}

		if (!$target) {
			return array('status'=>'ERROR','statusInfo'=>'targeted sentiment requires a non-null target');
		}

		//Add the URL encoded data to the options and analyze
		$options[$flavor] = $data;
		$options['target'] = $target;
		return $this->analyze($this->_ENDPOINTS['sentiment_targeted'][$flavor], $options);
	}


	/**
	  *	Extracts the cleaned text (removes ads, navigation, etc.) for text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/text-extraction/
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/text-extraction/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	useMetadata -> utilize meta description data, 0: disabled, 1: enabled (default)
	  *	extractLinks -> include links, 0: disabled (default), 1: enabled.
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function text($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['text'])) {
			return array('status'=>'ERROR','statusInfo'=>'Clean text extraction for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['text'][$flavor], $options);
	}


	/**
	  *	Extracts the raw text (includes ads, navigation, etc.) for a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/text-extraction/ 
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/text-extraction/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	none
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function text_raw($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['text_raw'])) {
			return array('status'=>'ERROR','statusInfo'=>'Raw text extraction for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['text_raw'][$flavor], $options);
	}
	

	/**
	  *	Extracts the author from a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/author-extraction/
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/author-extraction/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *
	  *	Availble Options:
	  *	none
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function author($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['author'])) {
			return array('status'=>'ERROR','statusInfo'=>'Author extration for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['author'][$flavor], $options);
	}


	/**
	  *	Detects the language for text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/api/language-detection/ 
	  *	For the docs, please refer to: http://www.alchemyapi.com/products/features/language-detection/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *
	  *	Available Options:
	  *	none
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function language($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['language'])) {
			return array('status'=>'ERROR','statusInfo'=>'Language detection for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['language'][$flavor], $options);
	}


	/**
	  *	Extracts the title for a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/text-extraction/ 
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/text-extraction/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	useMetadata -> utilize title info embedded in meta data, 0: disabled, 1: enabled (default) 
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function title($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['title'])) {
			return array('status'=>'ERROR','statusInfo'=>'Title text extraction for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['title'][$flavor], $options);
	}


	/**
	  *	Extracts the relations for text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/relation-extraction/ 
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/relation-extraction/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	sentiment -> 0: disabled (default), 1: enabled. Requires one additional API transaction if enabled.
	  *	keywords -> extract keywords from the subject and object. 0: disabled (default), 1: enabled. Requires one additional API transaction if enabled.
	  *	entities -> extract entities from the subject and object. 0: disabled (default), 1: enabled. Requires one additional API transaction if enabled.
	  *	requireEntities -> only extract relations that have entities. 0: disabled (default), 1: enabled.
	  *	sentimentExcludeEntities -> exclude full entity name in sentiment analysis. 0: disabled, 1: enabled (default)
	  *	disambiguate -> disambiguate entities (i.e. Apple the company vs. apple the fruit). 0: disabled, 1: enabled (default)
	  *	linkedData -> include linked data with disambiguated entities. 0: disabled, 1: enabled (default).
	  *	coreference -> resolve entity coreferences. 0: disabled, 1: enabled (default)  
	  *	showSourceText -> 0: disabled (default), 1: enabled.
	  *	maxRetrieve -> the maximum number of relations to extract (default: 50, max: 100)
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function relations($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['relations'])) {
			return array('status'=>'ERROR','statusInfo'=>'Relation extraction for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['relations'][$flavor], $options);
	}


	/**
	  *	Categorizes the text for text, a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/text-categorization/
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/text-categorization/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e. text, url or html.
	  *	data -> the data to analyze, either the text, the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	showSourceText -> 0: disabled (default), 1: enabled
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object.
	*/
	public function category($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['category'])) {
			return array('status'=>'ERROR','statusInfo'=>'Text categorization for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['category'][$flavor], $options);
	}
	

	/**
	  *	Detects the RSS/ATOM feeds for a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/feed-detection/ 
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/feed-detection/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e.  url or html.
	  *	data -> the data to analyze, either the the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *
	  *	Available Options:
	  *	none
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function feeds($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['feeds'])) {
			return array('status'=>'ERROR','statusInfo'=>'Feed detection for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['feeds'][$flavor], $options);
	}


	/**
	  *	Parses the microformats for a URL or HTML.
	  *	For an overview, please refer to: http://www.alchemyapi.com/products/features/microformats-parsing/
	  *	For the docs, please refer to: http://www.alchemyapi.com/api/microformats-parsing/
	  *	
	  *	INPUT:
	  *	flavor -> which version of the call, i.e.  url or html.
	  *	data -> the data to analyze, either the the url or html code.
	  *	options -> various parameters that can be used to adjust how the API works, see below for more info on the available options.
	  *	
	  *	Available Options:
	  *	none
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	public function microformats($flavor, $data, $options) {
		//Make sure this request supports the flavor
		if (!array_key_exists($flavor, $this->_ENDPOINTS['microformats'])) {
			return array('status'=>'ERROR','statusInfo'=>'Microformat parsing for ' . $flavor . ' not available');
		}

		//Add the data to the options and analyze
		$options[$flavor] = $data;
		return $this->analyze($this->_ENDPOINTS['microformats'][$flavor], $options);
	}


	/**
	  *	HTTP Request wrapper that is called by the endpoint functions. This function is not intended to be called through an external interface. 
	  *	It makes the call, then converts the returned JSON string into a PHP object. 
	  *	
	  *	INPUT:
	  *	url -> the full URI encoded url
	  *
	  *	OUTPUT:
	  *	The response, already converted from JSON to a PHP object. 
	*/
	private function analyze($endpoint, $params) {
		//Insert the base URL
		$url = $this->_BASE_URL . $endpoint;

		//Add the API Key and set the output mode to JSON
		$params['apikey'] = $this->_api_key;
		$params['outputMode'] = 'json';
		
		//Create the HTTP header
		$header = array('http' => array('method' => 'POST','header'=>'Content-Type: application/x-www-form-urlencode', 'content'=>http_build_query($params)));

		//Fire off the HTTP Request
		try {
			$fp = @fopen($url, 'rb',false, stream_context_create($header));
			$response = @stream_get_contents($fp);
			fclose($fp);
			return json_decode($response, true);
		} catch (Exception $e) {
			return array('status'=>'ERROR', 'statusInfo'=>'Network error');
		}
	}
}


/** 
  * Checks if file is called directly, and then writes the API key to api_key.txt if it's included in the args 
  *
  * Note: if you don't have an API key, register for one at: http://www.alchemyapi.com/api/register.html
  *
  * INPUT:
  * Your API Key (sent as a command line argument)
  *
  * OUTPUT:
  * none
*/  
if (php_sapi_name() == 'cli') {
	if (count($argv) == 2) {
		if (strlen($argv[1]) == 40) {
			file_put_contents('api_key.txt',$argv[1]);
			echo 'Key: ' . $argv[1] . ' successfully written to api_key.txt', PHP_EOL;
			echo 'You are now ready to start using AlchemyAPI. For example, run: php example.php', PHP_EOL;
		} else {
			echo 'Invalid key! Make sure it is 40 characters in length', PHP_EOL;
		}
	}
}

?>

