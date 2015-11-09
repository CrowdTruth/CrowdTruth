<?php

use crowdwatson\MechanicalTurkService;

class ProjectVizController extends BaseController {

  public function getIndex()
  {
    return View::make('media.search.pages.projects');
  }
}
