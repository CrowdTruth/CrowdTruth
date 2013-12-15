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
	    $files = Input::file('files');

	    foreach($files as $file) {
	        $file->move('uploads/');
	    }
        return 'done';
	}
}
