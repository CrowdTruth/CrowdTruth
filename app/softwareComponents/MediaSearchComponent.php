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


	/**
	 * map properties to keys with formats
	 */
	public function getKeys($entity, $parent = "") {

		$keys = [];
		// loop through all properties
		foreach($entity as $key => $value) {
			// exclude keys that are numbers
			if(! is_numeric($key) && $key != '_id') {
				// if the property has children
				if(is_array($entity[$key])) {
					// get properties of children
					$subKeys = $this->getKeys($entity[$key], $parent.$key.".");
					$keys = array_merge($keys, $subKeys);
				} else {
					if($parent != "") { // this has a parent
						array_push($keys, $parent.$key);
					} else {
						array_push($keys, $key);
					}
				}
			}
		};
		return $keys;
	}


	/**
	 * trace format of a value
	 */
	public function getFormat($value) {
		
		if(preg_match('/^.*\.(mp3|ogg|wmv)$/i',$value)) {
			$format = 'sound';
		} else if(preg_match('/^(http\:\/\/.*\.ggpht\.com.*|.*\.(jpg|jpeg|png|gif))$/i',$value)) {
			$format = 'image';
		} else if(preg_match('/^.*\.(avi|mpeg|mpg|mp4)$/i',$value)) {
			$format = 'video';
		} else if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}.*$/i',$value)) {
			$format = 'time';
		} else if(is_numeric($value)) {
			$format = 'number';
		} else {
			$format = 'string';
		}		

		return $format;
	}
	
	/**
	 * create a fancy label based on a key
	 */
	public function getLabel($label) {
		$label = str_replace(".", " > ", $label);
		$label = str_replace("_", " ", $label);
		$label = ucfirst($label);
		return $label;
	}

}
