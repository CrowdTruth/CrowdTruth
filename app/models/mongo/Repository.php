<?php 

namespace mongo;

use Exception;

class Repository {

	public function find($query){
		if(!is_array($query))
			parse_str($query, $query);

		$URI = urldecode(http_build_query($query));
	
		if($Collection = $this->returnCollectionObjectFor($query['type'], $query['provtype']))
			return $Collection::find($URI);
		return false;
	}

	public function delete($URI){
		if($entity = $this->find($URI)){
			$activity = $entity->wasGeneratedBy;

			if(!$activity->delete())
				Throw new Exception('Cannot delete activity. URI :' . $activity->_id);

			if(!$entity->delete())
				Throw new Exception('Cannot delete entity. URI :' . $entity->_id);

			return true;
		}
		return false;
	}

	public function returnCollectionObjectFor($type = 'text', $collection = 'entities'){
		$Collection = "\\mongo\\" . $type . "\\" . ucfirst($collection);
		return new $Collection;
	}

	public function getDistinctFieldInCollection($Collection, $field, array $conditions){
		$distinctFieldsWithConditions = $Collection::where(function($query) use ($conditions)
	    {
	    	foreach($conditions as $conditionKey => $conditionValue)
	    		$query->where($conditionKey, '=', $conditionValue);

	    })->distinct($field)->get();

	//    })->distinct($field)->remember(60, 'text_' . md5(serialize($field)))->get();

    	$fieldTypes = array();
		foreach($distinctFieldsWithConditions as $distinctField)
			array_push($fieldTypes, $distinctField[0]);

		if(count($fieldTypes) > 0) {
			sort($fieldTypes);
			return $fieldTypes;
		}
			
		return false;	    
	}

	public function getDocumentsWithFieldsInCollection($Collection, array $fields){
		$documents = $Collection::where(function($query) use ($fields)
	    {
	    	foreach($fields as $fieldKey => $fieldValue){
	    		$query->where($fieldKey, '=', $fieldValue);
	    	}
	    })->get();
//	    })->remember(60, 'text_' . md5(serialize($fields)))->get();

	    return $documents;
	}	
}

?>