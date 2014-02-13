<?php

namespace Api\v1;

use \BaseController as BaseController;
use \Input as Input;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;

class apiController extends \BaseController {

	protected $repository;

	public function __construct(Repository $repository){
		$this->repository = $repository;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(!$limit = (int) Input::get('limit'))
		{
			$limit = 100;
		}

		echo "<pre>";

		$input = Input::except("provtype", "limit");

		if($dots = Input::get('dots'))
		{
			if(is_array($dots))
			{
				$input = array_merge($input, $dots);
				unset($input['dots']);				
			}
		}

		// $doc = \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->where('content.properties.semicolonBetweenTerms', "1")->get();

		// dd($doc->toArray());

		if(!$provType = Input::get('provtype'))
		{
			$provType = "entity";
		}

		$Collection = $this->repository->returnCollectionObjectFor($provType);

		$documents = new $Collection;

		foreach($input as $field => $value)
		{
			if(is_numeric($value))
			{
				$documents = $documents->where($field, (int) $value);
				continue;
			}

			if(!is_array($value))
			{
				$value = array($value);
			}

			if($field == "userAgent")
			{
				$documents = $documents->whereIn("user_id", $value);
				continue;
			}			

			$documents = $documents->whereIn($field, $value);
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


	//	return $documents->remember(999999, md5(serialize(Input::all() + array($limit))))->take($limit)->get()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		return $documents->take($limit)->get()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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