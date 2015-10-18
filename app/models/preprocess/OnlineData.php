<?php

use \CurlRequest\SVBasicRequest as SVRequest;
//use Moloquent, Schema, URL, File, Exception;

class OnlineData extends Moloquent {
	private $url = "http://openbeelden.nl/feeds/oai/?";

	public function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if ($length == 0) {
        		return true;
    		}
    		return (substr($haystack, -$length) === $needle);
	}
	
	public function downloadFile($url) {
		$videoPathSplit = explode("/", (string)$url);
		$videoName = $videoPathSplit[sizeof($videoPathSplit) - 1];
		$path = public_path() . "/videostorage/fullvideos/" . $videoName;
 		$fp = fopen($path, 'w');
 
    		$ch = curl_init($url);
    		curl_setopt($ch, CURLOPT_FILE, $fp);
 
    		$data = curl_exec($ch);
 		
    		curl_close($ch);
    		fclose($fp);
	}


	public function listRecords($parameters, $noEntries, &$listOfRecords) {
		
		$curlRequest = new SVRequest;

		$url = $this->url . "verb=ListRecords";
		if (isset($parameters)) {
			if (!array_key_exists("metadataPrefix", $parameters)) {
				if (!array_key_exists("resumptionToken", $parameters)) {
					throw new Exception("Request must contain -metadataPrefix- parameter!");
				}
				else {
					foreach ($parameters as $param => $value) {
						$url .= "&" . $param . "=" . $value;
					}
				}
			}
			else {
				foreach ($parameters as $param => $value) {
					$url .= "&" . $param . "=" . $value;
				}
			}
		}
		else {
			throw new Exception('Request parameters missing!');
		}

		$entities = Entity::where('documentType', 'fullvideo')->lists("title");

		while ($noEntries > 0) {
		    //throw new Exception($noEntries);	
			$result = $curlRequest->curlRequest($url, "POST", null);
			$xml = simplexml_load_string($result["result"]);
			if ($xml === false) {
    				die('Error parsing XML');   
			}
			else {	
				$xmlNode = $xml->ListRecords;

				if (isset($xmlNode)){
					foreach ($xmlNode->record as $rNode) {

    					if(strpos((string)$rNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->type, "Moving Image") !== false) {
							if ($noEntries > 0) {

								if (!in_array((string)$rNode->header->identifier, $entities)) {
									array_push($listOfRecords, (string)$rNode->header->identifier);
									array_push($entities, (string)$rNode->header->identifier);
								//	dd($entities);
									$noEntries --;
								}
								else {
									continue;
								}
							}
							else {
								break;
							}
						}
					}
				}
				
				if (isset($xml->ListRecords->resumptionToken)) {
					if ($noEntries > 0) {
						if(!array_key_exists("resumptionToken", $parameters)) {
							$parameters["resumptionToken"] = (string)$xml->ListRecords->resumptionToken;
							unset($parameters["metadataPrefix"]);
							$this->listRecords($parameters, $noEntries, $listOfRecords);
						}
						else {
							$replacement = array("resumptionToken" => (string)$xml->ListRecords->resumptionToken);
							$parameters = array_replace($parameters, $replacement);
							$this->listRecords($parameters, $noEntries, $listOfRecords);
						}
					}
				}
			}
		}
	//	dd($listOfRecords);
	//	return $listOfRecords;
	}

	public function getRecord($recordId, $metadataPrefix) {
		
		$curlRequest = new SVRequest;
		$record = array();
		$url = $this->url . "verb=GetRecord";
		
		if (!isset($metadataPrefix)) {
			throw new Exception("Request must contain -metadataPrefix- parameter");
		}
		else {

			$url .= "&metadataPrefix=" . $metadataPrefix . "&identifier=" . $recordId;
			$result = $curlRequest->curlRequest($url, "POST", null);
			$xml = simplexml_load_string($result["result"]);

			if ($xml === false) {
    				die('Error parsing XML');   
			}
			else {
				$xmlNode = $xml->GetRecord->record;

				if (isset($xmlNode)) {
				//	$ancestors = array();
				//	$record["ancestors"] = $ancestors;
					$content = array("identifier" => $recordId, "datestamp" => (string)$xmlNode->header->datestamp, "specSet" => "beeldengeluid");
					$searchInFile = false;
					if(file_exists(public_path(). "/json/" . $recordId . ".json")) {
						$file = file_get_contents(public_path(). "/json/" . $recordId . ".json");
						$searchInFile = true;
					}

					$metadata = array();
					$titleJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->title as $title) {
						foreach ($title->attributes('xml', TRUE) as $lang => $value) {
							$titleJson[(string)$value] = (string)$title;
						}
					}
					$metadata["title"] = $titleJson;
				
					$alternativeJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->alternative as $alternative) {
						foreach ($alternative->attributes('xml', TRUE) as $lang => $value) {
							$alternativeJson[(string)$value] = (string)$alternative;
						}
					}
					$metadata["alternativeTitle"] = $alternativeJson;

					$creatorJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->creator as $creator) {
						foreach ($creator->attributes('xml', TRUE) as $lang => $value) { 
							$creatorJson[(string)$value] = (string)$creator;
						}
					}
					$metadata["creator"] = $creatorJson;

					$subjectJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->subject as $subject) {
						foreach ($subject->attributes('xml', TRUE) as $lang => $value) { 
							if (array_key_exists((string)$value, $subjectJson)) {
								$newValue = $subjectJson[(string)$value];
								array_push($newValue, (string)$subject);
								$subjectJson[(string)$value] = $newValue;
							}
							else {
								$subjectJson[(string)$value] = array();
								array_push($subjectJson[(string)$value], (string)$subject);
							}
						}
					}

					if ($searchInFile == true)
					if (isset($file['selecties'][0]['niveaus'][0]["trefwoorden"])) {
						if (!array_key_exists("nl", $subjectJson)) {
							$subjectJson["nl"] = array();
						}
						foreach ($file['selecties'][0]['niveaus'][0]["trefwoorden"] as $keywordSubject) {
							if (!in_array($keywordSubject["trefwoord"], $subjectJson["nl"])) {
								array_push($subjectJson["nl"], $keywordSubject["trefwoord"]);
							}
						}
					}
					if ($searchInFile == true)
					if(isset($file['selecties'][0]['niveaus'][0]["namen"])) {
						if (!array_key_exists("nl", $subjectJson)) {
							$subjectJson["nl"] = array();
						}
						foreach ($file['selecties'][0]['niveaus'][0]["namen"] as $keywordSubject) {
							if (!in_array($keywordSubject, $subjectJson["nl"])) {
								array_push($subjectJson["nl"], $keywordSubject);
							}
						}
					}

					$metadata["subject"] = $subjectJson;

					$descriptionJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->description as $description) {
						foreach ($description->attributes('xml', TRUE) as $lang => $value) { 
							$descriptionJson[(string)$value] = (string)$description;
						}
					}
					$metadata["description"] = $descriptionJson;

					$abstractJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->abstract as $abstract) {
						foreach ($abstract->attributes('xml', TRUE) as $lang => $value) { 
							$abstractJson[(string)$value] = (string)$abstract;
						}
					}
					$metadata["abstract"] = $abstractJson;

					$publisherJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->publisher as $publisher) {
						$attr = $publisher->attributes('xml', TRUE);
						if (!isset($attr['lang'])) {
							$publisherJson["source"] = (string)$publisher;
						}
						else {
							$publisherJson["name"] = (string)$publisher;
						}
					}

					$metadata["publisher"] = $publisherJson;	

					$date = $xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->date;
					$metadata["date"] = (string)$date;
		
					$extent = $xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->extent;
					$interval = new DateInterval((string)$extent);
					$metadata["extent"] = $interval->format('%H:%I:%S');

					$added = false;
					$videoName = "";
					$mediumJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->medium as $medium) {
						foreach ($medium->attributes() as $format => $value) { 
							if (array_key_exists((string)$value, $mediumJson)) {
								$newValue = $mediumJson[(string)$value];
								array_push($newValue, (string)$medium);
								$mediumJson[(string)$value] = $newValue;
							}
							else {
								$mediumJson[(string)$value] = array();
								array_push($mediumJson[(string)$value], (string)$medium);
							}
	
							if ($added == false) {
								if ($this->endsWith((string)$medium, ".mp4")) {
									$added = true;
									//echo $identifier . "--------" . (string)$medium . "\n";
									$videoPathSplit = explode("/", (string)$medium);
									$videoName = $videoPathSplit[sizeof($videoPathSplit) - 1];
									$content["storage_url"] = "/videostorage/fullvideos/" . $videoName;
									$this->downloadFile((string)$medium);
									$metadata["online_url"] = (string)$medium;
								}
							}						
				//			echo (string)$value . "--" . (string)$medium . "\n";
	
						}
					}
					$metadata["medium"] = $mediumJson;
					$content["videoName"] = $videoName;
					
					$sourceJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->source as $source) {
						foreach ($source->attributes('xml', TRUE) as $lang => $value) { 
							$sourceJson[(string)$value] = (string)$source;
						}
					}
					$metadata["source"] = $sourceJson;

					$language = $xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->language;
					$metadata["language"] = (string)$language;

					$spatialJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->spatial as $spatial) {
						foreach ($spatial->attributes('xml', TRUE) as $lang => $value) { 
							if (array_key_exists((string)$value, $spatialJson)) {
								$newValue = $spatialJson[(string)$value];
								array_push($newValue, (string)$spatial);
								$spatialJson[(string)$value] = $newValue;
							}
							else {
								$spatialJson[(string)$value] = array();
								array_push($spatialJson[(string)$value], (string)$spatial);
							}
						}
					}
					if ($searchInFile == true)
					if (isset($file['selecties'][0]['niveaus'][0]['geografische_namen'])) {
						if (!array_key_exists("nl", $spatialJson)) {
							$spatialJson["nl"] = array();
						}
						foreach ($file['selecties'][0]['niveaus'][0]['geografische_namen'] as $location) {
							if (!in_array($location, $spatialJson["nl"])) {
								array_push($spatialJson["nl"], $location);
							}
						}
					}
					$metadata["spatial"] = $spatialJson;

					$peopleArray = array();
					$peopleArray["nl"] = array();
					if ($searchInFile == true)
					if (isset($file['selecties'][0]['niveaus'][0]['persoonsnamen']))
						foreach ($file['selecties'][0]['niveaus'][0]['persoonsnamen'] as $person) {
								array_push($peopleArray["nl"], $person);
						}
					$metadata["people"] = $peopleArray;

					$attributionNameJson = array();
					foreach ($xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->attributionName as $attributionName) {
						foreach ($attributionName->attributes('xml', TRUE) as $lang => $value) { 
							if (array_key_exists((string)$value, $attributionNameJson)) {
								$newValue = $attributionNameJson[(string)$value];
								array_push($newValue, (string)$attributionName);
								$attributionNameJson[(string)$value] = $newValue;
							}
							else {
								$attributionNameJson[(string)$value] = array();
								array_push($attributionNameJson[(string)$value], (string)$attributionName);
							}
						}
					}
					$metadata["attributionName"] = $attributionNameJson;


					$attributionURL = $xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->attributionURL;
					$metadata["attributionURL"] = (string)$attributionURL;

					$license = $xmlNode->metadata->children('oai_oi', 1)->oi->children('oi', 1)->license;
					$metadata["license"] = (string)$license;
				}
			}
			$content["metadata"] = $metadata;
			$record["content"] = $content;
		}
		
		return $record;
		
	}


	public function storeVideoDescription ($parentEntity) {
		$title = "Description: " . $parentEntity->title;
		$status = array();

		try {
			$this->createOpenimagesVideoDescriptionExtractorSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['OnlineDataVideoDescr'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "videodescriptiongetter";
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$status['error']['OnlineDataVideoDescr'] = $e->getMessage();
			$activity->forceDelete();	
			return $status;
		}
		//dd($parentEntity->title);
		$languageValues = $parentEntity->content["metadata"]["abstract"];
		foreach($languageValues as $lang => $value) {
			if ($value != "" || $value != NULL) {
			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->title = $lang . '.' . strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "text";
				$entity->documentType = "metadatadescription";
				$entity->source = "openimages";
				$entity->videoContent = $parentEntity->content["storage_url"];
				$entity->videoTitle = $parentEntity->content["metadata"]["title"][$lang];
				$entity->language = $lang;
				$entity->parents = array($parentEntity->_id);
				
				$content = array();
				$content["description"] = $value;
				
				$entity->content = $content;

				$preprocessed = array();
				$preprocessed["automatedEntities"] = false;
				$preprocessed["automatedEvents"] = false;
				$entity->preprocessed = $preprocessed;
				$entity->tags = array("unit");
				$entity->hash = md5(serialize([$entity->content]));				
				$entity->activity_id = $activity->_id;  
				$entity->save();
				Queue::push('Queues\UpdateUnits', [$entity->_id]);
				$status['success'][$title] = $title . " was successfully uploaded. (URI: {$entity->_id})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$activity->forceDelete();
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}
			}		
		}

		return $status;
	}


	public function store ($format, $domain, $documentType, $parameters, $noOfVideos) {
		//fastcgi_finish_request();
		$listOfVideoIdentifiers = array();
		$this->listRecords($parameters, $noOfVideos, $listOfVideoIdentifiers);
		
	//	dd("done");
		$status = array();

		try {
			$this->createOpenimagesVideoGetterSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['OnlineData'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "openimagesgetter";
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$status['error']['OnlineData'] = $e->getMessage();
			$activity->forceDelete();	
			return $status;
		}
		$count["count"] = 0;

		foreach($listOfVideoIdentifiers as $video){
			$title = $video;
			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->title = strtolower($title);
				$entity->domain = $domain;
				$entity->format = $format;
				$entity->documentType = $documentType;
				$entity->source = "openimages";
				$videoMetadata = $this->getRecord($video, $parameters["metadataPrefix"]);
				$entity->content = $videoMetadata["content"];	
				$parents = array();
				$entity->parents = $parents;
				$entity->tags = array("unit");
				$entity->segments = $count;
				$entity->keyframes = $count;
				$entity->hash = md5(serialize([$entity->content]));				
				$entity->activity_id = $activity->_id;  
				$entity->save();
				Queue::push('Queues\UpdateUnits', [$entity->_id]);
				$status['success'][$title] = $title . " was successfully uploaded. (URI: {$entity->_id})";
	
				if (isset($status['success'])) {
					$this->storeVideoDescription($entity);
				//	dd($this->storeVideoDescription($entity));
				}
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$activity->forceDelete();
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}	
		}		
		$status["recno"] = 	count($listOfVideoIdentifiers);
		return $status;
	}

	public function createOpenimagesVideoGetterSoftwareAgent(){
		if(!SoftwareAgent::find('openimagesgetter'))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "openimagesgetter";
			$softwareAgent->label = "This component is used for getting the videos stored in openimages.nl and storing them as documents within MongoDB";
			$softwareAgent->save();
		}
	}
	
	public function createOpenimagesVideoDescriptionExtractorSoftwareAgent(){
		if(!SoftwareAgent::find('videodescriptiongetter'))
		{
			$softwareAgent = new SoftwareAgent;
			$softwareAgent->_id = "videodescriptiongetter";
			$softwareAgent->label = "This component is used for getting the videos description from openimages.nl and storing them as documents within MongoDB";
			$softwareAgent->save();
		}
	}
}