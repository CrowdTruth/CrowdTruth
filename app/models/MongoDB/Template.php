<?php

namespace MongoDB;

use Moloquent, Schema, Auth, Exception, Input;

use \Counter as Counter;

class Template extends Moloquent {

	protected $collection = 'templates';
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

        static::saving(function($template)
        {
            if(!Schema::hasCollection('templates'))
            {
                static::createSchema();
            }

            if(is_null($template->_id))
            {
               $template->_id = static::generateIncrementedBaseURI($template);
            }

            if (Auth::check())
            {
                $template->user_id = Auth::user()->_id;
            } else 
            {
                $template->user_id = "crowdwatson";
            }                
        });
    }

    public static function generateIncrementedBaseURI($template) {
    	$seqName = 'template' . '/' . $template->format;
    	$id = Counter::getNextId($seqName);
        return $seqName.'/'.$id;
    }

	public static function createSchema() {
		Schema::create('template', function($collection)
		{
            $collection->index('hash');
            $collection->index('format');

            $collection->index('version');
            $collection->index('type');    
            $collection->index('activity_id');
            $collection->index('user_id');
		});
	}

    public function wasAssociatedWithUserAgent(){
        return $this->hasOne('\MongoDB\UserAgent', '_id', 'user_id');
    }

    public function wasAssociatedWithCrowdAgent(){
        return $this->hasOne('\MongoDB\CrowdAgent', '_id', 'crowdAgent_id');
    }    

    public function wasAssociatedWithSoftwareAgent(){
        return $this->hasOne('\MongoDB\SoftwareAgent', '_id', 'softwareAgent_id');
    }
}
