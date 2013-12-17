<?php

class MongoFile extends Moloquent {

	protected $collection = 'textfiles';
  
    public function __construct($collection)
    {
    	$this->collection = $collection;
    }

	public function insert($data){
		DB::collection($this->collection)->insert(
		    array('rawText' => $data)
		);
	}
	
}
