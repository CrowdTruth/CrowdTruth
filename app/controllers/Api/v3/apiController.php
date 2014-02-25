<?php

namespace Api\v3;

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
	{	// Create new JobResult Object

			//Get jobconf-object

			//Array_push job-object

			//Calculate
				//totalJudgments

				//Elapsed time

				//totalCost


				//Completions

		// Paginate them, current page, page of choice etc.


		// Make sort possible on 
			//completion

			//totalCost

			//Date/Running time

			//Flagged workers %

			//Job size
		
		return Response::json($documents);


	}

}