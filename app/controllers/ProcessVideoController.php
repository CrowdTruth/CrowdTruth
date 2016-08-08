<?php

Use \Entities\Unit as Unit;
Use \Entity as Entity;


class ProcessVideoController extends BaseController {


    public function getIndex()
    {
        return Redirect::to('media/search');
    }

    public function getProcess()
    {
        return Redirect::to('media/search');
    }

    public function postProcess()
    {
        if (!Input::has('videofile'))
        {
            return Redirect::to('media/search');
        }
        $output = Array();
        $videofile = Input::get('videofile');
        $data = Entity::where('_id',$videofile)->get()->first()->toArray();

        $output = $data;
        /*
       // $videodata = Entity::where('type','downloadedvideo')->whereIn('parents',[$videofile])->first();

        //if (count($videodata) > 0) {
          //  $output['downloaded'] = $videodata->toArray();*/
            $kfdata = Entity::where('documentType','keyframe')->whereIn('parents',[$videofile])->get()->sortBy('content.scenestart')->toArray();
            if (count($kfdata) > 0) {
                $output['keyframes'] = $kfdata; }
              /*  $subdata = Entity::where('type','substitle')->whereIn('parents',[$videofile])->get()->sortBy('starttime')->toArray();
                if (count($subdata) > 0)
                {
                    $output['subtitles'] = $subdata;

        }*/ //}
        //}

        if (count(Unit::where('documentType', 'subtitlefile')->whereIn('parents',[$videofile])->get()->toArray()) > 0) $output['subtitles'] = 'true';
        //$temp = Unit::where('documentType', 'subtitlefile')->whereIn('parents',[$videofile])->get()->toArray();
        //print_r($temp);

        //print_r($output);
        return View::make('media.processvideo.pages.index')->with('data',$output);
    }
    
    public function getDownloadFile()
    {

        ini_set('memory_limit','256M');
        $getunit = Input::get('videounit');

        $videounit = Unit::where('_id',$getunit)->first();

        $videocontent = $videounit->content;
        $videourl = $videocontent['url'];

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

        $videocontent['downloadedvideo'] = $storagedir;

        $videounit->content = $videocontent;
        $videounit->save();



        $this->echoSuccess("Successfully downloaded the file to local storage.");
    }

    public function postUploadSubs()
    {
        $file = Input::file('subsfile');


        $getunit = Input::get('videounit');

        $videounit = Entity::where('_id',$getunit)->first()->toArray();

        $targetpath = 'subtitles/'.$getunit.'/';
        $uploadpath = storage_path('subtitles/'.$getunit.'/');
        Input::file('subsfile')->move($uploadpath,$file->getClientOriginalName());
        $newcontent = Array();
        $newcontent['filelocation'] = $targetpath . $file->getClientOriginalName();

        $entity = new Unit();
        $entity->parents = [$getunit];
        $entity->documentType = "subtitlefile";
        $entity->content = $newcontent;
        $entity->project = $videounit['project'];
        $entity->save();
        
        //parse!
        $subsxml = simplexml_load_file(storage_path($targetpath . $file->getClientOriginalName()));
        $base = $subsxml->body->div->p; //this is where the subs start

        foreach ($base as $subdata)
        {

            $subdata = (Array)$subdata;
            $time = explode(":",$subdata['@attributes']['begin']); //hours:minutes:seconds.millis
            $seconds = (float)((($time[0] * 3600) + ($time[1] * 60) + explode(".",$time[2])[0]) . "." . (explode(".",$time[2])[1]));
            $findframe = Unit::whereIn('parents',[$getunit])->where('documentType','keyframe')->where('content.scenestart','<',$seconds)->get()->sortBy('content.scenestart')->last();


            $newcontent = $findframe->content;
            if (is_array($subdata['span']))
            {
                foreach($subdata['span'] as $cursub)
                {
                    $newcontent['subtitles'][] = trim($cursub);
                }

            } else {
                $newcontent['subtitles'][] = trim($subdata['span']);
            }

            $findframe->content = $newcontent;

            $findframe->save();

            /*$newsubent = new Entity();
            $newsubent->type = "substitle";
            $newsubent->starttime = $seconds;
            $newsubent->humantime = $subdata['@attributes']['begin'];
            $newsubent->parents = [$getunit,$findframe['_id']];
            $newsubent->project = $videounit['project'];
            if (!is_array($subdata['span'])) {
                $newsubent->content = [$subdata['span']];
            } else {
                $newsubent->content = $subdata['span'];
            }

            $newsubent->save();*/

        }

        $this->echoSuccess("Successfully uploaded and processed subtitles");
        


        
    }

    public function getProcessKeyframes()
    {
        $getunit = Input::get('videounit');
        $ffmpegbinary = app_path('ffmpeg');
        $unit = Unit::where('_id',$getunit)->first();

        $videofile = storage_path($unit->content['downloadedvideo']);


        $scenetreshold = "5";

        $outdir = storage_path('videostorage/keyframes/'.$getunit.'/');

        $res = @mkdir($outdir,0777,true);
        if (!$res) $this->echoError("Could not create output directory.");

        //$buildcmd = "$ffmpegbinary -i $videofile -vf select=\"gt(scene\,0.".$scenetreshold.")\" -vsync 2 ".$outdir."frame%07d.png -loglevel debug 2>&1 | grep \"select:1\" | cut -d \" \" -f 6 - >".$outdir."frametimes.out";
        $buildcmd = "$ffmpegbinary -i $videofile -vf select=\"gt(scene\,0.".$scenetreshold.")\" -vsync 2 ".$outdir."frame%07d.png -loglevel debug 2>&1| grep \"select:1\" > ".$outdir."frametimes.out";
        $out = shell_exec($buildcmd);

        $fhtimes = fopen($outdir."frametimes.out","r");
        $count = 1;
        while (($curft = fgets($fhtimes)) !== false)
        {
            if (strstr($curft,"\r")) //ffmpeg can drop a stat line in the log
            {
                $tempexplode = explode("\r",$curft);
                $curft = $tempexplode[1];
            }
            $timeone = explode(" ",$curft);
            $curft = $timeone[5];
            $curframename = sprintf("frame%07d.png",$count);
            $curframefileloc = 'videostorage/keyframes/'.$getunit.'/' . $curframename;
            $curtime = explode(":",$curft);
            $curtime = array_pop($curtime);

            $timesecs = explode(".",$curtime)[0];
            $timemillis = explode(".",$curtime)[1];
            $timemillis = substr($timemillis,0,3);
            //Get a nice HH:mm:ss.mmmmmm format
            $humantime = sprintf("%02d:%02d:%02d.%d",floor($timesecs / 3600) , floor($timesecs / 60) , ($timesecs % 60) , $timemillis);

            $contentarray = Array();
            $contentarray['frames'][] = $curframefileloc;
            $contentarray['scenestart'] = (float)$curtime;
            $contentarray['humantime'] = $humantime;
            $contentarray['scenestartraw'] = $curtime;

            $newkfunit = new Unit();
            $newkfunit->parents = [$getunit];
            $newkfunit->content = $contentarray;
            $newkfunit->project = $unit['project'];
            $newkfunit->documentType = "keyframe";
            $newkfunit->save();
            $count++;


        }

        $this->echoSuccess("Keyframes successfully extracted and added to database.");

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

    public function getImage()
    {
        $getunit = Input::get("unit");
        $filenumber = Input::get("number");

        $videounit = Entity::where('_id',$getunit)->first()->toArray();
        $frameloc = $videounit['content']['frames'][$filenumber];
        $inputfile = storage_path($frameloc);

        if (Input::has("width"))
        {
            list($originalwidth,$originalheight) = getimagesize($inputfile);

            $targetwidth = Input::get("width");
            $targetheight = $originalheight * ($targetwidth / $originalwidth);

            $inputimage = imagecreatefrompng($inputfile);

            $outputimage = imagecreatetruecolor($targetwidth,$targetheight);

            imagecopyresized($outputimage,$inputimage,0,0,0,0,$targetwidth,$targetheight,$originalwidth,$originalheight);
            header("Cache-Control: max-age=2592000");
            header('Content-Type: image/png');
            imagepng($outputimage);

        } else {
            header("Cache-Control: max-age=2592000");
            header('Content-Type: image/png');
            imagepng(imagecreatefrompng($inputfile));

        }
    }

    public function getClarifai()
    {
        $images = Array();
        if (Input::has("all"))
        {
            $videofile = Input::get('videoid');
            $allkfs = Entity::where('documentType','keyframe')->whereIn('parents',[$videofile])->get()->sortBy('content.scenestart')->toArray();
            //print_r($allkfs);
            foreach($allkfs as $currentkeyf)
            {
                $images[] = $currentkeyf['_id'];
            }
        } else {
            $images[] = Input::get("keyframeid");
        }
        $successcount = 0;
        foreach ($images as $image) {


            $clarifai_accesstoken = $this->getClarifaiKey();

            $curlurl = "https://api.clarifai.com/v1/tag/";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $curlurl);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $postfields = Array();
            $videounit = Entity::where('_id', $image)->first()->toArray();

            if (isset($videounit['content']['tags'])) {

                foreach ($videounit['content']['tags'] as $curtag) {

                    if ($curtag['source'] == "clarifai") continue 2;
                }
            }

            $frameloc = $videounit['content']['frames'][0];
            $inputfile = storage_path($frameloc);
            $postfields['encoded_data'] = base64_encode(file_get_contents($inputfile));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);

            $curlheaders = Array();
            $curlheaders[] = "Authorization: Bearer $clarifai_accesstoken";
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheaders);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($curl);

            $cldata = json_decode($output, false, 512, JSON_BIGINT_AS_STRING);
            $updateframe = Unit::where('_id', $image)->get()->first();
            $newcontent = $updateframe->content;
            $classarray = Array();
            $results = $cldata->results[0];
            $results = (Array)$results->result->tag;

            //->results->tag;
            $classarray['source'] = 'clarifai';
            $classarray['timestamp'] = new MongoDate();
            foreach ($results['classes'] as $number => $tag) {
                $classarray['tags'][$number]['tag'] = $tag;
                $classarray['tags'][$number]['prob'] = $results['probs'][$number];
            }
            if (!isset($newcontent['tags'])) {
                $newcontent['tags'] = Array();
            }
            /*
            if (!isset($newcontent['tags']))
            {
                $newtags = 0;
            } else {
                $newtags = count($newcontent['tags']);
            }*/
            $newarray = $newcontent['tags'];
            $newarray[] = $classarray;

            $newcontent['tags'] = $newarray;

            $updateframe->content = $newcontent;
            $updateframe->save();
            $successcount++;

        }
        $this->echoSuccess("Successfully processed $successcount images through Clarifai!");

    }

    public function getImagga()
    {
        $images = Array();
        if (Input::has("all"))
        {
            $videofile = Input::get('videoid');
            $allkfs = Entity::where('documentType','keyframe')->whereIn('parents',[$videofile])->get()->sortBy('content.scenestart')->toArray();
            //print_r($allkfs);
            foreach($allkfs as $currentkeyf)
            {
                $images[] = $currentkeyf['_id'];
            }
        } else {
            $images[] = Input::get("keyframeid");
        }
        $successcount = 0;
        foreach ($images as $image) {

            $curlurl = "http://api.imagga.com/v1/content";
            $videounit = Entity::where('_id', $image)->first()->toArray();
            if (isset($videounit['content']['tags'])) {

                foreach ($videounit['content']['tags'] as $curtag) {

                    if ($curtag['source'] == "imagga") continue 2;
                }
            }
            $frameloc = $videounit['content']['frames'][0];
            $inputfile = storage_path($frameloc);

            // I tried to do this in PHP, I really did.
            $imagga_api_id = Config::get('config.imagga_api_key');
            $imagga_api_secret = Config::get('config.imagga_api_secret');


            $curlcmd = "curl --user \"$imagga_api_id:$imagga_api_secret\" -F \"image=@$inputfile\" $curlurl";
            $output = shell_exec($curlcmd);

            $output = json_decode($output, true);
            $uploadid = $output['uploaded'][0]['id'];

            $curlurl = "https://api.imagga.com/v1/tagging?content=$uploadid";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $curlurl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_USERPWD, "$imagga_api_id:$imagga_api_secret");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($curl);

            $output = json_decode($output, true);

            $updateframe = Unit::where('_id', $image)->get()->first();
            $newcontent = $updateframe->content;
            $classarray = Array();
            $results = $output['results'][0]['tags'];


            $classarray['source'] = 'imagga';
            $classarray['timestamp'] = time();
            foreach ($results as $index => $result) {
                $classarray['tags'][$index]['tag'] = $result['tag'];
                $classarray['tags'][$index]['prob'] = ($result['confidence'] / 100); //imagga does 0-100, so we normalize to 0-1;
            }

            if (!isset($newcontent['tags'])) {
                $newcontent['tags'] = Array();
            }

            /*} else {
                $newtags = array_keys($newcontent['tags']);
                $newtags = array_pop(array_sort($newtags));
            }*/

            $newarray = $newcontent['tags'];
            $newarray[] = $classarray;

            $newcontent['tags'] = $newarray;

            $updateframe->content = $newcontent;
            $updateframe->save();
            $successcount++;
        }
        $this->echoSuccess("Successfully processed $successcount images through Imagga!");



    }

    private function getClarifaiKey()
    {

        $expiretime = (Int)(Setting::get('clarifai_expiretime','-1')) - 5; //minus five seconds for some tolerance.

        $currenttime = time();


        if (($currenttime < $expiretime) && ($expiretime > 0))
        {
            return Setting::get('clarifai_currenttoken','-1');
        } else {
            $clarifai_id = Config::get('config.clarifai_client_id');
            $clarifai_secret = Config::get('config.clarifai_client_secret');
            $at_curl = curl_init();
            curl_setopt($at_curl, CURLOPT_URL, "https://api.clarifai.com/v1/token/");
            curl_setopt($at_curl, CURLOPT_POST, true);
            curl_setopt($at_curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($at_curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($at_curl, CURLOPT_SSL_VERIFYPEER, false);
            $at_postdata = Array();
            $at_postdata['client_id'] = $clarifai_id;
            $at_postdata['client_secret'] = $clarifai_secret;
            $at_postdata['grant_type'] = 'client_credentials';
            curl_setopt($at_curl, CURLOPT_POSTFIELDS, $at_postdata);

            $at_result = curl_exec($at_curl);
            $at_json = json_decode($at_result);
            Setting::set('clarifai_currenttoken',$at_json->access_token);
            $expiretime = time() + $at_json->expires_in;
            Setting::set('clarifai_expiretime',$expiretime);
            Setting::save();
            return $at_json->access_token;
        }
    }

}