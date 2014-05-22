<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use URL, Session, Exception;

class FullvideoStructurer {

	public function process($fullvideo) 
	{
		\Session::flash('flashSuccess', 'Your video is being pre-processed');

		$retVal = array();
		if ($fullvideo->keyframes["count"] == 0) {
			$retVal["keyframes"] = $this->processKeyFrames($fullvideo);	
		}
		if ($fullvideo->segments["count"] == 0) {
			$retVal["segments"] = $this->processSegments($fullvideo);
		}
		return $retVal;
	}

	public function store($parentEntity, $videoPreprocessing)
	{
		$retVal = array();
		if (isset($videoPreprocessing["keyframes"])) {
			$retVal["keyframes"] = $this->storeKeyframes($parentEntity, $videoPreprocessing["keyframes"]);
		}
		if (isset($videoPreprocessing["segments"])) {
			$retVal["segments"] = $this->storeVideoSegments($parentEntity, $videoPreprocessing["segments"]);
		}
		return $retVal;
	}

	public function processKeyframes($fullvideo)
	{
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		
		$videoPath = public_path() . $fullvideo->content["storage_url"];
		$keyframesPath = public_path() . "/videostorage/keyframes/";
		$ffmpegPath = base_path() . "/app/ffmpeg";
		$ffprobePath = base_path() . "/app/ffprobe";
		
		$videoName = explode("/", $videoPath);
		$thumbnailName = substr($videoName[sizeof($videoName) - 1], 0, -4) . "_keyframe_%02d.jpg";
		
		$command = $ffmpegPath . " -i " . $videoPath . " -vf select=\"gt(scene\,0.5)\" -vsync 2 -s 320x240 -f image2 " . $keyframesPath . $thumbnailName . " -loglevel debug 2>&1 | grep \"select:1\" | cut -d \" \" -f 6 -";
		$execCommand = exec($command, $output);
		
		$files = scandir($keyframesPath);
		$keyframes = 0;
		$keyFrameNames = array();
		foreach ($files as $file) {
			if (strpos($file, substr($videoName[sizeof($videoName) - 1], 0, -4) . "_keyframe_") !== false) {
				array_push($keyFrameNames, $file);
	    			$keyframes ++;
			}
		}
		if ($keyframes != sizeof($output)) {
			return ;
		}
		
		$keyFramesStructured = array();
		for ($i = 0; $i < sizeof($keyFrameNames); $i ++) {
			$keyFramesStructured[$i] = array();
			$keyFramesStructured[$i]["storage_url"] = "/videostorage/keyframes/" . $keyFrameNames[$i];
			$keyFramesStructured[$i]["height"] = "240";
			$keyFramesStructured[$i]["width"] = "320";
		//	$timestampExtraction = explode(":", );
			$keyFramesStructured[$i]["timestamp"] = $output[$i];
		}
		
		return $keyFramesStructured;		
	}

	public function processSegments($fullvideo)
	{
		set_time_limit(5200);
		\DB::connection()->disableQueryLog();
		
		$videoPath = public_path() . $fullvideo->content["storage_url"];
		$videoSegmentsPath = public_path() . "/videostorage/segmentvideos/";
		$ffmpegPath = app_path() ."/ffmpeg";
		$ffprobePath = app_path() ."ffprobe";
		$videoName = explode("/", $videoPath);
		$segmNames = substr($videoName[sizeof($videoName) - 1], 0, -4) . "_segment_";
		$segmExtension = ".mp4";
		$duration = $this->durationToSeconds((string)$fullvideo->content["metadata"]["extent"]);
		$videoIntervals = array();
		while ($duration > 0) {
			if ($duration <= 80) {
				array_push($videoIntervals, $duration);
				$duration = 0;
			}
			else {
				array_push($videoIntervals, 50);
				$duration -= 50;
			}
		}
		$endAt = 0;
		$startFrom = 0;
		$segments = 0;
		$segmentNames = array();
		for ($i = 0; $i < sizeof($videoIntervals); $i ++) {
			$startFrom = $endAt; 
			$cutFor = $videoIntervals[$i]; 
			$endAt = $endAt + $cutFor; 
			$command = $ffmpegPath . " -ss " . $startFrom . " -i " . $videoPath . " -t " . $cutFor . " -acodec copy -vcodec copy " . $videoSegmentsPath . $segmNames . $i . $segmExtension;
			$execCommand = exec($command, $output);
			array_push($segmentNames, $segmNames . $i . $segmExtension);
			$segments ++;
		}

		$videoSegmentStructured = array();
		$start_time = 0;
		$end_time = 0;
		for ($i = 0; $i < sizeof($segmentNames); $i ++) {
			$videoSegmentStructured[$i] = array();
			$videoSegmentStructured[$i]["storage_url"] = "/videostorage/segmentvideos/" . $segmentNames[$i];

			$timeProcess = exec($ffmpegPath . " -i " . $videoSegmentsPath . $segmentNames[$i] . " 2>&1 | awk '/Duration/ {split($2,a,\":\");print a[1]*3600+a[2]*60+a[3]}'", $output);

		
			$videoSegmentStructured[$i]["duration"] = $output[0];
			$videoSegmentStructured[$i]["start_time"] = $start_time;
			$end_time += $videoIntervals[$i];
			$videoSegmentStructured[$i]["end_time"] = $end_time;
			$start_time = $end_time;
			$output = 0;
		}
		
		return $videoSegmentStructured;		
	}

	public function storeKeyframes($parentEntity, $keyframeExtraction) 
	{
		$tempEntityID = null;
		$status = array();

		try {
			$this->createKeyframesExtractionSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['keyframeextraction'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "keyframeextraction";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		for ($i = 0; $i < sizeof($keyframeExtraction); $i ++){
			$keyFrameName = explode("/", $keyframeExtraction[$i]["storage_url"]);
			$title = $keyFrameName[sizeof($keyFrameName) - 1];

			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "image";
				$entity->documentType = "keyframe";
				$entity->parents = array($parentEntity->_id);
				$entity->source = $parentEntity->source;
				$entity->content = $keyframeExtraction[$i];

				//unset($twrexStructuredSentenceKeyVal['properties']);
				$entity->hash = md5(serialize($keyframeExtraction[$i]));
				$entity->activity_id = $activity->_id;
				$entity->save();
				
				$status['success'][$title] = $title . " was successfully processed into a key frame. (URI: {$entity->_id})";
				
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}

			$tempEntityID = $entity->_id;
		}

		$status['success']['noEntitiesCreated'] = sizeof($keyframeExtraction);
		return $status;
	}

	public function storeVideoSegments($parentEntity, $videoSegmenting)
	{
		$tempEntityID = null;
		$status = array();

		try {
			$this->createVideoSegmentingSoftwareAgent();
		} catch (Exception $e) {
			$status['error']['videosegmenting'] = $e->getMessage();
			return $status;
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = "videosegmenting";
			$activity->save();

		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			$status['error'][$title] = $e->getMessage();
			return $status;
		}

		for ($i = 0; $i < sizeof($videoSegmenting); $i ++){
			$videoSegmentName = explode("/", $videoSegmenting[$i]["storage_url"]);
			$title = $videoSegmentName[sizeof($videoSegmentName) - 1];

			try {
				$entity = new Entity;
				$entity->_id = $tempEntityID;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = "video";
				$entity->documentType = "videosegment";
				$entity->parents = array($parentEntity->_id);
				$entity->source = $parentEntity->source;
				$entity->content = $videoSegmenting[$i];

				//unset($twrexStructuredSentenceKeyVal['properties']);
				$entity->hash = md5(serialize($videoSegmenting[$i]));
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into a video segment. (URI: {$entity->_id})";
				
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}

			$tempEntityID = $entity->_id;
		}

		$status['success']['noEntitiesCreated'] = sizeof($videoSegmenting);
		//dd($status);
		return $status;
	}

	public function createKeyframesExtractionSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('keyframeextraction'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "keyframeextraction";
			$softwareAgent->label = "This component (pre)processes video documents by extracting the key frames";
			$softwareAgent->save();
		}
	}

	public function createVideoSegmentingSoftwareAgent(){
		if(!\MongoDB\SoftwareAgent::find('videosegmenting'))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = "videosegmenting";
			$softwareAgent->label = "This component (pre)processes video documents by splitting the video into segments";
			$softwareAgent->save();
		}
	}
	
	public function durationToSeconds($duration) {
		$durationEntities = explode(":", $duration);
		$retVal = (int)$durationEntities[0] * 3600 + (int)$durationEntities[1] * 60 + (int)$durationEntities[2];
		return $retVal;
	}	
	
}
