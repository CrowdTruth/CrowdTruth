<?php

namespace Api\v3;

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
 * This apiController is used merely in the result view,
 *
 * it therefore only retrieves jobConfigurations + jobs.
 *
 * Optimal url for : /page/1/creator/jelle/sort/completion/ or /?page=1&sort=completion&filter[creator]=jelle
 */
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
		/**
		* Return one entity
		*
		* @return one Entity
		*/
		if(Input::has('id')){
			$id = Input::get('id');
			
			// Check if is workerunit, when workerunit append units, if not append workerunits (assumption that it is a unit so far valid)
			$workerunitType = strpos($id, 'workerunit');

			if($workerunitType === false){

				$result = \MongoDB\Entity::with('hasWorkerunits')->where('_id', $id)->get();

			} else {

				$result = \MongoDB\Entity::with('hasUnit')->where('_id', $id)->get();
			
			}

			$result = $result->toArray();
						
			return $result;
		}

		$documents = $this->repository->returnCollectionObjectFor("entity")->where('documentType', 'job')->with('hasConfiguration')->with('wasAttributedToUserAgent');
		
		//Filter on wished for fields using using field of v2
		json_encode($documents);

		if(Input::has('filter'))
		{

			foreach(Input::get('filter') as $filter => $value)
			{	
				if(is_numeric($value))
				{
					$documents = $documents->where($filter, (int) $value);
					continue;
				}

				if($filter == "userAgent")
				{
					$filter = "user_id";
				}			

				if(is_array($value))
				{	
					foreach($value as $operator => $subvalue)
					{	
						if($filter == "username"){

							$documents = $documents->where('user_id', 'like', '%' . $subvalue . '%');

							continue;
						}

						if(in_array($operator, $this->operators)){
						
							if(is_numeric($subvalue))
							{
								$subvalue = (int) $subvalue;
							}

							if($operator == 'like')
							{
								$subvalue = '%' . $subvalue . '%'; 
								
							}


							// if (strpos($a,'are') !== false) {
 						// 	   echo 'true';
							// }

							if(strpos($filter, "hasConfiguration") !== false )
							{	

								$filter = explode(".", $filter);

								$jobConf = Entity::where('documentType', '=', 'jobconf')->where(end($filter), 'like', $subvalue);
								
								$allJobConfIDs = array_flatten($jobConf->get(['_id'])->toArray());


								$documents = $documents->whereIn('jobConf_id', $allJobConfIDs);

								continue;

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

		// Make sort possible on 
		if(Input::has('sortBy'))
		{
			$sortBy = Input::get('sortBy');
			
			if(Input::has('order'))
			
				{$order = Input::get('order');}

			$documents = $documents->OrderBy($sortBy, $order);

		}			

		// If no sort is selected, newest jobs come on top
		if(!Input::has('sortBy'))
		{
			$documents = $documents->OrderBy('created_at', 'des');

		}
		// Take limit of 100 unless otherwise indicated

		// if(!$limit = (int) Input::get('limit'))
		// {
		// 	$limit = 100;
		// }

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
		
		//dd($paginator);
		return Response::json($paginator);

		// Take limit of 100 unless otherwise indicated


		//Eager load jobConfiguration into job entity
		$entities = $documents->take($limit)->get();
		
		$jobs = array();

		//Push entity objects into array for paginator
		foreach($entities as $entity)
		{
			array_push($jobs, $entity);
		}
		
		// Paginate results, current page, page of choice etc.
		
		if(!$perPage = (int) Input::get('perpage'))
		{
			$perPage = 2;
		}
			
		$paginator = Paginator::make($jobs, count($entities), $perPage);

		
		//Return paginator
		
		return Response::json($paginator);


	}

}