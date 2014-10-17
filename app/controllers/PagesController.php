<?php

use \mongo\text\Entity as Entity;
use \mongo\text\Activity as Activity;

class PagesController extends BaseController {

	public function index()
	{
		return View::make('index');
	}

}