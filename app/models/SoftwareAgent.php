<?php

/*
 * This class manages the creation of new software agents in the database
 * These are used to track activities
 */
class SoftwareAgent extends Moloquent {

	protected $collection = 'softwareagents';
	protected $softDelete = true;
	protected static $unguarded = true;   
	
	public static function store($name, $title) {
 		if(!SoftwareAgent::find($name)){
 			$softwareAgent = new SoftwareAgent;
 			$softwareAgent->_id = $name;
 			$softwareAgent->label = $title;
 		}
 	}
}