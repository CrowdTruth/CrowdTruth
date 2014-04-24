<?php

use MongoDB\Entity;

class Batch extends Entity {
	
	protected $attributes = array('documentType' => 'batch');


// DOESN'T WORK WITH wasDerivedFrom! 
	/**
    *   Override the standard query to include documenttype.
    */
/*    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('documentType', 'batch');
        return $query;
    }*/ 

	// for testing
	public static function testBatch(){
		$batch = new Batch();
		$batch->fill(json_decode(file_get_contents(Config::get('config.csvdir') . 'testbatch.json'), true));
		return $batch;
	}

/*    public function units(){
        return $this->hasMany('\MongoDB\Entity', '_id', 'parents');
    }*/
}
















?>
