<?php

namespace mongo\text;

use Moloquent, Schema, URL, File, Exception, Auth, User;

class Helper extends Moloquent {

	public function storeFiles(array $files, $domainType, $documentType, $incrementIfExists = false){
		if(!Schema::connection('mongodb_text')->hasCollection('activities'))
			Activity::createSchema();

		if(!Schema::connection('mongodb_text')->hasCollection('entities'))
			Entity::createSchema();
								
		$user = Auth::user();
		$status = array();

		foreach($files as $file){
			$fileName = $file->getClientOriginalName();

			$activityURI = URL::to('/resource/text/activity') . '/' . $domainType . '/' . $fileName;

			if($incrementIfExists){
				$activityIncrement = 0;
				while(Activity::withTrashed()->find($activityURI)){
					$activityIncrement++;
					$fileNameWithIncrement = $fileName . "_" . $activityIncrement;
					$activityURI = URL::to('/resource/text/activity') . '/' . $domainType . '/' . $fileNameWithIncrement;
				}

				if($activityIncrement > 0)
					$fileName = $fileNameWithIncrement;
			}

			try {
				$activity = new Activity;
				$activity->_id = strtolower($activityURI);
				$activity->type = "fileupload";
				$activity->label = "This file was uploaded through the web interface.";
				$activity->user_id = $user->_id;
				$activity->software_id = URL::to('files/upload');
				$activity->save();

			} catch (Exception $e) {
				// Something went wrong with creating the Activity
				$status['error'][$fileName] = $e->getMessage();
				continue;
			}


			$entityURI = URL::to('/resource/text/entity') . '/' . $domainType . '/' . $fileName;

			try {
				$entity = new Entity;
				$entity->_id = strtolower($entityURI);
				$entity->title = strtolower($fileName);
				$entity->extension = $file->getClientOriginalExtension();
				$entity->domain = strtolower($domainType);
				$entity->fileType = "text";
				$entity->documentType = strtolower($documentType);
				$entity->parent_id = null;
				$entity->ancestors = null;
				$entity->activity_id = strtolower($activityURI);
				$entity->user_id = $user->_id;
				$entity->content = File::get($file->getRealPath());
				$entity->save();

				$status['success'][$fileName] = $fileName . " was successfully uploaded. (URI: {$entityURI})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$activity->forceDelete();
				$entity->forceDelete();
				$status['error'][$fileName] = $e->getMessage();
			}
		}
		return $status;
	}
}