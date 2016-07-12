<?php

Use \Entities\Unit as Unit;
Use \Entity as Entity;


class ProcessVideoController extends BaseController {


    public function getIndex()
    {
        return Redirect::to('media/search');
    }

    public function postProcess()
    {
        if (!Input::has('videofile'))
        {
            return Redirect::to('media/search');
        }

        $videofile = Input::get('videofile');
        $data = Entity::select('*')->where('_id',$videofile)->get();

        $videodata = Entity::select('*')->where('type','downloadedvideo')->whereIn('parents',[$videofile])->get();

        return View::make('media.processvideo.pages.index')->with('videofile',json_decode($data))->with('videodata',json_decode($videodata));
    }
    
    public function getDownloadFile()
    {

        ini_set('memory_limit','256M');
        $getunit = Input::get('videounit');

        $videounit = Unit::select('*')->where('_id',$getunit)->first();
        $videourl = $videounit->content['url'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $videourl,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => 1,));

        $extension = explode(".",$videourl);
        $extension = array_pop($extension);

        $targetfilename = str_replace("/",".",$getunit);
        $storagedir = 'videostorage/fullvideos/'.$targetfilename . '.' . $extension;
        $targetdownload = storage_path($storagedir);

        $curldata = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpcode != '200') $this->echoError("The file could not be downloaded.");

        $fwh = @fopen($targetdownload, "w");
        @fwrite($fwh, $curldata);
        @fclose($fwh);
        curl_close($curl);
        if (!$fwh) $this->echoError("The file could not be saved to local storage.");

        $newfile = new Unit();
        $newfile->parents = [$getunit];
        $newfile->type = "downloadedvideo";
        $newfile->downloadlocation = $storagedir;
        $newfile->project = $videounit->project;
        $newfile->save();


        $this->echoSuccess("Successfully downloaded the file to local storage.");
    }

    private function echoError($msg)
    {
        $output['status'] = "error";
        $output['message'] = $msg;

        echo json_encode($output);
        exit;
    }

    private function echoSuccess($msg)
    {
        $output['status'] = "success";
        $output['message'] =  $msg;

        echo json_encode($output);
    }

}