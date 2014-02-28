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
		$documents = $this->repository->returnCollectionObjectFor("entity")->where('documentType', 'job');
		
		//Eager load jobConfiguration into job entity
		$entities = $documents->with('hasConfiguration')->get();
		
		$jobs = array();

		//Push entity objects into array for paginator
		foreach($entities as $entity)
		{
			array_push($jobs, $entity);
		}
		// Make sort possible on 
			//completion

			//totalCost

			//Date/Running time

			//Flagged workers %

			//Job size
		
		//Filter on wished for fields using using field of v2 


		// Paginate results, current page, page of choice etc.
		
		if(!$perPage = (int) Input::get('perpage'))
		{
			$perPage = 15;
		}
			
		$paginator = Paginator::make($jobs, count($documents), $perPage);

		//Return paginator
		
		return Response::json($paginator);


	}

}