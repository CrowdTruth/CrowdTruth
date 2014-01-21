<?php

namespace mongo\text;

use Moloquent, Schema, URL, File, Exception, Auth, User;

class Helper extends Moloquent {

	public function storeFiles(array $files, $domain, $documentType, $incrementIfExists = false){
		if(!Schema::connection('mongodb_text')->hasCollection('activities'))
			Activity::createSchema();

		if(!Schema::connection('mongodb_text')->hasCollection('entities'))
			Entity::createSchema();
								
		$user = Auth::user();
		$status = array();

		foreach($files as $file){
			$title = $file->getClientOriginalName();

			$activityURI = "type=text" . "&provtype=activity" . "&domain=" . $domain . "&title=". $title;

			if($incrementIfExists){
				$activityIncrement = 0;
				while(Activity::withTrashed()->find($activityURI)){
					$activityIncrement++;
					$titleWithIncrement = $title . "_" . $activityIncrement;
					$activityURI = "type=text" . "&provtype=activity" . "&domain=" . $domain . "&title=" . $titleWithIncrement;
				}

				if($activityIncrement > 0)
					$title = $titleWithIncrement;
			}

			try {
				$activity = new Activity;
				$activity->_id = strtolower($activityURI);
				$activity->type = "fileupload";
				$activity->label = "This file was uploaded through the web interface.";
				$activity->user_id = $user->_id;
				$activity->software_id = "files/upload";
				$activity->save();

			} catch (Exception $e) {
				// Something went wrong with creating the Activity
				$status['error'][$title] = $e->getMessage();
				continue;
			}


			$entityURI = "type=text" . "&provtype=entity" . "&domain=" . $domain . "&title=". $title;

			try {
				$entity = new Entity;
				$entity->_id = strtolower($entityURI);
				$entity->title = strtolower($title);
				$entity->extension = $file->getClientOriginalExtension();
				$entity->domain = strtolower($domain);
				$entity->type = "text";
				$entity->documentType = strtolower($documentType);
				$entity->parent_id = null;
				$entity->ancestors = null;
				$entity->activity_id = strtolower($activityURI);
				$entity->user_id = $user->_id;
				$entity->content = File::get($file->getRealPath());
				$entity->save();

				$status['success'][$title] = $title . " was successfully uploaded. (URI: {$entityURI})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$activity->forceDelete();
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}
		}
		return $status;
	}
}