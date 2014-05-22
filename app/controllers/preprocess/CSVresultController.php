<?php

namespace preprocess;

use \MongoDB\Repository as Repository;
use \preprocess\CSVresultMapper as CSVresultMapper;
use BaseController, Cart, View, App, Input, Redirect, Session;

class csvresultController extends BaseController {

	protected $repository;
	protected $csvresultMapper;

	public function __construct(Repository $repository, CSVresultMapper $csvresultMapper)
	{
		$this->repository = $repository;
		$this->csvresultMapper = $csvresultMapper;
	}

	public function getIndex()
	{
		return Redirect::to('preprocess/csvresult/inputdata');
	}

	public function getInfo()
	{
		return View::make('preprocess.csvresult.pages.info');
	}

	public function getInputdata($action = null)
	{
		if(is_null($action))
		{
			$entities = \MongoDB\Entity::where('documentType', 'csvresult')->where('title', 'like', '%input%')->get();

			if(count($entities) > 0)
			{
				return View::make('preprocess.csvresult.pages.inputdata', compact('entities'));
			}

			return Redirect::to('files/upload')->with('flashNotice', 'You have not uploaded any "csvresult" documents yet');
		}
		elseif($action == "preview")
		{
			if($URI = Input::get('URI'))
			{
				if($entity = $this->repository->find($URI)) {
					if($entity->documentType == "csvresult")
					{
    					return $document = $this->csvresultMapper->processInputData($entity, true);
                    }
				}
			} 
		}
		elseif($action == "createbatch")
		{
			if($URI = Input::get('URI'))
			{
				if($entity = $this->repository->find($URI)) {
					if($entity->documentType == "csvresult")
					{
                        $batch = $this->csvresultMapper->processInputData($entity);
                        return Redirect::to('files/batch?selection=' . urlencode(json_encode($batch)));
                    }
				}
			} 
		}
	}

	public function getWorkerUnitdata($action = null)
	{
		if(is_null($action))
		{
			$entities = \MongoDB\Entity::where('documentType', 'csvresult')->where('title', 'like', '%workerUnit%')->get();

			if(count($entities) > 0)
			{
				return View::make('preprocess.csvresult.pages.workerUnitdata', compact('entities'));
			}

			return Redirect::to('files/upload')->with('flashNotice', 'You have not uploaded any "csvresult" documents yet');
		}
		elseif($action == "preview")
		{
			if($URI = Input::get('URI'))
			{
				if($entity = $this->repository->find($URI)) {
					if($entity->documentType == "csvresult")
					{
                        return $document = $this->csvresultMapper->processWorkerUnitData($entity, true);
					}


				}
			} 
		}
		elseif($action == "process")
		{
			if($URI = Input::get('URI'))
			{
				if($entity = $this->repository->find($URI)) {
					if($entity->documentType == "csvresult")
					{
                        return $document = $this->csvresultMapper->processWorkerUnitData($entity);
					}


				}
			} 
		}
	}


	// public function getActions()
	// {
	// 	$entities = \MongoDB\Entity::where('documentType', 'csvresult')->get();

	// 	if(count($entities) > 0)
	// 	{
	// 		return View::make('preprocess.csvresult.pages.actions', compact('entities'));
	// 	}

	// 	return Redirect::to('files/upload')->with('flashNotice', 'You have not uploaded any "csvresult" documents yet');
	// }

	// public function getPreview()
	// {
	// 	if($URI = Input::get('URI'))
	// 	{
	// 		if($entity = $this->repository->find($URI)) {
	// 			if($entity->documentType != "csvresult")
	// 			{
	// 				continue;
	// 			}

	// 			return $document = $this->csvresultMapper->process($entity, true);
	// 			// print_r($document);
	// 			// exit;
	// 			return View::make('preprocess.csvresult.pages.view', array('entity' => $entity, 'lines' => $document));
	// 		}
	// 	} 
	// 	else 
	// 	{
	// 		return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
	// 	}	
	// }

	// public function getCreatebatch()
	// {
	// 	if($URI = Input::get('URI'))
	// 	{
	// 		if($entity = $this->repository->find($URI)) {
	// 			if($entity->documentType != "csvresult")
	// 			{
	// 				continue;
	// 			}

	// 			$batch = $this->csvresultMapper->process($entity);

	// 			return Redirect::to('files/batch?selection=' . urlencode(json_encode($batch)));
	// 		}
	// 	} 
	// 	else 
	// 	{
	// 		return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
	// 	}	
	// }

	// public function getProcess()
	// {
	// 	dd('coming soon?');

	// 	if($URI = Input::get('URI'))
	// 	{
	// 		if($entity = $this->repository->find($URI)) {
	// 			if($entity->documentType != "csvresult")
	// 			{
	// 				continue;
	// 			}

	// 			$document = $this->csvresultMapper->process($entity);
	// 			$status_processing = $this->csvresultMapper->store($entity, $document);
	// 			echo "<pre>";
	// 			dd($status_processing);
	// 			return Redirect::back();
	// 		}
	// 	} 
	// 	else 
	// 	{
	// 		return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
	// 	}	
	// }

}