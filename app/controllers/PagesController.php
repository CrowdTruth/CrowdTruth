<?php

use \Security\ProjectHandler as ProjectHandler;

class PagesController extends BaseController {

	public function index()
	{
		// run database seeder on first load
		$groups = ProjectHandler::listProjects();
		if(count($groups) == 0) {
			Artisan::call('db:seed');
		}
		
		return View::make('index');
	}

}