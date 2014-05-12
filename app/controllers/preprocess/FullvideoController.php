<?php

namespace preprocess;

use \MongoDB\Repository as Repository;
use \preprocess\FullvideoStructurer as FullvideoStructurer;
use BaseController, Cart, View, App, Input, Redirect, Session;

class FullvideoController extends BaseController {

	protected $repository;
	protected $fullvideoStructurer;

	public function __construct(Repository $repository, FullvideoStructurer $fullvideoStructurer)
	{
		$this->repository = $repository;
		$this->fullvideoStructurer = $fullvideoStructurer;
	}

	public function getIndex()
	{
		return Redirect::to('media/preprocess/fullvideo/actions');
	}

	public function getInfo()
	{
		return View::make('media.preprocess.fullvideo.pages.info');
	}

	public function getActions()
	{
		$entities = \MongoDB\Entity::where('documentType', '=', 'fullvideo')->orWhere('keyframes.count', '=', 0)->orWhere('segments.count', '=', 0)->get();
		//dd($entities);
		if(count($entities) > 0)
		{
			return View::make('media.preprocess.fullvideo.pages.actions', compact('entities'));
		}

		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any "fullvideo" documents yet');


		$items = Cart::content();

		if(count($items) > 0)
		{
			$entities = array();

			foreach($items as $item)
			{
				if($entity = $this->repository->find($item['id']))
				{
					if($entity->documentType != "fullvideo")
					{
						continue;
					}
						
					$entity['rowid'] = $item['rowid'];
					array_push($entities, $entity);
				}
					
			}

			return View::make('preprocess.fullvideo.pages.actions', compact('entities'));

		}

		return Redirect::to('media/browse')->with('flashNotice', 'You have not added any "fullvideo" items to your selection yet');

	}

	public function getPreview()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "fullvideo")
				{
					continue;
				}

				$document = $this->fullvideoStructurer->process($entity);
				// print_r($document);
				// exit;
				return View::make('preprocess.fullvideo.pages.view', array('entity' => $entity, 'lines' => $document));
			}
		} 
		else 
		{
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

	public function getProcess()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "fullvideo")
				{
					continue;
				}
				
				$videoPreprocessing = $this->fullvideoStructurer->process($entity);
				
				$status_processing = $this->fullvideoStructurer->store($entity, $videoPreprocessing);
				if (isset($status_processing["keyframes"])) {
					if (!isset($status_processing["keyframes"]['error'])) {
						//update the parent
						\MongoDB\Entity::where('_id', '=', $entity->_id)->update( array('keyframes.count' => $status_processing["keyframes"]['success']["noEntitiesCreated"]));
					}
					echo "<pre>";
				}
				if (isset($status_processing["segments"])) {
					if (!isset($status_processing["segments"]['error'])) {
						//update the parent
						\MongoDB\Entity::where('_id', '=', $entity->_id)->update(array('segments.count' => $status_processing["segments"]['success']["noEntitiesCreated"]));
					}
					echo "<pre>";
				}
				if(isset($status_processing["keyframes"]['success']) && isset($status_processing["segments"]['success'])) {
					return Redirect::back()->with('flashSuccess', 'Your video has been pre-processed in keyframes and video segments');
				}
				else {
					return Redirect::back()->with('flashError', 'An error occurred while the video was being pre-processed in keyframes and video segments');
				}
			}
		} 
		else 
		{
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

}
