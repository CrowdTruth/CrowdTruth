<?php

namespace Preprocess;
require_once 'sparqllib.php';


use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use URL, Session, Exception;


class MetadatadescriptionStructurer {

	private $urlTDH = "http://entityclassifier.eu/thd/api/v2/extraction?";

	public function searchForStopWords($label) {
		$stopWords = array('aan', 'andere', 'de', 'doen', 'er', 'heb', 'hij', 'in', 
							'kunnen', 'mij', 'niets', 'ons', 'tegen', 'uw', 'was', 'wie', 
							'zei', 'zonder', 'af', 'ben', 'der', 'door', 'ge', 'hebben', 
							'hoe', 'is', 'maar', 'mijn', 'nog', 'ook', 'toch', 'van', 'wat', 
							'wij', 'zelf', 'zou', 'al', 'bij', 'deze', 'dus', 'geen', 'heeft', 
							'hun', 'ja', 'me', 'moet', 'nu', 'op', 'toen', 'veel', 'we', 'will', 
							'zich', 'alles', 'daar', 'die', 'een', 'geweest', 'hem', 'je', 
							'iemand', 'meer', 'na', 'of', 'over', 'tot', 'voor', 'wel', 
							'worden', 'zij', 'als', 'dan', 'eens', 'haar', 'het', 'iets', 
							'kan', 'men', 'naar', 'om', 'reeds', 'u', 'want', 'werd', 'zal', 
							'zijn', 'altijd', 'dat', 'doch', 'en', 'had', 'hier', 'ik', 'kon', 
							'met', 'omdat', 'te', 'uit', 'waren', 'wezen', 'ze', 'zo', 'niet', 
							'dit', 'onder', 'wil', 'div', 'div.', 'ext', 'ext.', 'shots', 'cu', 
							'dhr', 'ter', 'de div', 'ex-', 'jr', 'jr.');

		if (in_array(strtolower($label), $stopWords)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function searchForStopWordsEndLabel($label) {
		$stopWords = array('aan', 'andere', 'de', 'doen', 'er', 'heb', 'hij', 'in', 
							'kunnen', 'mij', 'niets', 'ons', 'tegen', 'uw', 'was', 'wie', 
							'zei', 'zonder', 'af', 'ben', 'der', 'door', 'ge', 'hebben', 
							'hoe', 'is', 'maar', 'mijn', 'nog', 'ook', 'toch', 'van', 'wat', 
							'wij', 'zelf', 'zou', 'al', 'bij', 'deze', 'dus', 'geen', 'heeft', 
							'hun', 'ja', 'me', 'moet', 'nu', 'op', 'toen', 'veel', 'we', 'will', 
							'zich', 'alles', 'daar', 'die', 'een', 'geweest', 'hem', 'je', 
							'iemand', 'meer', 'na', 'of', 'over', 'tot', 'voor', 'wel', 
							'worden', 'zij', 'als', 'dan', 'eens', 'haar', 'het', 'iets', 
							'kan', 'men', 'naar', 'om', 'reeds', 'u', 'want', 'werd', 'zal', 
							'zijn', 'altijd', 'dat', 'doch', 'en', 'had', 'hier', 'ik', 'kon', 
							'met', 'omdat', 'te', 'uit', 'waren', 'wezen', 'ze', 'zo', 'niet', 
							'dit', 'onder', 'wil', 'div', 'div.', 'ext', 'ext.', 'shots', 'cu', 
							'dhr', 'ter', 'jr', 'jr.');
		$newLabel = "";
		$words = explode(" ", $label);
		if (count($words) != 1) {
			if (in_array(strtolower($words[count($words) - 1]), $stopWords)) {
				for ($i = 0; $i < count($words) - 1; $i ++) {
					$newLabel .= $words[$i] . " ";
				}
				$newLabel = trim($newLabel);
				return $newLabel;
			}
			else {
				return $label;
			}
		}
		else {
			return $label;
		}
		
	}

	public function searchForLastWordLength($label) {
		$newLabel = "";
		$words = explode(" ", $label);
		if (count($words) != 1) {
			if (strlen($words[count($words) - 1]) == 1) {
				for ($i = 0; $i < count($words) - 1; $i ++) {
					$newLabel .= $words[$i] . " ";
				}
				$newLabel = trim($newLabel);
				return $newLabel;
			}
			else {
				return $label;
			}
		}
		else {
			return $label;
		}
		
	}

	public function searchAbbrWords($label) {
		$words = explode(" ", $label);
		if (count($words) != 1) {
			return false;
		}
		else {
			$pattern1 = "/(?:[A-Z]+\.)+/";
			$pattern2 = "/(?:[A-Za-z]+\.)+/";
			$pattern3 = "/^[0-5][0-9]:[0-5][0-9]$/";
			preg_match($pattern1 , $label, $matches1);
			preg_match($pattern2 , $label, $matches2);
			preg_match($pattern3 , $label, $matches3);

			if (empty($matches1)) {
				if (empty($matches2)) {
					if (empty($matches3)) {
						return false;
					}
					else {
						return true;
					}
				}
				else {
					return true;
				}
			}
			else {
				return true;
			}
		}
	}

	public function getDutchWikipediaLinkFromDutchResource($dutchResource) {
		$result = null;
		$db = sparql_connect('http://nl.dbpedia.org/sparql');
		$query = "select ?wikipedia where {<$dutchResource> foaf:isPrimaryTopicOf ?wikipedia}";
	//	print_r($query);
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);
			$row = sparql_fetch_array($resultQuery);
			if ($row != null) {
				$result = $row["wikipedia"];
			}
		}
		return $result;
	}

	public function getEnglishWikipediaLinkFromEnglishResource($englishResource) {
		$result = null;
		$db = sparql_connect('http://dbpedia.org/sparql');
		$query = "select ?wikipedia where {<$englishResource> foaf:isPrimaryTopicOf ?wikipedia}";
	//	print_r($query);
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);
			$row = sparql_fetch_array($resultQuery);
			if ($row != null) {
				$result = $row["wikipedia"];
			}
		}
		return $result;
	}

	public function getEnglishResourceFromDutchResource($dutchResource) {
		$result = null;
		$db = sparql_connect('http://nl.dbpedia.org/sparql');
		$query = "select ?resource where {<$dutchResource> owl:sameAs ?resource . 
filter (regex (str(?resource), \"http://dbpedia\", \"i\") ) .}";
//print_r($query);
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);
			$row = sparql_fetch_array($resultQuery);
		//	dd($resultQuery);
			if ($row != null) {
				$result = $row["resource"];
			}
		}
		return $result;
	}

	public function getDutchResourceFromEnglishResource($englishResource) {
		$result = null;
	//	dd($englishResource);
		$db = sparql_connect('http://dbpedia.org/sparql');
		$query = "select ?resource where {<".$englishResource."> owl:sameAs ?resource . 
filter (regex (str(?resource), \"http://nl.dbpedia\", \"i\") ) .}";
	//	dd($query);
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);
			$row = sparql_fetch_array($resultQuery);
			if ($row != null) {
				$result = $row["resource"];
			}
		}
		return $result;
	}

	public function getRDFTypesFromDutchResource($dutchResource) {
		$result = array();
		$db = sparql_connect('http://nl.dbpedia.org/sparql');
		$query = "select * where { <$dutchResource> rdf:type ?types .
				filter ( regex (str(?types), \"http://dbpedia.org/ontology/\", \"i\") ).
				filter ( regex(str(?types), \"^(?!http://dbpedia.org/ontology/Wikidata).+\", \"i\")) .}";
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);

			while($row = sparql_fetch_array($resultQuery)) {
				
				foreach($fields as $field) {
	 				if ($field == "types") {
	  					$typeArray = explode("/", (string)$row[$field]);
	  					array_push($result, $typeArray[count($typeArray) - 1]);
					}
				}
			}
		}
		return $result;
	}

	public function getRDFTypesFromEnglishResource($englishResource) {
		$result = array();
		$db = sparql_connect('http://dbpedia.org/sparql');
		$query = "select * where { <$englishResource> rdf:type ?types .
				filter ( regex (str(?types), \"http://dbpedia.org/ontology/\", \"i\") ).
				filter ( regex(str(?types), \"^(?!http://dbpedia.org/ontology/Wikidata).+\", \"i\")) .}";
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);

			while($row = sparql_fetch_array($resultQuery)) {
				
				foreach($fields as $field) {
	 				if ($field == "types") {
	  					$typeArray = explode("/", (string)$row[$field]);
	  					array_push($result, $typeArray[count($typeArray) - 1]);
					}
				}
			}
		}
		return $result;
	}

	public function getDutchResourceFromDutchWikipediaLink($dutchWikipediaLink) {
		$result = null;
		$db = sparql_connect('http://nl.dbpedia.org/sparql');
		$query = "select * where { ?resource foaf:isPrimaryTopicOf <$dutchWikipediaLink> .}";
	//	print_r($query);
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);
			$row = sparql_fetch_array($resultQuery);
			if ($row != null) {
				$result = $row["resource"];
			}
		}
		return $result;
	}
	
	public function getEnglishResourceFromEnglishWikipediaLink($englishWikipediaLink) {
		$result = null;
		$db = sparql_connect('http://dbpedia.org/sparql');
		$query = "select * where { ?resource foaf:isPrimaryTopicOf <$englishWikipediaLink> .}";
	//	print_r($query);
		$resultQuery = sparql_query($query);
		if ($resultQuery) {
			$fields = sparql_field_array($resultQuery);
			$row = sparql_fetch_array($resultQuery);
			if ($row != null) {
				$result = $row["resource"];
			}
		}
		return $result;
	}

	public function processTDHApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();

	//	dd("here");

		$descriptionContent = urlencode($entity->content["description"]);
		$initialDescription = utf8_decode($entity->content["description"]);
	//	print_r($descriptionContent);
		$lang = $entity->language;
		$format = "json";
		$entity_type = array("ne", "ce");
		$priority_entity_linking = "true";
		$apikey = "150284785c5a49709990f973ec825d1e";
		$result = array();
		$result["intermediateEntities"] = array();
		$result["initialEntities"] = array();
		$result["cleanedUpEntities"] = array();

		for ($i = 0; $i < count($entity_type); $i ++) {
			$output = "";
			$urlReq = $this->urlTDH . "apikey=". $apikey . "&format=" . $format . "&lang=" . $lang ."&priority_entity_linking=" . $priority_entity_linking . "&entity_type=" . $entity_type[$i]; // . "&provenance=dbpedia";
			$curlRequest = "curl -v \"" . $urlReq . "\" -d \"" . $descriptionContent . "\"";
		//	dd($curlRequest);
			$response = exec($curlRequest, $output);
		//	dd($output);
			$responseEntities = json_decode($output[0], true);	
		
			
			for ($j = 0; $j < count($responseEntities); $j++) {
			//	dd($responseEntities);
				$entity = array();
				$initialEntity = array();
			//	dd(utf8_decode($responseEntities[$j]["underlyingString"]));

				$entity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $responseEntities[$j]["underlyingString"]);
				$entity["startOffset"] = $responseEntities[$j]["startOffset"];
				$entity["endOffset"] = $responseEntities[$j]["endOffset"]; 
				$entity["timestamp"] = "00:00"; 
				$entity["description"] = ""; 

				if (strtolower(substr($initialDescription, (int)$entity["startOffset"], strlen($entity["label"]))) != utf8_decode(strtolower($entity["label"]))) {
					continue;
				}
				$entity["confidence"] = null;
				$entity["provenance"] = "thd";
				$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $responseEntities[$j]["underlyingString"]);
				$initialEntity["startOffset"] = $responseEntities[$j]["startOffset"];
				$initialEntity["endOffset"] = $responseEntities[$j]["endOffset"];
				$initialEntity["timestamp"] = "00:00"; 
				$initialEntity["description"] = ""; 

				$initialEntity["confidence"] = null;
				$initialEntity["provenance"] = "thd";
				$entity["types"] = array();
				$initialEntity["types"] = array();

				for ($k = 0; $k < count($responseEntities[$j]["types"]); $k++) {
					
					if ((strpos($responseEntities[$j]["types"][$k]["typeLabel"], "dbpedia.org/resource/") !== false)) {
						continue;
					}
					$initialType = array();
					$initialType["typeURI"] = null;

					if ($responseEntities[$j]["types"][$k]["provenance"] == "yago") {
						$initialType["typeURI"] = str_replace(" ", "", $responseEntities[$j]["types"][$k]["typeLabel"]);
						$initialType["typeURI"] = "YAGO::" . $initialType["typeURI"];
					}
					else if (strpos($responseEntities[$j]["types"][$k]["typeURI"], "dbpedia") !== false) {
						$initialType["typeURI"] = str_replace(" ", "", $responseEntities[$j]["types"][$k]["typeLabel"]);
						$initialType["typeURI"] = "DBpedia::" . $initialType["typeURI"];
						$type = array();
						$type["typeURI"] = $initialType["typeURI"];
						$type["wikiURI"] = array();
						$type["wikiURI"]["en"] = null;
						$type["wikiURI"]["nl"] = null;
						$type["entityURI"] = null;
						$type["confidence"] = array();
						$type["confidence"]["score"] = $responseEntities[$j]["types"][$k]["salience"]["confidence"];
					}
					else {
						$initialType["typeURI"] = null;
						$type = array();
						$type["typeURI"] = null;
						$type["wikiURI"] = array();
						$type["wikiURI"]["en"] = null;
						$type["wikiURI"]["nl"] = null;
						$type["entityURI"] = null;
						$type["confidence"] = array();
						$type["confidence"]["score"] = $responseEntities[$j]["types"][$k]["salience"]["confidence"];	
					}

					$initialType["wikiURI"] = array();
					$initialType["wikiURI"]["en"] = null;
					$initialType["wikiURI"]["nl"] = null;

				//	if (strpos((string)$responseEntities[$j]["types"][$k]["entityURI"], "/d" ))
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', (string)$responseEntities[$j]["types"][$k]["entityURI"]); 

					if ($responseEntities[$j]["types"][$k]["provenance"] == "yago") {
					}
					else  {
						if (strpos($responseEntities[$j]["types"][$k]["entityURI"], 'nl.dbpedia.org') !== false) {
							$type["entityURI"] = urldecode(iconv('UTF-8', 'UTF-8//IGNORE', (string)$responseEntities[$j]["types"][$k]["entityURI"])); 
							$englishEntityResource = $this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"]));
							$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource(utf8_encode($englishEntityResource));					
	  						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
						}
						else {
							$englishEntityResource = urldecode((string)$responseEntities[$j]["types"][$k]["entityURI"]);
							$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromEnglishResource($englishEntityResource));
	  						$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($englishEntityResource);
	  						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
						}
					}
					
					$initialType["confidence"] = array();
					$initialType["confidence"]["score"] = $responseEntities[$j]["types"][$k]["salience"]["confidence"];

					if (isset($type))
					foreach ($entity["types"] as $key => $value) {
						if (strtolower($value["typeURI"]) == strtolower($type["typeURI"]) && $value["entityURI"] == $type["entityURI"] && 
							$value["wikiURI"]["en"] == $type["wikiURI"]["en"] && $value["wikiURI"]["nl"] == $type["wikiURI"]["nl"]) {
							if ($type["confidence"]["score"] != null && $value["confidence"]["score"] != null) {
								$type["confidence"]["score"] = max($type["confidence"]["score"], $value["confidence"]["score"]);
							}
							else {
								if ($value["confidence"]["score"] != null) {
									$type["confidence"]["score"] = $value["confidence"]["score"];
								}
							}							
							unset($entity["types"][$key]);
						}
					}

					foreach ($initialEntity["types"] as $key => $value) {
						if (strtolower($value["typeURI"]) == strtolower($initialType["typeURI"]) && $value["entityURI"] == $initialType["entityURI"] && 
							$value["wikiURI"]["en"] == $initialType["wikiURI"]["en"] && $value["wikiURI"]["nl"] == $initialType["wikiURI"]["nl"]) {
							if ($initialType["confidence"]["score"] != null && $value["confidence"]["score"] != null) {
								$initialType["confidence"]["score"] = max($initialType["confidence"]["score"], $value["confidence"]["score"]);
							}
							else {
								if ($value["confidence"]["score"] != null) {
									$initialType["confidence"]["score"] = $value["confidence"]["score"];
								}
							}							
							unset($initialEntity["types"][$key]);
						}
					}

					if (isset($type))
						array_push($entity["types"], $type);
					array_push($initialEntity["types"], $initialType);

					$entity["types"] = array_map("unserialize", array_unique(array_map("serialize", $entity["types"])));
					$initialEntity["types"] = array_map("unserialize", array_unique(array_map("serialize", $initialEntity["types"])));
				}

				if (count($responseEntities[$j]["types"]) == 0) {
					$type = array();
					$type["typeURI"] = null;
					$type["entityURI"] = null;
					$type["wikiURI"] = array();
					$type["wikiURI"]["nl"] = null;
					$type["wikiURI"]["en"] = null;
					$type["confidence"] = array();
					$type["confidence"]["score"] = null;
					$type["confidence"]["bounds"] = null;

					array_push($entity["types"], $type);
					array_push($initialEntity["types"], $type);
				}

				array_push($result["intermediateEntities"], $entity);
				array_push($result["initialEntities"], $initialEntity);

				if ($this->searchForStopWords($entity["label"]) == false) {
					if (strtolower($this->searchForStopWordsEndLabel($entity["label"])) == strtolower($entity["label"])) {
						if ($this->searchAbbrWords($entity["label"]) == false) {
							if (strtolower($this->searchForLastWordLength($entity["label"])) == strtolower($entity["label"])) {
								if (strlen($entity["label"]) > 2) {
									if (substr($entity["label"], -1) == "-") {
										$cleanedUpEntity = array();
										$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
										$cleanedUpEntity["startOffset"] = $entity["startOffset"];
										$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
										$cleanedUpEntity["confidence"] = null;
										$cleanedUpEntity["provenance"] = "thd";
										$cleanedUpEntity["types"] = $entity["types"];
										array_push($result["cleanedUpEntities"], $cleanedUpEntity);
									}
									else {
										array_push($result["cleanedUpEntities"], $entity);
									}
								}
								else {
									if(strtoupper($entity["label"]) == $entity["label"]) {
										array_push($result["cleanedUpEntities"], $entity);
									}
								}
							}
							else {
								if (substr($entity["label"], -1) == "-") {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
								else {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = utf8_decode($this->searchForLastWordLength($entity["label"]));
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
							}

						}
					}
					else {
						$cleanedUpEntity = array();
						$cleanedUpEntity["label"] = utf8_decode($this->searchForStopWordsEndLabel($entity["label"]));
						$cleanedUpEntity["startOffset"] = $entity["startOffset"];
						$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 

						$cleanedUpEntity["confidence"] = null;
						$cleanedUpEntity["provenance"] = "thd";

						$cleanedUpEntity["types"] = $entity["types"];

						array_push($result["cleanedUpEntities"], $cleanedUpEntity);
					}
				}
			}
			$array["intermediateEntities"] = array_map("unserialize", array_unique(array_map("serialize", $result["intermediateEntities"])));
			$array["initialEntities"] = array_map("unserialize", array_unique(array_map("serialize", $result["initialEntities"])));
			$array["cleanedUpEntities"] = array_map("unserialize", array_unique(array_map("serialize", $result["cleanedUpEntities"])));
		}
	//	dd($array);
		return $array;	
	}

	public function processAutomatedEventExtraction($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		//dd("h");
		$reqURL = "http://text-processing.com/api/tag/";
		$descriptionContent = $entity->content["description"];
		$initialDescription = utf8_decode($entity->content["description"]);
		
		$lang = $entity->language;
		$language = "dutch";
		$result = array();
		$result["automatedEvents"] = array();

		$curlRequest = "curl -d \"language=" . $language . "&text=" . $initialDescription . "&output=tagged\" " . $reqURL;
		$response = shell_exec($curlRequest);
		$response = json_decode($response, true);

	//	dd($response);
		if (isset($response["text"])) {
			$wordsAndTags = explode(" ", $response["text"]);
			$cursor = 0;
			foreach($wordsAndTags as $tags) {
				$taggingArray = explode("/", $tags);

				if (!isset($taggingArray[1])) {
				}
				else if ($taggingArray[1] == "V") {
					$event = array();
					$event["label"] = $taggingArray[0];
					$event["startOffset"] = (int)strpos($initialDescription, $event["label"], $cursor);
					$event["endOffset"] = (int)$event["startOffset"] + strlen($event["label"]);
					$cursor = (int)$event["endOffset"];

					array_push($result["automatedEvents"], $event);
				}
			}
		}
		return $result;
	}

	public function processLupediaApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = $entity->content["description"];
		$initialDescription = utf8_decode($entity->content["description"]);
		$lang = $entity->language;
		$parameters = array("lookupText" => utf8_decode($descriptionContent), "lang" => $lang, "threshold" => "0.70", "dataset" => "all", 
			"skip_sh3" => "true", "skip_stp" => "true", "keep_fnl" => "true", "skip_ldata" => "false", "single_match" => "false",
			"keep_highest" => "true", "keep_specific" => "true", "case_sensitive" => "true", "graph" => "false");
		$urlLupedia = "http://lupedia.ontotext.com/lookup/text2html";
		$result = array();
		$result["intermediateEntities"] = array();
		$result["initialEntities"] = array();
		$result["cleanedUpEntities"] = array();
		$req = "curl -X POST \"http://lupedia.ontotext.com/lookup/text2json?lookupText=" . urlencode(utf8_decode($descriptionContent)) . "&lang=$lang&keep_specific=false\"";
		$response = shell_exec($req);
		$response = json_decode($response, true);
	//	echo $initialDescription;	
	//	dd($response);
		for ($i = 0; $i < count($response); $i ++) {
			$found = false;
			$entity = array();
			$initialEntity = array();
			$entity["startOffset"] = $response[$i]["startOffset"];
			$initialEntity["startOffset"] = $response[$i]["startOffset"];
			$entity["endOffset"] = $response[$i]["endOffset"];
			$initialEntity["endOffset"] = $response[$i]["endOffset"];
		//	echo substr($initialDescription, (int)$entity["startOffset"], (int)$entity["endOffset"] - (int)$entity["startOffset"]);

			$entity["label"] = utf8_encode(substr($initialDescription, (int)$entity["startOffset"], (int)$entity["endOffset"] - (int)$entity["startOffset"]));
			$initialEntity["label"] = utf8_encode(substr($initialDescription, (int)$entity["startOffset"], (int)$entity["endOffset"] - (int)$entity["startOffset"]));
		
			$entity["timestamp"] = "00:00"; 
			$entity["description"] = ""; 
			$initialEntity["timestamp"] = "00:00"; 
			$initialEntity["description"] = ""; 
			$entity["confidence"] = null;
			$initialEntity["confidence"] = null;
			$entity["provenance"] = "lupedia";
			$initialEntity["provenance"] = "lupedia";
			$entity["types"] = array();
			$initialEntity["types"] = array();

			foreach ($result["initialEntities"] as $key => $value) {
				if ($value["label"] == $entity["label"] && $value["endOffset"] == $entity["endOffset"] && $value["startOffset"] == $entity["startOffset"]) {
					$type = array();
					$initialType = array();
					$typeArray = explode("/", $response[$i]["instanceClass"]);
					$type["typeURI"] = null;
					$initialType["typeURI"] = null;
					if (isset($typeArray)) {
						$type["typeURI"] =  str_replace(" ", "", "DBpedia::" . $typeArray[count($typeArray) - 1]);
						$initialType["typeURI"] =  str_replace(" ", "", "DBpedia::" . $typeArray[count($typeArray) - 1]);
					}
					
					$type["entityURI"] = null;
					$type["wikiURI"] = array();
					$type["wikiURI"]["en"] = null;
					$type["wikiURI"]["nl"] = null;
					$initialType["entityURI"] = null;
					$initialType["wikiURI"] = array();
					$initialType["wikiURI"]["en"] = null;
					$initialType["wikiURI"]["nl"] = null;
					$initialType["entityURI"] = urlencode(iconv('UTF-8', 'UTF-8//IGNORE', $response[$i]["instanceUri"]));

					if ((strpos($response[$i]["instanceUri"], 'nl.dbpedia.org') !== false)) {
						$type["entityURI"] = urldecode(iconv('UTF-8', 'UTF-8//IGNORE', $response[$i]["instanceUri"]));
						$englishEntityResource = urldecode($this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"])));
						$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($englishEntityResource);
						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
					}
					else {
						$englishEntityResource = urldecode((string)$response[$i]["instanceUri"]);
						$type["entityURI"] = urldecode(iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromEnglishResource($englishEntityResource)));
						$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($englishEntityResource);
						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
					}
					$initialType["confidence"]["score"] = $response[$i]["weight"];
					$initialType["confidence"]["bounds"] = null;
					$type["confidence"]["score"] = $response[$i]["weight"];
					$type["confidence"]["bounds"] = null;

					array_push($value["types"], $type);
					unset($result["intermediateEntities"][$key]["types"]);
					$result["intermediateEntities"][$key]["types"] = $value["types"];

					array_push($initialEntity["types"], $initialType);
					$found = true;
					break;
				}
			}
			if ($found == false) {
				$entity["types"] = array();
				$initialEntity["types"] = array();
				$typeArray = explode("/", $response[$i]["instanceClass"]);
				$type["typeURI"] =  str_replace(" ", "", "DBpedia::" . $typeArray[count($typeArray) - 1]);
				$type["wikiURI"] = array();
				$type["wikiURI"]["en"] = null;
				$type["wikiURI"]["nl"] = null;
				$initialType["typeURI"] =  str_replace(" ", "", "DBpedia::" . $typeArray[count($typeArray) - 1]);
				$initialType["entityURI"] =  urldecode(iconv('UTF-8', 'UTF-8//IGNORE', $response[$i]["instanceUri"]));
				$initialType["wikiURI"] = array();
				$initialType["wikiURI"]["en"] = null;
				$initialType["wikiURI"]["nl"] = null;
				if ((strpos($response[$i]["instanceUri"], 'nl.dbpedia.org') !== false)) {
					$type["entityURI"] = urldecode(iconv('UTF-8', 'UTF-8//IGNORE', $response[$i]["instanceUri"]));
					$englishEntityResource = urldecode($this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"])));
					$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($englishEntityResource);
					$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource($type["entityURI"]));
				}
				else {
					$englishEntityResource = urldecode(utf8_decode((string)$response[$i]["instanceUri"]));
					$type["entityURI"] = urldecode(iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromEnglishResource($englishEntityResource)));
					$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($englishEntityResource);
					$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
				}
				$type["confidence"]["score"] = $response[$i]["weight"];
				$type["confidence"]["bounds"] = null;
				$initialType["confidence"]["score"] = $response[$i]["weight"];
				$initialType["confidence"]["bounds"] = null;
				array_push($entity["types"], $type);
				array_push($result["intermediateEntities"], $entity);

				array_push($initialEntity["types"], $initialType);
				array_push($result["initialEntities"], $initialEntity);

				if ($this->searchForStopWords($entity["label"]) == false) {
				if (strtolower($this->searchForStopWordsEndLabel($initialEntity["label"])) == strtolower($entity["label"])) {
					if ($this->searchAbbrWords($entity["label"]) == false) {
						if (strtolower($this->searchForLastWordLength($initialEntity["label"])) == strtolower($entity["label"])) {
							if (strlen($entity["label"]) > 2) {
								if (substr($entity["label"], -1) == "-") {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
								else {
									array_push($result["cleanedUpEntities"], $entity);
								}
							}
							else {
								if(strtoupper($entity["label"]) == $entity["label"]) {
									array_push($result["cleanedUpEntities"], $entity);
								}
							}
						}
						else {
							if (substr($entity["label"], -1) == "-") {
								$cleanedUpEntity = array();
								$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
								$cleanedUpEntity["startOffset"] = $entity["startOffset"];
								$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
								$cleanedUpEntity["confidence"] = null;
								$cleanedUpEntity["provenance"] = "thd";
								$cleanedUpEntity["types"] = $entity["types"];
								array_push($result["cleanedUpEntities"], $cleanedUpEntity);
							}
							else {
								$cleanedUpEntity = array();
								$cleanedUpEntity["label"] = utf8_decode($this->searchForLastWordLength($entity["label"]));
								$cleanedUpEntity["startOffset"] = $entity["startOffset"];
								$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
								$cleanedUpEntity["confidence"] = null;
								$cleanedUpEntity["provenance"] = "thd";
								$cleanedUpEntity["types"] = $entity["types"];
								array_push($result["cleanedUpEntities"], $cleanedUpEntity);
							}
						}
					}
				}
				else {
					$cleanedUpEntity = array();
					$cleanedUpEntity["label"] = utf8_decode($this->searchForStopWordsEndLabel($entity["label"]));
					$cleanedUpEntity["startOffset"] = $entity["startOffset"];
					$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
					$cleanedUpEntity["confidence"] = null;
					$cleanedUpEntity["provenance"] = "thd";
					$cleanedUpEntity["types"] = $entity["types"];
					array_push($result["cleanedUpEntities"], $cleanedUpEntity);
				}
			}
			}
		}
	//	dd($result);
		return $result;	
	}

	public function processTextRazorApi($entity) {
		require_once('TextRazor.php');
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$call_type = array("entities");
		$text = $entity->content["description"];
		$initialDescription = utf8_decode($text);
		$textrazor = new TextRazor("e6438f10fc2f974ff0a5b97969a4296fe637da043237aba49569bd58");
		$textrazor->addExtractor('entities,topics,words,phrases,dependency-trees,relations,entailments,senses');
		$response = $textrazor->analyze($text);
		$result = array();
		// the confidence score needs to be normalized 0 .. 1
		$initialMin = 0.5;
		$initialMax = 10;
	//	dd($response['response']);
		if (isset($response['response']['entities'])) {
    		$result["intermediateEntities"] = array();
    		$entities = array();
    		$result["initialEntities"] = array();
    		$result["cleanedUpEntities"] = array();
    		$initialEntities = array();
			for ($i = 0; $i < count($response['response']['entities']); $i ++) {
				$entity = array();
				$entity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["matchedText"]);
				$entity["startOffset"] = $response['response']['entities'][$i]["startingPos"];
				$entity["endOffset"] = $response['response']['entities'][$i]["endingPos"];
				$entity["confidence"] = 0 + ($response['response']['entities'][$i]["confidenceScore"] - $initialMin) * 1 / ($initialMax - $initialMin);
				$entity["provenance"] = "textrazor";
				$entity["timestamp"] = "00:00"; 
				$entity["description"] = ""; 
				$entity["types"] = array();	

				if (strtolower(substr($initialDescription, (int)$entity["startOffset"], strlen($entity["label"]))) != utf8_decode(strtolower($entity["label"]))) {
					continue;
				}

				$initialEntity = array();
				$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["matchedText"]);
				$initialEntity["startOffset"] = $response['response']['entities'][$i]["startingPos"];
				$initialEntity["endOffset"] = $response['response']['entities'][$i]["endingPos"];
				$initialEntity["confidence"] = 0 + ($response['response']['entities'][$i]["confidenceScore"] - $initialMin) * 1 / ($initialMax - $initialMin);
				$initialEntity["provenance"] = "textrazor";
				$initialEntity["timestamp"] = "00:00"; 
				$initialEntity["description"] = ""; 
				$initialEntity["types"] = array();	

				if (isset($response['response']['entities'][$i]["type"])) {
					for ($k = 0; $k < count($response['response']['entities'][$i]["type"]); $k++) {
						$type = array();
						$type["typeURI"] = str_replace(" ", "", "DBpedia::" . $response['response']['entities'][$i]["type"][$k]);

						$initialType = array();
						$initialType["typeURI"] = str_replace(" ", "", "DBpedia::" . $response['response']['entities'][$i]["type"][$k]);
						$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["wikiLink"]);
						$initialType["wikiURI"] = array();
						$initialType["wikiURI"]["nl"] = null;
						$initialType["wikiURI"]["en"] = null;

						if ($response['response']['entities'][$i]["wikiLink"] == "" || $response['response']['entities'][$i]["wikiLink"] == null) {
							$type["entityURI"] = null;
							$type["wikiURI"] = array();
							$type["wikiURI"]["nl"] = null;
						
							if ($response['response']['entities'][$i]["entityEnglishId"] == null || $response['response']['entities'][$i]["entityEnglishId"] == "") {
								$type["wikiURI"]["en"] = null;
							}
							else {
								$type["wikiURI"]["en"] = "http://en.wikipedia.org/wiki/" . $response['response']['entities'][$i]["entityEnglishId"];
							}
						}
						else {
							if (strpos($response['response']['entities'][$i]["wikiLink"], "nl.wikipedia") !== false) {
								$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromDutchWikipediaLink(utf8_encode($response['response']['entities'][$i]["wikiLink"])));
								$type["wikiURI"] = array();
								$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["wikiLink"]);
								if ($response['response']['entities'][$i]["wikiLink"] == null || $response['response']['entities'][$i]["wikiLink"] == "") {
									$type["entityURI"] = null;
									$type["wikiURI"]["nl"] = null;
								}
								if ($response['response']['entities'][$i]["entityEnglishId"] == null || $response['response']['entities'][$i]["entityEnglishId"] == "") {
									$type["wikiURI"]["en"] = null;
								}
								else {
									$type["wikiURI"]["en"] = "http://en.wikipedia.org/wiki/" . $response['response']['entities'][$i]["entityEnglishId"];
								}
							}
							else {
								$englishEntityResource = $this->getEnglishResourceFromEnglishWikipediaLink($response['response']['entities'][$i]["wikiLink"]);
								$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromEnglishResource($englishEntityResource));
								$type["wikiURI"] = array();
								$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
								$type["wikiURI"]["en"] = $response['response']['entities'][$i]["wikiLink"];
							}
						}
						$type["confidence"]["score"] = null;
						$type["confidence"]["bounds"] = null;
						$initialType["confidence"]["score"] = null;
						$initialType["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);
						array_push($initialEntity["types"], $initialType);
					}
				}
				
				if (isset($response['response']['entities'][$i]["freebaseTypes"])) {
					for ($k = 0; $k < count($response['response']['entities'][$i]["freebaseTypes"]); $k++) {
						$initialType = array();
						$initialType["typeURI"] = str_replace(" ", "", "Freebase::" . $response['response']['entities'][$i]["freebaseTypes"][$k]);	

						if (isset($response['response']['entities'][$i]["freebaseId"]))	{			
							$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', "http://www.freebase.com" . $response['response']['entities'][$i]["freebaseId"]);
						
						}
						else {
							$initialType["entityURI"] = null;
						}
						$initialType["wikiURI"] = array();
						$initialType["wikiURI"]["en"] = null;
						$initialType["wikiURI"]["nl"] = null;

						if (strpos($response['response']['entities'][$i]["wikiLink"], "nl.wikipedia") !== false) {
							$type = array();
							$type["typeURI"] = null;
							$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromDutchWikipediaLink(utf8_encode($response['response']['entities'][$i]["wikiLink"])));
							$type["wikiURI"] = array();
							$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["wikiLink"]);
							if ($response['response']['entities'][$i]["entityEnglishId"] == null || $response['response']['entities'][$i]["entityEnglishId"] == "") {
								$type["wikiURI"]["en"] = null;
							}
							else {
								$type["wikiURI"]["en"] = "http://en.wikipedia.org/wiki/" . $response['response']['entities'][$i]["entityEnglishId"];
							}
							$type["confidence"]["score"] = null;
							$type["confidence"]["bounds"] = null;
							array_push($entity["types"], $type);
							$entity["types"] = array_map("unserialize", array_unique(array_map("serialize", $entity["types"])));
						}
						if (strpos($response['response']['entities'][$i]["wikiLink"], "en.wikipedia") !== false) {
							$type = array();
							$type["typeURI"] = null;
							$englishResource = $this->getEnglishResourceFromEnglishWikipediaLink($response['response']['entities'][$i]["wikiLink"]);
							$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromEnglishResource($englishResource));
							$type["wikiURI"] = array();
							$type["wikiURI"]["en"] = utf8_decode($response['response']['entities'][$i]["wikiLink"]);
							$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
							$type["confidence"]["score"] = null;
							$type["confidence"]["bounds"] = null;

							array_push($entity["types"], $type);

							$entity["types"] = array_map("unserialize", array_unique(array_map("serialize", $entity["types"])));
		
						}

						$initialType["confidence"]["score"] = null;
						$initialType["confidence"]["bounds"] = null;
						array_push($initialEntity["types"], $initialType);
					}
				}
				if(!isset($response['response']['entities'][$i]["freebaseTypes"]) && !isset($response['response']['entities'][$i]["type"])) {
					$type = array();
					$type["typeURI"] = null;

					$initialType = array();
					$initialType["typeURI"] = null;
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["wikiLink"]);
					$initialType["wikiURI"] = array();
					$initialType["wikiURI"]["en"] = null;
					$initialType["wikiURI"]["nl"] = null;

					if ($response['response']['entities'][$i]["wikiLink"] == "" || $response['response']['entities'][$i]["wikiLink"] == null) {
						$type["entityURI"] = null;
						$type["wikiURI"] = array();
						$type["wikiURI"]["nl"] = null;
						
						if ($response['response']['entities'][$i]["entityEnglishId"] == null || $response['response']['entities'][$i]["entityEnglishId"] == "") {
							$type["wikiURI"]["en"] = null;
						}
						else {
							$type["wikiURI"]["en"] = "http://en.wikipedia.org/wiki/" . $response['response']['entities'][$i]["entityEnglishId"];
						}
					}
					else {
						if (strpos($response['response']['entities'][$i]["wikiLink"], "nl.wikipedia") !== false) {
							$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromDutchWikipediaLink(utf8_encode($response['response']['entities'][$i]["wikiLink"])));
							$type["wikiURI"] = array();
							$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($response['response']['entities'][$i]["wikiLink"]));
							if ($response['response']['entities'][$i]["wikiLink"] == null || $response['response']['entities'][$i]["wikiLink"] == "") {
								$type["entityURI"] = null;
								$type["wikiURI"]["nl"] = null;
							}
							if ($response['response']['entities'][$i]["entityEnglishId"] == null || $response['response']['entities'][$i]["entityEnglishId"] == "") {
								$type["wikiURI"]["en"] = null;
							}
							else {
								$type["wikiURI"]["en"] = "http://en.wikipedia.org/wiki/" . $response['response']['entities'][$i]["entityEnglishId"];
							}
						}
						else {
							$englishEntityResource = $this->getEnglishResourceFromEnglishWikipediaLink($response['response']['entities'][$i]["wikiLink"]);
							$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromEnglishResource($englishEntityResource));
							$type["wikiURI"] = array();
							$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
							$type["wikiURI"]["en"] = $response['response']['entities'][$i]["wikiLink"];
						}
					}

					$type["confidence"]["score"] = null;
					$type["confidence"]["bounds"] = null;
					array_push($entity["types"], $type);
					$entity["types"] = array_map("unserialize", array_unique(array_map("serialize", $entity["types"])));

					$initialType["confidence"]["score"] = null;
					$initialType["confidence"]["bounds"] = null;
					array_push($initialEntity["types"], $initialType);
				}

				if ($this->searchForStopWords($entity["label"]) == false) {
					if (strtolower($this->searchForStopWordsEndLabel($entity["label"])) == strtolower($entity["label"])) {
						if ($this->searchAbbrWords($entity["label"]) == false) {
							if (strtolower($this->searchForLastWordLength($entity["label"])) == strtolower($entity["label"])) {
								if (strlen($entity["label"]) > 2) {
									if (substr($entity["label"], -1) == "-") {
										$cleanedUpEntity = array();
										$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
										$cleanedUpEntity["startOffset"] = $entity["startOffset"];
										$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
										$cleanedUpEntity["confidence"] = null;
										$cleanedUpEntity["provenance"] = "thd";
										$cleanedUpEntity["types"] = $entity["types"];
										array_push($result["cleanedUpEntities"], $cleanedUpEntity);
									}
									else {
										array_push($result["cleanedUpEntities"], $entity);
									}
								}
								else {
									if(strtoupper($entity["label"]) == $entity["label"]) {
										array_push($result["cleanedUpEntities"], $entity);
									}
								}
							}
							else {
								if (substr($entity["label"], -1) == "-") {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
								else {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = utf8_decode($this->searchForLastWordLength($entity["label"]));
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
							}

						}
					}
					else {
						$cleanedUpEntity = array();
						$cleanedUpEntity["label"] = utf8_decode($this->searchForStopWordsEndLabel($entity["label"]));
						$cleanedUpEntity["startOffset"] = $entity["startOffset"];
						$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 

						$cleanedUpEntity["confidence"] = null;
						$cleanedUpEntity["provenance"] = "thd";

						$cleanedUpEntity["types"] = $entity["types"];

						array_push($result["cleanedUpEntities"], $cleanedUpEntity);
					}
				}

				array_push($result["intermediateEntities"], $entity);
				array_push($result["initialEntities"], $initialEntity);
			}
		}
		
		if (isset($response['response']['coarseTopics'])) {
			$result["topics"] = array();
			for ($i = 0; $i < count($response['response']['coarseTopics']); $i ++) {
				$topic = array();
				$topic["label"] = $response['response']['coarseTopics'][$i]["label"];
				$topic["wikiLink"] = $response['response']['coarseTopics'][$i]["wikiLink"];
				$topic["score"] = $response['response']['coarseTopics'][$i]["score"];				
				array_push($result["topics"], $topic);
			}
		}
	//	dd($result);
		return $result;	
	}

	public function processSemiTagsApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = urlencode($entity->content["description"]);
		$initialDescription = utf8_decode($entity->content["description"]);
		$lang = $entity->language;
		$result = array();
		$result["intermediateEntities"] = array();
		$result["initialEntities"] = array();
		$result["cleanedUpEntities"] = array();
		$entities = array();
		$curlRequest = "curl -d \"language=" . $lang . "&text=$descriptionContent\" http://ner.vse.cz/SemiTags/rest/v1/recognize";
		$response = exec($curlRequest, $output);
	//	dd($output);
		$xml = simplexml_load_string(urldecode($output[0]));
	//	dd($initialDescription);
	//	$descriptionContent = $entity->content["description"];
		if ($xml === false) {
    		die('Error parsing XML');   
		}
		else {	
			foreach ($xml->namedEntity as $rNode) {
				foreach ($rNode->occurrence as $occurrence) {
					$entity = array();
					$initialEntity = array();
					foreach ($occurrence->attributes() as $index => $value) {
						if ((string)$index == "start") {
							$entity["startOffset"] = (int)$value;
							$initialEntity["startOffset"] = (int)$value;
						}
						if ((string)$index == "end") {
							$entity["endOffset"] = (int)$value + 1;
							$initialEntity["endOffset"] = (int)$value + 1;
						}
					}
				
					$entity["label"] = substr($initialDescription, $entity["startOffset"], $entity["endOffset"] - $entity["startOffset"]);

					if ($entity["label"] != iconv('UTF-8', 'UTF-8//IGNORE', (string)$rNode->name)) {
						continue;
					}

					$initialEntity["label"] = substr($initialDescription, $entity["startOffset"], $entity["endOffset"] - $entity["startOffset"]);
					if ((string)$rNode->confidence != "") {
						$entity["confidence"] = floatval($rNode->confidence);
						$initialEntity["confidence"] = floatval($rNode->confidence);
					}
					else {
						$entity["confidence"] = null;
						$initialEntity["confidence"] = null;
					}
					$entity["provenance"] = "semitags";
					$initialEntity["provenance"] = "semitags";

					$entity["timestamp"] = "00:00"; 
					$entity["description"] = ""; 

					$initialEntity["timestamp"] = "00:00"; 
					$initialEntity["description"] = ""; 

					$entity["types"] = array();
					$initialEntity["types"] = array();
					
					$typeTemp = array();
					$initialTypeTemp = array();
					$typeTemp["wikiURI"] = array();
					$typeTemp["entityURI"] = null;
					$typeTemp["typeURI"] = null;
					$initialTypeTemp["wikiURI"] = array();
					$typeTemp["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', (string)$rNode->wikipediaUri);
					$typeTemp["wikiURI"]["en"] = null;
					$initialTypeTemp["wikiURI"]["nl"] = null;
					$initialTypeTemp["wikiURI"]["en"] = null;
					$initialTypeTemp["typeURI"] = "DBpedia::" . (string)$rNode->type;
					$initialTypeTemp["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', str_replace("de.dbpedia", "nl.dbpedia", utf8_encode((string)$rNode->dbpediaUri)));

					$initialTypeTemp["confidence"] = array();
	  				$initialTypeTemp["confidence"]["score"] = null;
					$initialTypeTemp["confidence"]["bounds"] = null;

					array_push($initialEntity["types"], $initialTypeTemp);
					$extractedTypes = array();
					if ((string)$rNode->dbpediaUri != "") {
						$typeTemp["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', str_replace("de.dbpedia", "nl.dbpedia", utf8_encode((string)$rNode->dbpediaUri)));
						$entityResource = iconv('UTF-8', 'UTF-8//IGNORE', $this->getEnglishResourceFromDutchResource(utf8_encode($typeTemp["entityURI"])));
						$typeTemp["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
						$extractedTypes = $this->getRDFTypesFromDutchResource(utf8_encode($typeTemp["entityURI"]));
					}
					else {
						$typeTemp["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromDutchWikipediaLink(utf8_encode($typeTemp["wikiURI"]["nl"])));
						$entityResource = $this->getEnglishResourceFromDutchResource(utf8_encode($typeTemp["entityURI"]));
						$typeTemp["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
						$extractedTypes = $this->getRDFTypesFromDutchResource($typeTemp["entityURI"]);
					}

					if (count($extractedTypes) > 0) {
						foreach($extractedTypes as $extractedType) {
							$type = array();
		  					$typeArray = explode("/", $extractedType);
		  					$type["typeURI"] = "DBpedia::" . $typeArray[count($typeArray) - 1];
		  					$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $typeTemp["entityURI"]);
		  					$type["wikiURI"]["en"] = $typeTemp["wikiURI"]["en"];
		  					$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', (string)$rNode->wikipediaUri);
		  					$type["confidence"]["score"] = floatval($rNode->confidence);
							$type["confidence"]["bounds"] = null;
							array_push($entity["types"], $type);
						}
					} else {
						$type = array();
	  					$type["typeURI"] = null;
	  					$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $typeTemp["entityURI"]);
	  					$type["wikiURI"]["en"] = $typeTemp["wikiURI"]["en"];
	  					$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', (string)$rNode->wikipediaUri);
	  					$type["confidence"]["score"] = floatval($rNode->confidence);
						$type["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);
					}

					if(sizeof($entity["types"]) == 0) {
						$type = array();
	  					$type["typeURI"] = null;
	  					$type["entityURI"] = null;
	  					$type["wikiURI"] = array();
	  					$type["wikiURI"]["en"] = null;
	  					$type["wikiURI"]["nl"] = null;
	  					$type["confidence"] = array();
	  					$type["confidence"]["score"] = null;
						$type["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);
					}

					if ($this->searchForStopWords($entity["label"]) == false) {
					if (strtolower($this->searchForStopWordsEndLabel($entity["label"])) == strtolower($entity["label"])) {
						if ($this->searchAbbrWords($entity["label"]) == false) {
							if (strtolower($this->searchForLastWordLength($entity["label"])) == strtolower($entity["label"])) {
								if (strlen($entity["label"]) > 2) {
									if (substr($entity["label"], -1) == "-") {
										$cleanedUpEntity = array();
										$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
										$cleanedUpEntity["startOffset"] = $entity["startOffset"];
										$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
										$cleanedUpEntity["confidence"] = null;
										$cleanedUpEntity["provenance"] = "thd";
										$cleanedUpEntity["types"] = $entity["types"];
										array_push($result["cleanedUpEntities"], $cleanedUpEntity);
									}
									else {
										array_push($result["cleanedUpEntities"], $entity);
									}
								}
								else {
									if(strtoupper($entity["label"]) == $entity["label"]) {
										array_push($result["cleanedUpEntities"], $entity);
									}
								}
							}
							else {
								if (substr($entity["label"], -1) == "-") {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
								else {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = utf8_decode($this->searchForLastWordLength($entity["label"]));
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
							}

						}
					}
					else {
						$cleanedUpEntity = array();
						$cleanedUpEntity["label"] = utf8_decode($this->searchForStopWordsEndLabel($entity["label"]));
						$cleanedUpEntity["startOffset"] = $entity["startOffset"];
						$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 

						$cleanedUpEntity["confidence"] = null;
						$cleanedUpEntity["provenance"] = "thd";

						$cleanedUpEntity["types"] = $entity["types"];

						array_push($result["cleanedUpEntities"], $cleanedUpEntity);
					}
				}
					array_push($result["intermediateEntities"], $entity);
					array_push($result["initialEntities"], $initialEntity);
				}				
			}
		}
	//	dd($result);
		return $result;	
	}

	public function processDBpediaSpotlightApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = $entity->content["description"];
		$initialDescription = utf8_decode($descriptionContent);
		$lang = $entity->language;
		$result = array();
		$result["intermediateEntities"] = array();
		$result["cleanedUpEntities"] = array();
		$result["initialEntities"] = array();
		$curlRequest = "curl -H \"Accept: application/json\"  http://nl.dbpedia.org/spotlight/rest/annotate --data-urlencode \"text=$descriptionContent\" --data \"confidence=0.2\"";
		$response = shell_exec($curlRequest);
		$response = json_decode($response, true);
	//	dd($response);
		if ($response != null)
		foreach ($response["Resources"] as $extractedEntity) {
			$entity = array();
			$initialEntity = array();
			$entity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@surfaceForm"]);
			$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@surfaceForm"]);
			$occ = 0;
			
			if((int)$extractedEntity["@offset"] != 0)
				$occ = substr_count($initialDescription, '"', 0, (int)$extractedEntity["@offset"] + strlen($extractedEntity["@surfaceForm"]) - 1);

			$entity["startOffset"] = (int)$extractedEntity["@offset"] + $occ;
			$initialEntity["startOffset"] = (int)$extractedEntity["@offset"]  + $occ;
			$entity["endOffset"] = (int)$extractedEntity["@offset"] + strlen(utf8_decode($extractedEntity["@surfaceForm"])) + $occ;
			$initialEntity["endOffset"] = (int)$extractedEntity["@offset"] + strlen(utf8_decode($extractedEntity["@surfaceForm"])) + $occ;
			if ($extractedEntity["@similarityScore"] != "") {
				$entity["confidence"] = (float)$extractedEntity["@similarityScore"];
				$initialEntity["confidence"] = (float)$extractedEntity["@similarityScore"];
			}
			else { 
				$entity["confidence"] = null;
				$initialEntity["confidence"] = null;
			}

			$entity["timestamp"] = "00:00"; 
			$entity["description"] = ""; 
			$initialEntity["timestamp"] = "00:00"; 
			$initialEntity["description"] = ""; 
			
			$entity["provenance"] = "dbpediaspotlight";
			$initialEntity["provenance"] = "dbpediaspotlight";
			$entity["types"] = array();
			$initialEntity["types"] = array();
			$i = 0;
			if (!empty($extractedEntity["@types"]) && $extractedEntity["@types"] != "") {
				$extractedTypes = explode(",", $extractedEntity["@types"]);
				foreach ($extractedTypes as $typeName) {
					if (explode(":", $typeName)[0] == "DBpedia") {
						$i ++;
						$type = array();
						$initialType = array();
		  				$type["typeURI"] = str_replace(" ","", "DBpedia::" . explode(":", $typeName)[1]);
		  				$initialType["typeURI"] = str_replace(" ","", "DBpedia::" . explode(":", $typeName)[1]);
						$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
						$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
						$type["wikiURI"] = array();
						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
						$entityResource = $this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"]));
						$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
						$initialType["wikiURI"] = array();
						$initialType["wikiURI"]["nl"] = null;
						$initialType["wikiURI"]["en"] = null;
						if ($extractedEntity["@similarityScore"] != "") {
							$type["confidence"]["score"] = (float)$extractedEntity["@similarityScore"];
							$initialType["confidence"]["score"] = (float)$extractedEntity["@similarityScore"];
						}
						else { 
							$type["confidence"]["score"] = null;
							$initialType["confidence"]["score"] = null;
						}
						$type["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);

						$initialType["confidence"]["bounds"] = null;
						array_push($initialEntity["types"], $initialType);
					}
				}
				if ($i == 0) {
					//no dbpedia types found, query for them
					$extractedTypes = $this->getRDFTypesFromDutchResource(utf8_encode($extractedEntity["@URI"]));
					foreach($extractedTypes as $extractedType) {
						$type = array();
						$typeArray = explode("/", $extractedType);
		  				$type["typeURI"] = str_replace(" ", "", "DBpedia::" . $typeArray[count($typeArray) - 1]);
						$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
						$type["wikiURI"] = array();
						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
						$entityResource = $this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"]));
						$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
						if ($extractedEntity["@similarityScore"] != "")
							$type["confidence"]["score"] = (float)$extractedEntity["@similarityScore"];
						else 
							$type["confidence"]["score"] = null;
						$type["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);
					}

					$initialType = array();
					$initialType["typeURI"] = null;
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
					$initialType["wikiURI"] = array();
					$initialType["wikiURI"]["nl"] = null;
					$initialType["wikiURI"]["en"] = null;
					if ($extractedEntity["@similarityScore"] != "")
						$initialType["confidence"]["score"] = (float)$extractedEntity["@similarityScore"];
					else 
						$initialType["confidence"]["score"] = null;
					$initialType["confidence"]["bounds"] = null;
					array_push($initialEntity["types"], $initialType);
				}
			}
			else {

				$initialType = array();
				$initialType["typeURI"] = null;
				$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
				$initialType["wikiURI"] = array();
				$initialType["wikiURI"]["nl"] = null;
				$initialType["wikiURI"]["en"] = null;
				if ($extractedEntity["@similarityScore"] != "")
					$initialType["confidence"]["score"] = (float)$extractedEntity["@similarityScore"];
				else 
					$initialType["confidence"]["score"] = null;
				$initialType["confidence"]["bounds"] = null;
				array_push($initialEntity["types"], $initialType);

				$extractedTypes = $this->getRDFTypesFromDutchResource(utf8_encode($extractedEntity["@URI"]));
				if (count($extractedTypes == 0)) {
					$type = array();
					$type["typeURI"] = null;
					$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
					$type["wikiURI"] = array();
					$type["wikiURI"] = array();
					$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
					$entityResource = $this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"]));
					$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);				
					if ($extractedEntity["@similarityScore"] != "")
							$type["confidence"]["score"] = (float)$extractedEntity["@similarityScore"];
						else 
							$type["confidence"]["score"] = null;
					$type["confidence"]["bounds"] = null;
					array_push($entity["types"], $type);
				}
				else {
					foreach($extractedTypes as $extractedType) {
						$type = array();
						$typeArray = explode("/", $extractedType);
		  				$type["typeURI"] = str_replace(" ", "", "DBpedia::" . $typeArray[count($typeArray) - 1]);
						$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
						$type["wikiURI"] = array();
						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
						$entityResource = $this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"]));
						$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
						if ($extractedEntity["@similarityScore"] != "")
							$type["confidence"]["score"] = (float)$extractedEntity["@similarityScore"];
						else 
							$type["confidence"]["score"] = null;
						$type["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);
					}
				}
			}

			if (sizeof($entity["types"]) == 0) {
				$type = array();
				$type["typeURI"] = null;
				$type["entityURI"] = null;
				$type["wikiURI"] = array();
				$type["wikiURI"]["nl"] = null;
				$type["wikiURI"]["en"] = null;
				$type["confidence"] = array();
				$type["confidence"]["score"] = null;
				$type["confidence"]["bounds"] = null;
				array_push($entity["types"], $type);

				$initialType = array();
				$initialType["typeURI"] = null;
				$initialType["entityURI"] = null;
				$initialType["wikiURI"] = array();
				$initialType["wikiURI"]["nl"] = null;
				$initialType["wikiURI"]["en"] = null;
				$initialType["confidence"] = array();
				$initialType["confidence"]["score"] = null;
				$initialType["confidence"]["bounds"] = null;
				array_push($initialEntity["types"], $initialType);
			}

			if ($this->searchForStopWords($entity["label"]) == false) {
				if (strtolower($this->searchForStopWordsEndLabel($initialEntity["label"])) == strtolower($entity["label"])) {
					if ($this->searchAbbrWords($entity["label"]) == false) {
						if (strtolower($this->searchForLastWordLength($initialEntity["label"])) == strtolower($entity["label"])) {
							if (strlen($entity["label"]) > 2) {
								if (substr($entity["label"], -1) == "-") {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
								else {
									array_push($result["cleanedUpEntities"], $entity);
								}
							}
							else {
								if(strtoupper($entity["label"]) == $entity["label"]) {
									array_push($result["cleanedUpEntities"], $entity);
								}
							}
						}
						else {
							if (substr($entity["label"], -1) == "-") {
								$cleanedUpEntity = array();
								$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
								$cleanedUpEntity["startOffset"] = $entity["startOffset"];
								$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
								$cleanedUpEntity["confidence"] = null;
								$cleanedUpEntity["provenance"] = "thd";
								$cleanedUpEntity["types"] = $entity["types"];
								array_push($result["cleanedUpEntities"], $cleanedUpEntity);
							}
							else {
								$cleanedUpEntity = array();
								$cleanedUpEntity["label"] = utf8_decode($this->searchForLastWordLength($entity["label"]));
								$cleanedUpEntity["startOffset"] = $entity["startOffset"];
								$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
								$cleanedUpEntity["confidence"] = null;
								$cleanedUpEntity["provenance"] = "thd";
								$cleanedUpEntity["types"] = $entity["types"];
								array_push($result["cleanedUpEntities"], $cleanedUpEntity);
							}
						}
					}
				}
				else {
					$cleanedUpEntity = array();
					$cleanedUpEntity["label"] = utf8_decode($this->searchForStopWordsEndLabel($entity["label"]));
					$cleanedUpEntity["startOffset"] = $entity["startOffset"];
					$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
					$cleanedUpEntity["confidence"] = null;
					$cleanedUpEntity["provenance"] = "thd";
					$cleanedUpEntity["types"] = $entity["types"];
					array_push($result["cleanedUpEntities"], $cleanedUpEntity);
				}
			}

			array_push($result["intermediateEntities"], $entity);
			array_push($result["initialEntities"], $initialEntity);
		}
	//	dd($result);
		return $result;	
	}

	public function processNERDApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = $entity->content["description"];
		$initialDescription = utf8_decode($descriptionContent);
		$lang = $entity->language;
		$apikey = "9u0sd79j21vpvv0tqin1oleb4di32oo6";
		$result = array();
		$result["intermediateEntities"] = array();
		$result["initialEntities"] = array();
		$result["cleanedUpEntities"] = array();
		$entities = array();
		$curlRequest = "curl -i -X POST http://nerd.eurecom.fr/api/document -d \"text=" . urlencode($descriptionContent) . "&key=" . $apikey . "\"";
		$response = exec($curlRequest, $output);
		$documentId = "";
		if (strpos($output[count($output) - 1], 'idDocument') !== false) {
			$documentId = explode("}", explode(":", $output[count($output) - 1])[1])[0];
		}
		$annotationId = "";
		$curlRequest = "curl -i -X POST \"http://nerd.eurecom.fr/api/annotation\" -d \"key=" . $apikey . "&idDocument=" . $documentId . "&extractor=combined&ontology=extended&timeout=10\"";
		$response = exec($curlRequest, $output);
		if (strpos($output[count($output) - 1], 'idAnnotation') !== false) {
			$annotationId = explode("}", explode(":", $output[count($output) - 1])[1])[0];
		}
		$curlRequest = "curl -i -X GET -H \"Accept: application/json\" \"http://nerd.eurecom.fr/api/entity?key=" . $apikey . "&idAnnotation=" . $annotationId . "&granularity=oen\"";
		$response = exec($curlRequest, $output);
		$resultArray = json_decode($output[count($output) - 1], true);
	//	dd($resultArray);
		foreach($resultArray as $key => $value) {
			$entity = array();
			$entity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["label"]);
			$entity["startOffset"] = (int)$value['startChar'];
			$entity["endOffset"] = (int)$value['endChar'];
			$initialEntity = array();
			$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["label"]);
			$initialEntity["startOffset"] = $value['startChar'];
			$initialEntity["endOffset"] = $value['endChar'];

			$initialEntity["timestamp"] = "00:00"; 
			$initialEntity["description"] = ""; 

			$entity["timestamp"] = "00:00"; 
			$entity["description"] = ""; 

		//	echo utf8_decode($entity["label"]) . "--";
		//	echo substr($initialDescription, (int)$entity["startOffset"], strlen($entity["label"])). "--";

			if (strtolower(substr($initialDescription, (int)$entity["startOffset"], strlen(utf8_decode($entity["label"])))) != utf8_decode(strtolower($entity["label"]))) {
				continue;
			}

			$entity["confidence"] = $value["relevance"];
			$entity["provenance"] = "nerd";
			$entity["types"] = array();
			
			$initialEntity["confidence"] = $value["relevance"];
			$initialEntity["provenance"] = "nerd";
			$initialEntity["types"] = array();

			$dbpediaTypes = array();
			$freebaseTypes = array();

			if (strpos($value["extractorType"], "http") !== false) {
			//	dd($extractedType);
				$typeArray = explode("/", $value["extractorType"]);
				array_push($dbpediaTypes, $typeArray[count($typeArray) - 1]);
			}
			else {
				$typesArray = explode(";", $value["extractorType"]);
				foreach ($typesArray as $content) {
					if (strpos($content, 'DBpedia') !== false) {
						$temp = explode(":", $content);
						$dbpediaTypes = explode(",", $temp[1]);
					}
					if (strpos($content, 'Freebase') !== false) {
						$temp = explode(":", $content);
						$freebaseTypes = explode(",", $temp[1]);
					}
				}
			}

			$typesRDF = $this->getRDFTypesFromDutchResource(utf8_encode(str_replace("de.dbpedia.org", "nl.dbpedia.org", $value["uri"])));
		//	print_r($typesRDF);
			if (count($typesRDF) > 0) {
				foreach($typesRDF as $extractedType) {
		  			$typeArray = explode("/", $extractedType);
		  			if (!in_array($typeArray[count($typeArray) - 1], $dbpediaTypes)) {
		  				array_push($dbpediaTypes, $typeArray[count($typeArray) - 1]);
		  			}
				}
			}

			if (strpos($value["uri"], "/dbpedia") !== false) {
				$typesRDF = $this->getRDFTypesFromEnglishResource(utf8_encode($value["uri"]));
			//	print_r($typesRDF);
				if (count($typesRDF) > 0) {
					foreach($typesRDF as $extractedType) {
			  			$typeArray = explode("/", $extractedType);
			  			if (!in_array($typeArray[count($typeArray) - 1], $dbpediaTypes)) {
			  				array_push($dbpediaTypes, $typeArray[count($typeArray) - 1]);
			  			}
					}
				}
			}

			if (count($dbpediaTypes) > 0) {
				foreach ($dbpediaTypes as $typeValue) {
					$type = array();
					$type["typeURI"] = str_replace(" ", "", "DBpedia::" . $typeValue);
					$type["wikiURI"] = array();
					$type["wikiURI"]["nl"] = null;
					$type["wikiURI"]["en"] = null;
					$type["entityURI"] = null;

					$initialType = array();
					$initialType["typeURI"] = str_replace(" ", "", "DBpedia::" . $typeValue);
					$initialType["wikiURI"] = array();
					$initialType["wikiURI"]["nl"] = null;
					$initialType["wikiURI"]["en"] = null;
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);

					if ($value["uri"] != "") {
						//it means we have the wiki link
						if (strpos($value["uri"], "wikipedia") !== false) {

							$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
							$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', str_replace("de.wikipedia.org", "nl.wikipedia.org", $value["uri"]));
							$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromDutchWikipediaLink(utf8_encode($type["wikiURI"]["nl"])));
							$englishEntityResource = $this->getEnglishResourceFromDutchResource(utf8_encode($type["entityURI"]));
							$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($englishEntityResource);
						}
						else { //it means we have the dbpedia resource

							$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);

							if (strpos($value["uri"], "de.dbpedia") !== false) {
								$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', str_replace("de.dbpedia.org", "nl.dbpedia.org", $value["uri"]));
								$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
								$entityResource = $this->getEnglishResourceFromDutchResource($type["entityURI"]);
								$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
							}
							else if (strpos($value["uri"], "nl.dbpedia") !== false) {
								$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
								$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
								$entityResource = $this->getEnglishResourceFromDutchResource($type["entityURI"]);
								$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
							}
							else {
								$entityResource = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
								$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
								$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromEnglishResource($entityResource));
								$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource(utf8_encode($type["entityURI"])));
							}
						}
					}
					else {
						$type = array();
						$type["typeURI"] = str_replace(" ", "", "DBpedia::" . $typeValue);
						$type["wikiURI"] = array();
						$type["wikiURI"]["nl"] = null;
						$type["wikiURI"]["en"] = null;
						$type["entityURI"] = null;

						$initialType = array();
						$initialType["typeURI"] = str_replace(" ", "", "DBpedia::" . $typeValue);
						$initialType["wikiURI"] = array();
						$initialType["wikiURI"]["nl"] = null;
						$initialType["wikiURI"]["en"] = null;
						$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
					}
					$type["confidence"]["score"] = $value["relevance"];
					$type["confidence"]["bounds"] = null;
					array_push($entity["types"], $type);

					$initialType["confidence"]["score"] = $value["relevance"];
					$initialType["confidence"]["bounds"] = null;
					array_push($initialEntity["types"], $initialType);
				}
			}
			if (count($freebaseTypes) > 0) {
				foreach ($freebaseTypes as $typeValue) {
					$type = array();
					$type["typeURI"] = null;
					$type["wikiURI"] = array();
					$type["wikiURI"]["nl"] = null;
					$type["wikiURI"]["en"] = null;
					$type["entityURI"] = null;

					$initialType = array();
					$initialType["typeURI"] = str_replace(" ", "", "Freebase::" . $typeValue);
					$initialType["wikiURI"] = array();
					$initialType["wikiURI"]["nl"] = null;
					$initialType["wikiURI"]["en"] = null;
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);


					if ($value["uri"] != "") {
						//it means we have the wiki link
						if (strpos($value["uri"], "nl.wikipedia") !== false) {
							$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
						}
						else { //it means we have the dbpedia resource
							if (strpos($value["uri"], "en.wikipedia") !== false) {
								$type["wikiURI"]["en"] = $value["uri"];
							}
						}
					}
					else {
	
						$initialType = array();
						$initialType["typeURI"] = str_replace(" ", "", "Freebase::" . $typeValue);
						$initialType["wikiURI"] = array();
						$initialType["wikiURI"]["nl"] = null;
						$initialType["wikiURI"]["en"] = null;
						$initialType["entityURI"] = null;
					}
					$type["confidence"]["score"] = $value["confidence"];
					$type["confidence"]["bounds"] = null;
					array_push($entity["types"], $type);

					$entity["types"] = array_map("unserialize", array_unique(array_map("serialize", $entity["types"])));

					$initialType["confidence"]["score"] = $value["confidence"];
					$initialType["confidence"]["bounds"] = null;
					array_push($initialEntity["types"], $initialType);
				}
			}
			if (count($dbpediaTypes) == 0 && count($freebaseTypes) == 0) {
				$type = array();
				$type["typeURI"] = null;
				$type["wikiURI"] = array();
				$type["wikiURI"]["nl"] = null;
				$type["wikiURI"]["en"] = null;
				$type["entityURI"] = null;

				$initialType = array();
				$initialType["typeURI"] = null;
				$initialType["wikiURI"] = array();
				$initialType["wikiURI"]["nl"] = null;
				$initialType["wikiURI"]["en"] = null;
				if ($value["uri"] != "")
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
				else 
					$initialType["entityURI"] = null;
				if ($value["uri"] != "") {
					if (strpos($value["uri"], "wikipedia") !== false) {
						$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
						$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchResourceFromDutchWikipediaLink($value["uri"]));
						$entityResource = $this->getEnglishResourceFromDutchResource($type["entityURI"]);
						$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
					}
					else {
						if (strpos($value["uri"], "de.dbpedia") !== false) {
							$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', str_replace("de.dbpedia.org", "nl.dbpedia.org", $value["uri"]));
							$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource($type["entityURI"]));
							$entityResource = $this->getEnglishResourceFromDutchResource($type["entityURI"]);
							$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
						}
						else {
							if (strpos($value["uri"], "nl.dbpedia") !== false) {
								$type["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
								$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource($type["entityURI"]));
								$entityResource = $this->getEnglishResourceFromDutchResource($type["entityURI"]);
								$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
							}
							else {
								$entityResource = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
								$type["wikiURI"]["en"] = $this->getEnglishWikipediaLinkFromEnglishResource($entityResource);
								$type["entityURI"] = $this->getDutchResourceFromEnglishResource($entityResource);
								$type["wikiURI"]["nl"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->getDutchWikipediaLinkFromDutchResource($type["entityURI"]));
							}
						}
						
					}
				}
				else {
					$type = array();
					$type["typeURI"] = null;
					$type["wikiURI"] = array();
					$type["wikiURI"]["nl"] = null;
					$type["wikiURI"]["en"] = null;
					$type["entityURI"] = null;

					$initialType = array();
					$initialType["typeURI"] = null;
					$initialType["wikiURI"] = array();
					$initialType["wikiURI"]["nl"] = null;
					$initialType["wikiURI"]["en"] = null;
					$initialType["entityURI"] = null;
				}
				$type["confidence"]["score"] = $value["confidence"];
				$type["confidence"]["bounds"] = null;
	
				array_push($entity["types"], $type);
				$entity["types"] = array_map("unserialize", array_unique(array_map("serialize", $entity["types"])));

				$initialType["confidence"]["score"] = $value["confidence"];
				$initialType["confidence"]["bounds"] = null;
				array_push($initialEntity["types"], $initialType);
			}
			if (sizeof($entity["types"]) == 0) {
				$type = array();
				$type["typeURI"] = null;
				$type["entityURI"] = null;
				$type["wikiURI"] = array();
				$type["wikiURI"]["nl"] = null;
				$type["wikiURI"]["en"] = null;
				$type["confidence"] = array();
				$type["confidence"]["score"] = null;
				$type["confidence"]["bounds"] = null;
				array_push($entity["types"], $type);
			}

			if (sizeof($initialEntity["types"]) == 0) {
				$initialType = array();
				$initialType["typeURI"] = null;
				$initialType["entityURI"] = null;
				$initialType["wikiURI"] = array();
				$initialType["wikiURI"]["nl"] = null;
				$initialType["wikiURI"]["en"] = null;
				$initialType["confidence"] = array();
				$initialType["confidence"]["score"] = null;
				$initialType["confidence"]["bounds"] = null;
				array_push($initialEntity["types"], $initialType);
			}

			if ($this->searchForStopWords($entity["label"]) == false) {
				if (strtolower($this->searchForStopWordsEndLabel($initialEntity["label"])) == strtolower($entity["label"])) {
					if ($this->searchAbbrWords($entity["label"]) == false) {
						if (strtolower($this->searchForLastWordLength($initialEntity["label"])) == strtolower($entity["label"])) {
							if (strlen($entity["label"]) > 2) {
								if (substr($entity["label"], -1) == "-") {
									$cleanedUpEntity = array();
									$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
									$cleanedUpEntity["startOffset"] = $entity["startOffset"];
									$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
									$cleanedUpEntity["confidence"] = null;
									$cleanedUpEntity["provenance"] = "thd";
									$cleanedUpEntity["types"] = $entity["types"];
									array_push($result["cleanedUpEntities"], $cleanedUpEntity);
								}
								else {
									array_push($result["cleanedUpEntities"], $entity);
								}
							}
							else {
								if(strtoupper($entity["label"]) == $entity["label"]) {
									array_push($result["cleanedUpEntities"], $entity);
								}
							}
						}
						else {
							if (substr($entity["label"], -1) == "-") {
								$cleanedUpEntity = array();
								$cleanedUpEntity["label"] = trim(utf8_decode($this->searchForLastWordLength($entity["label"])), "-");
								$cleanedUpEntity["startOffset"] = $entity["startOffset"];
								$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
								$cleanedUpEntity["confidence"] = null;
								$cleanedUpEntity["provenance"] = "thd";
								$cleanedUpEntity["types"] = $entity["types"];
								array_push($result["cleanedUpEntities"], $cleanedUpEntity);
							}
							else {
								$cleanedUpEntity = array();
								$cleanedUpEntity["label"] = utf8_decode($this->searchForLastWordLength($entity["label"]));
								$cleanedUpEntity["startOffset"] = $entity["startOffset"];
								$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
								$cleanedUpEntity["confidence"] = null;
								$cleanedUpEntity["provenance"] = "thd";
								$cleanedUpEntity["types"] = $entity["types"];
								array_push($result["cleanedUpEntities"], $cleanedUpEntity);
							}
						}
					}
				}
				else {
					$cleanedUpEntity = array();
					$cleanedUpEntity["label"] = utf8_decode($this->searchForStopWordsEndLabel($entity["label"]));
					$cleanedUpEntity["startOffset"] = $entity["startOffset"];
					$cleanedUpEntity["endOffset"] = (int)$entity["startOffset"] + strlen($cleanedUpEntity["label"]); 
					$cleanedUpEntity["confidence"] = null;
					$cleanedUpEntity["provenance"] = "thd";
					$cleanedUpEntity["types"] = $entity["types"];
					array_push($result["cleanedUpEntities"], $cleanedUpEntity);
				}
			}

			array_push($result["intermediateEntities"], $entity);
			array_push($result["initialEntities"], $initialEntity);
		}
	//	dd($result);
		return $result;	
	}

	public function process($entity) 
	{	
		\Session::flash('flashSuccess', 'Your video description is being pre-processed');

		if ($entity->preprocessed["automatedEvents"] == false) {
			if ($entity->language != "en") {
				$retVal["processAutomatedEventExtraction"] = $this->processAutomatedEventExtraction($entity);
			}
		}

		if ($entity->preprocessed["automatedEntities"] == false) {
			$retVal["tdhapi"] = $this->processTDHApi($entity);	
			$retVal["textrazorapi"] = $this->processTextRazorApi($entity);
			if ($entity->language != "en") {
		//		$retVal["semitagsapi"] = $this->processSemiTagsApi($entity);
				$retVal["dbpediaspotlightapi"] = $this->processDBpediaSpotlightApi($entity);
			}
			$retVal["nerdapi"] = $this->processNERDApi($entity);	
			$retVal["lupediaapi"] = $this->processLupediaApi($entity);
		}
		return $retVal;
	}

	public function store($parentEntity, $metadataDescriptionPreprocessing)
	{
		$retVal = array();
		if (isset($metadataDescriptionPreprocessing["processAutomatedEventExtraction"])) {
			$retVal["processAutomatedEventExtraction"] = $this->storeAutomatedEventExtraction($parentEntity, $metadataDescriptionPreprocessing["processAutomatedEventExtraction"]);
		}
		if (isset($metadataDescriptionPreprocessing["tdhapi"])) {
			$retVal["thdapi"] = $this->storeTDHApi($parentEntity, $metadataDescriptionPreprocessing["tdhapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["textrazorapi"])) {
			$retVal["textrazorapi"] = $this->storeTextRazorApi($parentEntity, $metadataDescriptionPreprocessing["textrazorapi"]);
		}
	/*	if (isset($metadataDescriptionPreprocessing["semitagsapi"])) {
			$retVal["semitagsapi"] = $this->storeSemiTagsApi($parentEntity, $metadataDescriptionPreprocessing["semitagsapi"]);
		}
	*/	if (isset($metadataDescriptionPreprocessing["nerdapi"])) {
			$retVal["nerdapi"] = $this->storeNERDApi($parentEntity, $metadataDescriptionPreprocessing["nerdapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["lupediaapi"])) {
			$retVal["lupediaapi"] = $this->storeLupediaApi($parentEntity, $metadataDescriptionPreprocessing["lupediaapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["dbpediaspotlightapi"])) {
			$retVal["dbpediaspotlightapi"] = $this->storeDBpediaSpotlightApi($parentEntity, $metadataDescriptionPreprocessing["dbpediaspotlightapi"]);
		}
		$retVal["parentId"] = $parentEntity->_id;
		return $retVal;
	}

	public function storeAutomatedEventExtraction($parentEntity, $metadataDescriptionPreprocessing)
	{
	//	$metadataDescriptionPreprocessing = unserialize(utf8_decode(utf8_encode(serialize($metadataDescriptionPreprocessing))));
		$status = array();

		try {
			$this->createAutomatedEventExtractionSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['processAutomatedEventExtraction'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "automatedEventExtraction";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		$tempEntityID = null;
		$title = "automatedEventExtraction-" . $parentEntity["title"];
		try {
			$entity = new Entity;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->domain = $parentEntity->domain;
			$entity->format = "text";
			$entity->documentType = "automatedEventExtraction";
			$entity->parents = array($parentEntity->_id);
			$entity->source = $parentEntity->source;
			$content = array();
			$content["description"] = $parentEntity->content;
			$content["automatedEvents"] = $metadataDescriptionPreprocessing["automatedEvents"];
			$entity->content = $content;
			$entity->hash = md5(serialize($entity));
			$entity->activity_id = $activity->_id;

			$entity->save();
			$status['success'][$title] = $title . " was successfully processed into automated event extraction. (URI: {$entity->_id})";
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$entity->forceDelete();
			$status['error'][$title] = $e->getMessage();
		}
		$tempEntityID = $entity->_id;

		return $status;
	}

	public function storeDBpediaSpotlightApi($parentEntity, $metadataDescriptionPreprocessing)
	{
	//	$metadataDescriptionPreprocessing = unserialize(utf8_decode(utf8_encode(serialize($metadataDescriptionPreprocessing))));
		$status = array();

		try {
			$this->createNamedEntitiesExtractionDBpediaSpotlightApiSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['dbpediaspotlightextractor'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "dbpediaspotlightextractor";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		$tempEntityID = null;
		$title = "dbpediaspotlightextractor-" . $parentEntity["title"];
		try {
			$entity = new Entity;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->domain = $parentEntity->domain;
			$entity->format = "text";
			$entity->documentType = "dbpediaspotlightextractor";
			$entity->parents = array($parentEntity->_id);
			$entity->source = $parentEntity->source;
			$content = array();
			$content["description"] = $parentEntity->content;
			foreach ($metadataDescriptionPreprocessing as $key => $value){
				$content["features"][$key] = $value;
			}
			$entity->content = $content;
			$entity->hash = md5(serialize($entity));
			$entity->activity_id = $activity->_id;

			$entity->save();
			$status['success'][$title] = $title . " was successfully processed into entities extraction. (URI: {$entity->_id})";
	//		dd($status);
		} catch (Exception $e) {
			// Something went wrong with creating the Entity
			$entity->forceDelete();
			$status['error'][$title] = $e->getMessage();
		}
		$tempEntityID = $entity->_id;

		return $status;
	}

	public function storeLupediaApi($parentEntity, $metadataDescriptionPreprocessing)
	{
	//	dd("here");
	//	$metadataDescriptionPreprocessing = unserialize(utf8_decode(utf8_encode(serialize($metadataDescriptionPreprocessing))));
		$status = array();

		try {
			$this->createNamedEntitiesExtractionLupediaApiSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['lupediaextractor'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "lupediaextractor";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		
			$tempEntityID = null;
			$title = "lupediaextractor-" . $parentEntity["title"];
			//dd($title);
			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "text";
				$entity->documentType = "lupediaextractor";
				$entity->parents = array($parentEntity->_id);
				$entity->source = $parentEntity->source;
				$content = array();
				$content["description"] = $parentEntity->content;
				foreach ($metadataDescriptionPreprocessing as $key => $value){
					$content["features"][$key] = $value;
				}
				$entity->content = $content;
				$entity->hash = md5(serialize($entity));
				$entity->activity_id = $activity->_id;

			//	dd($entity);
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into entities extraction. (URI: {$entity->_id})";

			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}

			$tempEntityID = $entity->_id;
		

		return $status;
	}

	public function storeTextRazorApi($parentEntity, $metadataDescriptionPreprocessing) {
	//	$metadataDescriptionPreprocessing = unserialize(utf8_decode(utf8_encode(serialize($metadataDescriptionPreprocessing))));
		$status = array();

		try {
			$this->createNamedEntitiesExtractionTextRazorApiSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['textrazorextractor'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "textrazorextractor";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		
			$tempEntityID = null;
			$title = "textrazorextractor-" . $parentEntity["title"];
			//dd($title);
			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "text";
				$entity->documentType = "textrazorextractor";
				$entity->parents = array($parentEntity->_id);
				$entity->source = $parentEntity->source;

				$content = array();
				$content["description"] = $parentEntity->content;
				foreach ($metadataDescriptionPreprocessing as $key => $value){
					$content["features"][$key] = $value;
				}
				$entity->content = $content;

				$entity->hash = md5(serialize($entity));
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into entities extraction. (URI: {$entity->_id})";

			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}
		//	dd($entity);
		return $status;
	}

	public function storeTDHApi($parentEntity, $metadataDescriptionPreprocessing)
	{
		$metadataDescriptionPreprocessing = unserialize(utf8_decode(utf8_encode(serialize($metadataDescriptionPreprocessing))));
		$status = array();

		try {
			$this->createNamedEntitiesExtractionTHDApiSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['thdextractor'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "thdextractor";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error']['thdextractor'] = $e->getMessage();
			return $status;
		}

		
			$tempEntityID = null;
			$title = "thdextractor-" . $parentEntity["title"];
			//dd($title);
			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "text";
				$entity->documentType = "thdextractor";
				$entity->parents = array($parentEntity->_id);
				$entity->source = $parentEntity->source;
				$content = array();
				$content["description"] = $parentEntity->content;
				foreach ($metadataDescriptionPreprocessing as $key => $value){
					$content["features"][$key] = $value;
				}
				$entity->content = $content;

			//	
				//unset($twrexStructuredSentenceKeyVal['properties']);
				$entity->hash = md5(serialize($entity));
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into entities extraction. (URI: {$entity->_id})";

			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}

			$tempEntityID = $entity->_id;
		

		return $status;
	}

	public function storeSemiTagsApi($parentEntity, $metadataDescriptionPreprocessing)
	{
	//	$metadataDescriptionPreprocessing = unserialize(utf8_decode(utf8_encode(serialize($metadataDescriptionPreprocessing))));
		$status = array();

		try {
			$this->createNamedEntitiesExtractionSemiTagsApiSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['semitags'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "semitagsextractor";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		
			$tempEntityID = null;
			$title = "semitagsextractor-" . $parentEntity["title"];
			//dd($title);
			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "text";
				$entity->documentType = "semitagsextractor";
				$entity->parents = array($parentEntity->_id);
				$entity->source = $parentEntity->source;
				$content = array();
				$content["description"] = $parentEntity->content;
				foreach ($metadataDescriptionPreprocessing as $key => $value){
					$content["features"][$key] = $value;
				}
				$entity->content = $content;

			//	
				//unset($twrexStructuredSentenceKeyVal['properties']);
				$entity->hash = md5(serialize($entity));
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into entities extraction. (URI: {$entity->_id})";

			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}

			$tempEntityID = $entity->_id;
		

		return $status;
	}

	public function storeNERDApi($parentEntity, $metadataDescriptionPreprocessing)
	{
	//	$metadataDescriptionPreprocessing = unserialize(utf8_decode(utf8_encode(serialize($metadataDescriptionPreprocessing))));
		$status = array();

		try {
			$this->createNamedEntitiesExtractionNERDApiSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['nerd'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "nerdextractor";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		
			$tempEntityID = null;
			$title = "nerdextractor-" . $parentEntity["title"];
			//dd($title);
			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "text";
				$entity->documentType = "nerdextractor";
				$entity->parents = array($parentEntity->_id);
				$entity->source = $parentEntity->source;
				$content = array();
				$content["description"] = $parentEntity->content;
				foreach ($metadataDescriptionPreprocessing as $key => $value){
					$content["features"][$key] = $value;
				}
				$entity->content = $content;

			//	
				//unset($twrexStructuredSentenceKeyVal['properties']);
				$entity->hash = md5(serialize($entity));
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into entities extraction. (URI: {$entity->_id})";

			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}

			$tempEntityID = $entity->_id;
		

		return $status;
	}

	public function createNamedEntitiesExtractionTHDApiSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('thdextractor'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "thdextractor";
			$softwareAgent->label = "This component uses THD API in order to extract entities from video metadata description";
			$softwareAgent->save();
		}
	}

	public function createAutomatedEventExtractionSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('automatedEventExtraction'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "automatedEventExtraction";
			$softwareAgent->label = "This component extracts events from video metadata description";
			$softwareAgent->save();
		}
	}


	public function createNamedEntitiesExtractionTextRazorApiSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('textrazorextractor'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "textrazorextractor";
			$softwareAgent->label = "This component uses TextRazor API in order to extract named entities from video metadata description";
			$softwareAgent->save();
		}
	}

	public function createNamedEntitiesExtractionSemiTagsApiSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('semitagsextractor'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "semitagsextractor";
			$softwareAgent->label = "This component uses SemiTags API in order to extract named entities from video metadata description";
			$softwareAgent->save();
		}
	}

	public function createNamedEntitiesExtractionNERDApiSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('nerdextractor'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "nerdextractor";
			$softwareAgent->label = "This component uses the combined NERD API in order to extract named entities from video metadata description";
			$softwareAgent->save();
		}
	}

	public function createNamedEntitiesExtractionLupediaApiSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('lupediaextractor'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "lupediaextractor";
			$softwareAgent->label = "This component uses the Lupedia API in order to extract named entities from video metadata description";
			$softwareAgent->save();
		}
	}
	
	public function createNamedEntitiesExtractionDBpediaSpotlightApiSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('dbpediaspotlightextractor'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "dbpediaspotlightextractor";
			$softwareAgent->label = "This component uses the DBpediaSpotlight API in order to extract named entities from video metadata description";
			$softwareAgent->save();
		}
	}
	
}