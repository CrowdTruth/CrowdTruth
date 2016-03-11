<?php

/*
 * This class manages the creation of new software agents in the database
 * These are used to track activities
 */
class SoftwareAgent extends Moloquent {

	protected $collection = 'softwareagents';
	protected $softDelete = true;
	protected static $unguarded = true;   
	
	public function __construct($id = '', $label='') {
		$this->_id = $id;
		$this->label = $label;
		//$softwareAgent->save();
	}
}
