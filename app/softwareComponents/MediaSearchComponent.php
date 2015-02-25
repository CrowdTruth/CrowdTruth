<?php
namespace SoftwareComponents;

use \SoftwareComponent as SoftwareComponent;

class MediaSearchComponent {
	protected $softwareComponent;
	
	public function __construct() {
		$this->softwareComponent = SoftwareComponent::find('mediasearchcomponent');
	}
	
	// clear all data
	public function clear() {
		$this->softwareComponent->keys = [];
		$this->softwareComponent->formats = [
						'string' => 'fa-file-text-o',
						'number' => 'fa-bar-chart',
						'time' => 'fa-calendar',
						'image' => 'fa-picture-o',
						'video' => 'fa-film',
						'sound' => 'fa-music'
						];
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
		
		// loop through keys to update formats
		foreach($keys as $k => $v) {
		
			// update data if key already exists
			if(array_key_exists($k,$allKeys)) {
				
				// prioritize formats
				if($allKeys[$k]['format'] != $keys[$k]['format']) {
					$format = $this->prioritizeFormat([$allKeys[$k]['format'], $keys[$k]['format']]);
				} else {
					$format = $keys[$k]['format'];
				}
				$allKeys[$k]['format'] = $format;
				
				// add unique documents
				foreach($keys[$k]['documents'] as $doc) {
					if(!in_array($doc, $allKeys[$k]['documents'])) {
						array_push($allKeys[$k]['documents'], $doc);
					}				
				}
			} else {
				// add new key
				$allKeys[$k] = $v;				
			}
		}
		$this->softwareComponent['keys'] = $allKeys;
		$this->softwareComponent->save();		
	}
}