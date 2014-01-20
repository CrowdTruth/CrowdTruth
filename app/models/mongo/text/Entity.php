<?php

namespace mongo\text;

use Moloquent, Schema, Cache;

class Entity extends Moloquent {

	protected $connection = 'mongodb_text';
	protected $collection = 'entities';
	protected $softDelete = true;
	protected static $unguarded = true;

    public static function boot()
    {
        parent::boot();
        static::saved(function($entity)
        {
            Cache::flush();
        });

        static::deleted(function($entity)
        {
            Cache::flush();
        });
    }

	public static function createSchema(){
		Schema::connection('mongodb_text')->create('entities', function($collection)
		{
		    $collection->index('domain');
		    $collection->index('documentType');
		    $collection->index('parent_id');		    
		    $collection->index('activity_id');
		    $collection->index('user_id');
		    $collection->index('ancestors');
		});
	}

    public function wasGeneratedBy(){
    	return $this->hasOne('\mongo\text\Activity', '_id', 'activity_id');
    }

    public function wasDerivedFrom(){
    	return $this->hasOne('\mongo\text\Entity', '_id', 'parent_id');
    }

    public function wasAttributedTo(){
    	return $this->hasOne('User', '_id', 'user_id');
    }    
}