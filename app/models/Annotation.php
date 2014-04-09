<?php
use MongoDB\Entity;

class Annotation extends Entity {

	protected $attributes = array(  'format' => 'text', 
                                    'domain' => 'medical', 
                                    'documentType' => 'annotation');
	
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
            if(empty($annotation->type) or empty($annotation->domain) or empty($annotation->format)){
                $j = Job::where('_id', $annotation->job_id)->first();
                $annotation->type = $j->type;
                $annotation->domain = $j->domain;
                $annotation->format = $j->format;
            }
            
            if(empty($annotation->dictionary))
                $annotation->dictionary = $annotation->createDictionary();

            if(empty($annotation->activity_id)){
                try {
                    $activity = new Activity;
                    $activity->label = "Annotation is saved.";
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

     //todo make private. 
     // TODO exceptionhandling, smart checks.
    public function createDictionary(){
        switch ($this->type) {
            case 'FactSpan':
                return  $this->createDictionaryFactSpan();
                break;
            case 'RelDir':
                return $this->createDictionaryRelDir();
                break;
            case 'RelEx':
                return $this->createDictionaryRelEx();
                break;
            
            default:
               throw new Exception('No rules for creating a dictionary for this type.');
                break;
        }        
    }
    //todo should be private
        public function createDictionaryFactSpan(){
            //if(isset($this->unit->content['sentence']['formatted']))
                $sentence = $this->unit->content['sentence']['formatted'];
            //else 
            //    $sentence = $this->unit->content['sentence']['text'];
            
            $term1 = $this->unit->content['terms']['first']['formatted'];
            $term2 = $this->unit->content['terms']['second']['formatted'];

            // Set indices1
            $charindex1 = strpos($sentence, $term1);
            $index1start = substr_count(substr($sentence, 0, $charindex1), ' ')+1;
            $indices1 = array($index1start);
            for ($i=0; $i < substr_count($term1, ' '); $i++)
                array_push($indices1, $index1start+$i+1);

            // Set indices2
            $charindex2 = strpos($sentence, $term2);
            $index2start = substr_count(substr($sentence, 0, $charindex2), ' ')+1;
            $indices2 = array($index2start);
            for ($i=0; $i < substr_count($term2, ' '); $i++)
                array_push($indices2, $index2start+$i+1);
            
            $ans = $this->content;

            //AMT
            if(isset($ans['expl1span'])){ // TODO
                $expl1span = $ans["expl1span"];
                $expl2span = $ans["expl2span"];
                $a1indices = explode(',', $ans['expl1span']);
                $a2indices = explode(',', $ans['expl2span']);
                $expltext1 = rtrim($ans["expltext1"]);
                $expltext1yesquestion = $ans['expltext1yesquestion'];
                $expltext2 = rtrim($ans["expltext2"]);
                $expltext2yesquestion = $ans['expltext2yesquestion'];

                // Sometimes, in AMT, this is missing. It seems like it should be 'YES' in those cases.
                if(!isset($ans['Q1'])) $ans['Q1'] = 'YES';
                if(!isset($ans['Q2'])) $ans['Q2'] = 'YES';

                // Q1
                if($ans["Q1"] == 'YES'){
                    if((rtrim($expltext1) != $term1) or (!$this->isOkYesQuestion($expltext1yesquestion, $term1, $sentence))) // [maybe check indices as well?]
                        $vector1 = $this->createFactVect(true); // FAILED
                    else {
                        $vector1 = $this->createFactVect(false, 0, 0); // YES it's the same.
                    }   
                } else {
                    if(rtrim($expltext1 == $term1) or (empty($expl1span)))
                        $vector1 = $this->createFactVect(true); // FAILED
                    else {
                        $startdiff = $a1indices[0] - $indices1[0];
                        $enddiff = end($a1indices) - end($indices1);
                        $vector1 = $this->createFactVect(false, $startdiff, $enddiff);      
                    }   
                }
                
                // Q2
                if($ans["Q2"] == 'YES'){
                    if(($expltext2 != $term2) or (!$this->isOkYesQuestion($expltext2, $term2, $sentence))) // TODO: harsher
                        $vector2 = $this->createFactVect(true); // FAILED
                    else {
                        $vector2 = $this->createFactVect(false, 0, 0); // YES it's the same.
                    }   
                } else {
                    if(($expltext1 == $term2) or (empty($expl2span)))
                        $vector2 = $this->createFactVect(true); // FAILED
                    else {
                        $startdiff = $a2indices[0] - $indices2[0];
                        $enddiff = end($a2indices) - end($indices2);
                        $vector2 = $this->createFactVect(false, $startdiff, $enddiff); 
                    }
                }   



            // CF
            } elseif(isset($ans['confirmfirstfactor']) or isset($ans['saveselectionid1'])) {
               // $sentence = str_replace('-', ' ', strtolower($this->unit->content['sentence']['text']));
                $sentence = strtolower($this->unit->content['sentence']['text']);
                $term1 = strtolower($this->unit->content['terms']['first']['text']);
                $term2 = strtolower($this->unit->content['terms']['second']['text']);

                // Term1
                
                 // first space after last index

                // words1 = the first span according to the confirmids
               

                
//$words1 == strtolower($ans['confirmfirstfactor'])
                // Should be the same

                try{
                    // User selected YES -> NIL or exception
                    if(!empty($ans['confirmids1'])){  
                        $ids1 = explode('-', $ans['confirmids1']);
                        $length1 = strpos(substr($sentence, intval(end($ids1))), ' ');
                        $words1 = strtolower(substr($sentence, intval($ids1[0]), $length1));

                        echo("{$this->_id}\r\n[$term1]=[$words1]=[{$ans['confirmfirstfactor']}]\r\n$sentence\r\n");
                        
                        if($words1 == $term1) // [NIL] 
                            $vector1 = $this->createFactVect(false, 0, 0);
                        else
                           throw new Exception("User selected YES but '$words1' != '$term1'");

                    // User selected NO -> anything but [NIL] (else: exception)
                    } elseif(!empty($ans['saveselectionids1'])) {
                        
                        // Create text from indices.
                        $ids1 = explode('-', $ans['saveselectionids1']);
                        $length1 = strpos(substr($sentence, intval(end($ids1))), ' ')+(intval(end($ids1))-intval($ids1[0]));
                        $words1 = substr($sentence, intval($ids1[0]), $length1);

                        // TEST
                       // $words1 = str_replace('-', ' ', $words1);
       
                        // The selected words match the given text
                        if($words1 != strtolower($ans['firstfactor']))
                            throw new Exception("User selected NO but '$words1' != '{$ans['firstfactor']}'");

                        $term1initialindex = strpos($sentence, $term1);
                        $wordindex = array_search($term1initialindex, $ids1);
                        $startdiff = -$wordindex;
                        $enddiff = count($ids1) - $wordindex -1;
                        var_dump($ids1);

                        if($startdiff == 0 and $enddiff == 0)
                            throw new Exception('User selected NO but startdiff and enddiff are 0.');
                        
                        if($words1 == $term1)
                            throw new Exception('User selected NO but provided the same term.');

                        $vector1 = $this->createFactVect(false, $startdiff, $enddiff);
                        echo("{$this->_id}\r\n[$term1]!=([$words1]=[{$ans['firstfactor']}])\r\n$sentence\r\n");

             /*           echo "$term1initialindex vs {$ans['saveselectionids1']}: $wordindex dus $startdiff en $enddiff";
                         echo("{$this->_id}\r\n[$term1]!={$ans['firstfactor']} - $sentence\r\n");
                        */


                    }

                }catch(Exception $e){
                    $vector1 = $this->createFactVect(true);
                    echo '-----------------------------';
                    echo $e->getMessage() . "\r\n$sentence\r\n";
                    print_r($ans);

                    dd($vector1);
                    echo '-----------------------------';
                }
               // print_r($vector1);
            }     





           // return array('term1' => $vector1, 'term2' => $vector2);
     }


    private function isOkYesQuestion($yesquestion, $term, $inputsentence){
        
/*      1. the sentence contains the complete term
        2. the sentence has more than 4 words
        3. the sentence is not equal to the input sentence*/
        return true;

    }

    private function createFactVect($failed = false, $startdiff=null, $enddiff=null){
        $vector = array(
        "[WORD_-3]"=>0,
        "[WORD_-2]"=>0,
        "[WORD_-1]"=>0,
        "[WORD_+1]"=>0,
        "[WORD_+2]"=>0,
        "[WORD_+3]"=>0,
        "[WORD_OTHER]"=>0,
        "[NIL]"=>0,
        "[CHECK_FAILED]"=>0);

        if($failed){
            $vector["[CHECK_FAILED]"] = 1;
            return $vector;
        }

        if($startdiff<-3 or $enddiff > 3){
            $vector["[WORD_OTHER]"]=1;
            return $vector;
        } // TODO; when else?

        if($startdiff == 0 and $enddiff == 0){
            $vector["[NIL]"]=1;
            return $vector;
        } 

        $vector["[WORD_-3]"]= ($startdiff < -2 ? 1 : 0);
        $vector["[WORD_-2]"]= ($startdiff < -1 ? 1 : 0);
        $vector["[WORD_-1]"]= ($startdiff <  0 ? 1 : 0);
        $vector["[WORD_+1]"]= ($enddiff   >  0 ? 1 : 0);
        $vector["[WORD_+2]"]= ($enddiff   >  1 ? 1 : 0);
        $vector["[WORD_+3]"]= ($enddiff   >  2 ? 1 : 0);


        return $vector;
    }


    private function createDictionaryRelDir(){
        $ans = $this->content['direction'];
        return array(
            'Choice1' => (($ans == 'Choice1') ? 1 : 0),
            'Choice2' => (($ans == 'Choice2') ? 1 : 0),
            'Choice3' => (($ans == 'Choice3') ? 1 : 0)
            );

    }



     /**
     * Creates a Dictionary ( possible multiple choice answers with 1 or 0 ) and saves it in the Annotation.
     */
    private function createDictionaryDEPRECATED(){
       
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
            $field = strtolower($field);
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

    public function createDictionaryRelEx(){
        if(isset($this->content['Q1text'])){
            $ans = str_replace(" ", "_", rtrim($this->content['Q1text']));           
            $ans = str_replace("[DIAGNOSED_BY_TEST_OR_DRUG]", "[DIAGNOSE_BY_TEST_OR_DRUG]", $ans);
            if($ans == '')  throw new Exception('Answer is empty.');

            $ans = str_replace("]_[", "]*[", $ans);
            $ans = explode('*', $ans);
        } else {
            return array('Not yet implemented for CF.');
        }


        $dic = array(
        "[TREATS]" =>                   (in_array("[TREATS]",                   $ans ) ? 1 : 0),
        "[CAUSES]" =>                   (in_array("[CAUSES]",                   $ans ) ? 1 : 0),
        "[PREVENTS]" =>                 (in_array("[PREVENTS]",                 $ans ) ? 1 : 0),
        "[IS_A]" =>                     (in_array("[IS_A]",                     $ans ) ? 1 : 0),
        "[OTHER]" =>                    (in_array("[OTHER]",                    $ans ) ? 1 : 0),
        "[NONE]" =>                     (in_array("[NONE]",                     $ans ) ? 1 : 0),
        "[PART_OF]" =>                  (in_array("[PART_OF]",                  $ans ) ? 1 : 0),
        "[DIAGNOSE_BY_TEST_OR_DRUG]" => (in_array("[DIAGNOSE_BY_TEST_OR_DRUG]", $ans ) ? 1 : 0),
        "[ASSOCIATED_WITH]" =>          (in_array("[ASSOCIATED_WITH]",          $ans ) ? 1 : 0),
        "[SIDE_EFFECT]" =>              (in_array("[SIDE_EFFECT]",              $ans ) ? 1 : 0),
        "[SYMPTOM]" =>                  (in_array("[SYMPTOM]",                  $ans ) ? 1 : 0),
        "[LOCATION]" =>                 (in_array("[LOCATION]",                 $ans ) ? 1 : 0),
        "[MANIFESTATION]" =>            (in_array("[MANIFESTATION]",            $ans ) ? 1 : 0),
        "[CONTRAINDICATES]" =>          (in_array("[CONTRAINDICATES]",          $ans ) ? 1 : 0));

        foreach($ans as $a){
            if(!in_array($a, $dic))
               throw new Exception("Answer $a not in dictionary.");
        }

        if(!in_array(1, $dic)) throw new Exception('Dictionary EMPTY');

        return $dic;
   
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
		return $this->belongsTo('Job', '_id', 'job_id');
	}

    public function unit(){
        return $this->hasOne('MongoDB\Entity', '_id', 'unit_id');
    }



}

?>
