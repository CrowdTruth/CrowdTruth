<?php
namespace SoftwareComponents;
use \MongoDB\Entity as Entity;
use \MongoDB\SoftwareComponent as SoftwareComponent;
class MediaSearchComponent {
	protected $softwareComponent;
	
	public function __construct() {
		$this->softwareComponent = SoftwareComponent::find('mediasearchcomponent');
	}
	
	// clear all data
	public function clear() {
		$this->softwareComponent->keys = [];
		$this->softwareComponent->save();
	}
	
	/**
	 * from the list of formats select the lowest granularity
	 */
	public function prioritizeFormat($formats) {
		if(in_array('string', $formats)) { $format = 'string'; }
		else if(in_array('number', $formats)) { $format = 'number'; }
		else if(in_array('time', $formats)) { $format = 'time'; }
		else if(in_array('image', $formats)) { $format = 'image'; }
		else if(in_array('video', $formats)) { $format = 'video'; }
		else if(in_array('sound', $formats)) { $format = 'sound'; }
		else { $format = 'video'; }
		return $format;
	}
	
	// get all keys
	public function getKeys($documents = Array('all')) {
		$keys = $this->softwareComponent['keys'];
		
		// remove keys that are not in these docTypes
		if($documents[0] != 'all') {
			foreach($keys as $key => $value) {
				$exists = false;
				foreach($documents as $document) {
					if(in_array($document, $keys[$key]['documents'])) {
						$exists = true;
					}
				}
				if($exists == false) {
					unset($keys[$key]);
				}
			}
		}
		
		return $keys;
	}	
	
	// get all formats
	public function getFormats() {
		return $this->softwareComponent['formats'];
	}

	// create new index of keys in the database
	public function store($keys) {
		$allKeys = $this->softwareComponent['keys'];
		$allKeys = [];
		// loop through keys to update formats
		foreach($keys as $k => $v) {
			// if format is something else then the current format, prioritize it
			if(array_key_exists($k,$allKeys)) {
				if($allKeys[$k]['format'] != $keys[$k]['format']) {
					$format = $this->prioritizeFormat([$allKeys[$k]['format'],$keys[$k]['format']]);
				} else {
					$format = $keys[$k]['format'];
				}
				$this->softwareComponent['keys'][$k]['format'] = $format;
				foreach($documents as $doc) {
					if(!in_array($this->softwareComponent['keys'][$k]['documents'], $doc)) {
						array_push($this->softwareComponent['keys'][$k]['documents'],$doc);
					}				
				}
				
				//$this->softwareComponent->save();
			} else {
				$key = $this->softwareComponent->keys;
				$key[$k] = $v;
				$this->softwareComponent->keys = $key;
				
				// $this->softwareComponent['keys'] = ['aa'];
				
			}
		}
		$this->softwareComponent->save();		
	}
}