<?php

use \mongo\text\Entity as Entity;
use \mongo\text\Activity as Activity;

class PagesController extends BaseController {

	public function index()
	{
		return View::make('index');
	}

	public function info()
	{
		return View::make('info');
	}


	public function papers()
	{
		return View::make('papers');
	}


	public function team()
	{
		return View::make('team');
	}

	public function apiExamples()
	{
		return View::make('api_examples');
	}

}