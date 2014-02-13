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
		return Redirect::to('preprocess/twrex/info');
	}

	public function getInfo()
	{
		return View::make('preprocess.twrex.pages.info');
	}

	public function getActions()
	{
		$items = Cart::content();

		if(count($items) > 0)
		{
			$entities = array();

			foreach($items as $item)
			{
				if($entity = $this->repository->find($item['id']))
				{
					if($entity->documentType != "twrex")
					{
						continue;
					}
						
					$entity['rowid'] = $item['rowid'];
					array_push($entities, $entity);
				}
					
			}

			return View::make('preprocess.twrex.pages.actions', compact('entities'));

		}

		return Redirect::to('files/browse')->with('flashNotice', 'You have not added any "twrex" items to your selection yet');

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

				$document = $this->twrexStructurer->process($entity);
				// print_r($document);
				// exit;
				return View::make('preprocess.twrex.pages.view', array('entity' => $entity, 'lines' => $document));
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

				$document = $this->twrexStructurer->process($entity);
				$status_processing = $this->twrexStructurer->store($entity, $document);
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