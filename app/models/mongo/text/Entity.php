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

	public static function getDistinctFieldinArray($field, array $conditions){
		$distinctFieldsWithConditions = Entity::where(function($query) use ($conditions)
	    {
	    	foreach($conditions as $conditionKey => $conditionValue)
	    		$query->where($conditionKey, '=', $conditionValue);

	    })->distinct($field)->get();
	//    })->distinct($field)->remember(60, 'text_' . md5(serialize($field)))->get();

    	$fieldTypes = array();
		foreach($distinctFieldsWithConditions as $distinctField)
			array_push($fieldTypes, $distinctField[0]);

		if(count($fieldTypes) > 0) {
			sort($fieldTypes);
			return $fieldTypes;
		}
			
		return false;	    
	}

	public static function getEntitiesWithFields(array $fields){
		$entities = Entity::where(function($query) use ($fields)
	    {
	    	foreach($fields as $fieldKey => $fieldValue){
	    		$query->where($fieldKey, '=', $fieldValue);
	    	}
	    })->get();
//	    })->remember(60, 'text_' . md5(serialize($fields)))->get();

	    return $entities;
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