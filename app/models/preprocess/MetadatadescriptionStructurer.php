<?php

namespace Preprocess;
require_once 'sparqllib.php';

use URL, Session, Exception, Config;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;
use \Entity as Entity;
use \Entities\Unit as Unit;

class MetadatadescriptionStructurer {

	public function object_to_array_data($data) {
	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] = $this->object_to_array_data($value);
	        }
	        return $result;
	    }
	    return $data;
	}

	public function processTDHApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$urlTDH = "http://entityclassifier.eu/thd/api/v2/extraction?";
	//	$descriptionContent = urlencode($entity->content["metadata"]["abstract"]["nl"]);
		$descriptionContent = urlencode($entity["content"]["programSynopsis"]);
	//	$lang = $entity->content["metadata"]["language"];
		$lang = "en";
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
					$initialType["typeConfidence"] = array();
					$initialType["typeConfidence"]["score"] = $responseEntities[$j]["types"][$k]["classificationConfidence"]["value"];
					$initialType["resourceConfidence"] = array();
					$initialType["resourceConfidence"]["score"] = $responseEntities[$j]["types"][$k]["linkingConfidence"]["value"];
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

	public function processTextRazorApi($entity) {
		require_once('TextRazor.php');
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$call_type = array("entities");
		$text = $entity->content["programSynopsis"];
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

	public function processSemiTagsApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = urlencode($entity->content["programSynopsis"]);
		//$lang = $entity->content["language"];
		$lang = "en";
		$result = array();
		$result["initialEntities"] = array();
		$curlRequest = "curl -d \"language=" . $lang . "&text=$descriptionContent\" http://nlp.vse.cz:8081/recognize";
		$response = exec($curlRequest, $output);
		$response = json_decode($output[0]);
		$response = $this->object_to_array_data($response);

		foreach ($response as $entity) {
			$initialEntity = array();
			$initialEntity["label"] = $entity["name"];
			$initialEntity["startOffset"] = (int)$entity["start"];
			$initialEntity["endOffset"] = (int)$entity["start"] + strlen(utf8_decode($entity["name"]));
			$initialEntity["types"] = array();
			$initialEntity["types"]["type"] = $entity["type"];
			$initialEntity["types"]["typeURI"] = "";
			$initialEntity["types"]["entityURI"] = "";
			if ($entity["link"] != NULL) {
				$initialEntity["types"]["entityURI"] = "http://wikipedia.org/wiki/" . $entity["link"];
			}
			$initialEntity["types"]["confidence"] = floatval($entity["score"]);
			array_push($result["initialEntities"], $initialEntity);
		}			
		return $result;	
	}

	public function processDBpediaSpotlightApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = $entity->content["programSynopsis"];
		//$lang = $entity->content["metadata"]["language"];
		$lang = "en";
		$result = array();

		$result["initialEntities"] = array();
		$curlRequest = "curl -H \"Accept: application/json\"  http://spotlight.sztaki.hu:2222/rest/annotate --data-urlencode \"text=$descriptionContent\" --data \"confidence=0.35\"";
		$response = shell_exec($curlRequest);
		$response = json_decode($response, true);
		//dd($response);
		if ($response != null)
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
	//	dd($result);
		return $result;	
	}

	public function processNERDApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		$descriptionContent = $entity->content["programSynopsis"];
	//	$lang = $entity->content["metadata"]["language"];
	//	$descriptionContent = $entity["content"]["programSynopsis"];
		$lang = "en";
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

	public function storeTHDApi($parentEntity, $metadataDescriptionPreprocessing) {
		
		$status = array();
		try {
			$this->createNamedEntitiesExtractionSoftwareAgent("thdextractor", "THD API");
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
		$title = "thdextractor-enriched-synopsis-" . $parentEntity["content"]["programId"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-thd";
			$entity->extractor = "thd";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "BBC";
			$content = array();	
			$content["programSynopsis"] = $parentEntity["content"]["programSynopsis"];
			$content["programId"] = $parentEntity["content"]["programId"];
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
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
		$tempEntityID = $entity->_id;
		return $status;
	}

	public function storeNERDApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			$this->createNamedEntitiesExtractionSoftwareAgent("nerdextractor", "NERD API");
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
		$title = "nerdextractor-enriched-synopsis-" . $parentEntity["content"]["programId"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-nerd";
			$entity->extractor = "nerd";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "BBC";
			$content = array();	
			$content["programSynopsis"] = $parentEntity["content"]["programSynopsis"];
			$content["programId"] = $parentEntity["content"]["programId"];
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
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
		$tempEntityID = $entity->_id;
		return $status;
	}


	public function storeDBpediaSpotlightApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			$this->createNamedEntitiesExtractionSoftwareAgent("dbpediaspotlightextractor", "DBPEDIASPOTLIGHT API");
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
		$title = "dbpediaspotlight-enriched-synopsis-" . $parentEntity["content"]["programId"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-dbpediaspotlight";
			$entity->extractor = "dbpediaspotlight";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "BBC";
			$content = array();	
			$content["programSynopsis"] = $parentEntity["content"]["programSynopsis"];
			$content["programId"] = $parentEntity["content"]["programId"];
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
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
		$tempEntityID = $entity->_id;
		return $status;
	}

	public function storeTextRazorApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			$this->createNamedEntitiesExtractionSoftwareAgent("textrazorextractor", "TEXTRAZOR API");
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
		$title = "textrazorextractor-enriched-synopsis-" . $parentEntity["content"]["programId"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-textrazorextractor";
			$entity->extractor = "textrazorextractor";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "BBC";
			$content = array();	
			$content["programSynopsis"] = $parentEntity["content"]["programSynopsis"];
			$content["programId"] = $parentEntity["content"]["programId"];
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
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
		return $status;
	}

	

	public function storeSemiTagsApi($parentEntity, $metadataDescriptionPreprocessing) {
		$status = array();
		try {
			$this->createNamedEntitiesExtractionSoftwareAgent("semitagsextractor", "SEMITAGS API");
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
		$tempEntityID = null;
		$title = "semitagsextractor-enriched-synopsis-" . $parentEntity["content"]["programId"];
		try {
			$entity = new Unit;
			$entity->_id = $tempEntityID;
			$entity->title = strtolower($title);
			$entity->type = "unit";
			$entity->project = $parentEntity["project"];
			$entity->documentType = "enriched-synopsis-semitagsextractor";
			$entity->extractor = "semitagsextractor";
			$entity->parents = array($parentEntity["_id"]);
			$entity->source = "BBC";
			$content = array();	
			$content["programSynopsis"] = $parentEntity["content"]["programSynopsis"];
			$content["programId"] = $parentEntity["content"]["programId"];
			$content["named-entities"] = $metadataDescriptionPreprocessing["initialEntities"];
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
		return $status;
	}

	public function createNamedEntitiesExtractionSoftwareAgent($extractor, $label){
		if(!SoftwareAgent::find($extractor))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = $extractor;
			$softwareAgent->label = "This component uses " . $label . " in order to extract entities from texts";
			$softwareAgent->save();
		}
	}
}