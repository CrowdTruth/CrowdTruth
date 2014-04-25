<?php

namespace preprocess;

use \MongoDB\Repository as Repository;
use \preprocess\TwrexStructurer as TwrexStructurer;
use BaseController, Cart, View, App, Input, Redirect, Session;

class TwrexController extends BaseController {

	protected $repository;
	protected $twrexStructurer;

	public function __construct(Repository $repository, TwrexStructurer $twrexStructurer)
	{
		$this->repository = $repository;
		$this->twrexStructurer = $twrexStructurer;
	}

	public function getIndex()
	{
		return Redirect::to('media/preprocess/twrex/actions');
	}

	public function getInfo()
	{
		return View::make('media.preprocess.twrex.pages.info');
	}

	public function getActions()
	{
		$entities = \MongoDB\Entity::where('documentType', 'twrex')->get();

		if(count($entities) > 0)
		{
			return View::make('media.preprocess.twrex.pages.actions', compact('entities'));
		}

		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any "twrex" documents yet');
	}

	public function getPreview()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "twrex")
				{
					continue;
				}

				$document = $this->twrexStructurer->process($entity, true);
				// print_r($document);
				// exit;
				return View::make('media.preprocess.twrex.pages.view', array('entity' => $entity, 'lines' => $document));
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
				if($entity->documentType != "twrex")
				{
					continue;
				}

				$entity = $entity->toArray();

				return $status_processing = $this->twrexStructurer->process($entity);
				echo "<pre>";
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