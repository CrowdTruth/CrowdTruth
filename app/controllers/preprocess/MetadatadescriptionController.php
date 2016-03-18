<?php

namespace preprocess;


use \preprocess\MetadatadescriptionStructurer as MetadatadescriptionStructurer;
use BaseController, Cart, View, App, Input, Redirect, Session;
use \Repository as Repository;
use \Entity as Entity;
use \Entities\Unit as Unit;

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

		$entities = Unit::where('documentType', '=', 'BBC-synopsis')->get();
	
		if(count($entities) > 0)
		{
			return View::make('media.preprocess.metadatadescription.pages.actions', compact('entities'));
		}

		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any "BBC-synopsis" documents yet');


		$items = Cart::content();

		if(count($items) > 0)
		{
			$entities = array();

			foreach($items as $item)
			{
				if($entity = $this->repository->find($item['id']))
				{
					if($entity->documentType != "BBC-synopsis")
					{
						continue;
					}
						
					$entity['rowid'] = $item['rowid'];
					array_push($entities, $entity);
				}
					
			}
			
			return View::make('media.preprocess.metadatadescription.pages.actions', compact('entities'));

		}

		return Redirect::to('media/browse')->with('flashNotice', 'You have not added any "BBC-synopsis" items to your selection yet');

	}

	public function getPreview()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "BBC-synopsis")
				{
					continue;
				}

				$document = $this->metadataAnnotationStructurer->process($entity);
				
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
				if($entity->documentType != "BBC-synopsis")
				{
					continue;
				}
			
				$metadataProcessing = $this->metadataAnnotationStructurer->process($entity);
				$status_processing = $this->metadataAnnotationStructurer->store($entity, $metadataProcessing);
            /*    if (isset($status_processing["processAutomatedEventExtraction"])) {
                	if (!isset($status_processing["processAutomatedEventExtraction"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEvents' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["thdapi"])) {	
					if (!isset($status_processing["thdapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["textrazorapi"])) {	
					if (!isset($status_processing["textrazorapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["semitagsapi"])) {	
					if (!isset($status_processing["semitagsapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["nerdapi"])) {	
					if (!isset($status_processing["nerdapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["lupediaapi"])) {	
					if (!isset($status_processing["lupediaapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["dbpediaspotlightapi"])) {	
					if (!isset($status_processing["dbpediaspotlightapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}

				if(isset($status_processing["processAutomatedEventExtraction"]['success'])) {
					$this->createStatisticsForMetadatadescriptionCache($entity->_id);
					return Redirect::back()->with('flashSuccess', 'Your video description has been pre-processed in named entities and putative events');
				}
				else {
					return Redirect::back()->with('flashError', 'An error occurred while the video description was being pre-processed in named entities and putative events');
				}
				*/
			}
		} 
		else 
		{
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

}

