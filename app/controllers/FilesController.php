<?php

class FilesController extends BaseController {

    public function __construct(Files $files)
    {
        $this->files = $files;
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
		    $status_upload = $this->files->process(Input::file('files'));
		} else {
		    $status_upload['error']['message'] = "You did not select any files";
		}
		return View::make('files.upload', compact('status_upload'));
	}
}
