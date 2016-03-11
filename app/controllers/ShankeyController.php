<?php

use crowdwatson\MechanicalTurkService;

class ShankeyController extends BaseController {

  public function getIndex()
  {
    return View::make('media.search.pages.shankey');
  }
}
