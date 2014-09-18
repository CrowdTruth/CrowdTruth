<?php

namespace Preprocess;
// http_build_query has its own ideas about how to serialize different types, so we use our own version here.
class TextRazorQueryBuilder {
    private $params = array();

    public function add($key, $value) {
			if (is_null($value)) {
				return;
			}
			else if (is_array($value)) {
				foreach ($value as $listItem) {
					$this->add($key, $listItem);
				}
			}
			else if (is_bool($value)) {
				$this->add($key, $value ? "true" : "false");
			}
			else {
				$this->params[] = urlencode($key) . '=' . urlencode($value);
			}
    }

    public function build() {
        return implode("&", $this->params);
    }
}

class TextRazor {
	private $apiKey;
	private $endPoint;
	private $secureEndPoint;

	private $enableEncryption;
	private $enableCompression;

	private $extractors;

	private $rules;

	private $cleanupHTML;

	private $languageOverride;

	private $dbpediaTypeFilters;
	private $freebaseTypeFilters;

	public function __construct($apiKey) {
		if(!is_string($apiKey)) {
			throw new Exception('TextRazor Error: Invalid API key');
		}

		if(!function_exists('curl_version')) {
			throw new Exception('TextRazor Error: TextRazor requires cURL support to be enabled on your PHP installation');
		}

		$this->apiKey = $apiKey;
		$this->endPoint = 'http://api.textrazor.com/';
		$this->secureEndPoint = 'https://api.textrazor.com/';

		$this->enableCompression = true;
		$this->enableEncryption = false;

		$this->extractors = array();
		$this->rules = NULL;
		$this->cleanupHTML = false;
		$this->languageOverride = NULL;

		$this->dbpediaTypeFilters = array();
		$this->freebaseTypeFilters = array();
		$this->enrichmentQueries = array();
		$this->allowOverlap = true;
	}

	public function setAPIKey($apiKey) {
		if(!is_string($apiKey)) {
			throw new Exception('TextRazor Error: Invalid API key');
		}

		$this->apiKey = $apiKey;
	}

	public function setEndPoint($endPoint) {
		if(!is_string($endPoint)) {
			throw new Exception('TextRazor Error: Invalid HTTP Endpoint');
		}

		$this->endPoint = $endPoint;
	}

	public function setSecureEndPoint($endPoint) {
		if(!is_string($endPoint)) {
			throw new Exception('TextRazor Error: Invalid HTTPS Endpoint');
		}

		$this->secureEndPoint = $endPoint;
	}

	public function setEnableCompression($enableCompression) {
		if(!is_bool($enableCompression)) {
			throw new Exception('TextRazor Error: enableCompression must be a bool');
		}

		$this->enableCompression = $enableCompression;
	}

	public function setEnableEncryption($enableEncryption) {
		if(!is_bool($enableEncryption)) {
			throw new Exception('TextRazor Error: enableEncryption must be a bool');
		}

		$this->enableEncryption = $enableEncryption;
	}

	public function setExtractors($extractors) {
		if(!is_array($extractors)) {
			throw new Exception('TextRazor Error: extractors must be an array of strings');
		}

		$this->extractors = $extractors;
	}

	public function addExtractor($extractor) {
		if(!is_string($extractor)) {
			throw new Exception('TextRazor Error: extractor must be a string');
		}

		array_push($this->extractors, $extractor);
	}

	public function setRules($rules) {
		if(!is_string($rules)) {
			throw new Exception('TextRazor Error: rules must be a string');
		}

		$this->rules = $rules;
	}

	public function setCleanupHTML($cleanupHTML) {
		if(!is_bool($cleanupHTML)) {
			throw new Exception('TextRazor Error: cleanupHTML must be a bool');
		}

		$this->cleanupHTML = $cleanupHTML;
	}

	public function setLanguageOverride($languageOverride) {
		if(!is_string($languageOverride)) {
			throw new Exception('TextRazor Error: languageOverride must be a string');
		}

		$this->languageOverride = $languageOverride;
	}

	public function setAllowOverlap($allowOverlap) {
		if (!is_bool($allowOverlap)) {
			throw new Exception('TextRazor Error: allowOverlap must be a bool');
		}

		$this->allowOverlap = $allowOverlap;
	}

	public function addDbpediaTypeFilter($filter) {
		if(!is_string($filter)) {
			throw new Exception('TextRazor Error: filter must be a string');
		}

		array_push($this->dbpediaTypeFilters, $filter);
	}

	public function addFreebaseTypeFilter($filter) {
		if(!is_string($filter)) {
			throw new Exception('TextRazor Error: filter must be a string');
		}

		array_push($this->freebaseTypeFilters, $filter);
	}

	public function addEnrichmentQuery($query) {
		if(!is_string($query)) {
			throw new Exception('TextRazor Error: query must be a string');
		}

		array_push($this->enrichmentQueries, $query);
	}

	public function analyze($text) {
		if(!is_string($text)) {
			throw new Exception('TextRazor Error: text must be a UTF8 encoded string');
		}

		if (empty($this->extractors)) {
			throw new Exception('TextRazor Error: Please specify at least one extractor');
		}

		$builder = new TextRazorQueryBuilder();

		$builder->add('extractors', $this->extractors);
		$builder->add('apiKey', $this->apiKey);
		$builder->add('text', $text);
		$builder->add('cleanupHTML', $this->cleanupHTML);
		$builder->add('extractors', $this->extractors);
		$builder->add('rules', $this->rules);
		$builder->add('languageOverride', $this->languageOverride);

		$builder->add('entities.allowOverlap', $this->allowOverlap);
		$builder->add('entities.filterDbpediaTypes', $this->dbpediaTypeFilters);
		$builder->add('entities.filterFreebaseTypes', $this->freebaseTypeFilters);
		$builder->add('entities.enrichmentQueries', $this->enrichmentQueries);

		return $this->sendPOST($builder->build());
	}

	public function sendPOST($textrazorParams) {
		$ch = curl_init();

		if ($this->enableEncryption) {
			curl_setopt($ch, CURLOPT_URL, $this->secureEndPoint);
		}
		else {
			curl_setopt($ch, CURLOPT_URL, $this->endPoint);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if ($this->enableCompression) {
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		}

		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $textrazorParams);

		$reply = curl_exec ($ch);

		$rc = curl_errno($ch);
		if (0 != $rc) {
			throw new Exception('TextRazor Error: Network problem connecting to TextRazor. CURL Error Code:' . $rc);
		}

		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (200 != $httpStatus) {
			throw new Exception('TextRazor Error: TextRazor returned HTTP code: ' . $httpStatus . ' Message:' . $reply);
		}

		curl_close ($ch);
		unset($ch);

		$jsonReply = json_decode($reply,true);

		return $jsonReply;
	}

}