<?php

namespace Api\v4;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;
use \Paginator as Paginator;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;

/**
 * This apiController is used for the worker overview and the individual worker view,
 *
 * Check the files workers/overview and workers/worker
 **/
class apiController extends BaseController {

	protected $repository;

	public function __construct(Repository $repository){
		$this->repository = $repository;
	}

    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'exists', 'type', 'mod', 'where', 'all', 'size', 'regex',
    );

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{	
		// $documents = $this->repository->returnCollectionObjectFor($collection)->where('documentType', $documentType);
		// if(Input::has('id')){
		// 	$documents = $this->repository->returnCollectionObjectFor("crowdagent")->with('hasDoneJobs')->with('hasGeneratedworkerunits');
		// } else {

				
		//The following  block returns one worker for the individual worker view
		if(Input::has('id')) {
		
			$id = Input::get('id');
			
			$result = \MongoDB\CrowdAgent::with('hasGeneratedWorkerunits.hasJob')->with('hasGeneratedWorkerunits.hasUnit')->where('_id', $id)->get();

			$result = $result->toArray();
			
			$flattened = array();

			foreach($result as $resultValue)
			{
				if(count($resultValue['hasGeneratedWorkerunits']) > 0)
				{
					$resultValue['jobs'] = array();

					foreach($resultValue['hasGeneratedWorkerunits'] as $hasGeneratedWorkerunitKey => $hasGeneratedWorkerunitVal)
					{
						array_push($resultValue['jobs'], $hasGeneratedWorkerunitVal['hasJob']);
						unset($resultValue['hasGeneratedWorkerunits'][$hasGeneratedWorkerunitKey]['hasJob']);
					}

					$resultValue['jobs'] = array_unique($resultValue['jobs'], SORT_REGULAR);

					$resultValue['units'] = array();

					foreach($resultValue['hasGeneratedWorkerunits'] as $hasGeneratedWorkerunitKey => $hasGeneratedWorkerunitVal)
					{
						array_push($resultValue['units'], $hasGeneratedWorkerunitVal['hasUnit']);
						unset($resultValue['hasGeneratedWorkerunits'][$hasGeneratedWorkerunitKey]['hasUnit']);
					}

					$resultValue['units'] = array_unique($resultValue['units'], SORT_REGULAR);

					array_push($flattened, $resultValue);
				}

			}

			return $flattened;

		} 

		$documents = $this->repository->returnCollectionObjectFor("crowdagent")->with('hasGeneratedWorkerunits');

		if(Input::has('filter'))
		{

			foreach(Input::get('filter') as $filter => $value)
			{	
				if(is_numeric($value))
				{
					$documents = $documents->where($filter, (int) $value);
					continue;
				}

				if(is_array($value))
				{	
					foreach($value as $operator => $subvalue)
					{	

						if(in_array($operator, $this->operators)){
						
							if(is_numeric($subvalue))
							{
								$subvalue = (int) $subvalue;

							}

							if($operator == 'like')
							{
								$subvalue = '%' . $subvalue . '%'; 
								
							}

							$documents = $documents->where($filter, $operator, $subvalue);

							}
						
					}
					
					continue;
				}
				else
				{
					$value = array($value);
				}

				$documents = $documents->whereIn($filter, $value);
			}
		}

		// // Make sort possible on 
		if(Input::has('sortBy'))
		{
			$sortBy = Input::get('sortBy');
			
			if(Input::has('order'))
			
				{$order = Input::get('order');}

			$documents = $documents->OrderBy($sortBy, $order);

		}			

		// // If no sort is selected, newest jobs come on top
		if(!Input::has('sortBy'))
		{
			$documents = $documents->OrderBy('created_at', 'des');

		}

		if(!$perPage = (int) Input::get('perPage'))
		{
			$perPage = 10;
		}

		if(!$page = (int) Input::get('page'))
		{
			$page = 1;
			$calcPage = 0;
		} else {
			$calcPage = $page - 1;
		}

		$total = $documents->count();
		$skip = $calcPage * $perPage;

		$jobs = $documents->skip($skip)->take($perPage)->get();

		// Paginate results, total amount of records, records per page and currentPage etc.
		//todo efficiency improvement to cache $jobs for pagination
		
		$paginator = array(
			"total" => $total,
			"perPage" => $perPage,
			"currentPage" => $page,
			"data" => $jobs->toArray() 
			);
		
		
		return Response::json($paginator);

	}

}