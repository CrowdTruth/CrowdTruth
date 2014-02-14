<?php

use mongo\text\sentence;
use mongo\text\entity;

class Batch extends Entity {

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