<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use URL, Session, Exception;

class MetadatadescriptionStructurer {

	private $urlTDH = "http://entityclassifier.eu/thd/api/v2/extraction?";

	public function processTDHApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();

		$descriptionContent = $entity->content["description"];
	//	dd($descriptionContent);
		$lang = $entity->language;
		$format = "json";
		$entity_type = array("ne");
		$priority_entity_linking = "true";
		$apikey = "150284785c5a49709990f973ec825d1e";
		$result = array();
		$result["entities"] = array();
		$entities = array();
		//for ($i = 0; $i < count($entity_type); $i ++) {
			$output = "";
			$urlReq = $this->urlTDH . "apikey=". $apikey . "&format=" . $format . "&lang=" . $lang . "&entity_type=ne&priority_entity_linking=" . $priority_entity_linking;
			
			$curlRequest = "curl -v \"" . $urlReq . "\" -d \"" . $descriptionContent . "\"";
			$response = exec($curlRequest, $output);
			$responseEntities = json_decode($output[0], true);	
			//dd($output[0]);
			for ($j = 0; $j < count($responseEntities); $j++) {
				$entity = array();
				$entity["value"] = $responseEntities[$j]["underlyingString"];
				$entity["startOffset"] = $responseEntities[$j]["startOffset"];
				$entity["endOffset"] = $responseEntities[$j]["endOffset"];
				$entity["types"] = array();
		
				for ($k = 0; $k < count($responseEntities[$j]["types"]); $k++) {
					$type = array();
					$type["typeLabel"] = $responseEntities[$j]["types"][$k]["typeLabel"];
					$type["typeURI"] = $responseEntities[$j]["types"][$k]["typeURI"];
					$type["entityURI"] = $responseEntities[$j]["types"][$k]["entityURI"];
					$type["provenance"] = $responseEntities[$j]["types"][$k]["provenance"];
					$type["confidence"]["score"] = $responseEntities[$j]["types"][$k]["confidence"]["value"];
					$type["confidence"]["bounds"] = $responseEntities[$j]["types"][$k]["confidence"]["bounds"];
					array_push($entity["types"], $type);
				}
				array_push($result["entities"], $entity);
			}
			$array = array_map("unserialize", array_unique(array_map("serialize", $result["entities"])));
		//	dd($array);

		//}
		return $array;	
	}

	public function processTextRazorApi($entity) {
		require_once('TextRazor.php');
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();

		$call_type = array("entities");
		$text = $entity->content["description"];
		$textrazor = new TextRazor("e6438f10fc2f974ff0a5b97969a4296fe637da043237aba49569bd58");
		$textrazor->addExtractor('entities,topics');

		$response = $textrazor->analyze($text);
		$result = array();

		if (isset($response['response']['entities'])) {
    		$result["entities"] = array();
    		$entities = array();
    	//	dd($response['response']['entities']);
			for ($i = 0; $i < count($response['response']['entities']); $i ++) {
				$entity = array();
				$entity["value"] = $response['response']['entities'][$i]["matchedText"];
				$entity["startOffset"] = $response['response']['entities'][$i]["startingPos"];
				$entity["endOffset"] = $response['response']['entities'][$i]["endingPos"];
				$entity["types"] = array();
				
				if (isset($response['response']['entities'][$i]["type"]))
					for ($k = 0; $k < count($response['response']['entities'][$i]["type"]); $k++) {
						$type = array();
						$type["typeLabel"] = $response['response']['entities'][$i]["type"][$k];
						$type["typeURI"] = "http://dbpedia.org/ontology/" . $response['response']['entities'][$i]["type"][$k];
						$type["entityURI"] = $response['response']['entities'][$i]["wikiLink"];
						$type["provenance"] = "dbpedia";
						$type["confidence"]["score"] = $response['response']['entities'][$i]["confidenceScore"];
						$type["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);
					}

				if (isset($response['response']['entities'][$i]["freebaseTypes"]))
					for ($k = 0; $k < count($response['response']['entities'][$i]["freebaseTypes"]); $k++) {
						$type = array();
						$type["typeLabel"] = $response['response']['entities'][$i]["freebaseTypes"][$k];
						$type["typeURI"] = "http://www.freebase.com" . $response['response']['entities'][$i]["freebaseTypes"][$k];
						$type["entityURI"] = $response['response']['entities'][$i]["wikiLink"];
						$type["provenance"] = "freebase";
						$type["confidence"]["score"] = $response['response']['entities'][$i]["confidenceScore"];
						$type["confidence"]["bounds"] = null;
						array_push($entity["types"], $type);
					}
				array_push($result["entities"], $entity);
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
		return $result;	
	}

	public function processSemiTagsApi($entity) {
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();

		$descriptionContent = $entity->content["description"];
		$tempFile = public_path() . "/temp.txt";
		$fh = fopen($tempFile, 'w') or die("Can't create file");
		file_put_contents($tempFile, $descriptionContent);
		$lang = $entity->language;
		
		$apikey = "9u0sd79j21vpvv0tqin1oleb4di32oo6";
		$result = array();
		$result["entities"] = array();
		$entities = array();
		//dd($descriptionContent);
		$curlRequest = "curl -i -X POST http://nerd.eurecom.fr/api/document -d \"text=" . addslashes($descriptionContent) . "&key=" . $apikey . "\"";
		//dd($curlRequest);
		$response = exec($curlRequest, $output);
		$documentId = "";
		//$resultArray = json_decode($output, true);
		//dd($output);
		if (strpos($output[count($output) - 1], 'idDocument') !== false) {
			$documentId = explode("}", explode(":", $output[count($output) - 1])[1])[0];
		//	dd($documentId);
		}
		
		$annotationId = "";
		$curlRequest = "curl -i -X POST \"http://nerd.eurecom.fr/api/annotation\" -d \"key=" . $apikey . "&idDocument=" . $documentId . "&extractor=semitags&ontology=extended&timeout=10\"";
		$response = exec($curlRequest, $output);
		//dd($curlRequest);
		if (strpos($output[count($output) - 1], 'idAnnotation') !== false) {
			$annotationId = explode("}", explode(":", $output[count($output) - 1])[1])[0];
		//	dd($annotationId);
		}

		$curlRequest = "curl -i -X GET -H \"Accept: application/json\" \"http://nerd.eurecom.fr/api/entity?key=" . $apikey . "&idAnnotation=" . $annotationId . "&granularity=oen\"";
		$response = exec($curlRequest, $output);
		//dd($output);
		$resultArray = json_decode($output[count($output) - 1], true);
		
		foreach($resultArray as $key => $value) {
			$entity = array();
			$entity["value"] = $value["label"];
			$entity["startOffset"] = $value['startChar'];
			$entity["endOffset"] = $value['endChar'];
			$entity["types"] = array();
			$type = array();
			$type["typeLabel"] = $value["extractorType"];
			$type["typeURI"] = $value["nerdType"];
			$type["entityURI"] = $value["uri"];
			$type["provenance"] = "dbpedia";
			$type["confidence"]["score"] = $value["confidence"];
			$type["confidence"]["bounds"] = null;
			array_push($entity["types"], $type);
			array_push($result["entities"], $entity);
		}
	//	dd($result);
		return $result;	
	}

	public function process($entity) 
	{
		$retVal = array();
		$retVal["tdhapi"] = $this->processTDHApi($entity);	
		$retVal["textrazorapi"] = $this->processTextRazorApi($entity);
	//	if ($entity->language != "en") {
	//		$retVal["semitagsapi"] = $this->processSemiTagsApi($entity);
	//	}
	//	$retVal["nerdapi"] = $this->processNERDApi($entity);
		return $retVal;

	}

	public function store($parentEntity, $metadataDescriptionPreprocessing)
	{
		$retVal = array();
		if (isset($metadataDescriptionPreprocessing["tdhapi"])) {
			$retVal["tdhapi"] = $this->storeTDHApi($parentEntity, $metadataDescriptionPreprocessing["tdhapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["textrazorapi"])) {
			$retVal["textrazorapi"] = $this->storeTextRazorApi($parentEntity, $metadataDescriptionPreprocessing["textrazorapi"]);
		}
		if (isset($metadataDescriptionPreprocessing["semitagsapi"])) {
			$retVal["semitagsapi"] = $this->storeSemiTagsApi($parentEntity, $metadataDescriptionPreprocessing["semitagsapi"]);
		}
		return $retVal;
	}

	public function storeTextRazorApi($parentEntity, $metadataDescriptionPreprocessing) {
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

				$entity->hash = md5(serialize($entity->content));
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
			$status['error'][$title] = $e->getMessage();
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
				$entity->hash = md5(serialize($entity->content));
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
				$entity->hash = md5(serialize($entity->content));
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
}