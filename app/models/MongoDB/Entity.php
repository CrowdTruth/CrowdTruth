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
        if(isset($input['without'])) $this->hidden = array_merge($this->hidden, array_flatten(array($input['without'])));
    }       

    protected static function boot()
    {
        parent::boot();

        static::creating(function($entity)
        {
            if(!Schema::hasCollection('entities'))
            {
                static::createSchema();
            }

            if(!empty($entity->hash))
            {
                if(Entity::withTrashed()->where('hash', $entity->hash)->first())
                {
                    throw new Exception("Hash already exists for: " . $entity->title);
                }
            }            

            $entity->_id = static::generateIncrementedBaseURI($entity);

            if (Auth::check())
            {
                $entity->user_id = Auth::user()->_id;
            } else 
            {
                $entity->user_id = "crowdwatson";
            }           
        });

        static::saving(function($entity)
        {
            $entity->format = strtolower($entity->format);            
            $entity->domain = strtolower($entity->domain);
            $entity->documentType = strtolower($entity->documentType);

            static::validateEntity($entity);         
        });

        static::saved(function($entity)
        {
            Cache::flush();
        });

        static::deleted(function($entity)
        {
            Cache::flush();
        });
    }

    public static function generateIncrementedBaseURI($entity)
    {
        if(is_null($entity->_id))
        {
            $lastMongoIncUsed = Entity::where('format', $entity->format)->where('domain', $entity->domain)->where("documentType", $entity->documentType)->count();
        
            if(isset($lastMongoIncUsed))
            {
                $inc = $lastMongoIncUsed;
            } else {
                $inc = 0;
            }
        }
        else
        {
            $entityIDSegments = explode("/", $entity->_id);
            $inc = (end($entityIDSegments) + 1);
        }

        return 'entity/' . $entity->format . '/' . $entity->domain . '/' . $entity->documentType . '/' . $inc;
    }     
  

    public static function validateEntity($entity){
        if(($entity->format == "text" || $entity->format == "image" || $entity->format == "video") == FALSE){
            throw new Exception("Entity has a wrong value \"{$entity->format}\" for the format field");
        }

        if(($entity->domain == "medical" || $entity->domain == "news" || $entity->domain == "cultural" || $entity->domain == "art") == FALSE){
            throw new Exception("Entity has a wrong value \"{$entity->domain}\" for the domain field");
        }
    }

    public static function createSchema(){
        Schema::create('entities', function($collection)
        {
            $collection->index('hash');
            $collection->index('domain');
		    $collection->index('documentType');    
		    $collection->index('activity_id');
		    $collection->index('user_id');
		    $collection->index('parents');
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

    	return $this->hasMany('\MongoDB\Entity', '_id', 'parents');

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
        if(isset($this->parents))
        {
            return Entity::whereIn('_id', $this->parents)->remember(1)->get()->toArray();         
        }
    } 

}

