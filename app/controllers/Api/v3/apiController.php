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
	{	//Get all job-object

		// return Input::all();

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
							
							$user = \User::where('username', 'like', '%' . $subvalue . '%')->first();

							// return $user;
							$user_id = $user->_id;

							// dd($user_id);

							$documents = $documents->where('user_id', $user_id);

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

		if(!$limit = (int) Input::get('limit'))
		{
			$limit = 100;
		}

		//Eager load jobConfiguration into job entity
		$entities = $documents->take($limit)->get();
		
		$jobs = array();

		//Push entity objects into array for paginator
		foreach($entities as $entity)
		{
			array_push($jobs, $entity);
		}
		
		// Paginate results, current page, page of choice etc.
		
		if(!$perPage = (int) Input::get('perPage'))
		{
			$perPage = 2;
		}
			
		$paginator = Paginator::make($jobs, count($entities), $perPage);

		
		//Return paginator
		
		return Response::json($paginator);


	}

}