<?php

namespace MongoDB;

use Moloquent, Schema, Cache, Input, Exception, Auth, User, Session;

class Entity extends Moloquent {

    protected $collection = 'entities';
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
        if(array_key_exists('wasDerivedFrom', $input))    array_push($this->appends, 'wasDerivedFrom');
        if(array_key_exists('wasGeneratedBy', $input))    array_push($this->with, 'wasGeneratedBy');
        if(array_key_exists('wasAttributedTo', $input))    $this->with = array_merge($this->with, array('wasAttributedToUserAgent', 'wasAttributedToCrowdAgent'));
        if(array_key_exists('wasAttributedToUserAgent', $input))    array_push($this->with, 'wasAttributedToUserAgent');
        if(array_key_exists('wasAttributedToCrowdAgent', $input))    array_push($this->with, 'wasAttributedToCrowdAgent');        
    
        if(isset($input['wasDerivedFrom']['without'])) $this->hidden = array_merge($this->hidden, array_flatten(array($input['wasDerivedFrom']['without'])));

    }       

    protected static function boot()
    {
        parent::boot();

        static::saving(function($entity)
        {
            $entity->_id = strtolower($entity->_id);
            $entity->domain = strtolower($entity->domain);
            $entity->format = strtolower($entity->format);
            $entity->documentType = strtolower($entity->documentType);

            static::validateEntity($entity);

            if(!Schema::hasCollection('entities'))
            {
                static::createSchema();
            }

            if(is_null($entity->hash))
            {
                if(is_array($entity->content))
                {
                    $entity->hash = md5(serialize($entity->content));
                } 
                else
                {
                    $entity->hash = md5($entity->content);
                }
            }            

            if(Entity::withTrashed()->where('hash', $entity->hash)->first())
            {
                //throw new Exception("Hash already exists for: " . $entity->title);
            }

            $baseURI = static::generateIncrementedBaseURI($entity);

            if (Auth::check())
            {
                $entity->user_id = Auth::user()->_id;
            } else 
            {
                $entity->user_id = "CrowdWatson";
            }

            if(empty($entity->_id))
                $entity->_id = 'entity/' . $baseURI;
           
        });

        static::saved(function($entity)
        {
            $baseURI = static::generateIncrementedBaseURI($entity);

            if(is_null($entity->activity_id))
            {
                $entity->activity_id = 'activity/' . $baseURI;
            }

            Cache::flush();
        });

        static::deleted(function($entity)
        {
            Cache::flush();
        });
    }

    // public static function generateIncrementedBaseURI($entity){
    //     $lastMongoURIUsed = Entity::where('format', $entity->format)->where('domain', $entity->domain)->where("documentType", $entity->documentType)->orderBy('natural', 'desc')->take(1)->get(array("_id"));

    //     if(isset($lastMongoURIUsed[0])){
    //         $lastMongoIDUsed = explode("/", $lastMongoURIUsed[0]['_id']);
    //         $id = end($lastMongoIDUsed) + 1;
    //     } else {
    //         $id = 0;
    //     }
        
    //     return $entity->format . '/' . $entity->domain . '/' . $entity->documentType . '/' . $id;
    // } 

    public static function generateIncrementedBaseURI($entity){
        // if(Session::has('lastMongoIDUsed'))
        // {
        //     $id = (Session::get('lastMongoIDUsed') + 1);
        //     return $entity->format . '/' . $entity->domain . '/' . $entity->documentType . '/' . $id;
        // }

        $lastMongoURIUsed = Entity::where('format', $entity->format)->where('domain', $entity->domain)->where("documentType", $entity->documentType)->get(array("_id"));
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

        // if($entity->documentType == "twrex-structured-sentence")
        //     Session::put('lastMongoIDUsed', $id);
       
        return $entity->format . '/' . $entity->domain . '/' . $entity->documentType . '/' . $id;
    }    

    public static function validateEntity($entity){
        if(($entity->format == "text" || $entity->format == "image" || $entity->format == "video") == FALSE){
            throw new Exception("Entity has a wrong value \"{$entity->format}\" for the format field");
        }

        if(($entity->domain == "medical" || $entity->format == "news" || $entity->format == "other") == FALSE){
            throw new Exception("Entity has a wrong value \"{$entity->format}\" for the domain field");
        }
    }

	public static function createSchema(){
		Schema::create('entities', function($collection)
		{
            $collection->index('hash');
            $collection->index('domain');
		    $collection->index('documentType');
		    $collection->index('parent_id');		    
		    $collection->index('activity_id');
		    $collection->index('user_id');
		    $collection->index('ancestors');
		});
	}

    public static function getDistinctValuesForField($field, $conditions = array()){
        $distinctFields = Entity::where(function($query) use ($conditions)
        {
            foreach($conditions as $conditionKey => $conditionValue)
            {
                $query->where($conditionKey, '=', $conditionValue);
            }

        })->distinct($field)->get();

    //    })->distinct($field)->remember(60, 'text_' . md5(serialize($field)))->get();

        $fieldValues = array();
        foreach($distinctFields as $distinctField)
        {
            array_push($fieldValues, $distinctField[0]);
        }

        if(count($fieldValues) > 0)
        {
            sort($fieldValues);
            return $fieldValues;
        }
            
        return false;       
    }    

    public function wasGeneratedBy(){
    	return $this->hasOne('\MongoDB\Activity', '_id', 'activity_id');
    }

    public function wasDerivedFrom(){
    	return $this->hasOne('\MongoDB\Entity', '_id', 'parent_id');
    }

    public function wasAttributedToUserAgent(){
        return $this->hasOne('User', '_id', 'user_id');
    }

    public function wasAttributedToCrowdAgent(){
        return $this->hasOne('\MongoDB\CrowdAgent', '_id', 'crowdagent_id');
    }

    public function hasConfiguration(){
        return $this->hasOne('\MongoDB\Entity', '_id', 'jobConf_id');
    }

    public function getWasDerivedFromAttribute()
    {
        if(isset($this->ancestors))
        {
            return Entity::whereIn('_id', $this->ancestors)->remember(1)->get()->toArray();         
        }
    }    
}
