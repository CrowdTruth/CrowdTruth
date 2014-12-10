<?php

namespace MongoDB;

use Moloquent, Schema, Auth, Exception, User, Input;

use \Counter as Counter;

class Activity extends Moloquent {

	protected $collection = 'activities';
	protected $softDelete = true;
	protected static $unguarded = true;
    public static $snakeAttributes = false;

    // TODO: add parameters to Activity
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

    public static function generateIncrementedBaseURI($activity) {
    	$seqName = 'activity' . '/' . $activity->softwareAgent_id;
    	$id = Counter::getNextId($seqName);
        return $seqName.'/'.$id;
    }

	public static function createSchema() {
		Schema::create('activities', function($collection)
		{
		    $collection->index('type');
		    $collection->index('user_id');
		    $collection->index('softwareAgent_id');
		});
	}
	
		
	public static function getActivities()
    {
		return Activity::where('user_id', Auth::user()->_id)->get();
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
}
