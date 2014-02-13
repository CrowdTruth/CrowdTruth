<?php
namespace crowdwatson;

use crowdwatson\sentence;
use mongo\text\entity;

class Batch extends Entity {

	public $entities = array();
	public $title;

	public function __construct($entities, $title){
		foreach ($entities as $newEntity) {
			array_push($this->entities, $newEntity);
		}
		$this->title = $title;
	}

	public function addEntity($entity){
		$this->entities = array_push($entities, $entity);
	}
}
















?>