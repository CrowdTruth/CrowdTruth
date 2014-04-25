<?php

class ProvenanceController extends BaseController {

	public function getIndex(){
		dd('index works');
	}

	public function getGenerate(){
		$provenance["prefix"] = $this->getPrefix();
		$provenance["entity"] = $this->getEntities();

		echo "<pre>";
		echo json_encode($provenance, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		
		// foreach(Provenance::all() as $item){
		// 	print_r($item['_id']);
		// }
	}

	protected function getPrefix(){
		return array (
			"CT" => "http://crowd-truth.net/resource/",
			"JC" => "http://jolicrowd.org/resource/",
			"LH" => "http://localhost/resource/",
		);
	}

	protected function getEntities(){
		$documents = DB::collection('text')->get();
		$entities = array();
		foreach($documents as $document){
			$entityID = $document['_id'];
			unset($document['_id']);
			array_push($entities, array($entityID => $document));
		}

		return $entities;
	}
}