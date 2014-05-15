<?php

//use mongo\text\sentence;
use MongoDB\Entity;
use MongoDB\Activity;

class QuestionTemplate extends Entity {

	protected $attributes = array('documentType' => 'questiontemplate', 
                                  'type' => 'todo');

    /**
    *   Override the standard query to include documenttype.
    */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('documentType', 'questiontemplate');
        return $query;
    }
	
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
                    Log::debug("Saving QuestionTemplate {$questiontemplate->_id} with activity {$questiontemplate->activity_id}.");
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
        $r = array_change_key_case($this->content['replaceValues'], CASE_LOWER);

        // Flatten array. Use _ as separator.
        if(isset($unit['content']) and is_array($unit['content']))
            $uco = array_dot($unit['content']);
        else throw new Exception("Unit content not found.");
        foreach($uco as $key=>$val){
            $uc[str_replace('.', '_', $key)] = $val;
        }

        // ReplaceRules
        foreach($r as $field=>$wasbecomes){
            $field = strtolower($field);
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
                foreach($field['options'] as $okey=>$oval){
                    foreach ($uc as $key=>$val){
                        $okey = str_replace('{{' . $key . '}}', $val, $okey);
                        $oval = str_replace('{{' . $key . '}}', $val, $oval); 
                    }
                }  
                $field['options'] = $temp;
            }
            $q2[] = $field; 
        }

        return $q2;

    }

    public function flattenAndReplace($unitcontent){

        $uco = array_dot($unitcontent);
        foreach($uco as $key=>$val)
            $uc[str_replace('.', '_', $key)] = $val;

        if(!isset($this->content['replaceValues'])) return $uc;
        $r = $this->content['replaceValues'];
        foreach($r as $field=>$wasbecomes){
            if(isset($uc[$field]))
               foreach($wasbecomes as $was=>$becomes)
                   if($uc[$field] == $was) $uc[$field] = $becomes;    
        }

        return $uc;
    }

    public function getDictionary($unit, $answer){
        $question = $this->getQuestionWithUnit($unit);
        $dictionary = array();
        foreach($answer as $akey=>$aval)
            foreach($question as $component=>$field)
                if (isset($field['options']))
                    foreach($field['options'] as $okey=>$oval)
                       $dictionary[$okey] = ($aval == $okey ? 1 : 0);
            
        return $dictionary;
    }
}
















