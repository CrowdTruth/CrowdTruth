<?php

//use mongo\text\sentence;
use MongoDB\Entity;

class Batch extends Entity {

	public $title;

	public function __construct($entities = null, $title = null){
		if(!empty($entities) and (!empty($title))){ // Added the if for testing.
			foreach ($entities as $newEntity) {
				array_push($this->entities, $newEntity);
			}
			$this->title = $title;
		}
	}

	public function addEntity($entity){
		$this->entities = array_push($this->entities, $entity);
	}

	public function toArray(){
		$array = $this->attributes;
		$return = array();
		foreach ($array as $row){
			$content = $row['content'];
			$content['uid'] = $row['_id'];
			$content['_golden'] = 'false';
			unset($content['properties']);
			$return[] = $content;
		}	

		return $return;
	}


	/**
	* @return path to the csv, ready to be sent to the CrowdFlower API.
	*/
	public function toCFCSV($path = null){
		if(empty($path)) $path = base_path() . '/app/storage/tmp/crowdflower.csv';
		//$tmpfname = tempnam("/tmp", "csv");
		$out = fopen($path, 'w');
		//$out = fopen('php://memory', 'r+');
		$array = $this->toArray();
		$headers = $array[0];

		fputcsv($out, array_change_key_case(str_replace('.', '_', array_keys(array_dot($headers))), CASE_LOWER));
		
		foreach ($array as $row){
			// TODO: replace
			fputcsv($out, array_dot($row));	
		}
		//file_put_contents('test.csv', $contents);
		rewind($out);
		//$contents = stream_get_contents($out);
		fclose($out);

		return $path;
	}

	// for testing
	public static function testBatch(){
		$batch = new Batch();
		$batch->fill(json_decode(file_get_contents(Config::get('config.csvdir') . 'testbatch.json'), true));
		return $batch;
	}
}
















?>