<?php

namespace mongo\text;

use Moloquent, Schema;

class Entity extends Moloquent {

	protected $connection = 'mongodb_text';
	protected $collection = 'entities';
	protected $softDelete = true;
	protected static $unguarded = true;

	public static function createSchema(){
		Schema::connection('mongodb_text')->create('entities', function($collection)
		{
		    $collection->index('domain');
		    $collection->index('type');
		    $collection->index('activity_id');
		    $collection->index('user_id');		    
		});
	}

    public function wasGeneratedBy(){
    	return $this->hasOne('\mongo\text\Activity', '_id', 'activity_id');
    }

    public function wasDerivedFrom(){
    	return $this->hasOne('\mongo\text\Entity', '_id', 'parent');
    }

    public function wasAttributedTo(){
    	return $this->hasOne('User', '_id', 'user_id');
    }    
}