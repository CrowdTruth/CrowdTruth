<?php

namespace MongoDB;

use Moloquent, Schema, Auth, Exception, User, Input;

class Activity extends Moloquent {

	protected $collection = 'activities';
	protected $softDelete = true;
	protected static $unguarded = true;
    public static $snakeAttributes = false;

    public function __construct()
    {
        $this->filterResults();
        parent::__construct();
    }

    public function filterResults()
    {
        $input = Input::all();
        if(array_key_exists('wasAssociatedWithUserAgent', $input))    array_push($this->with, 'wasAssociatedWithUserAgent');
        if(array_key_exists('wasAssociatedWithCrowdAgent', $input))    array_push($this->with, 'wasAssociatedWithCrowdAgent');
        if(array_key_exists('wasAssociatedWithSoftwareAgent', $input))    array_push($this->with, 'wasAssociatedWithSoftwareAgent');
        if(array_key_exists('wasAssociatedWith', $input))    $this->with = array_merge($this->with, array('wasAssociatedWithUserAgent', 'wasAssociatedWithCrowdAgent', 'wasAssociatedWithSoftwareAgent'));
    }  

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
               $activity->_id = static::generateIncrementedBaseURI($activity);
               // Throw new Exception("Activity ID is null");
            }

            if (Auth::check())
            {
                $activity->user_id = Auth::user()->_id;
            } else 
            {
                $activity->user_id = "crowdwatson";
            }                
        });
    }

    // public static function generateIncrementedBaseURI($activity)
    // {
    //     $lastMongoIncUsed = Activity::where('softwareAgent_id', $activity->softwareAgent_id)->count();

    //     if(isset($lastMongoIncUsed)){
    //         $inc = $lastMongoIncUsed;
    //     } else {
    //         $inc = 0;
    //     }

    //     return 'activity' . '/' . $activity->softwareAgent_id . '/' . $inc;
    // }

    public static function generateIncrementedBaseURI($activity){
        $lastMongoURIUsed = Activity::where('softwareAgent_id', $activity->softwareAgent_id)->get(array("_id"));
        if(is_object($lastMongoURIUsed)) {
            $lastMongoURIUsed = $lastMongoURIUsed->sortBy(function($entity) {
                return $entity->_id;
            }, SORT_NATURAL)->toArray();
        }

        if(!end($lastMongoURIUsed)){
            $id = 0;
        } else {
            $lastMongoIDUsed = explode("/", end($lastMongoURIUsed)['_id']);
            $id = end($lastMongoIDUsed) + 1;
        }
       
        return 'activity' . '/' . $activity->softwareAgent_id . '/' . $id;
    }

	public static function createSchema(){
		Schema::create('activities', function($collection)
		{
		    $collection->index('type');
		    $collection->index('user_id');
		    $collection->index('softwareAgent_id');
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

    // public function used(){
    // 	return $this->hasOne('\MongoDB\Entity', '_id', 'entityUsed_id');
    // }
}
