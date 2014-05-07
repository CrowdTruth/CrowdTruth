<?php

namespace Api\media;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;
use \Exception as Exception;

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
    	'=' , '<', '>', '<=', '>=', '<>', 'like'
    );

	// public function anyPost()
	// {
	// 	if(!$data = Input::get('data'))
	// 	{
	// 		if(!$data = \Request::getContent())
	// 		{
	// 			return false;
	// 		}
	// 	}

	// 	// $c = Input::get('collection', 'Entity');

	// 	// $collection = $this->repository->returnCollectionObjectFor($c);

 //  //   	if(Input::has('field'))
 //  //   	{
	// 	// 	$collection = $this->processFields($collection);
	// 	// }
			
	// 	if(empty($data))
	// 		return false;

	// 	//return $data;

	// 	$data = json_decode($data, true);
	// 	$data['softwareAgent_id'] = strtolower($data['softwareAgent_id']);

	// 	try {
	// 		$this->createPostSoftwareAgent($data);
	// 	} catch (Exception $e) {
	// 		return serialize([$e->getMessage()]);
	// 	}

	// 	try {
	// 		$activity = new Activity;
	// 		$activity->softwareAgent_id = $data['softwareAgent_id'];
	// 		$activity->save();
	// 	} catch (Exception $e) {
	// 		// Something went wrong with creating the Activity
	// 		$activity->forceDelete();
	// 		return serialize([$e->getMessage()]);
	// 	}

	// 	$entity = new Entity;
	// 	$entity->_id = null;
	// 	$entity->title = $data['title'];
	// 	$entity->format = $data['format'];
	// 	$entity->domain = $data['domain'];
	// 	$entity->documentType = $data['documentType'];
	// 	$entity->softwareAgent_configuration = $data['softwareAgent_configuration'];

	// 	if(isset($data['source']))
	// 	{
	// 		$entity->source = $data['source'];
	// 	}

	// 	if(isset($data['parents']))
	// 	{
	// 		$entity->parents = $data['parents'];
	// 	}

	// 	$entity->content = $data['content'];

	// 	if(isset($data['hash']))
	// 	{
	// 		$entity->hash = $data['hash'];
	// 	}
	// 	else
	// 	{
	// 		$entity->hash = md5(serialize(array_flatten([$data['content']])));
	// 	}
		
	// 	$entity->activity_id = $activity->_id;

	// 	$entity->save();

	// 	return Response::json($entity);
	// }





	public function anyTest()
	{
		try{
		if(!$data = Input::get('data'))
		{
			if(!$data = \Request::getContent())
			{
				return false;
			}
		}
			

		if(empty($data))
			return false;

		$data = json_decode($data, true);
		$data['softwareAgent_id'] = strtolower($data['softwareAgent_id']);

		try {
			$this->createPostSoftwareAgent($data);
		} catch (Exception $e) {
			return serialize([$e->getMessage()]);
		}

		try {
			$activity = new Activity;
			$activity->softwareAgent_id = $data['softwareAgent_id'];
			$activity->save();
		} catch (Exception $e) {
			// Something went wrong with creating the Activity
			$activity->forceDelete();
			return serialize([$e->getMessage()]);
		}

		$entity = new Entity;
		$entity->format = 'image';
		$entity->domain = $data['domain'];
		$entity->tag = $data['tag'];
		$entity->documentType = $data['documentType'];
		$entity->softwareAgent_configuration = $data['softwareAgent_configuration'];
		

		if(isset($data['parents']))
		{
			$entity->parents = $data['parents'];
		}

		$entity->content = $data['content'];

		if(isset($data['hash']))
		{
			$entity->hash = $data['hash'];
		}
		else
		{
			$entity->hash = md5(serialize($data['content']));
		}
		$entity->activity_id = $activity->_id;
		
		if(Entity::where('hash', $entity->hash)->first()){
			//dd('asdasd');
		}
		else {
			
			$entity->save();
		}
		return Response::json($entity);
		} catch (Exception $e){
			dd($e->getMessage());
		}
	}

















	public function createPostSoftwareAgent($data){
		if(isset($data['softwareAgent_id']))
		{
			if(!\MongoDB\SoftwareAgent::find($data['softwareAgent_id']))
			{
				$softwareAgent = new \MongoDB\SoftwareAgent;
				$softwareAgent->_id = strtolower($data['softwareAgent_id']);

				if(isset($data['softwareAgent_label']))
				{
					$softwareAgent->label = $data['softwareAgent_label'];
				}

				$softwareAgent->save();
			}

			return true;
		}

		Throw new Exception("Error creating SoftwareAgent");
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