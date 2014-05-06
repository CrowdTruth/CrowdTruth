<?php

use crowdwatson\MechanicalTurkService;

class JobsController extends BaseController {

 	public $restful = true;

	public function getIndex()
	{
		$facetedSearch = App::make('FacetedSearch');
		$mainSearchFilters = $facetedSearch->getMainSearchFilters("job");

		return View::make('media.search.pages.index', compact('mainSearchFilters'));
	}	

	//Change JobConfiguration into JobConfiguration
	public function getListview() {
		return View::make('jobs.listview');
	}

	public function getTableview(){
		return View::make('jobs.tableview');
	}
}
