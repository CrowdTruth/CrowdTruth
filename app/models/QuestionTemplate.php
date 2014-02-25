<?php

//use mongo\text\sentence;
use MongoDB\Entity;
use MongoDB\Activity;

class QuestionTemplate extends Entity {

	
	protected $fillable = array('question', 'replace', 'format', 'documentType', 'domain');
	protected $attributes = array('format' => 'text', 'domain' => 'medical', 'documentType' => 'questiontemplate');
	
    public static function boot ()
    {
        parent::boot();

        static::saving(function ( $questiontemplate )
        {
            if(empty($questiontemplate->activity_id)){
                try {
                    $activity = new Activity;
                    $activity->label = "Questiontemplate is saved.";
                    $activity->software_id = 'templatebuilder';
                    if(!is_null($questiontemplate->ancestors))
                        $activity->entity_used_id = end($questiontemplate->ancestors);
                    $activity->save();
                    $questiontemplate->activity_id = $activity->_id;

                } catch (Exception $e) {

                    if($activity) $activity->forceDelete();
                    throw new Exception('Error saving activity.');
                }
            }
            //$this->$format = 'text';
            //$this->$domain = 'medical';
        });
    }


}
















?>