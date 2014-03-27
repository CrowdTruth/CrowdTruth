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
		$csv = Reader::createFromString($csvresult['content']);

		// $csvLines = explode("\n", $csvresult['content']);
		// dd($csvLines);

		$headers = $csv->fetchOne();

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

	public function processAnnotationData($csvresult, $preview = false)
	{
		$csv = Reader::createFromString($csvresult['content']);
		$csv->setDelimiter(';');

		$csvLines = explode("\n", $csvresult['content']);

		$headers = $csv->fetchOne();

		// dd($csv->fetchAll());

		$data = $csv->fetchAssoc($headers);

		dd($data);

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