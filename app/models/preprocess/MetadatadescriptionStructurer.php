<?php

namespace preprocess;
require_once 'sparqllib.php';

use URL, Session, Exception, Config;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;
use \Entity as Entity;
use \Entities\Unit as Unit;

class MetadatadescriptionStructurer {

	public static function object_to_array($data) {
	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] = self::object_to_array($value);
	        }
	        return $result;
	    }
	    return $data;
	}

	public static function processTHDApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$urlTDH = "http://entityclassifier.eu/thd/api/v2/extraction?";
	//	$descriptionContent = urlencode($entity->content["metadata"]["abstract"]["nl"]);
		$descriptionContent = urlencode($entity["content"]["description"]);
	//	$lang = $entity->content["metadata"]["language"];
		$lang = "nl";
		$format = "json";
		$priority_entity_linking = "true";
		$apikey = Config::get('config.thd_api_key');
		$result = array();
		$result["initialEntities"] = array();
		$output = "";
		$urlReq = $urlTDH . "apikey=". $apikey . "&format=" . $format . "&lang=" . $lang . "&entity_type=all&linking_method=AllVoting";
		$curlRequest = "curl -v \"" . $urlReq . "\" -d \"" . $descriptionContent . "\"";
		$response = exec($curlRequest, $output);
		$responseEntities = json_decode($output[0], true);	

		for ($j = 0; $j < count($responseEntities); $j++) {
			$initialEntity = array();
			$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $responseEntities[$j]["underlyingString"]);
			$initialEntity["startOffset"] = $responseEntities[$j]["startOffset"];
			$initialEntity["endOffset"] = $responseEntities[$j]["endOffset"]; 
			$initialEntity["provenance"] = "thd";		
			$initialEntity["types"] = array();
			if (isset($responseEntities[$j]["types"])) {
				for ($k = 0; $k < count($responseEntities[$j]["types"]); $k++) {
					if ((strpos($responseEntities[$j]["types"][$k]["typeLabel"], "dbpedia.org/resource/") !== false)) {
						continue;
					}
					$initialType = array();
					$initialType["type"] =$responseEntities[$j]["types"][$k]["typeLabel"];
					$initialType["typeURI"] =$responseEntities[$j]["types"][$k]["typeURI"];
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', (string)$responseEntities[$j]["types"][$k]["entityURI"]); 
					$initialType["typeConfidenceScore"] = $responseEntities[$j]["types"][$k]["classificationConfidence"]["value"];
					$initialType["resourceConfidenceScore"] = $responseEntities[$j]["types"][$k]["linkingConfidence"]["value"];
					$initialType["typeProvenance"] = $responseEntities[$j]["types"][$k]["provenance"];
					$initialType["salience"] = array();
					$initialType["salience"]["score"] = $responseEntities[$j]["types"][$k]["salience"]["score"];
					$initialType["salience"]["confidence"] = $responseEntities[$j]["types"][$k]["salience"]["confidence"];
					$initialType["salience"]["class"] = $responseEntities[$j]["types"][$k]["salience"]["classLabel"];
					array_push($initialEntity["types"], $initialType);

					$initialEntity["types"] = array_map("unserialize", array_unique(array_map("serialize", $initialEntity["types"])));
				}
				array_push($result["initialEntities"], $initialEntity);
			}
			$array["initialEntities"] = array_map("unserialize", array_unique(array_map("serialize", $result["initialEntities"])));
		}
		//dd($array);
		return $array;	
	}

	public static function processTextRazorApi($entity) {
		require_once('TextRazor.php');
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$call_type = array("entities");
		$text = $entity["content"]["description"];
		$initialDescription = utf8_decode($text);
		$textrazor = new TextRazor(Config::get('config.textrazor_api_key'));
		$textrazor->addExtractor('entities,topics,words,phrases,dependency-trees,relations,entailments,senses');
		$response = $textrazor->analyze($text);
		$result = array();
		// the confidence score needs to be normalized 0 .. 1
		$initialMin = 0.5;
		$initialMax = 10;

		if (isset($response['response']['entities'])) {
    		$result["initialEntities"] = array();
    		$initialEntities = array();
			for ($i = 0; $i < count($response['response']['entities']); $i ++) {	
				$initialEntity = array();
				$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["matchedText"]);
				$initialEntity["startOffset"] = $response['response']['entities'][$i]["startingPos"];
				$initialEntity["endOffset"] = $response['response']['entities'][$i]["endingPos"];
				$initialEntity["confidenceScore"] = 0 + ($response['response']['entities'][$i]["confidenceScore"] - $initialMin) * 1 / ($initialMax - $initialMin);
				$initialEntity["relevanceScore"] = $response['response']['entities'][$i]["relevanceScore"];
				$initialEntity["provenance"] = "textrazor";
				$initialEntity["types"] = array();	

				if (isset($response['response']['entities'][$i]["type"])) {
					for ($k = 0; $k < count($response['response']['entities'][$i]["type"]); $k++) {
						$initialType = array();
						$initialType["type"] = $response['response']['entities'][$i]["type"][$k]; 
						$initialType["typeURI"] = "";
						$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["wikiLink"]);
						$initialType["freebaseId"] = "";
						$initialType["freebaseURI"] = "";
						if (isset($response['response']['entities'][$i]["freebaseId"]))	{			
							$initialType["freebaseURI"] = iconv('UTF-8', 'UTF-8//IGNORE', "http://www.freebase.com" . $response['response']['entities'][$i]["freebaseId"]);
							$initialType["freebaseId"] = $response['response']['entities'][$i]["freebaseId"];
						}
						$initialType["entityEnglishId"] = "";
						if (isset($response['response']['entities'][$i]["entityEnglishId"])) {
							$initialType["entityEnglishId"] = $response['response']['entities'][$i]["entityEnglishId"];
						}
						array_push($initialEntity["types"], $initialType);
					}
				}
				
				if (isset($response['response']['entities'][$i]["freebaseTypes"])) {
					for ($k = 0; $k < count($response['response']['entities'][$i]["freebaseTypes"]); $k++) {
						$initialType = array();
						$initialType["type"] = $response['response']['entities'][$i]["freebaseTypes"][$k];
						$initialType["typeURI"] = "";	
						$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $response['response']['entities'][$i]["wikiLink"]);
						$initialType["freebaseId"] = "";
						$initialType["freebaseURI"] = "";
						if (isset($response['response']['entities'][$i]["freebaseId"]))	{			
							$initialType["freebaseURI"] = iconv('UTF-8', 'UTF-8//IGNORE', "http://www.freebase.com" . $response['response']['entities'][$i]["freebaseId"]);
							$initialType["freebaseId"] = $response['response']['entities'][$i]["freebaseId"];
						}
						$initialType["entityEnglishId"] = "";
						if (isset($response['response']['entities'][$i]["entityEnglishId"])) {
							$initialType["entityEnglishId"] = $response['response']['entities'][$i]["entityEnglishId"];
						}
						array_push($initialEntity["types"], $initialType);
					}
				}
				array_push($result["initialEntities"], $initialEntity);
			}
		}
		
		if (isset($response['response']['coarseTopics'])) {
			$result["coarseTopics"] = array();
			for ($i = 0; $i < count($response['response']['coarseTopics']); $i ++) {
				$topic = array();
				$topic["label"] = $response['response']['coarseTopics'][$i]["label"];
				$topic["wikiLink"] = $response['response']['coarseTopics'][$i]["wikiLink"];
				$topic["score"] = $response['response']['coarseTopics'][$i]["score"];				
				array_push($result["coarseTopics"], $topic);
			}
		}

		if (isset($response['response']['topics'])) {
			$result["topics"] = array();
			for ($i = 0; $i < count($response['response']['topics']); $i ++) {
				$topic = array();
				$topic["label"] = $response['response']['topics'][$i]["label"];
				$topic["wikiLink"] = $response['response']['topics'][$i]["wikiLink"];
				$topic["score"] = $response['response']['topics'][$i]["score"];				
				array_push($result["topics"], $topic);
			}
		}
		return $result;	
	}

	public static function processSemiTagsApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		//$descriptionContent = urlencode($entity->content["programSynopsis"]);
		$descriptionContent = urlencode($entity["content"]["description"]);
		//$lang = $entity->content["language"];
		$lang = "nl";
		$result = array();
		$result["initialEntities"] = array();
		$curlRequest = "curl -d \"language=" . $lang . "&text=$descriptionContent\" http://nlp.vse.cz:8081/recognize";
		$response = exec($curlRequest, $output);
		//dd($curlRequest);
		$response = json_decode($output[0]);

	//	$response = object_to_array($response);
		return $response;

	}

	public static function bla($response) {
		$result = array();
		$result["initialEntities"] = array();
		foreach ($response as $entity) {
			$initialEntity = array();
			$initialEntity["label"] = $entity["name"];
			$initialEntity["startOffset"] = (int)$entity["start"];
			$initialEntity["endOffset"] = (int)$entity["start"] + strlen(utf8_decode($entity["name"]));
			$initialEntity["types"] = array();
			$type = array();
			$type["type"] = $entity["type"];
			$type["typeURI"] = "";
			$type["entityURI"] = "";
			if ($entity["link"] != NULL) {
				$type["entityURI"] = "http://wikipedia.org/wiki/" . $entity["link"];
			}
			$type["confidence"] = floatval($entity["score"]);
			array_push($initialEntity["types"], $type);
			array_push($result["initialEntities"], $initialEntity);
		}			
		return $result;	
	}

	public static function processDBpediaSpotlightApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = $entity["content"]["description"];
		//$lang = $entity->content["metadata"]["language"];
		$lang = "nl";
		$result = array();

		$result["initialEntities"] = array();
		$curlRequest = "curl -H \"Accept: application/json\"  http://spotlight.sztaki.hu:2222/rest/annotate --data-urlencode \"text=$descriptionContent\" --data \"confidence=0.3\"";
		$response = shell_exec($curlRequest);
		$response = json_decode($response, true);
		//dd($response);
		if ($response != null)
			if(isset($response["Resources"])) {
				foreach ($response["Resources"] as $extractedEntity) {
					$initialEntity = array();
					$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@surfaceForm"]);
					$initialEntity["startOffset"] = (int)$extractedEntity["@offset"];
					$initialEntity["endOffset"] = (int)$extractedEntity["@offset"] + strlen(utf8_decode($extractedEntity["@surfaceForm"]));
					$initialEntity["similarityScore"] = (float)$extractedEntity["@similarityScore"];
					$initialEntity["suport"] = $extractedEntity["@support"];
					$initialEntity["provenance"] = "dbpediaspotlight";
				
					$initialEntity["types"] = array();
				
					if (!empty($extractedEntity["@types"]) && $extractedEntity["@types"] != "") {
						$extractedTypes = explode(",", $extractedEntity["@types"]);
						foreach ($extractedTypes as $typeName) {
							$initialType = array();
							$initialType["type"] = $typeName; 
							$initialType["typeURI"] = "";
							$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $extractedEntity["@URI"]);
							array_push($initialEntity["types"], $initialType);
						}
					}
					array_push($result["initialEntities"], $initialEntity);
				}
			}
			else {

			}
	//	dd($result);
		return $result;	
	}

	public static function processNERDApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
	//	$descriptionContent = $entity->content["programSynopsis"];
	//	$lang = $entity->content["metadata"]["language"];
		$descriptionContent = $entity["content"]["description"];
		$lang = "nl";
		$apikey = Config::get('config.nerd_api_key');
		$result = array();
		$result["initialEntities"] = array();
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
		if (isset($resultArray)) {
			foreach($resultArray as $key => $value) {
				$initialEntity = array();
				$initialEntity["label"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["label"]);
				$initialEntity["startOffset"] = $value['startChar'];
				$initialEntity["endOffset"] = $value['endChar'];			
				$initialEntity["confidenceScore"] = $value["confidence"];
				$initialEntity["relevanceScore"] = $value["relevance"];
				$initialEntity["provenance"] = "nerd";
				$initialEntity["types"] = array();
				$typesArray = explode(",", $value["extractorType"]);
				foreach ($typesArray as $typeValue) {
					$initialType = array();
					$nerdTypeArray = explode("#", $value["nerdType"]);
					$initialType["type"] =  $nerdTypeArray[1];
					$initialType["typeURI"] = $value["nerdType"];
					$initialType["extractorType"] = $typeValue;
					$initialType["entityURI"] = iconv('UTF-8', 'UTF-8//IGNORE', $value["uri"]);
					$initialType["extractor"] = $value["extractor"];
					array_push($initialEntity["types"], $initialType);
				}
				array_push($result["initialEntities"], $initialEntity);
			}
		}
		
		return $result;
	}



	public function process($entity) 
	{	
		\Session::flash('flashSuccess', 'Your video description is being pre-processed');
		$retVal["thdapi"] = $this->processTDHApi($entity);	
		$retVal["textrazorapi"] = $this->processTextRazorApi($entity);
		$retVal["semitagsapi"] = $this->processSemiTagsApi($entity);
		$retVal["dbpediaspotlightapi"] = $this->processDBpediaSpotlightApi($entity);
		$retVal["nerdapi"] = $this->processNERDApi($entity);	
		return $retVal;
	}

	public function store($parentEntity, $metadataDescriptionPreprocessing)
	{
		$retVal = array();
		if (isset($metadataDescriptionPreprocessing["thdapi"])) {
			$retVal["thdapi"] = $this->storeTHDApi($parentEntity, $metadataDescriptionPreprocessing["thdapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["textrazorapi"])) {
			$retVal["textrazorapi"] = $this->storeTextRazorApi($parentEntity, $metadataDescriptionPreprocessing["textrazorapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["semitagsapi"])) {
			$retVal["semitagsapi"] = $this->storeSemiTagsApi($parentEntity, $metadataDescriptionPreprocessing["semitagsapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["nerdapi"])) {
			$retVal["nerdapi"] = $this->storeNERDApi($parentEntity, $metadataDescriptionPreprocessing["nerdapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["dbpediaspotlightapi"])) {
			$retVal["dbpediaspotlightapi"] = $this->storeDBpediaSpotlightApi($parentEntity, $metadataDescriptionPreprocessing["dbpediaspotlightapi"]);
		}
		$retVal["parentId"] = $parentEntity->_id;
		return $retVal;
	}

	public static function storeTHDApi($parentEntity, $metadataDescriptionPreprocessing) {
		
		$status = array();
		try {
			if(!SoftwareAgent::find("thdextractor"))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "thdextractor";
			$softwareAgent->label = "This component uses " . "THD API" . " in order to extract entities from texts";
			$softwareAgent->save();
		}
		//	createNamedEntitiesExtractionSoftwareAgent("thdextractor", "THD API");
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
		$title = "thdextractor-enriched-synopsis-" . $parentEntity["content"]["identifier"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-thd";
			$entity->extractor = "thd";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "Sound&Vision";
			$content = array();	
			$content["identifier"] = $parentEntity["content"]["identifier"];
			$content["description"] = $parentEntity["content"]["description"];
			$content["provenance"] = "thd";
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
			$entity->content = $content;
			$entity->hash = md5(serialize($content));
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

	public static function storeNERDApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			if(!SoftwareAgent::find("nerdextractor"))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "nerdextractor";
			$softwareAgent->label = "This component uses " . "NERD API" . " in order to extract entities from texts";
			$softwareAgent->save();
		}
		//	createNamedEntitiesExtractionSoftwareAgent("thdextractor", "THD API");
		} catch (Exception $e) {
			$status['error']['nerdextractor'] = $e->getMessage();
			return $status;
		}
		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "nerdextractor";
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error']['nerdextractor'] = $e->getMessage();
			return $status;
		}

		$tempEntityID = null;
		$title = "nerdextractor-enriched-synopsis-" . $parentEntity["content"]["identifier"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-nerd";
			$entity->extractor = "nerd";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "Sound&Vision";
			$content = array();	
			$content["identifier"] = $parentEntity["content"]["identifier"];
			$content["description"] = $parentEntity["content"]["description"];
			$content["provenance"] = "nerd";
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
			$entity->content = $content;
			$entity->hash = md5(serialize($content));
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


	public static function storeDBpediaSpotlightApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			if(!SoftwareAgent::find("dbpediaspotlightextractor"))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "dbpediaspotlightextractor";
			$softwareAgent->label = "This component uses " . "DBpediaSpotlight API" . " in order to extract entities from texts";
			$softwareAgent->save();
		}
		//	createNamedEntitiesExtractionSoftwareAgent("thdextractor", "THD API");
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
			$status['error']['dbpediaspotlightextractor'] = $e->getMessage();
			return $status;
		}

		$tempEntityID = null;
		$title = "dbpdiaspotlightextractor-enriched-synopsis-" . $parentEntity["content"]["identifier"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-dbpediaspotlight";
			$entity->extractor = "dbpdiaspotlight";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "Sound&Vision";
			$content = array();	
			$content["description"] = $parentEntity["content"]["description"];
			$content["provenance"] = "dbpediaspotlight";
			$content["identifier"] = $parentEntity["content"]["identifier"];
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
			$entity->content = $content;
			$entity->hash = md5(serialize($content));
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

	public static function storeTextRazorApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			if(!SoftwareAgent::find("textrazorextractor"))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "textrazorextractor";
			$softwareAgent->label = "This component uses " . "TextRazor API" . " in order to extract entities from texts";
			$softwareAgent->save();
		}
		//	createNamedEntitiesExtractionSoftwareAgent("thdextractor", "THD API");
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
			$status['error']['textrazorextractor'] = $e->getMessage();
			return $status;
		}

		$tempEntityID = null;
		$title = "textrazorextractor-enriched-synopsis-" . $parentEntity["content"]["identifier"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-textrazor";
			$entity->extractor = "textrazor";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "Sound&Vision";
			$content = array();	
			$content["identifier"] = $parentEntity["content"]["identifier"];
			$content["description"] = $parentEntity["content"]["description"];
			$content["provenance"] = "textrazor";
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
			$entity->content = $content;
			$entity->hash = md5(serialize($content));
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

	

	public static function storeSemiTagsApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			if(!SoftwareAgent::find("semitagsextractor"))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "semitagsextractor";
			$softwareAgent->label = "This component uses " . "SemiTags API" . " in order to extract entities from texts";
			$softwareAgent->save();
		}
		//	createNamedEntitiesExtractionSoftwareAgent("thdextractor", "THD API");
		} catch (Exception $e) {
			$status['error']['semitagsextractor'] = $e->getMessage();
			return $status;
		}
		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "semitagsextractor";
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error']['semitagsextractor'] = $e->getMessage();
			return $status;
		}

		$tempEntityID = null;
		$title = "semitagsextractor-enriched-synopsis-" . $parentEntity["content"]["identifier"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-semitags";
			$entity->extractor = "semitags";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "Sound&Vision";
			$content = array();	
			$content["identifier"] = $parentEntity["content"]["identifier"];
			$content["description"] = $parentEntity["content"]["description"];
			$content["provenance"] = "textrazor";
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
			$entity->content = $content;
			$entity->hash = md5(serialize($content));
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

	public static function createNamedEntitiesExtractionSoftwareAgent($extractor, $label){
		if(!SoftwareAgent::find($extractor))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = $extractor;
			$softwareAgent->label = "This component uses " . $label . " in order to extract entities from texts";
			$softwareAgent->save();
		}
	}
}