<?php

namespace Api\video;

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
    	'=' , '<', '>', '<=', '>=', '<>', 'like'
    );

	public function anyPost()
	{

		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

    	if(Input::has('field'))
    	{
			$collection = $this->processFields($collection);
		}

		if($data = Input::get('data'))
		{
			$data = json_decode($data, true);

			// return $data;

			try {
				$this->createPostSoftwareAgent($data['softwareAgent_id']);
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
			$entity->_id = null;
			$entity->title = $data['title'];
			$entity->format = $data['format'];
			$entity->domain = $data['domain'];
			$entity->documentType = $data['documentType'];
			$entity->source = $data['source'];
			$entity->ancestors = $data['ancestors'];
			$entity->content = $data['content'];
			$entity->hash = $data['hash'];
			$entity->activity_id = $activity->_id;
			$entity->save();
			return $entity;


			dd('end');
			$collection->update($data, array('upsert' => true));

			// foreach($data as $dataKey => $dataValue)
			// {
			// 	$dataValue = json_decode($dataValue, true);

			// 	dd($dataValue);

			// 	$collection->update($dataKey, $dataValue);
			// }
		}

		return $collection->get();
	}

	public function store($format, $domain, $documentType, $ancestors, $content)
	{

		$status = array();

		try {
			$this->createPostSoftwareAgent();
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

		foreach($twrexStructuredSentences as $twrexStructuredSentenceKey => $twrexStructuredSentenceKeyVal){
			$title = $parentEntity->title . "_index_" . $twrexStructuredSentenceKey;

			try {
				$entity = new Entity;
				$entity->_id = $entity->_id;
				$entity->title = strtolower($title);
				$entity->domain = $parentEntity->domain;
				$entity->format = $parentEntity->format;
				$entity->documentType = "twrex-structured-sentence";
				$entity->ancestors = array($parentEntity->_id);
				$entity->content = $twrexStructuredSentenceKeyVal;

				unset($twrexStructuredSentenceKeyVal['properties']);
				$entity->hash = md5(serialize($twrexStructuredSentenceKeyVal));
				$entity->activity_id = $activity->_id;
				$entity->save();

				$status['success'][$title] = $title . " was successfully processed into a twrex-structured-sentence. (URI: {$entity->_id})";
			} catch (Exception $e) {
				// Something went wrong with creating the Entity
				$entity->forceDelete();
				$status['error'][$title] = $e->getMessage();
			}

		}

		// Session::forget('lastMongoIDUsed');

		return $status;
	}

	public function createPostSoftwareAgent($softwareAgent_id){
		if(!\MongoDB\SoftwareAgent::find($softwareAgent_id))
		{
			$softwareAgent = new \MongoDB\SoftwareAgent;
			$softwareAgent->_id = strtolower($softwareAgent_id);
			$softwareAgent->save();
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