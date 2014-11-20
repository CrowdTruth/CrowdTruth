<?php

namespace Api\search;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;

use League\Csv\Reader as Reader;
use League\Csv\Writer as Writer;

class apiController extends BaseController {

	protected $repository;

	public function __construct(Repository $repository){
		$this->repository = $repository;
	}

    protected $operators = array(
    	'=' , '<', '>', '<=', '>=', '<>', 'like'
    );	

	public function getIndex()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('match'))
    	{
			$collection = $this->processFields($collection);
		}

		// if(!array_key_exists('noCache', Input::all()))
		// {
		// 	$collection = $collection->remember(1, md5(serialize(array_values(Input::except('pretty')))));
		// }

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);
		$only = Input::get('only', array());

		// return $collection = ["entries" => $collection->skip($start)->take($limit)->get($only)->toArray()];

		if($orderBy = Input::get('orderBy')){
			foreach($orderBy as $sortingColumnName => $sortingDirection)
			{
				$collection = $collection->orderBy($sortingColumnName, $sortingDirection);
			}
		}

		$collection = $collection->paginate($limit, $only);
		$pagination = $collection->links()->render();
		$count = $collection->toArray();
		unset($count['data']);
		$documents = $collection->toArray()['data'];

		if(array_key_exists('tocsv', Input::all()))
		{	
			set_time_limit(1200);
			$writer = new Writer(new \SplTempFileObject);
			$writer->setNullHandlingMode(Writer::NULL_AS_EMPTY);


			$headerDotted = array();

			foreach($documents as $line_index => $row)
			{
				unset($row['metrics'], $row['platformJobId'], $row['results'], $row['cache']);

				if(isset($row['parents']))
				{
					$row['wasDerivedFrom'] = implode(",", $row['parents']);
					unset($row['parents']);
				}

				foreach(array_dot($row) as $k => $v)
				{
					array_push($headerDotted, $k);
				}

			}

			$headerDotted = array_unique($headerDotted);
			natcasesort($headerDotted);

			$csvHeader = array_change_key_case(str_replace('.', '_', array_values($headerDotted)), CASE_LOWER);
			$writer->insertOne($csvHeader);

			foreach($documents as $line_index => $row)
			{
				if(isset($row['parents']))
				{
					$row['wasDerivedFrom'] = implode(",", $row['parents']);
					unset($row['parents']);
				}

				$row = array_dot($row);

				foreach($headerDotted as $column)
				{
					if(isset($row[$column]))
					{
						$csvRow[str_replace('.', '_', $column)] = $row[$column];
					}
					else
					{
						$csvRow[str_replace('.', '_', $column)] = "";
					}
				}				

				$writer->insertOne($csvRow);
			}

			$writer->output(time() . '.csv');

			die;
		}		

		return Response::json([
			"count" => $count,
			"pagination" => $pagination,
			"searchQuery" => Input::except('page'),
			"documents" => $documents
			]);

		if(array_key_exists('getQueryLog', Input::all()))
		{
			return Response::json(\DB::getQueryLog());
		}		

		if(array_key_exists('pretty', Input::all()))
		{	
			echo "<pre>";
			return json_encode($collection->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}

		return Response::json($collection->toArray());

	}

	public function recur_ksort(&$array) {
	   foreach ($array as &$value) {
	      if (is_array($value)) $this->recur_ksort($value);
	   }
	   return ksort($array);
	}	

	public function anyPost()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(isset(Input::get('match')['_id']))
    	{
			$collection = $this->processFields($collection);

			if($data = Input::get('data'))
			{
				$data = json_decode($data, true);

				$collection->update($data, array('upsert' => true));
			}

			return $collection->get();			
		}
	}

	public function anyPut()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(isset(Input::get('match')['_id']))
    	{
			$collection = $this->processFields($collection);

			if($data = Input::get('data'))
			{
				$data = json_decode($data, true);

				$original = $collection->first();
				$originalArray = $original->toArray();

				if(array_key_exists(key($data), $originalArray))
				{
					$merged = array_replace_recursive($originalArray, $data);
					$original->update($merged, array('upsert' => true));
				}

				return Response::json($original);
			}			
		}
	}

	protected function processFields($collection)
	{
		foreach(Input::get('match') as $field => $value)
		{
			if(is_array($value))
			{
				foreach($value as $operator => $subvalue)
				{
					if(is_int($operator) || $operator == "")
					{
						$collection = $collection->whereIn($field, array($subvalue));
						continue;
					}

					if(in_array($operator, $this->operators))
					{
						if(is_numeric($subvalue))
						{
							$subvalue = (double) $subvalue;
							

						}

						if($operator == "like")
						{
							$collection = $collection->where($field, $operator, "%" . preg_quote($subvalue, '/') . "%");
						}
						elseif($field == "created_at" || $field == "updated_at")
						{
							$date = new \DateTime($subvalue);

							if($operator == "<=")
							{
								$date->add(new \DateInterval('P1D'));
								$operator = "<";
							}

							$collection = $collection->where($field, $operator, $date);
						}
						else
						{
							$collection = $collection->where($field, $operator, $subvalue);
						}		
					}
				}

			}
			else
			{
				if(is_numeric($value))
				{
					$value = (int) $value;
				}					

				$collection = $collection->whereIn($field, array($value));
			}
		}

		return $collection;		
	}
}