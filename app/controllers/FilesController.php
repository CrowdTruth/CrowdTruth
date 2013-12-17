<?php

use Models\File;

class FilesController extends BaseController {

    public function __construct(File $file)
    {
        $this->file = $file;
    }

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
		if (Input::hasFile('files')) {
		    $status_upload = $this->file->process(Input::file('files'));
		} else {
		    $status_upload['error']['message'] = "You did not select any files";
		}
		return View::make('files.upload', compact('status_upload'));
	}

	public function getMongo(){
		$users = DB::collection('files')->get();
		dd($users);
		return View::make('files.mongo');
	}
}
