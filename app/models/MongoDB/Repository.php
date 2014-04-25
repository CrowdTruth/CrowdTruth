<?php 

namespace MongoDB;

use User, Exception;


class Repository {

	protected $entity;
	protected $activity;

	public function __construct(Entity $entity, Activity $activity)
	{
		$this->entity = $entity;
		$this->activity = $activity;
	}

	public function find($URI){
		$URI_array = explode("/", $URI);
		
		if($URI_array[0] == "entity")
		{
			return $this->entity->find($URI);
		} 
			elseif($URI_array[0] == "activity") 
		{
			return $this->activity->find($URI);
		} 

		return false;
	}

	public function getSearchFieldsAndValues($format, $domain)
	{
	
		$fields = array();
		$fields['formats'] = array("text", "image", "video");

		if(is_null($format))
		{
			$domains = Entity::distinct('domain')->get();
			$usersInvolvedInEntities = array_flatten(Entity::distinct('user_id')->get()->toArray());
		}
		else
		{
			$domains = Entity::where('format', $format)->distinct('domain')->get();
			$usersInvolvedInEntities = array_flatten(Entity::where('format', $format)->distinct('user_id')->get()->toArray());

			if($key = array_search($format, $fields['formats']))
			{
			    $value = $fields['formats'][$key];
			    unset($fields['formats'][$key]);
			    array_unshift($fields['formats'], $value);				
			}			
		}

		if(is_null($domain))
		{
			$documentTypes = Entity::where('format', $format)->distinct('documentType')->get();
		}
		else
		{
			$documentTypes = Entity::where('format', $format)->where('domain', $domain)->distinct('documentType')->get();
			$usersInvolvedInEntities = array_flatten(Entity::where('format', $format)->where('domain', $domain)->distinct('user_id')->get()->toArray());
		}

		foreach($usersInvolvedInEntities as $key => $user_id)
		{
			$fields['userAgents'][$key] = User::find($user_id);
		}

		$fields['domains'] = array_flatten($domains->toArray());
		$fields['documentTypes'] = array_flatten($documentTypes->toArray());

		return $fields;
	}


	// public function delete($URI){
	// 	if($entity = $this->find($URI)){
	// 		$activity = $entity->wasGeneratedBy;

	// 		if(!$activity->delete())
	// 			Throw new Exception('Cannot delete activity. URI :' . $activity->_id);

	// 		if(!$entity->delete())
	// 			Throw new Exception('Cannot delete entity. URI :' . $entity->_id);

	// 		return true;
	// 	}
	// 	return false;
	// }

	public function returnCollectionObjectFor($collection){
		$collection = strtolower($collection);

		switch ($collection) {
		    case 'entity':
		    case 'entities':
		        return new Entity;
		    case 'activity':
		    case 'activities':
		        return new Activity;
		    case 'softwareagent':
		    case 'softwareagents':
		        return new SoftwareAgent;
		    case 'crowdagent':
		    case 'crowdagents':
		        return new CrowdAgent;
		    case 'useragent':
		    case 'useragents':
		    case 'user':
		    case 'users':
		        return new \User;
		        return new \User;
		    case 'temp':
		        return new \MongoDB\Temp;
		}

		throw new Exception('Collection / Model does not exist');
	}

// 	public function getDistinctFieldInCollection($Collection, $field, array $conditions){
// 		$distinctFieldsWithConditions = $Collection::where(function($query) use ($conditions)
// 	    {
// 	    	foreach($conditions as $conditionKey => $conditionValue)
// 	    		$query->where($conditionKey, '=', $conditionValue);

// 	    })->distinct($field)->get();

// 	//    })->distinct($field)->remember(60, 'text_' . md5(serialize($field)))->get();

//     	$fieldTypes = array();
// 		foreach($distinctFieldsWithConditions as $distinctField)
// 			array_push($fieldTypes, $distinctField[0]);

// 		if(count($fieldTypes) > 0) {
// 			sort($fieldTypes);
// 			return $fieldTypes;
// 		}
			
// 		return false;	    
// 	}

// 	public function getDocumentsWithFieldsInCollection($Collection, array $fields){
// 		$documents = $Collection::where(function($query) use ($fields)
// 	    {
// 	    	foreach($fields as $fieldKey => $fieldValue){
// 	    		$query->where($fieldKey, '=', $fieldValue);
// 	    	}
// 	    })->get();
// //	    })->remember(60, 'text_' . md5(serialize($fields)))->get();

// 	    return $documents;
// 	}	
}

?>