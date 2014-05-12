<?php

use crowdwatson\MechanicalTurkService;

class JobsController extends BaseController {

	public function getIndex()
	{
		$mainSearchFilters = \MongoDB\Temp::getMainSearchFiltersCache()['filters'];

		return View::make('media.search.pages.jobs', compact('mainSearchFilters'));
	}
}
