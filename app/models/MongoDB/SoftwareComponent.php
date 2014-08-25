<?php

namespace MongoDB;

use Moloquent, Schema, Auth, Exception, User, Input;

class SoftwareComponent extends Moloquent {
	protected $collection = 'softwarecomponents';
	protected static $unguarded = true;
	public static $snakeAttributes = false;
	
	public static function boot() {
		parent::boot();
	
		static::saving(function($activity) {
			if(!Schema::hasCollection('softwarecomponents')) {
				static::createSchema();
			}

			if(is_null($activity->_id)) {
				$activity->_id = static::generateIncrementedBaseURI($activity);
				// Throw new Exception("Activity ID is null");
			}

			if (Auth::check()) {
				$activity->user_id = Auth::user()->_id;
			} else {
				$activity->user_id = "crowdwatson";
			}
		});
	}
	
	// TODO: Can this be removed ?
	public static function createSchema() {
		Schema::create('softwarecomponents', function($collection)
		{
			// TODO: Can this be removed ?
		});
	}
}
