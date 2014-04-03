<?php

namespace Preprocess;

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use URL, Session, Exception, File, Input, Redirect;

use League\Csv\Reader as Reader;

class CSVresultMapper {

	public function processInputData($csvresult, $preview = false)
	{
		$csvLines = explode("\n", $csvresult['content']);
		$csvLines = array_filter($csvLines, 'strlen');

		$csv = Reader::createFromString(implode("\n", $csvLines));

		// $csvLines = explode("\n", $csvresult['content']);
		// dd($csvLines);

		$headers0 = $csv->fetchOne();
		$headers = array();
		$count = 0; $c1 = 0;
		foreach($headers0 as $h){
			if(empty($h)){
				$h = "empty$count";
				$count++;
			} 

			if(in_array($h, $headers)){
				$h = "$h$c1";
				$c1++;
			}

			$headers[] = $h;
		}
		
		$data = $csv->fetchAssoc($headers);

		$rowsMappedWithUnits = array();
		$batch = array();

		unset($data[0]); // Unsetting header
$count = 0;
		foreach ($data as $line_index => $row)
		{
			$count++;
			$entity = Entity::where('documentType', 'twrex-structured-sentence');

			if(isset($row['relation-type']))
			{
				$entity = $entity->where('content.relation.original', '=', $row['relation-type']);
			}
			elseif (isset($row['relation_type'])) 
			{
				$entity = $entity->where('content.relation.original', '=', $row['relation_type']);
			}
			else
			{
				continue;
			}

			if(isset($row['term1']))
			{
				if (substr($row['term1'], 0, 1) == '[')
					$entity = $entity->where('content.terms.first.formatted', '=', $row['term1']);
				else
					$entity = $entity->where('content.terms.first.text', '=', $row['term1']);
			}
			else
			{
				continue;
			}

			if(isset($row['term2']))
			{
				if (substr($row['term2'], 0, 1) == '[')
					$entity = $entity->where('content.terms.second.formatted', '=', $row['term2']);
				else
					$entity = $entity->where('content.terms.second.text', '=', $row['term2']);
			}
			else
			{
				continue;
			}
/*echo $row['term2'] . '>';			
echo $entity->first()->content['terms']['first']['startIndex'];
echo ' - ' . $row['b1'] . '. ';*/
			if(isset($row['b1']))
			{
				$entity = $entity->where('content.terms.first.startIndex', '=', (int) $row['b1']);
			}
			else
			{
				continue;
			}
		
			if(isset($row['e1']))
			{
				$entity = $entity->where('content.terms.first.endIndex', '=', (int) $row['e1']);
			}

			if(isset($row['b2']))
			{
				$entity = $entity->where('content.terms.second.startIndex', '=', (int) $row['b2']);
			}
			else
			{
				continue;
			}

			if(isset($row['e2']))
			{
				$entity = $entity->where('content.terms.second.endIndex', '=', (int) $row['e2']);
			}
			
			if(isset($row['unit_id0'])) unset($row['unit_id0']);
			
			if($result = $entity->first())
			{
			    $row['unit'] = $result->toArray();
			    array_push($rowsMappedWithUnits, $row);
			    array_push($batch, $result->toArray()['_id']);	
			} else {
				print_r($row);
				echo "\r\n";
			}
		}
		
		if(count($rowsMappedWithUnits) != $count)
			return count($rowsMappedWithUnits) . " != $count";
		

		if($preview)
		{
			return $rowsMappedWithUnits;
		}

		return $batch;
	}

	public function processAnnotationData($csvresult, $preview = false)
	{
		$csv = Reader::createFromString($csvresult['content']);
		$csv->setDelimiter(';');

		$csvLines = explode("\n", $csvresult['content']);

		$headers = $csv->fetchOne();

		// dd($csv->fetchAll());

		$data = $csv->fetchAssoc($headers);

		$rowsMappedWithUnits = array();
		$batch = array();

		unset($data[0]); // Unsetting header



		foreach ($data as $line_index => $row)
		{
			$entity = Entity::where('documentType', 'twrex-structured-sentence');

			if(isset($row['relation-type']))
			{
				$entity = $entity->where('content.relation.original', '=', $row['relation-type']);
			}
			else
			{
				continue;
			}

			if(isset($row['term1']))
			{
				$entity = $entity->where('content.terms.first.text', '=', $row['term1']);
			}
			else
			{
				continue;
			}

			if(isset($row['term2']))
			{
				$entity = $entity->where('content.terms.second.text', '=', $row['term2']);
			}
			else
			{
				continue;
			}

			if(isset($row['b1']))
			{
				$entity = $entity->where('content.terms.first.startIndex', '=', (int) $row['b1']);
			}
			else
			{
				continue;
			}

			if(isset($row['e1']))
			{
				$entity = $entity->where('content.terms.first.endIndex', '=', (int) $row['e1']);
			}

			if(isset($row['b2']))
			{
				$entity = $entity->where('content.terms.second.startIndex', '=', (int) $row['b2']);
			}
			else
			{
				continue;
			}

			if(isset($row['e2']))
			{
				$entity = $entity->where('content.terms.second.endIndex', '=', (int) $row['e2']);
			}

			if($result = $entity->first())
			{
			    $row['unit'] = $result->toArray();
			    array_push($rowsMappedWithUnits, $row);
			    array_push($batch, $result->toArray()['_id']);	
			}
			
		}

		if($preview)
		{
			return $rowsMappedWithUnits;
		}

		return $batch;
	}

}