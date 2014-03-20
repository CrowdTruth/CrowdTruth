<?php

//use mongo\text\sentence;
use MongoDB\Entity;
use MongoDB\Activity;

class QuestionTemplate extends Entity {

	
	//protected $fillable = array('question', 'replace', 'format', 'documentType', 'domain');
	protected $attributes = array(  'format' => 'text', 
                                    'domain' => 'medical', 
                                    'documentType' => 'questiontemplate', 
                                    'type', 'todo');
	
    public static function boot ()
    {
        parent::boot();

        static::saving(function ( $questiontemplate )
        {
            if(empty($questiontemplate->activity_id)){
                try {
                    $activity = new Activity;
                    $activity->label = "Questiontemplate is saved.";
                    $activity->softwareAgent_id = 'templatebuilder';
                    $activity->save();
                    $questiontemplate->activity_id = $activity->_id;
                    Log::debug("Saved QuestionTemplate with activity {$questiontemplate->activity_id}.");
                } catch (Exception $e) {

                    if($activity) $activity->forceDelete();
                    if($questiontemplate) $questiontemplate->forceDelete();
                    throw new Exception('Error saving activity for QuestionTemplate.');
                }
            }
        });

     }   
    
    public function getQuestionWithUnit($unit){
        $q = $this->content['question'];
        $r = $this->content['replaceValues'];
        $uco = array_dot($unit['content']);

        foreach($uco as $key=>$val){
            $uc[str_replace('.', '_', $key)] = $val;
        }

        foreach($r as $field=>$wasbecomes){
            if(in_array($field, $uc))
               foreach($wasbecomes as $was=>$becomes)
                   if($uc[$field] == $was) $uc[$field] = $becomes;
        }
        
        $q2 = array();
        foreach($q as $component=>$field){
            if(isset($field['value']))
                foreach ($uc as $key=>$val)
                    $field['value'] = str_replace('{{' . $key . '}}', $val, $field['value']);

            if(isset($field['options'])){
                $temp = array();
                foreach($field['options'] as $okey=>$oval){
                    foreach ($uc as $key=>$val){
                        $okey = str_replace('{{' . $key . '}}', $val, $okey);
                        $oval = str_replace('{{' . $key . '}}', $val, $oval); 
                    }
                    $temp[$okey] = $oval;
                }  
                $field['options'] = $temp;
            }
            $q2[] = $field; 
        }

        return $q2;

    }

    public function getDictionary($unit, $answer){
        $question = $this->getQuestionWithUnit($unit);
        $dictionary = array();
        foreach($answer as $akey=>$aval){
            foreach($question as $component=>$field){
                if (isset($field['options'])){
                    $otemp = array();
                    foreach($field['options'] as $okey=>$oval){
                       $otemp[$okey] = ($aval == $okey ? 1 : 0);
                    }
                    $dictionary[] = $otemp;
                }
            }
        }
        return $dictionary;
    }

}
















