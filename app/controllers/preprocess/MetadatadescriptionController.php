<?php

namespace preprocess;

use \MongoDB\Repository as Repository;
use \preprocess\MetadatadescriptionStructurer as MetadatadescriptionStructurer;
use BaseController, Cart, View, App, Input, Redirect, Session;

class MetadatadescriptionController extends BaseController {

	protected $repository;
	protected $metadataAnnotationStructurer;

	public function __construct(Repository $repository, MetadatadescriptionStructurer $metadataAnnotationStructurer)
	{
		$this->repository = $repository;
		$this->metadataAnnotationStructurer = $metadataAnnotationStructurer;
	}

	public function getIndex()
	{
		
		return Redirect::to('media/preprocess/metadatadescription/actions');
	}

	public function getInfo()
	{
		return View::make('media.preprocess.metadatadescription.pages.info');
	}

	public function getActions()
	{

		$entities = \MongoDB\Entity::where('documentType', '=', 'metadatadescription')->get();
	
		if(count($entities) > 0)
		{
			return View::make('media.preprocess.metadatadescription.pages.actions', compact('entities'));
		}

		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any "metadatadescription" documents yet');


		$items = Cart::content();

		if(count($items) > 0)
		{
			$entities = array();

			foreach($items as $item)
			{
				if($entity = $this->repository->find($item['id']))
				{
					if($entity->documentType != "metadatadescription")
					{
						continue;
					}
						
					$entity['rowid'] = $item['rowid'];
					array_push($entities, $entity);
				}
					
			}
			
			return View::make('media.preprocess.metadatadescription.pages.actions', compact('entities'));

		}

		return Redirect::to('media/browse')->with('flashNotice', 'You have not added any "metadatadescription" items to your selection yet');

	}

	public function getPreview()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "metadatadescription")
				{
					continue;
				}

				$document = $this->metadataAnnotationStructurer->process($entity);
				// print_r($document);
				// exit;
				return View::make('media.preprocess.metadatadescription.pages.view', array('entity' => $entity, 'lines' => $document));
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
				if($entity->documentType != "metadatadescription")
				{
					continue;
				}
				
				$metadataProcessing = $this->metadataAnnotationStructurer->process($entity);

			//	dd($metadataProcessing);
				$status_processing = $this->metadataAnnotationStructurer->store($entity, $metadataProcessing);
				
				dd($status_processing);
				
				return Redirect::back();
			}
		} 
		else 
		{
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

}