<?php

namespace Api\v2;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;

class apiController extends BaseController {

	protected $repository;

	public function __construct(Repository $repository){
		$this->repository = $repository;
	}

    protected $operators = array(
    	'=' , '<', '>', '<>'
    );	

	public function getDistinct($field = null)
    {
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

		if($collection->getTable() == "useragents")
		{
			return Response::json(\User::all());
		}		

    	if(Input::has('field'))
    	{
    		$collection = $this->processFields($collection);
    	}
    	
    	$collection = array_flatten($collection->distinct($field)->get()->toArray());
    	return Response::json($collection);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		// echo "<pre>";
		// dd(Input::all());

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

			$count = new \MongoDB\Entity;
			$count = $this->processFields($count);
			$count = $count->count();

			$collection = $collection->skip($start)->orderBy($sortingColumnName, $sortingDirection)->take($limit)->get($only);

			return Response::json([
		        "sEcho" => Input::get('sEcho', 10),
		        "iTotalRecords" => $count,
		        "iTotalDisplayRecords" => $count,
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

		return Response::json($collection);

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

						$collection = $collection->where($field, $operator, $subvalue);
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