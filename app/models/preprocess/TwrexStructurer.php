<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDate;
use Exception, Auth;

class TwrexStructurer {

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

	public function process($twrex, $preview = false)
	{
		$twrexLines = $this->array_unique_multidimensional(explode("\n", $twrex['content']));
		$twrex['content'] = null;

		if($preview)
		{
			$twrexLines = array_slice($twrexLines, 0, 100);
			return $this->processLines($twrexLines);
		}

		if(count($twrexLines) > 10000)
		{
			$arrayChunks = array_chunk($twrexLines, 10000);
		}
		else
		{
			$arrayChunks = array($twrexLines);
		}

		unset($twrexLines);

		$this->createSoftwareAgent();
		$this->createActivity();
		$inc = $this->getLastDocumentInc();

		foreach($arrayChunks as &$chunkVal)
		{
			try {
				$inc = $this->store($twrex, $this->processLines($chunkVal), $inc);		
			} catch(Exception $e) {
				$this->status['store'] = $e->getMessage();
				$this->activity->forceDelete();
			}

		}

		return $this->status;
	}

	public function processLines(&$twrexLines)
	{
		// fastcgi_finish_request();
		// dd(count($twrexLines));

		$twrexStructuredSentences = array();
		$tempTwrexStructuredSentences = array();

		foreach($twrexLines as $twrexLineKey => &$twrexLineVal)
		{
			$twrexLineSegments = explode("\t", $twrexLineVal);

			if($twrexLineSegments[0] == "0")
			{
				unset($twrexLineSegments[0]);
				$twrexLineSegments = array_values($twrexLineSegments);
			}

			if(count($twrexLineSegments) < 8)
			{
				continue;
			}

			// if($twrexLineKey < 340 || $twrexLineKey > 1000)
			// 	continue;
				
			$tempTwrexStructuredSentence = [
				"relation" => [
					"original" => $twrexLineSegments[0],
					"noPrefix" => explode("-", $twrexLineSegments[0])[1]
				],
				"terms" => [
					"first" => [
						"startIndex" => (int) $twrexLineSegments[2],
						"endIndex" => (int) $twrexLineSegments[3],
						"text" => $twrexLineSegments[1]
					],
					"second" => [
						"startIndex" => (int) $twrexLineSegments[5],
						"endIndex" => (int) $twrexLineSegments[6],
						"text" => $twrexLineSegments[4]
					]
				],
				"sentence" => [
					"text" => $twrexLineSegments[7]
				]
			];

			array_push($tempTwrexStructuredSentences, $this->getAllTermCombinations($tempTwrexStructuredSentence));
		}

		// dd(count($this->array_unique_multidimensional($tempTwrexStructuredSentences)));
		// dd(count($tempTwrexStructuredSentences));

		unset($twrexLines);

		$tempTwrexStructuredSentences = $this->array_unique_multidimensional($tempTwrexStructuredSentences);

		$length = count($tempTwrexStructuredSentences);

		for($i = 0; $i < $length; $i++)
		{
			$tempTwrexStructuredSentence = $tempTwrexStructuredSentences[$i];
			unset($tempTwrexStructuredSentences[$i]);

			foreach($tempTwrexStructuredSentence['terms']['first'] as $firstTerm)
			{
				foreach($tempTwrexStructuredSentence['terms']['second'] as $secondTerm)
				{
					$twrexStructuredSentence = $tempTwrexStructuredSentence;
					$twrexStructuredSentence['terms']['first'] = $firstTerm;
					$twrexStructuredSentence['terms']['second'] = $secondTerm;

					if($this->overlappingOffsets($twrexStructuredSentence))
					{
						// array_push($overlappingOffsetSentences, $twrexStructuredSentence);
						continue;
					}

					$twrexStructuredSentence['properties'] = [
						"sentenceWordCount" => str_word_count($twrexStructuredSentence['sentence']['text']),
						"relationInSentence" => $this->relationInSentence($twrexStructuredSentence),
						"relationOutsideTerms" => $this->relationOutsideTerms($twrexStructuredSentence),
						"relationBetweenTerms" => $this->relationBetweenTerms($twrexStructuredSentence),
						"semicolonBetweenTerms" => $this->semicolonBetweenTerms($twrexStructuredSentence),
						"commaSeparatedTerms" => $this->commaSeparatedTerms($twrexStructuredSentence),
						"parenthesisAroundTerms" => $this->parenthesisAroundTerms($twrexStructuredSentence),
						"overlappingTerms" => $this->overlappingTerms($twrexStructuredSentence)
					];

					$twrexStructuredSentence = $this->formatUppercase($twrexStructuredSentence);

					ksort($twrexStructuredSentence['terms']);

					array_push($twrexStructuredSentences, $twrexStructuredSentence);

				}
			}

			// unset($tempTwrexStructuredSentences[$tKey]);
		}

		return $twrexStructuredSentences;

		// $allHashes = array();
		// $duplicates = array();

		// foreach($twrexStructuredSentences as $u)
		// {
		// 	$hash = sha1(serialize($u));
		// 	$u['hash'] = $hash;

		// 	if(in_array($hash, $allHashes)){
		// 		array_push($duplicates, $u);
		// 	}
		// 	else
		// 	{
		// 		array_push($allHashes, $hash);
		// 	}
			
		// }

		// return $duplicates;

		// dd(count($twrexStructuredSentences));

		// dd(count(array_unique($twrexStructuredSentences, SORT_REGULAR)));

		// return $this->array_unique_multidimensional($twrexStructuredSentences);

		// dd(count($this->array_unique_multidimensional($twrexStructuredSentences)));

		// echo count($twrexStructuredSentences) . PHP_EOL;
		// echo count(array_unique($twrexStructuredSentences, SORT_REGULAR)) . PHP_EOL;

		// exit;
		// // return array_slice($twrexStructuredSentences, 0, 100);

		return $twrexStructuredSentences;
	}

	public function array_unique_multidimensional($array)
	{
		return array_values(array_intersect_key($array, array_unique(array_map('serialize', $array))));	    
	}	

	public function formatUppercase($twrexStructuredSentence)
	{
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];

		$firstTermUppercaseWithBrackets = "[" . strtoupper($twrexStructuredSentence['terms']['first']['text']) . "]";
		$secondTermUppercaseWithBrackets = "[" . strtoupper($twrexStructuredSentence['terms']['second']['text']) . "]";

		$twrexStructuredSentence['terms']['first']['formatted'] = $firstTermUppercaseWithBrackets;
		$twrexStructuredSentence['terms']['second']['formatted'] = $secondTermUppercaseWithBrackets;

		// if($twrexStructuredSentence['properties']['overlappingTerms'] == "1")
		// {
		// 	// dd($twrexStructuredSentence);
		// 	return $twrexStructuredSentence;
		// }		

		$formattedSentence = $twrexStructuredSentence['sentence']['text'];

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


		$twrexStructuredSentence['sentence']['formatted'] = $formattedSentence;

		return $twrexStructuredSentence;
	}

	public function getAllTermCombinations(&$tempTwrexStructuredSentence)
	{
		$firstTerm = strtolower($tempTwrexStructuredSentence['terms']['first']['text']);
		$secondTerm = strtolower($tempTwrexStructuredSentence['terms']['second']['text']);
		$sentenceText = strtolower($tempTwrexStructuredSentence['sentence']['text']);

		// $firstTerm = '/\b' . preg_quote($firstTerm, '/') . '\b/'; Use this for matching on whole words only
		$firstTerm = '/' . preg_quote($firstTerm, '/') . '/';
		preg_match_all($firstTerm, $sentenceText, $firstTermMatch, PREG_OFFSET_CAPTURE);

		// $secondTerm = '/\b' . preg_quote($secondTerm, '/') . '\b/'; Use this for matching on whole words only
		$secondTerm = '/' . preg_quote($secondTerm, '/') . '/';
		preg_match_all($secondTerm, $sentenceText, $secondTermMatch, PREG_OFFSET_CAPTURE);

		if(count($firstTermMatch[0]) > 0)
		{
			unset($tempTwrexStructuredSentence['terms']['first']);

			foreach($firstTermMatch[0] as $firstTermOccurenceKey => $firstTermOccurenceVal)
			{
				$tempTwrexStructuredSentence['terms']['first'][$firstTermOccurenceKey] = [
					"text" => $firstTermOccurenceVal[0],
					"startIndex" => $firstTermOccurenceVal[1],
					"endIndex" => $firstTermOccurenceVal[1] + strlen($firstTermOccurenceVal[0])
				];
			}
		} else {
			$tempTwrexStructuredSentence['terms']['first'] = array($tempTwrexStructuredSentence['terms']['first']);
			// dd($tempTwrexStructuredSentence);
		}

		if(count($secondTermMatch[0]) > 0)
		{
			unset($tempTwrexStructuredSentence['terms']['second']);

			foreach($secondTermMatch[0] as $secondTermOccurenceKey => $secondTermOccurenceVal)
			{
				$tempTwrexStructuredSentence['terms']['second'][$secondTermOccurenceKey] = [
					"text" => $secondTermOccurenceVal[0],
					"startIndex" => $secondTermOccurenceVal[1],
					"endIndex" => $secondTermOccurenceVal[1] + strlen($secondTermOccurenceVal[0])
				];
			}
		} else {
			$tempTwrexStructuredSentence['terms']['second'] = array($tempTwrexStructuredSentence['terms']['second']);
			// dd($tempTwrexStructuredSentence);
		}

		return $tempTwrexStructuredSentence;
	}

	public function relationInSentence($twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

		if(stripos($sentenceText, $relationWithoutPrefixStemmed))
		{
			return 1;
		}

		return 0;
	}

	public function relationOutsideTerms(&$twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

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

	public function relationBetweenTerms(&$twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

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

	public function semicolonBetweenTerms(&$twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];

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

	public function commaSeparatedTerms(&$twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$firstTerms = strtolower($twrexStructuredSentence['terms']['first']['text']);
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];
		$secondTerms = strtolower($twrexStructuredSentence['terms']['second']['text']);				
		$relationWithoutPrefixStemmed =  $this->simpleStem($twrexStructuredSentence['relation']['noPrefix']);

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
			dd($twrexStructuredSentence);
		}
			
		return 0;
	}

	public function parenthesisAroundTerms(&$twrexStructuredSentence)
	{
		$sentenceText = strtolower($twrexStructuredSentence['sentence']['text']);
		$firstTerms = strtolower($twrexStructuredSentence['terms']['first']['text']);
		$secondTerms = strtolower($twrexStructuredSentence['terms']['second']['text']);				

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
				    $twrexStructuredSentence['terms']['first']['startIndex'], 
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
				    $twrexStructuredSentence['terms']['second']['startIndex'], 
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

	public function overlappingTerms(&$twrexStructuredSentence)
	{
		$firstTerms = strtolower($twrexStructuredSentence['terms']['first']['text']);
		$secondTerms = strtolower($twrexStructuredSentence['terms']['second']['text']);

		$firstTermsArray = explode(" ", $firstTerms);
		$secondTermsArray = explode(" ", $secondTerms);

		foreach($firstTermsArray as $term){
			if(in_array($term, $secondTermsArray)) {
				return 1;
			}
		}

		return 0;	
	}

	public function overlappingOffsets(&$twrexStructuredSentence)
	{
		$b1 = $twrexStructuredSentence['terms']['first']['startIndex'];
		$b2 = $twrexStructuredSentence['terms']['second']['startIndex'];
		$e1 = $twrexStructuredSentence['terms']['first']['endIndex'];
		$e2 = $twrexStructuredSentence['terms']['second']['endIndex'];	

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

	public function store(&$parentEntity, $twrexStructuredSentences, $inc)
	{
		// dd('test');

		$allEntities = array();

        foreach($twrexStructuredSentences as $tKey => &$twrexStructuredSentence)
        {

			$title = $parentEntity['title'] . "_index_" . $inc;

			$hash = md5(serialize(array_except($twrexStructuredSentence, ['properties'])));

			if($dup = Entity::where('hash', $hash)->first())
			{
				array_push($this->status['store']['error']['skipped_duplicates'], $tKey . " ---> " . $dup->_id);
				continue;
			}

            if (Auth::check())
            {
                $user_id = Auth::user()->_id;
            } else 
            {
                $user_id = "crowdwatson";
            } 			

			$entity = [
				"_id" => 'entity/text/medical/twrex-structured-sentence/' . $inc,
				"title" => strtolower($title),
				"domain" => $parentEntity['domain'],
				"format" => $parentEntity['format'],
				"tags" => ['unit'],
				"documentType" => "twrex-structured-sentence",
				"parents" => [$parentEntity['_id']],
				"content" => $twrexStructuredSentence,
				"hash" => $hash,
				"activity_id" => $this->activity->_id,
				"user_id" => $user_id,
				"updated_at" => new MongoDate(time()),
				"created_at" => new MongoDate(time())
			];

			array_push($allEntities, $entity);

			$inc++;

			// array_push($this->status['store']['success'], $tKey);

			array_push($this->status['store']['success'], $tKey . " ---> URI: {$entity['_id']}");

			// $this->status['store']['success'][$title] = "URI: {$entity['_id']})";
		}

		if(count($allEntities) > 1)
		{
			\DB::collection('entities')->insert($allEntities);
			\MongoDB\Temp::truncate();
		}

		//	$allEntities = array_slice($allEntities, 0, 100);

		// dd('yes');

		// if(count($allEntities) > 1)
		// {
		// 	if(count($allEntities) > 20000)
		// 	{
		// 		$chunkSize = ceil(count($allEntities) / 20000);
		// 		$arrayChunks = array_chunk($allEntities, $chunkSize);
		// 		unset($allEntities);

		// 		foreach($arrayChunks as $chunkKey => &$chunkVal)
		// 		{
		// 			try{
		// 				\DB::collection('entities')->insert($chunkVal);
		// 			} catch (Exception $e) {
		// 				$status['error']['insert_chunk' . $chunkKey] = $e->getMessage();
		// 			}
		// 		}	
		// 	}
		// 	else
		// 	{
		// 		try{
		// 			\DB::collection('entities')->insert($allEntities);
		// 		} catch (Exception $e) {
		// 			$status['error']['insert_batch'] = $e->getMessage();
		// 		}

		// 	}
		// }
		// else
		// {
		// 	$activity->forceDelete();
		// }

		return $inc;
	}

	public function createActivity()
	{
		try {
			$this->activity->softwareAgent_id = "twrexstructurer";
			$this->activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$this->activity->forceDelete();
			$this->status['error']['activity'] = $e->getMessage();
		}		
	}

	public function createSoftwareAgent(){
		try {
			if(!\MongoDB\SoftwareAgent::find('twrexstructurer'))
			{
				$this->softwareAgent->_id = "twrexstructurer";
				$this->softwareAgent->label = "This component (pre)processes chang documents into structured twrex documents";
				$this->softwareAgent->save();
			}
		} catch (Exception $e) {
			$this->status['error']['twrexstructurer'] = $e->getMessage();
		}
	}	

	public function getLastDocumentInc()
	{
        $lastMongoURIUsed = Entity::where('format', 'text')->where('domain', 'medical')->where("documentType", 'twrex-structured-sentence')->get(array("_id"));
    
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

	// public function originalStore($parentEntity, $twrexStructuredSentences)
	// {
	// 	// echo "storing";
	// 	// 		fastcgi_finish_request();

	// 	$tempEntityID = null;
	// 	$status = array();


	// 	try {
	// 		$activity = new Activity;
	// 		$activity->softwareAgent_id = "twrexstructurer";
	// 		$activity->save();

	// 	} catch (Exception $e) {
	// 		// Something went wrong with creating the Activity
	// 		$activity->forceDelete();
	// 		$status['error']['activity'] = $e->getMessage();
	// 		return $status;
	// 	}

	// 	foreach($twrexStructuredSentences as $twrexStructuredSentenceKey => $twrexStructuredSentenceKeyVal){
	// 		$title = $parentEntity->title . "_index_" . $twrexStructuredSentenceKey;

	// 		try {
	// 			$entity = new Entity;
	// 			$entity->_id = $tempEntityID;
	// 			$entity->title = strtolower($title);
	// 			$entity->domain = $parentEntity->domain;
	// 			$entity->format = $parentEntity->format;
	// 			$entity->documentType = "twrex-structured-sentence";
	// 			$entity->parents = array($parentEntity->_id);
	// 			$entity->content = $twrexStructuredSentenceKeyVal;

	// 			unset($twrexStructuredSentenceKeyVal['properties']);
	// 			$entity->hash = md5(serialize($twrexStructuredSentenceKeyVal));
	// 			$entity->activity_id = $activity->_id;
	// 			$entity->save();

	// 			$status['success'][$title] = $title . " was successfully processed into a twrex-structured-sentence. (URI: {$entity->_id})";
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