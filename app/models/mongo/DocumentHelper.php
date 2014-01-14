<?php 

namespace mongo;

class DocumentHelper {

	public function find($URI){
		if($Entity = $this->getEntityObjectFor($URI))
			return $Entity::find($URI);
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

	public function getEntityObjectFor($URI){
		if($collectionType = $this->getCollectionTypeFor($URI)){
			$Entity = "\\mongo\\" . $collectionType . "\\Entity";
			return new $Entity;
		}
		return false;		
	}

	public function getCollectionTypeFor($URI){
		if (strpos($URI, 'resource/text') !== false) {
		    return 'text';
		} elseif(strpos($URI, 'resource/images') !== false) {
		    return 'images';
		} elseif(strpos($URI, 'resource/videos') !== false) {
		    return 'videos';
		} else {
			return false;
		}
	}
}

?>