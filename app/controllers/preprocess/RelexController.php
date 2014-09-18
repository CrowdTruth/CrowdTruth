<?php

namespace preprocess;

use \MongoDB\Repository as Repository;
use \preprocess\RelexStructurer as RelexStructurer;
use BaseController, Cart, View, App, Input, Redirect, Session;

class RelexController extends BaseController {
//RelexController
	protected $repository;
	protected $relexStructurer;

	public function __construct(Repository $repository, RelexStructurer $relexStructurer)
	{
		$this->repository = $repository;
		$this->relexStructurer = $relexStructurer;
	}

	public function getIndex()
	{
		return Redirect::to('media/preprocess/relex/actions');
	}

	public function getInfo()
	{
		return View::make('media.preprocess.relex.pages.info');
	}

	public function getActions()
	{
		// TODO: format=text so pre-processing is applicable to ANY text document.
		// TODO:   Rename class from RelexController to TEXT-CONTROLLER or something like that
		// $entities = \MongoDB\Entity::where('documentType', 'relex')->get();
		// TODO: Select document types dynamically
		$entities = \MongoDB\Entity::whereIn('documentType', [ 'relex', 'csvesult', 'article', 'biographynet', 
				'termpairs', 'qa-passages' ])->get();

		if(count($entities) > 0)
		{
			return View::make('media.preprocess.relex.pages.actions', compact('entities'));
		}

		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any "relex" documents yet');
	}

	public function getPreview()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "relex")
				{
					continue;
				}

				$document = $this->relexStructurer->process($entity, true);
				// print_r($document);
				// exit;
				return View::make('media.preprocess.relex.pages.view', array('entity' => $entity, 'lines' => $document));
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
				if($entity->documentType != "relex")
				{
					continue;
				}

				$entity = $entity->toArray();

				return $status_processing = $this->relexStructurer->process($entity);
			}
		} 
		else 
		{
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

}