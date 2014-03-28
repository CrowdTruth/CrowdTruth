<?php

namespace Api\v1;

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

    // protected $operators = array(
    //     '=', '<', '>', '<=', '>=', '<>', '!=',
    //     'like', 'not like', 'between', 'ilike',
    //     '&', '|', '^', '<<', '>>',
    //     'exists', 'type', 'mod', 'where', 'all', 'size', 'regex',
    // );

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		// return Input::all();

		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('field'))
    	{
			$collection = $this->processFields($collection);
		}

		if(!array_key_exists('noCache', Input::all()))
		{
			$collection = $collection->remember(1, md5(serialize(array_values(Input::except('pretty')))));
		}

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);
		$only = Input::get('only', array());

		if(Input::has('datatables'))
		{
			$start = (int) Input::get('iDisplayStart', 0);
			$limit = (int) Input::get('iDisplayLength', 100);

			$sortingColumnIndex = (int) Input::get('iSortCol_0', 0);
			$sortingColumnName = Input::get('mDataProp_' . $sortingColumnIndex, '_id');
			$sortingDirection = Input::get('sSortDir_0', 'asc');

			$sortingColumnName = $sortingColumnName == "_id" ? "natural" : $sortingColumnName;

			$iTotalDisplayRecords = new \MongoDB\Entity;
			$iTotalDisplayRecords = $this->processFields($iTotalDisplayRecords);
			$iTotalDisplayRecords = $iTotalDisplayRecords->count();
		
			$collection = $collection->skip($start)->orderBy($sortingColumnName, $sortingDirection)->take($limit)->get($only);

			if($input = Input::get('field'))
			{
				$iTotalRecords = new \MongoDB\Entity;

				if(isset($input['format']))
				{
					$iTotalRecords = $iTotalRecords->whereIn('format', array_flatten([$input['format']]));
				}

				if(isset($input['domain']))
				{
					$iTotalRecords = $iTotalRecords->whereIn('domain', array_flatten([$input['domain']]));
				}

				if(isset($input['documentType']))
				{
					$iTotalRecords = $iTotalRecords->whereIn('documentType', array_flatten([$input['documentType']]));
				}

				$iTotalRecords = $iTotalRecords->count();
			}

			return Response::json([
		        "sEcho" => Input::get('sEcho', 10),
		        "iTotalRecords" => $iTotalRecords,
		        "iTotalDisplayRecords" => $iTotalDisplayRecords,
		        "aaData" => $collection->toArray()
		   ]);			
		}

		$collection = $collection->skip($start)->take($limit)->get($only);

		if(array_key_exists('getQueryLog', Input::all()))
		{
			return Response::json(\DB::getQueryLog());
		}		

		if(array_key_exists('pretty', Input::all()))
		{	
			echo "<pre>";
			return json_encode($collection->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}

		if(array_key_exists('tocsv', Input::all()))
		{	
			$documents = $collection->toArray();

			$writer = new Writer(new \SplTempFileObject);

			foreach($documents as $documentKey => $documentValue)
			{
				if(!isset($documentValue['content']['sentence']['formatted']))
				{
					$documentValue['content']['sentence']['formatted'] = " ";
				}

				$this->recur_ksort($documentValue['content']);

				$row['_id'] = $documentValue['_id'];

				if(isset($documentValue['parents']))
				{
					$row['wasDerivedFrom'] = implode(",", $documentValue['parents']);
				}

				$row['content'] = $documentValue['content'];

				if($documentKey == 0)
				{
					$writer->insertOne(array_change_key_case(str_replace('.', '_', array_keys(array_dot($row))), CASE_LOWER));
				}

				$writer->insertOne(array_flatten($row));
			}

			$writer->output('test.csv');

			die;
			// return array_dot($csv);
		}

		return Response::json($collection);

	}

	public function recur_ksort(&$array) {
	   foreach ($array as &$value) {
	      if (is_array($value)) $this->recur_ksort($value);
	   }
	   return ksort($array);
	}

	public function getMin($specificField = null)
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('field'))
    	{
			$collection = $this->processFields($collection);
		}

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);

		return $collection->skip($start)->take($limit)->min($specificField);		
	}

	public function getMax($specificField = null)
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('field'))
    	{
			$collection = $this->processFields($collection);
		}

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);

		return $collection->skip($start)->take($limit)->max($specificField);				
	}

	public function getAvg($specificField = null)
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('field'))
    	{
			$collection = $this->processFields($collection);
		}

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);

		return $collection->skip($start)->take($limit)->avg($specificField);		
	}

	public function getSum($specificField = null)
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('field'))
    	{
			$collection = $this->processFields($collection);
		}

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);

		return $collection->skip($start)->take($limit)->sum($specificField);		
	}

	public function getCount()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('field'))
    	{
			$collection = $this->processFields($collection);
		}

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);

		return $collection->skip($start)->take($limit)->count();
	}

	public function anyPost()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(isset(Input::get('field')['_id']))
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

    	if(isset(Input::get('field')['_id']))
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
		foreach(Input::get('field') as $field => $value)
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
							$subvalue = (int) $subvalue;
						}

						if($operator == "like")
						{
							$collection = $collection->where($field, $operator, "%" . $subvalue . "%");
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