<?php
namespace SoftwareComponents;
use \MongoDB\Entity as Entity;
use \MongoDB\SoftwareComponent as SoftwareComponent;
class MediaSearchComponent {
	protected $softwareComponent;
	
	public function __construct() {
		$this->softwareComponent = SoftwareComponent::find('mediasearchcomponent');
	}
	public function clear() {
		$this->softwareComponent['keys'] = [];
		$this->softwareComponent['keyLabels'] = [];
		$this->softwareComponent['keyTypes'] = [];
		$this->softwareComponent->save();
	}
	
	public function getKeyLabels() {
		return $this->softwareComponent['keyLabels'];
	}
	
	public function getKeyTypes() {
		return $this->softwareComponent['keyTypes'];
	}
	
	public function store($keys) {
		$allKeys = $this->softwareComponent['keys'];
		$allKeys = array_unique(array_merge($allKeys, $keys));
		$labels = $this->softwareComponent['keyLabels'];
		$types = $this->softwareComponent['keyTypes'];
		foreach($keys as $key) {
			if( ! array_key_exists($key, $labels)) {
				$key2 = str_replace(".", "_", $key);
				$label = str_replace("_", " ", $key2);
				$labels[$key2] = ucfirst($label);
				
				// determine type
				if(strpos($key2, '_count') !== false) {
					$types[$key2] = 'int'; // integer
				} else if(stripos(strrev($key2), '_at') === 0 || strpos($key2, 'date') !== false ) {
					$types[$key2] = 'date'; // date
				} else {
					$types[$key2] = 'string'; // string
				}
			}
		}
		$this->softwareComponent['keys'] = $allKeys;
		$this->softwareComponent['keyTypes'] = $types;
		$this->softwareComponent['keyLabels'] = $labels;
		$this->softwareComponent->save();
	}
}
