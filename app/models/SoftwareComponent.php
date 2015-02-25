<?php
class SoftwareComponent extends Moloquent {
	protected $collection = 'softwarecomponents';
	protected static $unguarded = true;
	public static $snakeAttributes = false;
	
	public function __construct($id = '', $label='')
	{
		$attributes = [];
		parent::__construct($attributes);
		$this->_id = $id;
		$this->label = $label;
	}
}
