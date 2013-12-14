<?php

class FilesController extends BaseController {

	public function getIndex()
	{
        return Redirect::to('files/upload');
	}

	public function getUpload()
	{
        return View::make('files.upload');
	}

	public function getBrowse()
	{
        return View::make('files.upload');
	}

	public function postUpload()
	{
        $input = Input::all();
        dd($input);
	}
}
