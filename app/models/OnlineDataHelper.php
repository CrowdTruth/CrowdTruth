<?php

class OnlineDataHelper {

	protected $input;
	protected $format;

	public function __construct(array $input){
		$this->input = $input;
	}
	public function getOnlineSource () {
		return $this->input['source_name'];
	}

	public function getType(){
		switch ($this->input['source_name']) {
		    case 'source_beeldengeluid':
		        return $this->format = 'video';
		}
		throw new Exception('Unknown file format!');
	}

	public function getDomain(){
		switch ($this->input['source_name']) {
		    case 'source_beeldengeluid':
		        return 'Cultural';
		}
		throw new Exception('Unknown file domain!');		
	}

	public function getDocumentType(){
		switch ($this->input['source_name']) {
		    case 'source_beeldengeluid':
		        return 'fullvideo';
		}
		throw new Exception('Unknown document type!');		
	}

	public function getNoOfVideos() {
		$number = (int)$this->input['numberVideos'];
		if ($number) {
			if ($number == 0) {
				throw new Exception('Please select a number grater or equal to 1!');		
			}
			if ($number < 0) {
				throw new Exception('Please select a number grater or equal to 1!');		
			}
			return $number;
		}
	}
}
