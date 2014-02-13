<?php

namespace MongoDB;

use Moloquent, Schema, Auth, Exception, User;

class Activity extends Moloquent {

	protected $collection = 'activities';
	protected $softDelete = true;
	protected static $unguarded = true;

    public static function boot()
    {
        parent::boot();

        static::saving(function($activity)
        {
            if(!Schema::hasCollection('activities'))
            {
                static::createSchema();
            }

            if(is_null($activity->_id))
            {
                Throw new Exception("Activity ID is null");
            }

            if (Auth::check())
            {
                $activity->user_id = Auth::user()->_id;
            } else 
            {
                $activity->user_id = "CrowdWatson";
            }                
        });
    }	

	public static function createSchema(){
		Schema::create('activities', function($collection)
		{
		    $collection->index('type');
		    $collection->index('user_id');
		    $collection->index('software_id');
		});
	}

    public function wasAssociatedWithUserAgent(){
        return $this->hasOne('User', '_id', 'user_id');
    }

    public function wasAssociatedWithCrowdAgent(){
        return $this->hasOne('\MongoDB\CrowdAgent', '_id', 'crowdAgent_id');
    }    

    public function wasAssociatedWithSoftwareAgent(){
        return $this->hasOne('\MongoDB\SoftwareAgent', '_id', 'softwareAgent_id');
    }

    public function used(){
    	return $this->hasOne('\MongoDB\Entity', '_id', 'entityUsed_id');
    }
}