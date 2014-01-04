<?php

class Chang {

	protected $originalDocument;
	protected $processedDocument;

	public function __construct($originalDocument){
		$this->originalDocument = $originalDocument;
	}

	public function process(){
		$documentSeparatedByNewline = explode("\n", $this->originalDocument['content']);

		// print_r($documentSeparatedByNewline);
		// exit;

		$changDocument = array();

		foreach($documentSeparatedByNewline as $lineNumber => $lineValue){
			if($lineValue == "")
				continue;

			if(preg_match("/(TWrex)\-[a-zA-Z-]+/", $lineValue, $matches)){
				$TWrexRelation = $matches[0];
			}

			if(preg_match_all("/\t+\d+\t\d+\t+/", $lineValue, $matches)){
				$b1 = preg_split("/\s+/", trim($matches[0][0]))[0];
				$e1 = preg_split("/\s+/", trim($matches[0][0]))[1];
				$b2 = preg_split("/\s+/", trim($matches[0][1]))[0];
				$e2 = preg_split("/\s+/", trim($matches[0][1]))[1];
				$sentenceOffset = strpos($lineValue, $matches[0][1]) + strlen($matches[0][1]);
			}
				$sentenceText = ltrim(substr($lineValue, $sentenceOffset));

				$changDocument[$lineNumber]['TWrex-relation'] = $TWrexRelation;
				$changDocument[$lineNumber]['factors']['first']['startIndex'] = $b1;
				$changDocument[$lineNumber]['factors']['first']['endIndex'] = $e1;
				$changDocument[$lineNumber]['factors']['first']['text'] = substr($sentenceText, $b1, $e1 - $b1);
				$changDocument[$lineNumber]['factors']['second']['startIndex'] = $b2;
				$changDocument[$lineNumber]['factors']['second']['endIndex'] = $e2;
				$changDocument[$lineNumber]['factors']['second']['text'] = substr($sentenceText, $b2, $e2 - $b2);

			//	$changDocument[$lineNumber]['Factors'][1] = substr($sentenceText, $offsets['b1'], $offsets['e1']);
			//	$changDocument[$lineNumber]['Factors'][2] = substr($sentenceText, $offsets['e1'], $offsets['e2']);
				$changDocument[$lineNumber]['sentence']['startIndex'] = $sentenceOffset;
				$changDocument[$lineNumber]['sentence']['text'] = $sentenceText;

		}

		$this->processedDocument = $changDocument;
	}

	public function generate(){
		return $this->processedDocument;
	}

	public function save(){
		$fileRepository = App::make('FileRepository');

		$document = array(
			    	'_id' => $this->originalDocument['_id'] . '/chang',
			    	'title' => $this->originalDocument['title'] . '/chang',
			    	'category' => $this->originalDocument['category'],
			    	'created_at' => new DateTime(),
			    	'created_by' => 'vu',
			    	'wasDerivedFrom' => $this->originalDocument['_id'],
			    	'content' => $this->processedDocument
			    );

		$fileRepository->storeCustomDocument($document);
	}
}