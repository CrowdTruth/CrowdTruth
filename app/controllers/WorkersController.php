<?php

use crowdwatson\MechanicalTurkService;

class WorkersController extends BaseController {

	public function getIndex()
	{
		$mainSearchFilters = Temp::getMainSearchFiltersCache()['filters'];

		return View::make('media.search.pages.workers', compact('mainSearchFilters'));
	}
}
