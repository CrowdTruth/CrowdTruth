<?php
use MongoDB\Entity;

class Annotation extends Entity {

	protected $attributes = array(  'format' => 'text', 
                                    'domain' => 'medical', 
                                    'documentType' => 'annotation', 
                                    'type' => 'todo');
	
    /**
    *   Override the standard query to include documenttype.
    */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('documentType', 'annotation');
        return $query;
    }

    public static function boot ()
    {
        parent::boot();

        static::saving(function ( $annotation )
        {
            if(empty($annotation->dictionary))
                $annotation->createDictionary();

            if(empty($annotation->activity_id)){
                try {
                    $activity = new Activity;
                    $activity->label = "annotation is saved.";
                    $activity->softwareAgent_id = $annotation->softwareAgent_id;
                    $activity->save();
                    $annotation->activity_id = $activity->_id;
                    Log::debug("Saving annotation {$annotation->_id} with activity {$annotation->activity_id}.");
                } catch (Exception $e) {

                    if($activity) $activity->forceDelete();
                    if($annotation) $annotation->forceDelete();
                    throw new Exception('Error saving activity for annotation.');
                }
            }

        });

     }   

     /**
     * Creates a Dictionary ( possible multiple choice answers with 1 or 0 ) and saves it in the Annotation.
     */
    private function createDictionary(){
       
        $q = $this->job->questionTemplate->content['question'];
        $r = $this->job->questionTemplate->content['replaceValues'];

        // Flatten array.
       // $unit = Entity::where('_id', $this->unit_id)->first(); // TODO: relation.
        $unit = $this->unit; 
        if(isset($unit['content']) and is_array($unit['content']))
            $uco = array_change_key_case(array_dot($unit['content']), CASE_LOWER);
        else throw new Exception("Unit content not found."); // Todo: how do we handle exceptions here?
        //else return true; // TODO: DEBUGGING

        // Use _ as separator.
        foreach($uco as $key=>$val)
            $uc[str_replace('.', '_', $key)] = $val;

        // ReplaceRules REVERSED
        foreach($r as $field=>$wasbecomes){
            $field = array_change_key_case($field, CASE_LOWER);
            if(isset($uc[$field]))
               foreach($wasbecomes as $was=>$becomes)
                   if($uc[$field] == $becomes) $uc[$field] = $was;
        }
        // Create array of all the answers, in parameter format.
        $temp = array();
        foreach($this->content as $singleans){
        	foreach ($uc as $key=>$val)
        		$singleans = str_replace($val, '{{' . strtolower($key) . '}}', $singleans);

        	$temp[] = $singleans;
        }

        // Create dictionary.
        $dictionary = array();
        foreach($q as $field)                           // 0 => options => a causes b
            foreach($field as $key=>$val)               // options => a causes b
                if($key == 'options') 
                   foreach (array_keys($val) as $possibleans)
                        foreach($temp as $givenans)
                            $dictionary[strtolower($possibleans)] = (strtolower($givenans) == strtolower($possibleans) ? 1 : 0);

        $this->dictionary = $dictionary;
    }

     private function createCrowdAgent(){
		if($id = CrowdAgent::where('platformAgentId', $workerId)->where('softwareAgent_id', $this->softwareAgent_id)->pluck('_id')) 
			return $id;

		else {
			$agent = new CrowdAgent;
			$agent->_id= "crowdagent/$platform/$workerId";
			$agent->softwareAgent_id= $platform;
			$agent->platformAgentId = $workerId;
			$agent->save();
			
			return $agent->_id;
		}

	}

	public function job(){
		return $this->belongsTo('Job', 'job_id');
	}

    public function unit(){
        return $this->hasOne('MongoDB\Entity', 'unit_id');
    }



}

?>
