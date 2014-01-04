<?php

namespace mongo\text;

use Moloquent, Schema;

class Activity extends Moloquent {

	protected $connection = 'mongodb_text';
	protected $collection = 'activities';
	protected $softDelete = true;
	protected static $unguarded = true;

	public static function createSchema(){
		Schema::connection('mongodb_text')->create('activities', function($collection)
		{
		    $collection->index('type');
		    $collection->index('user_id');		    
		});
	}

    public function wasAssociatedWith(){
    	return $this->hasOne('User', '_id', 'user_id');
    }

    public function used(){
    	return $this->hasOne('\mongo\text\Entity', 'activity_id', '_id');
    }
}