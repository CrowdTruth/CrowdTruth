<?php
use MongoDB\Entity;

class Annotation extends Entity {

	protected $attributes = array(  'format' => 'text', 
                                    'domain' => 'medical', 
                                    'documentType' => 'annotation',
                                    'spam' => false);
	
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

        static::creating(function ( $annotation )
        {
            
            // Inherit type, domain and format
            if(empty($annotation->type) or empty($annotation->domain) or empty($annotation->format)){
                $j = Job::where('_id', $annotation->job_id)->first();
                $annotation->type = $j->type;
                $annotation->domain = $j->domain;
                $annotation->format = $j->format;
            }  

            $annotation->dictionary = $annotation->createDictionary();

            // Activity if not exists
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
                    //if($annotation) $annotation->forceDelete();
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
               //return  $this->createDictionaryFactSpan(); // For Debugging!
                Log::debug("TYPE {$this->type} UNKNOWN: {$this->_id}");
                throw new Exception("TYPE {$this->type} UNKNOWN: {$this->_id}");
                break;
        }        
    }
    //todo should be private
    public function createDictionaryFactSpan(){
        if(empty($this->unit_id))
            return null;

        $sentence = str_replace('/', ' ', $this->unit->content['sentence']['formatted']);
        $term1 = str_replace('/', ' ', $this->unit->content['terms']['first']['formatted']);
        $term2 = str_replace('/', ' ', $this->unit->content['terms']['second']['formatted']);
        $term1text = str_replace('/', ' ', $this->unit->content['terms']['first']['text']);
        $term2text = str_replace('/', ' ', $this->unit->content['terms']['second']['text']);

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
        if(isset($ans['expl1span'])){
            $expl1span = $ans["expl1span"];
            $expl2span = $ans["expl2span"];
            
            $a1indices = explode(',', $ans['expl1span']);
            $a2indices = explode(',', $ans['expl2span']);
            
            $expltext1 = rtrim($ans["expltext1"]);
            $expltext1yesquestion = $ans['expltext1yesquestion'];
            $expltext2 = rtrim($ans["expltext2"]);
            $expltext2yesquestion = $ans['expltext2yesquestion'];

            // Sometimes this is missing. It seems like it should be 'YES' in those cases.
            if(!isset($ans['Q1'])) $ans['Q1'] = 'YES';
            if(!isset($ans['Q2'])) $ans['Q2'] = 'YES';

            // Q1
            try{
                if($ans["Q1"] == 'YES'){
                    $this->isOkYesQuestion($expltext1, $expltext1yesquestion, $term1, $term1text, $sentence);
                    $vector1 = $this->createFactVect(false, 0, 0); // YES it's the same.    
                } else {
                    if(preg_replace("/[^A-Za-z]/",'',$expltext1) == preg_replace("/[^A-Za-z]/",'',$term1))
                        throw new Exception("User selected NO but Expltext $expltext1 == term $term1.");

                    if(empty($expl1span))
                        throw new Exception("User selected NO but Explspan is empty.");

                    $startdiff = $a1indices[0] - $indices1[0];
                    $enddiff = end($a1indices) - end($indices1);

                   // HACK
                    if((strpos($expltext1, '/') > 0) or (strpos($expltext1, '-') > 0))
                        $enddiff += 1;

                    $vector1 = $this->createFactVect(false, $startdiff, $enddiff);      
                }
            } catch (Exception $e) {
                Log::debug("{$e->getMessage()}");
               // echo "\r\n<span style='color:red'>!!! {$e->getMessage()}</span><br>\r\n";
                $vector1 = $this->createFactVect(true);
            }  
            
            // Q2
            try {
                if($ans["Q2"] == 'YES'){
                    $this->isOkYesQuestion($expltext2, $expltext2yesquestion, $term2, $term2text, $sentence);
                    $vector2 = $this->createFactVect(false, 0, 0); // YES it's the same.    
                } else {
                    if(preg_replace("/[^A-Za-z]/",'',$expltext2) == preg_replace("/[^A-Za-z]/",'',$term2))
                        throw new Exception("User selected NO but Expltext $expltext2 == term $term2.");

                    if(empty($expl2span))
                        throw new Exception("User selected NO but Explspan is empty.");

                   // HACK
                    if((strpos($expltext2, '/') > 0) or (strpos($expltext2, '-') > 0))
                        $enddiff += 1;

                    $startdiff = $a2indices[0] - $indices2[0];
                    $enddiff = end($a2indices) - end($indices2);
                    $vector2 = $this->createFactVect(false, $startdiff, $enddiff); 

                 } 
            } catch (Exception $e) {
                Log::debug("{$e->getMessage()}");
                //echo "\r\n<span style='color:red'>!!! {$e->getMessage()}</span><br>\r\n";
                $vector2 = $this->createFactVect(true);
            }  



        // CF
        } elseif(isset($ans['confirmfirstfactor']) or isset($ans['factor1'])) {
           // $sentence = str_replace('-', ' ', strtolower($this->unit->content['sentence']['text']));
           // Log::debug($ans);
            print_r($ans);
            // Patching the fact that the CML is different from the WebSci results [fugly]
            //if(!isset($ans['confirmfirstfactor'])){
            if(isset($ans['factor1'])) $ans['firstfactor'] = $ans['factor1'];
            if(isset($ans['factor2'])) $ans['secondfactor'] = $ans['factor2'];

            if(isset($ans['question1'])){
                if($ans['question1'] == 'no'){
                    $ans['confirmfirstfactor'] = '';
                    $ans['confirmids1'] = '';
                } else {
                    $ans['saveselectionids1'] = $ans['b1'];  
                }
            }    

            if(isset($ans['question2'])){
                if ($ans['question2'] == 'no'){
                    $ans['confirmsecondfactor'] = '';
                    $ans['confirmids2'] = '';  
                } else {
                    $ans['saveselectionids2'] = $ans['b2']; 
                }
            }

            Log::debug($ans);
         //   print_r($ans);



            $term = strtolower($this->unit->content['terms']['first']['text']);
            $b = $this->unit->content['terms']['first']['startIndex'];
             echo "\r\n{$ans['confirmids1']}, {$ans['confirmfirstfactor']}, $term, $b, {$ans['saveselectionids1']}, {$ans['firstfactor']}";
            $vector1 = $this->createSingleFactVect($ans['confirmids1'], $ans['confirmfirstfactor'], $term, $b, $ans['saveselectionids1'], $ans['firstfactor']);
           
            $term = strtolower($this->unit->content['terms']['second']['text']);
            $b = $this->unit->content['terms']['second']['startIndex'];
             echo "\r\n{$ans['confirmids2']}, {$ans['confirmsecondfactor']}, $term, $b, {$ans['saveselectionids2']}, {$ans['secondfactor']}";
            $vector2 = $this->createSingleFactVect($ans['confirmids2'], $ans['confirmsecondfactor'], $term, $b, $ans['saveselectionids2'], $ans['secondfactor']);
        } else {
            Log::debug("Can't determine if it's CF or AMT.");
        }    

        return array('term1' => $vector1, 'term2' => $vector2);
     }


    private function isOkYesQuestion($expltext, $yesquestion, $term, $termtext, $inputsentence){
        $yesquestion = strtolower($yesquestion);
        $termtext = strtolower($termtext);

        if(preg_replace("/[^A-Za-z]/",'',$expltext) !=  preg_replace("/[^A-Za-z]/",'',$term)
            and strpos($expltext, '/') === false and strpos($expltext, '-') === false) // HACK, SOME CHEATS PASS
           throw new Exception("User selected YES but Expltext $expltext != term $term");

        if(strpos($yesquestion, $termtext) === false and strpos($yesquestion, $term)=== false)
            throw new Exception("User selected YES but term $term is not in explanation text.");

        if(substr_count($yesquestion, ' ') < 4)
           throw new Exception("User selected YES but explanationtext < 4 words.");

        if($inputsentence == $yesquestion)
           throw new Exception("User selected YES but Explanationtext == input sentence.");


        return true;
    }

    private function createSingleFactVect($confirmids, $confirmfactor, $term, $b, $saveselectionids, $factor){
        $sentence = strtolower($this->unit->content['sentence']['text']);
       // echo "{$this->_id}: ";
        try{

            // User selected YES -> NIL or exception
            if($confirmids != ''){  
                $ids = explode('-', $confirmids);
                sort($ids);
                $words = $this->getWords($sentence, $ids);

               // echo("[$term]=[$words]=[$confirmfactor]\r\n");
                
                if($words != $term and rtrim($words, ";,.") != $term) 
                    throw new Exception("User selected YES but '$words' [based on index] != '$term' [text]");
                    
                return $this->createFactVect(false, 0, 0); // [NIL]               

            // User selected NO -> anything but [NIL] (else: exception)
            } elseif(!empty($saveselectionids)) {
                
                // Create text from indices.
                $ids = explode('-', $saveselectionids);
                sort($ids);
                $words = $this->getWords($sentence, $ids);

                // The selected words match the given text
                if ($words != strtolower($factor) and $words != rtrim($words, ";,."))
                    throw new Exception("User selected NO but '$words' [based on indices] != '$factor' [text]");

                $wordindex = array_search($b, $ids);
                if($wordindex===false)
                     throw new Exception("User selected NO but base index $b not found in answer $saveselectionids.");

                $startdiff = -$wordindex;
                $enddiff = count($ids) - $wordindex - substr_count($term, " ") - 1;
                //count($ids) - $wordindex -(count(explode(' ', $words))-1);

                if($startdiff == 0 and $enddiff == 0)
                    throw new Exception('User selected NO but answered NIL.');
                
                if($words == $term)
                    throw new Exception('User selected NO but provided the same term.');

                return $this->createFactVect(false, $startdiff, $enddiff);
                //echo("[$term]!=([$words]=[$factor])[$startdiff<->$enddiff] ($b in $saveselectionids)\r\n");
                //echo "^ $b is in pos $wordindex so start=$startdiff and end = $enddiff.\r\n";

            } else {
                throw new Exception('Confirmids and saveselectionids are both empty.');
            }

        }catch(Exception $e){
             Log::debug("\r\nFAIL: {$e->getMessage()}\r\n");
            echo "\r\n\r\nException: {$e->getMessage()}\r\n\r\n";
            return $this->createFactVect(true);

        }
     }

    private function getWords($sentence, $indexes, $chars = [' ','-','/']){
        $words = array();
        foreach ($indexes as $index) {
            $foundarray = array();
            foreach ($chars as $c)
                if($found = strpos(substr($sentence, $index), $c))
                    $foundarray[] = $found;

            if(count($foundarray)>0)
                $length = min($foundarray);
            else $length = 0;

            $words[] = substr($sentence, $index, $length);
        }
        return implode(' ', $words);
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

        if($startdiff == 0 and $enddiff == 0){
            $vector["[NIL]"]=1;
            return $vector;
        } 

       if($startdiff<-3 or $enddiff > 3){
            $vector["[WORD_OTHER]"]=1;
        } 

        $vector["[WORD_-3]"]= ($startdiff < -2 ? 1 : 0);
        $vector["[WORD_-2]"]= ($startdiff < -1 ? 1 : 0);
        $vector["[WORD_-1]"]= ($startdiff <  0 ? 1 : 0);
        $vector["[WORD_+1]"]= ($enddiff   >  0 ? 1 : 0);
        $vector["[WORD_+2]"]= ($enddiff   >  1 ? 1 : 0);
        $vector["[WORD_+3]"]= ($enddiff   >  2 ? 1 : 0);

        $vector["[WORD_OTHER]"]=(($startdiff < -3 or $enddiff > 3) ? 1 : 0);

        return $vector;
    }

    // TODO: unify templates
    private function createDictionaryRelDir(){
        if(isset($this->content['direction']))
            $ans = $this->content['direction'];
        else
            $ans = $this->content['Q1'];

        // The CF template works like this.
        if($this->softwareAgent_id == 'cf'){
            $u = $this->unit->content;
/*            if($ans == 'no_relation')
                $ans = 'Choice3';*/
            
            if (strpos($ans, $u['terms']['first']['formatted']) === 0)
                $ans = 'term1_term2';
            elseif (strpos($ans, $u['terms']['first']['formatted']) > 0)
                $ans = 'term2_term1';
        }

        return array('direction' => array(
            'term1_term2' => (($ans == 'term1_term2') ? 1 : 0),
            'term2_term1' => (($ans == 'term2_term1') ? 1 : 0),
            'no_relation' => (($ans == 'no_relation') ? 1 : 0)
            ));

    }



     /**
     * Creates a Dictionary ( possible multiple choice answers with 1 or 0 ) and saves it in the Annotation.
     * This might be reused when we start using the JSON QuestionTemplates.
     */
 /*   private function createDictionaryDEPRECATED(){
       
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
    }*/

    public function createDictionaryRelEx(){
        try {
            // AMT
            if(isset($this->content['Q1text'])){
                $ans = str_replace(" ", "_", rtrim($this->content['Q1text']));           
                $ans = str_replace("[DIAGNOSED_BY_TEST_OR_DRUG]", "[DIAGNOSE_BY_TEST_OR_DRUG]", $ans);
                if($ans == '') return null;
                   // throw new Exception('Answer is empty.');

                $ans = str_replace("]_[", "]*[", $ans);
                $ans = explode('*', $ans);

            // CF    
            } elseif(isset($this->content['step_1_select_the_valid_relations'])) {
                if(is_array($this->content['step_1_select_the_valid_relations']))
                    $ans = $this->content['step_1_select_the_valid_relations'];
                else {
                    $ans = str_replace("\n", "_", rtrim($this->content['step_1_select_the_valid_relations']));
                    $ans = str_replace("\r", "_", $ans);
                    $ans = str_replace("]_[", "]*[", $ans);
                    $ans = explode('*', $ans);
                }    
            } else {
                throw new Exception('Can\'t determine if it\'s CF or AMT.');
            }
           
/*            if(in_array("[NONE]", $ans)){
                if(count($ans)>1)
                    throw new Exception('Worker selected none but also other relations.');

                if(empty($this->content['Q2b']))
                    throw new Exception('Worker selected [NONE] but gave no explanation.');

            } elseif(empty($this->content['Q2a'])){
                throw new Exception('Worker selected a relation but didn\'t hightlight words');
            }*/
                

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

            // We decided to give the spammers just empty vectors.

/*            foreach($ans as $a){
                if(!in_array($a, array_keys($dic)))
                   throw new Exception("Answer $a not in dictionary.");  
            }

            if(!in_array(1, $dic)){
                throw new Exception('Dictionary EMPTY');
            }*/

            return array('extraction'=>$dic);

        } catch (Exception $e){
            echo $e->getMessage();
            Log::debug($e->getMessage());
            return null;
        }    
   
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
