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
		// echo "<pre>";

		// dd(Input::all());

		if(!$limit = (int) Input::get('limit'))
		{
			$limit = 100;
		}

		// $doc = \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->where('content.properties.semicolonBetweenTerms', "1")->get();

		// dd($doc->toArray());

		if(!$collection = strtolower(Input::get('collection')))
		{
			$collection = "entity";
		}

		$documents = $this->repository->returnCollectionObjectFor($collection);

		// dd(Input::all());

		if(Input::has('field'))
		{

			foreach(Input::get('field') as $field => $value)
			{
				if(is_numeric($value))
				{
					$documents = $documents->where($field, (int) $value);
					continue;
				}

				if($field == "userAgent")
				{
					$field = "user_id";
				}			

				if(is_array($value))
				{

					foreach($value as $operator => $subvalue)
					{
						if(in_array($operator, $this->operators))
						{
							if(is_numeric($subvalue))
							{
								$subvalue = (int) $subvalue;
							}

							$documents = $documents->where($field, $operator, $subvalue);
						}
					}

					continue;
				}
				else
				{
					$value = array($value);
				}

				$documents = $documents->whereIn($field, $value);
			}
		}

		// if($format = Input::get('format'))
		// {
		// 	$documents = $Collection::where('format', $format);
		// }

		// if($domains = Input::get('domain'))
		// {
		// 	if(!is_array($domains))
		// 	{
		// 		$domains = array($domains);
		// 	}

		// 	$documents = $documents->whereIn('domain', $domains);
		// }		

		// if($documentTypes = Input::get('documentType'))
		// {
		// 	if(!is_array($documentTypes))
		// 	{
		// 		$documentTypes = array($documentTypes);
		// 	}

		// 	$documents = $documents->whereIn('documentType', $documentTypes);
		// }

		// if($userAgents = Input::get('userAgent'))
		// {
		// 	if(!is_array($userAgents))
		// 	{
		// 		$userAgents = array($userAgents);
		// 	}

		// 	$documents = $documents->whereIn('user_id', $userAgents);
		// }


//		return $documents->remember(999999, md5(serialize(Input::all() + array($limit))))->take($limit)->get()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);


		if($collection == "entity" || $collection == "entities")
		{
			if(Input::has('wasGeneratedBy'))
			{

				$entities = $documents->with('wasGeneratedBy')->limit($limit)->get(array('activity_id'));

				$activities = array();

				foreach($entities as $entity)
				{
					array_push($activities, $entity->wasGeneratedBy->toArray());
				}
				
				return Response::json($activities);
			}
		}

		return Response::json($documents->take($limit)->get());


	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}
}