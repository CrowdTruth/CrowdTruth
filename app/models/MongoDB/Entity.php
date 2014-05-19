<?php

namespace MongoDB;

use Moloquent, Schema, Cache, Input, Exception, Auth, User, Session;

class Entity extends Moloquent {

    protected $collection = 'entities';
    protected $softDelete = true;
    protected static $unguarded = true;
    public static $snakeAttributes = false;

    public static function getKeyLabelMapping() {
        return [
            "twrex-structured-sentence" => "Relex-structured sentence",
            "keyframes" => "Key-frames",
            "totalrelevantfeatures" => "Relevant Features",
            "fullvideo" => "Full Video",
            "documentType" => "Document Type",
            "painting" => "Painting",
            "drawing" => "Drawing",
            "relation" => "Seed Relation"
        ];
    }

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
        if(array_key_exists('hasConfiguration', $input))    array_push($this->with, 'hasConfiguration');
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

            \MongoDB\Temp::truncate();

            Cache::flush();
        });

        static::deleted(function($entity)
        {
            Cache::flush();
        });
    }

    // public static function generateIncrementedBaseURI($entity)
    // {
    //     if(is_null($entity->_id))
    //     {
    //         $lastMongoIncUsed = Entity::where('format', $entity->format)->where('domain', $entity->domain)->where("documentType", $entity->documentType)->count();
        
    //         if(isset($lastMongoIncUsed))
    //         {
    //             $inc = $lastMongoIncUsed;
    //         } else {
    //             $inc = 0;
    //         }
    //     }
    //     else
    //     {
    //         $entityIDSegments = explode("/", $entity->_id);
    //         $inc = (end($entityIDSegments) + 1);
    //     }

    //     return 'entity/' . $entity->format . '/' . $entity->domain . '/' . $entity->documentType . '/' . $inc;
    // }     

    public static function generateIncrementedBaseURI($entity)
    {
        $inc = 0;

        if(is_null($entity->_id))
        {
            $lastMongoURIUsed = Entity::where('format', $entity->format)->where('domain', $entity->domain)->where("documentType", $entity->documentType)->get(array("_id"));
        
            if(count($lastMongoURIUsed) > 0) {
                $lastMongoURIUsed = $lastMongoURIUsed->sortBy(function($entity) {
                    return $entity->_id;
                }, SORT_NATURAL)->toArray();

                if(end($lastMongoURIUsed)){
                    $lastMongoIDUsed = explode("/", end($lastMongoURIUsed)['_id']);
                    $inc = end($lastMongoIDUsed) + 1;                
                }
            }

        } else {
                $lastMongoIDUsed = explode("/", $entity->_id);
                $inc = end($lastMongoIDUsed) + 1;
        }

        return 'entity/' . $entity->format . '/' . $entity->domain . '/' . $entity->documentType . '/' . $inc;
    }     
  
    // public static function generateIncrementedBaseURI($entity)
    // {
    //     if(is_null($entity->inc))
    //     {
    //         $lastMongoIncUsed = Entity::where('format', $entity->format)->where('domain', $entity->domain)->where("documentType", $entity->documentType)->max('inc');
        
    //         if(isset($lastMongoIncUsed)){
    //             $entity->inc = $lastMongoIncUsed;
    //         }
    //     }

    //     $entity->inc++;

    //     return $entity->format . '/' . $entity->domain . '/' . $entity->documentType . '/' . $entity->inc;
    // }  


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

    public function hasJob(){
        return $this->hasOne('\MongoDB\Entity', '_id', 'job_id');
    }

    public function hasChildren(){
        return $this->hasOne('\MongoDB\Entity', '_id', 'parents');
    }

    public function hasUnit(){
        return $this->hasOne('\MongoDB\Entity', '_id', 'unit_id');
    }

    public function annotations(){
        return $this->hasMany('\MongoDB\Entity', 'unit_id', '_id');
    }

    public function getWasDerivedFromAttribute()
    {
        if(isset($this->parents))
        {
            return Entity::whereIn('_id', array_values($this->parents))->remember(1)->get()->toArray();         
        }
    }

    public function scopeDomain($query, $domain)
    {
        return $query->whereDomain($domain);
    }

    public function scopeType($query, $type)
    {
        return $query->whereType($type);
    }

    public function scopeFormat($query, $format)
    {
        return $query->whereFormat($format);
    }

    public function scopeId($query, $id)
    {
        return $query->where_id($id);
    }
    
    public function getJobCountAttribute(){
        if($this->documentType == "twrex-structured-sentence"){
            return $annotations = count(array_flatten(Entity::where('unit_id', $this->_id)->distinct('job_id')->get()->toArray()));
        }
    }

    public function toArray()
    {
        if(\Session::has('rawArray'))
        {
            $attributes = $this->getArrayableAttributes();
        }
        else
        {
            $attributes = $this->attributesToArray();
        }
        
        return array_merge($attributes, $this->relationsToArray());
    }     
}
