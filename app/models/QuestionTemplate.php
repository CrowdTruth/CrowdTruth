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
            $hash = md5(serialize($questiontemplate->content));
            $existing = QuestionTemplate::where('hash', $hash)->pluck('_id');
            
            if($existing) 
                return false; // Stop saving, it already exists.

            $questiontemplate->hash = $hash;


            if(empty($questiontemplate->activity_id)){
                try {
                    $activity = new Activity;
                    $activity->label = "Questiontemplate is saved.";
                    $activity->softwareAgent_id = 'templatebuilder';
                    $activity->save();
                    $questiontemplate->activity_id = $activity->_id;

                } catch (Exception $e) {

                    if($activity) $activity->forceDelete();
                    if($questiontemplate) $questiontemplate->forceDelete();
                    throw new Exception('Error saving activity for QuestionTemplate.');
                }
            }

             Log::debug("Saved entity {$questiontemplate->_id} with activity {$questiontemplate->activity_id}.");
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
            foreach ($uc as $key=>$val) {   
                $param = '{{' . $key . '}}';//dd($key);
                if(isset($field['value']))
                    $field['value'] = str_replace($param, $val, $field['value']);
               
                if(isset($field['options'])){
                    $otemp = array();
                    foreach($field['options'] as $okey=>$oval){
                        $okey = str_replace($param, $val, $okey);
                        $oval = str_replace($param, $val, $oval);
                        $otemp[$okey] = $oval;
                    }
                    $field['options'] = $otemp;
               }

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
                    $dictionary[$field['id']] = $otemp;
                }
            }
        }
        return $dictionary;
    }

}
















