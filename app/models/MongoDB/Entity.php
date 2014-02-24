<?php

namespace MongoDB;

use Moloquent, Schema, Cache, Input, Exception, Auth, User;

class Entity extends Moloquent {

	protected $collection = 'entities';
	protected $softDelete = true;
	protected static $unguarded = true;

    protected static function boot()
    {
        parent::boot();

        static::saving(function($entity)
        {
            static::validateEntity($entity);

            if(!Schema::hasCollection('entities'))
            {
                static::createSchema();
            }

            if(is_array($entity->content))
            {
                $hash = md5(serialize($entity->content));
            } 
            else
            {
                $hash = md5($entity->content);
            }

            if(Entity::withTrashed()->where('hash', $hash)->first())
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
        //    dd($entity->_id);
            $entity->hash = $hash;

            if(is_null($entity->activity_id))
            {
                $entity->activity_id = 'activity/' . $baseURI;
            }
            
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

    public static function generateIncrementedBaseURI($entity){
        $lastMongoURIUsed = Entity::where('domain', $entity->domain)->where("documentType", $entity->documentType)->get(array("_id"))->sortBy(function($entity)
        {
            return $entity->_id;
        }, SORT_NATURAL)->toArray();

        if(!end($lastMongoURIUsed)){
            $id = 0;
        } else {
            $lastMongoIDUsed = explode("/", end($lastMongoURIUsed)['_id']);
            $id = end($lastMongoIDUsed) + 1;
        }
       
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
        return $this->hasOne('CrowdAgent', '_id', 'crowdagent_id');
    }
}