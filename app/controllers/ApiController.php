<?php

class ApiController extends BaseController {

	public function getIndex()
	{
		return Redirect::to('api/alchemyapi');
	}

	public function getAlchemyapi()
	{
		return View::make('api.alchemyapi.pages.index');
	}

}