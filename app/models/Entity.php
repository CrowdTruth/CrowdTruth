<?php

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
                $entity->user_id = "crowdtruth";
            }
        });

        static::saving(function($entity)
        {
            $entity->documentType = strtolower($entity->documentType);
        });

        static::saved(function($entity)
        {

            Temp::truncate();

            Cache::flush();
        });

        static::deleted(function($entity)
        {
            Cache::flush();
        });
    }

    public static function generateIncrementedBaseURI($entity)
    {
        $seqName = 'entity/' . $entity->project . '/' . $entity->documentType;
        $id = Counter::getNextId($seqName);
        return $seqName.'/'.$id;
    }     
  
    public static function createSchema(){
        Schema::create('entities', function($collection)
        {
            $collection->index('hash');
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
        return $this->hasOne('Activity', '_id', 'activity_id');
    }

    public function wasDerivedFrom(){
        return $this->hasMany('Entity', '_id', 'parents');
    }

    public function wasAttributedToUserAgent(){
        return $this->hasOne('UserAgent', '_id', 'user_id');
    }

    public function wasAttributedToCrowdAgent(){
        return $this->hasOne('CrowdAgent', '_id', 'crowdagent_id');
    }

    public function hasConfiguration(){
        return $this->hasOne('Entity', '_id', 'jobConf_id');
    }

    public function hasJob(){
        return $this->hasOne('Entity', '_id', 'job_id');
    }

    public function hasChildren(){
        return $this->hasOne('Entity', '_id', 'parents');
    }

    public function hasUnit(){
        return $this->hasOne('Entity', '_id', 'unit_id');
    }

    public function workerunits(){
        return $this->hasMany('Entity', 'unit_id', '_id');
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
        if($this->documentType == "relex-structured-sentence"){
            return $workerunits = count(array_flatten(Entity::where('unit_id', $this->_id)->distinct('job_id')->get()->toArray()));
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
