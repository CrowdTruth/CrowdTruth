<?php

//use mongo\text\sentence;
use MongoDB\Entity;

class Batch extends Entity {

	// for testing
	public static function testBatch(){
		$batch = new Batch();
		$batch->fill(json_decode(file_get_contents(Config::get('config.csvdir') . 'testbatch.json'), true));
		return $batch;
	}
}
















?>
