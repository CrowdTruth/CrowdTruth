<?php

use crowdwatson\MechanicalTurkService;

class WorkersController extends BaseController {

 	public $restful = true;

	public function getIndex()
	{
		$facetedSearch = App::make('FacetedSearch');
		$mainSearchFilters = $facetedSearch->getMainSearchFilters("job");

		return View::make('media.search.pages.index', compact('mainSearchFilters'));
	}	

	public function getWorker() {
		return View::make('workers.worker');
	}

	public function getMessage() {
		return View::make('workers.message');
	}

	public function getFlag() {
		return View::make('workers.flag');
	}
}
