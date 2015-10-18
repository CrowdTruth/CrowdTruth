<?php

namespace Preprocess;

use \MongoDate;
use Exception, Auth;

class RelexStructurer {

	protected $activity, $softwareAgent;
	protected $status = [
		"store" => [
			"error" => [
				"skipped_duplicates" => []
			],
			"success" => []
		]
	];

	public function __construct(Activity $activity, SoftwareAgent $softwareAgent)
	{
		set_time_limit(1200);
		\DB::connection()->disableQueryLog();
		$this->activity = $activity;
		$this->softwareAgent = $softwareAgent;
	}

	public function process($relex, $preview = false)
	{
		$relexLines = $this->array_unique_multidimensional(explode("\n", $relex['content']));
		$relex['content'] = null;

		if($preview)
		{
			$relexLines = array_slice($relexLines, 0, 100);
			return $this->processLines($relexLines);
		}

		if(count($relexLines) > 10000)
		{
			$arrayChunks = array_chunk($relexLines, 10000);
		}
		else
		{
			$arrayChunks = array($relexLines);
		}

		unset($relexLines);

		$this->createSoftwareAgent();
		$this->createActivity();
		$inc = $this->getLastDocumentInc();

		foreach($arrayChunks as &$chunkVal)
		{
			try {
				$inc = $this->store($relex, $this->processLines($chunkVal), $inc);
			} catch(Exception $e) {
				$this->status['store'] = $e->getMessage();
				$this->activity->forceDelete();
			}

		}

		return $this->status;
	}

	public function processLines(&$relexLines)
	{
		$relexStructuredSentences = array();
		$tempRelexStructuredSentences = array();

		foreach($relexLines as $relexLineKey => &$relexLineVal)
		{
			$relexLineSegments = explode("\t", $relexLineVal);

			// Ignore "0" on first column
			if($relexLineSegments[0] == "0")
			{
				unset($relexLineSegments[0]);
				$relexLineSegments = array_values($relexLineSegments);
			}

			// Ignore lines with less than 8 elements
			if(count($relexLineSegments) < 8)
			{
				continue;
			}

			$tempRelexStructuredSentence = [
				"relation" => [
					"original" => $relexLineSegments[0],
					"noPrefix" => explode("-", $relexLineSegments[0])[1]
				],
				"terms" => [
					"first" => [
						"startIndex" => (int) $relexLineSegments[2],
						"endIndex" => (int) $relexLineSegments[3],
						"text" => $relexLineSegments[1]
					],
					"second" => [
						"startIndex" => (int) $relexLineSegments[5],
						"endIndex" => (int) $relexLineSegments[6],
						"text" => $relexLineSegments[4]
					]
				],
				"sentence" => [
					"text" => $relexLineSegments[7]
				]
			];

			array_push($tempRelexStructuredSentences, $this->getAllTermCombinations($tempRelexStructuredSentence));
		}

		unset($relexLines);

		$tempRelexStructuredSentences = $this->array_unique_multidimensional($tempRelexStructuredSentences);

		$length = count($tempRelexStructuredSentences);

		for($i = 0; $i < $length; $i++)
		{
			$tempRelexStructuredSentence = $tempRelexStructuredSentences[$i];
			unset($tempRelexStructuredSentences[$i]);

			foreach($tempRelexStructuredSentence['terms']['first'] as $firstTerm)
			{
				foreach($tempRelexStructuredSentence['terms']['second'] as $secondTerm)
				{
					$relexStructuredSentence = $tempRelexStructuredSentence;
					$relexStructuredSentence['terms']['first'] = $firstTerm;
					$relexStructuredSentence['terms']['second'] = $secondTerm;

					if($this->overlappingOffsets($relexStructuredSentence))
					{
						continue;
					}

					$relexStructuredSentence['properties'] = [
						"sentenceWordCount" => str_word_count($relexStructuredSentence['sentence']['text']),
						"relationInSentence" => $this->relationInSentence($relexStructuredSentence),
						"relationOutsideTerms" => $this->relationOutsideTerms($relexStructuredSentence),
						"relationBetweenTerms" => $this->relationBetweenTerms($relexStructuredSentence),
						"semicolonBetweenTerms" => $this->semicolonBetweenTerms($relexStructuredSentence),
						"commaSeparatedTerms" => $this->commaSeparatedTerms($relexStructuredSentence),
						"parenthesisAroundTerms" => $this->parenthesisAroundTerms($relexStructuredSentence),
						"overlappingTerms" => $this->overlappingTerms($relexStructuredSentence)
					];

					$relexStructuredSentence = $this->formatUppercase($relexStructuredSentence);

					ksort($relexStructuredSentence['terms']);

					array_push($relexStructuredSentences, $relexStructuredSentence);

				}
			}
		}

		return $relexStructuredSentences;
	}

	public function array_unique_multidimensional($array)
	{
		return array_values(array_intersect_key($array, array_unique(array_map('serialize', $array))));	    
	}	

	public function formatUppercase($relexStructuredSentence)
	{
		$b1 = $relexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $relexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $relexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $relexStructuredSentence['terms']['second']['endIndex'];

		$firstTermUppercaseWithBrackets = "[" . strtoupper($relexStructuredSentence['terms']['first']['text']) . "]";
		$secondTermUppercaseWithBrackets = "[" . strtoupper($relexStructuredSentence['terms']['second']['text']) . "]";

		$relexStructuredSentence['terms']['first']['formatted'] = $firstTermUppercaseWithBrackets;
		$relexStructuredSentence['terms']['second']['formatted'] = $secondTermUppercaseWithBrackets;

		// if($relexStructuredSentence['properties']['overlappingTerms'] == "1")
		// {
		// 	// dd($relexStructuredSentence);
		// 	return $relexStructuredSentence;
		// }		

		$formattedSentence = $relexStructuredSentence['sentence']['text'];

		$firstTermInSentenceUppercaseWithBrackets = "[" . strtoupper(substr($formattedSentence, $b1, $e1 - $b1)) . "]";
		$secondTermInSentenceUppercaseWithBrackets = "[" . strtoupper(substr($formattedSentence, $b2, $e2 - $b2)) . "]";


		if($b1 < $b2)
		{
			$formattedSentence = substr_replace($formattedSentence, $firstTermInSentenceUppercaseWithBrackets, $b1, $e1 - $b1);
			$formattedSentence = substr_replace($formattedSentence, $secondTermInSentenceUppercaseWithBrackets, $b2 + 2, $e2 - $b2);
		}
		else
		{
			$formattedSentence = substr_replace($formattedSentence, $secondTermInSentenceUppercaseWithBrackets, $b2, $e2 - $b2);			
			$formattedSentence = substr_replace($formattedSentence, $firstTermInSentenceUppercaseWithBrackets, $b1 + 2, $e1 - $b1);
		}


		$relexStructuredSentence['sentence']['formatted'] = $formattedSentence;

		return $relexStructuredSentence;
	}

	public function getAllTermCombinations(&$tempRelexStructuredSentence)
	{
		$firstTerm = strtolower($tempRelexStructuredSentence['terms']['first']['text']);
		$secondTerm = strtolower($tempRelexStructuredSentence['terms']['second']['text']);
		$sentenceText = strtolower($tempRelexStructuredSentence['sentence']['text']);

		// $firstTerm = '/\b' . preg_quote($firstTerm, '/') . '\b/'; Use this for matching on whole words only
		$firstTerm = '/' . preg_quote($firstTerm, '/') . '/';
		preg_match_all($firstTerm, $sentenceText, $firstTermMatch, PREG_OFFSET_CAPTURE);

		// $secondTerm = '/\b' . preg_quote($secondTerm, '/') . '\b/'; Use this for matching on whole words only
		$secondTerm = '/' . preg_quote($secondTerm, '/') . '/';
		preg_match_all($secondTerm, $sentenceText, $secondTermMatch, PREG_OFFSET_CAPTURE);

		if(count($firstTermMatch[0]) > 0)
		{
			unset($tempRelexStructuredSentence['terms']['first']);

			foreach($firstTermMatch[0] as $firstTermOccurenceKey => $firstTermOccurenceVal)
			{
				$tempRelexStructuredSentence['terms']['first'][$firstTermOccurenceKey] = [
					"text" => $firstTermOccurenceVal[0],
					"startIndex" => $firstTermOccurenceVal[1],
					"endIndex" => $firstTermOccurenceVal[1] + strlen($firstTermOccurenceVal[0])
				];
			}
		} else {
			$tempRelexStructuredSentence['terms']['first'] = array($tempRelexStructuredSentence['terms']['first']);
			// dd($tempRelexStructuredSentence);
		}

		if(count($secondTermMatch[0]) > 0)
		{
			unset($tempRelexStructuredSentence['terms']['second']);

			foreach($secondTermMatch[0] as $secondTermOccurenceKey => $secondTermOccurenceVal)
			{
				$tempRelexStructuredSentence['terms']['second'][$secondTermOccurenceKey] = [
					"text" => $secondTermOccurenceVal[0],
					"startIndex" => $secondTermOccurenceVal[1],
					"endIndex" => $secondTermOccurenceVal[1] + strlen($secondTermOccurenceVal[0])
				];
			}
		} else {
			$tempRelexStructuredSentence['terms']['second'] = array($tempRelexStructuredSentence['terms']['second']);
			// dd($tempRelexStructuredSentence);
		}

		return $tempRelexStructuredSentence;
	}

	public function relationInSentence($relexStructuredSentence)
	{
		$sentenceText = strtolower($relexStructuredSentence['sentence']['text']);
		$relationWithoutPrefixStemmed =  $this->simpleStem($relexStructuredSentence['relation']['noPrefix']);

		if(stripos($sentenceText, $relationWithoutPrefixStemmed))
		{
			return 1;
		}

		return 0;
	}

	public function relationOutsideTerms(&$relexStructuredSentence)
	{
		$sentenceText = strtolower($relexStructuredSentence['sentence']['text']);
		$b1 = $relexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $relexStructuredSentence['terms']['second']['startIndex'];
		$relationWithoutPrefixStemmed =  $this->simpleStem($relexStructuredSentence['relation']['noPrefix']);

		if($b1 < $b2)
		{
			if(stripos(substr($sentenceText, 0, $b1), $relationWithoutPrefixStemmed))
			{
				return 1;
			}

			if(stripos(substr($sentenceText, $b2), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}
		else
		{
			if(stripos(substr($sentenceText, $b1), $relationWithoutPrefixStemmed))
			{
				return 1;
			}

			if(stripos(substr($sentenceText, 0, $b2), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}
		
		return 0;		
	}

	public function relationBetweenTerms(&$relexStructuredSentence)
	{
		$sentenceText = strtolower($relexStructuredSentence['sentence']['text']);
		$b1 = $relexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $relexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $relexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $relexStructuredSentence['terms']['second']['endIndex'];
		$relationWithoutPrefixStemmed =  $this->simpleStem($relexStructuredSentence['relation']['noPrefix']);

		if($b1 < $b2)
		{
			if(stripos(substr($sentenceText, $e1, $b2 - $e1), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}
		else
		{
			if(stripos(substr($sentenceText, $e2, $b1 - $e2), $relationWithoutPrefixStemmed))
			{
				return 1;
			}
		}

		return 0;
	}

	public function semicolonBetweenTerms(&$relexStructuredSentence)
	{
		$sentenceText = strtolower($relexStructuredSentence['sentence']['text']);
		$b1 = $relexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $relexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $relexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $relexStructuredSentence['terms']['second']['endIndex'];

		if($b1 < $b2)
		{
			if(stripos(substr($sentenceText, $e1, $b2 - $e1), ';')){
				return 1;
			}
		}
		else
		{
			if(stripos(substr($sentenceText, $e2, $b1 - $e2), ';')){
				return 1;
			}
		}

		return 0;
	}

	public function commaSeparatedTerms(&$relexStructuredSentence)
	{
		$sentenceText = strtolower($relexStructuredSentence['sentence']['text']);
		$b1 = $relexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $relexStructuredSentence['terms']['second']['startIndex'];
		$firstTerms = strtolower($relexStructuredSentence['terms']['first']['text']);
		$e1 = $relexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $relexStructuredSentence['terms']['second']['endIndex'];
		$secondTerms = strtolower($relexStructuredSentence['terms']['second']['text']);
		$relationWithoutPrefixStemmed =  $this->simpleStem($relexStructuredSentence['relation']['noPrefix']);

		if($b1 < $b2)
		{
			$textWithAndBetweenTerms = substr($sentenceText, $b1, $e2 - $b1);
		}
		else
		{
			$textWithAndBetweenTerms = substr($sentenceText, $b2, $e1 - $b2);
		}


		$numberOfWordsBetweenTerms = str_word_count($textWithAndBetweenTerms);
		$numberOfCommasBetweenTerms = substr_count($textWithAndBetweenTerms, ",");

		if($numberOfWordsBetweenTerms < (($numberOfCommasBetweenTerms * 3) + 1))
		{
			return 1;
		}

		// $firstTerms = str_replace(["(", ")"], "", $firstTerms);
		// $secondTerms = str_replace(["(", ")"], "", $secondTerms);


		// $firstTerms = preg_quote($firstTerms, '/');
		// $secondTerms = preg_quote($firstTerms, '/');


		// if(preg_match("/(" . $firstTerms . ")\s+\,\s+( " . $secondTerms . ")/", $sentenceText))
		// {
		// 	return 1;
		// }

		$pattern = '#' . preg_quote($firstTerms, '#') . '\s*(and|or|,)\s*' . preg_quote($secondTerms, '#') . '#i';

		try {
			if(preg_match_all($pattern, $sentenceText, $matches, PREG_OFFSET_CAPTURE))
			{
				foreach($matches as $match)
				{
					if(filter_var(
					    $b1, 
					    FILTER_VALIDATE_INT, 
					    array(
					        'options' => array(
					            'min_range' => $match[0][1], 
					            'max_range' => (strlen($match[0][0]) + $match[0][1])
					        )
					    )
					))
					{
						// return array("andor" => $matches);					
						return 1;
					}
				}
			}
		}
		catch (Exception $e)
		{
			dd($relexStructuredSentence);
		}
			
		return 0;
	}

	public function parenthesisAroundTerms(&$relexStructuredSentence)
	{
		$sentenceText = strtolower($relexStructuredSentence['sentence']['text']);
		$firstTerms = strtolower($relexStructuredSentence['terms']['first']['text']);
		$secondTerms = strtolower($relexStructuredSentence['terms']['second']['text']);

		if(stripos($sentenceText, "(" . $firstTerms . ")") !== false)
		{
			return 1;
		}

		if(stripos($sentenceText, "(" . $secondTerms . ")") !== false)
		{
			return 1;
		}

		$pattern = '#\([^)]*' . preg_quote($firstTerms, '#') . '[^)]*\)#i';

		if(preg_match_all($pattern, $sentenceText, $matches, PREG_OFFSET_CAPTURE))
		{
			foreach($matches as $match)
			{
				if(filter_var(
				    $relexStructuredSentence['terms']['first']['startIndex'],
				    FILTER_VALIDATE_INT, 
				    array(
				        'options' => array(
				            'min_range' => $match[0][1], 
				            'max_range' => (strlen($match[0][0]) + $match[0][1])
				        )
				    )
				))
				{
					// return array("debug_first" => $matches);
					return 1;					
				}
			}
		}

		$pattern = '#\([^)]*' . preg_quote($secondTerms, '#') . '[^)]*\)#i';

		if(preg_match_all($pattern, $sentenceText, $matches, PREG_OFFSET_CAPTURE))
		{
			foreach($matches as $match)
			{
				if(filter_var(
				    $relexStructuredSentence['terms']['second']['startIndex'],
				    FILTER_VALIDATE_INT, 
				    array(
				        'options' => array(
				            'min_range' => $match[0][1], 
				            'max_range' => (strlen($match[0][0]) + $match[0][1])
				        )
				    )
				))
				{
					// return array("debug_second" => $matches);
					return 1;					
				}
			}
		}

		return 0;
	}

	public function overlappingTerms(&$relexStructuredSentence)
	{
		$firstTerms = strtolower($relexStructuredSentence['terms']['first']['text']);
		$secondTerms = strtolower($relexStructuredSentence['terms']['second']['text']);

		$firstTermsArray = explode(" ", $firstTerms);
		$secondTermsArray = explode(" ", $secondTerms);

		foreach($firstTermsArray as $term){
			if(in_array($term, $secondTermsArray)) {
				return 1;
			}
		}

		return 0;	
	}

	public function overlappingOffsets(&$relexStructuredSentence)
	{
		$b1 = $relexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $relexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $relexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $relexStructuredSentence['terms']['second']['endIndex'];

		if(filter_var(
		    $b2, 
		    FILTER_VALIDATE_INT, 
		    array(
		        'options' => array(
		            'min_range' => $b1, 
		            'max_range' => $e1
		        )
		    )
		) || filter_var(
		    $e2, 
		    FILTER_VALIDATE_INT, 
		    array(
		        'options' => array(
		            'min_range' => $b1, 
		            'max_range' => $e1
		        )
		    )
		) || filter_var(
		    $b1, 
		    FILTER_VALIDATE_INT, 
		    array(
		        'options' => array(
		            'min_range' => $b2, 
		            'max_range' => $e2
		        )
		    )
		) || filter_var(
		    $e1, 
		    FILTER_VALIDATE_INT, 
		    array(
		        'options' => array(
		            'min_range' => $b2, 
		            'max_range' => $e2
		        )
		    )
		))
		{
			// echo "<pre>";
			// echo $b1 . "\n", $e1 . "\n", $b2 . "\n", $e2. "\n";			
			// dd('yes');
			return 1;
		}
		else
		{
			// dd('no');
			return 0;
		}
	}

	public function simpleStem($relationWithoutPrefix){
		switch (strtolower($relationWithoutPrefix)) {
		    case 'cause':
		        return 'caus';
		    case 'location':
		        return 'locat';
		    case 'diagnose':
		        return 'diagnos';
		}

		return $relationWithoutPrefix;
	}

	public function store(&$parentEntity, $relexStructuredSentences, $inc) {
		// dd('test');

		$allEntities = array();

        foreach($relexStructuredSentences as $tKey => &$relexStructuredSentence) {
			$title = $parentEntity['title'] . "_index_" . $inc;
			$hash = md5(serialize(array_except($relexStructuredSentence, ['properties'])));

			if($dup = Entity::where('hash', $hash)->first()) {
				array_push($this->status['store']['error']['skipped_duplicates'], $tKey . " ---> " . $dup->_id);
				continue;
			}

            if (Auth::check()) {
                $user_id = Auth::user()->_id;
            } else  {
                $user_id = "crowdwatson";
            }

			$entity = [
				"_id" => 'entity/text/medical/relex-structured-sentence/' . $inc,
				"title" => strtolower($title),
				"domain" => $parentEntity['domain'],
				"format" => $parentEntity['format'],
				"tags" => ['unit'],
				"documentType" => "relex-structured-sentence",
				"parents" => [$parentEntity['_id']],
				"content" => $relexStructuredSentence,
				"hash" => $hash,
				"activity_id" => $this->activity->_id,
				"user_id" => $user_id,
				"updated_at" => new MongoDate(time()),
				"created_at" => new MongoDate(time())
			];

			array_push($allEntities, $entity);

			$inc++;

			array_push($this->status['store']['success'], $tKey . " ---> URI: {$entity['_id']}");
		}

		if(count($allEntities) > 1) {
			\DB::collection('entities')->insert($allEntities);
			Temp::truncate();
		}

		return $inc;
	}

	public function createActivity()
	{
		try {
			$this->activity->softwareAgent_id = "relexstructurer";
			$this->activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$this->activity->forceDelete();
			$this->status['error']['activity'] = $e->getMessage();
		}		
	}

	public function createSoftwareAgent(){
		try {
			if(!SoftwareAgent::find('relexstructurer'))
			{
				$this->softwareAgent->_id = "relexstructurer";
				$this->softwareAgent->label = "This component (pre)processes chang documents into structured relex documents";
				$this->softwareAgent->save();
			}
		} catch (Exception $e) {
			$this->status['error']['relexstructurer'] = $e->getMessage();
		}
	}	

	public function getLastDocumentInc()
	{
        $lastMongoURIUsed = Entity::where('format', 'text')->where('domain', 'medical')->where("documentType", 'relex-structured-sentence')->get(array("_id"));
    
        if(count($lastMongoURIUsed) > 0) {
            $lastMongoURIUsed = $lastMongoURIUsed->sortBy(function($entity) {
                return $entity->_id;
            }, SORT_NATURAL)->toArray();

            if(end($lastMongoURIUsed)){
                $lastMongoIDUsed = explode("/", end($lastMongoURIUsed)['_id']);
                $inc = end($lastMongoIDUsed) + 1;          
            }
        }
        else
        {
        	$inc = 0;
        }

        unset($lastMongoURIUsed);

        return $inc;		
	}

	// public function originalStore($parentEntity, $relexStructuredSentences)
	// {
	// 	// echo "storing";
	// 	// 		fastcgi_finish_request();

	// 	$tempEntityID = null;
	// 	$status = array();


	// 	try {
	// 		$activity = new Activity;
	// 		$activity->softwareAgent_id = "relexstructurer";
	// 		$activity->save();

	// 	} catch (Exception $e) {
	// 		// Something went wrong with creating the Activity
	// 		$activity->forceDelete();
	// 		$status['error']['activity'] = $e->getMessage();
	// 		return $status;
	// 	}

	// 	foreach($relexStructuredSentences as $relexStructuredSentenceKey => $relexStructuredSentenceKeyVal){
	// 		$title = $parentEntity->title . "_index_" . $relexStructuredSentenceKey;

	// 		try {
	// 			$entity = new Entity;
	// 			$entity->_id = $tempEntityID;
	// 			$entity->title = strtolower($title);
	// 			$entity->domain = $parentEntity->domain;
	// 			$entity->format = $parentEntity->format;
	// 			$entity->documentType = "relex-structured-sentence";
	// 			$entity->parents = array($parentEntity->_id);
	// 			$entity->content = $relexStructuredSentenceKeyVal;

	// 			unset($relexStructuredSentenceKeyVal['properties']);
	// 			$entity->hash = md5(serialize($relexStructuredSentenceKeyVal));
	// 			$entity->activity_id = $activity->_id;
	// 			$entity->save();

	// 			$status['success'][$title] = $title . " was successfully processed into a relex-structured-sentence. (URI: {$entity->_id})";
	// 		} catch (Exception $e) {
	// 			// Something went wrong with creating the Entity
	// 			$entity->forceDelete();
	// 			$status['error'][$title] = $e->getMessage();
	// 		}

	// 		$tempEntityID = $entity->_id;
	// 	}

	// 	return $status;
	// }
}