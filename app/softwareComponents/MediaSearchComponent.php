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
		$this->softwareComponent->save();
	}
	
	public function getKeyLabels() {
		return $this->softwareComponent['keyLabels'];
	}

	public function store($keys) {
		$allKeys = $this->softwareComponent['keys'];
		$allKeys = array_unique(array_merge($allKeys, $keys));

		$labels = $this->softwareComponent['keyLabels'];
		foreach($keys as $key) {
			if( ! array_key_exists($key, $labels)) {
				$key2 = str_replace(".", "_", $key);
				$labels[$key2] = $key;
			}
		}
		$this->softwareComponent['keys'] = $allKeys;
		$this->softwareComponent['keyLabels'] = $labels;
		$this->softwareComponent->save();
	}
}
