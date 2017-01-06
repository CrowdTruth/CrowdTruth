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

    public function postUploadVideos()
    {
        if (!Input::has('keyframes')) {
            return Redirect::to('media/search');
        }

        $keyframes = Input::get('keyframes');
        $videounit = Input::get('videounit');
        $outarray = Array();
        foreach($keyframes as $keyframe)
        {
            $curkf = Entity::where('_id',$keyframe)->get()->first()->toArray();

            if (isset($curkf['content']['cliplocation'])) {
                $image = base64_encode(file_get_contents(storage_path($curkf['content']['cliplocation'])));
                $exploded1 = explode("/", $keyframe);
                $imagefilename = array_pop($exploded1) . ".mp4";

                $curout = Array();
                $curout['filename'] = $imagefilename;
                $curout['content'] = $image;
                array_push($outarray, $curout);
            } else {
                $this->echoError("Please generate video clips first!");
            }

            $postdata['unitdir'] = str_replace("/",".",$videounit);
            $postdata['image'] = json_encode($outarray);
            $curl = curl_init();
            $curltarget = "https://joran.org/ct/receiver/post.php";
            curl_setopt($curl,CURLOPT_USERPWD,"joran:ctpost");
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_URL,$curltarget);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$postdata);
            curl_exec($curl);

        }


        $this->echoSuccess("Uploaded ". count($keyframes). " videos!");
    }

    public function postUploadImages()
    {
        if (!Input::has('keyframes')) {
            return Redirect::to('media/search');
        }

        $keyframes = Input::get('keyframes');
        $videounit = Input::get('videounit');
        $outarray = Array();
        foreach($keyframes as $keyframe)
        {
            $curkf = Entity::where('_id',$keyframe)->get()->first()->toArray();

            $image = base64_encode(file_get_contents(storage_path($curkf['content']['frames'][0])));
            $exploded1 = explode("/",$keyframe);
            $imagefilename = array_pop($exploded1) . ".png";

            $curout = Array();
            $curout['filename'] = $imagefilename;
            $curout['content'] = $image;
            array_push($outarray,$curout);
        }

        $postdata['unitdir'] = str_replace("/",".",$videounit);
        $postdata['image'] = json_encode($outarray);
        $curl = curl_init();
        $curltarget = "https://joran.org/ct/receiver/post.php";
        curl_setopt($curl,CURLOPT_USERPWD,"joran:ctpost");
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_URL,$curltarget);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$postdata);
        curl_exec($curl);

        $this->echoSuccess("Uploaded ".count($keyframes)." images!");






        exit;

        $keyframeid = Input::get('keyframeid');

        $output = Entity::where('_id', $keyframeid)->get()->first()->toArray();

        $videofile = Entity::where('_id', $output['parents'][0])->get()->first()->toArray();
        $output['videodata'] = $videofile;

                if (isset($output['videodata']['content']['tags']))
                {
                    foreach ($output['videodata']['content']['tags'] as $curtags)
                    {
                        if ($curtags['source'] != "nerd") continue;
                        $kfcount = 0;
                        $curdes = $output['videodata']['content']['description'];
                        $tt_part1a = "<span kfcount=\"";
                        $tt_part1b = "\" class=\"vidtooltip\">";
                        $tt_part2 = "<span class=\"vidtooltiptext\">";
                        $tt_part3 = "</span></span>";
                        $curoffset = 0;
                        foreach ($curtags['tags'] as $destag) {
                            $tagstr = $destag['label'];
                            $tagoff = (Int)$destag['startChar'];
                            $tagcon = (Float)$destag['confidence'];
                            $tagcon = sprintf("%.03f", $tagcon);
                            $tagurl = "Source: NerdML<br> Conf: $tagcon<br>" . $destag['uri'];

                            $taglength = strlen($tagstr);


                            $createstr =  $tagstr . "__" . $tagoff . "__" . ($tagoff+$taglength-1) . "_###_";
                            echo $createstr;

                            $makett = $tt_part1a . $kfcount . $tt_part1b . $tagstr . $tt_part2 . $tagurl . $tt_part3;

                            $curdes = substr_replace($curdes, $makett, $tagoff + $curoffset, $taglength);
                            $curoffset += strlen($makett) - $taglength;
                            $kfcount++;
                        }
                        $output['videodata']['content']['taggeddes'] = $curdes;
                    }
                }
                echo "\n";
                $allimagetags = Array();
                if (isset($output['content']['tags'])) {
                    foreach ($output['content']['tags'] as $curtagkey => $curtag) {
                        if ($curtag['source'] == "imagga" || $curtag['source'] == "clarifai")
                        {
                            foreach($curtag['tags'] as $curtagsingle)
                            {
                                $inserttag = $curtagsingle;
                                $inserttag['source'] = $curtag['source'];
                                $allimagetags[] = $inserttag;
                            }
                        }



                        $kfcount = 0;

                        if ($curtag['source'] == "nerd") {
                            $cursub = implode(" ", $output['content']['subtitles']);
                            $tt_part1a = "<span kfcount=\"";
                            $tt_part1b = "\" class=\"vidtooltip\">";
                            $tt_part2 = "<span class=\"vidtooltiptext\">";
                            $tt_part3 = "</span></span>";
                            $curoffset = 0;
                            foreach ($curtag['tags'] as $subtag) {
                                $tagstr = $subtag['label'];
                                $tagoff = (Int)$subtag['startChar'];
                                $tagcon = (Float)$subtag['confidence'];
                                $tagcon = sprintf("%.03f", $tagcon);
                                $tagurl = "Source: NerdML<br> Conf: $tagcon<br>" . $subtag['uri'];

                                $taglength = strlen($tagstr);
                                $createstr =  $tagstr . "__" . $tagoff . "__" . ($tagoff+$taglength-1) . "_###_";
                                echo $createstr;


                                $makett = $tt_part1a . $kfcount . $tt_part1b . $tagstr . $tt_part2 . $tagurl . $tt_part3;

                                $cursub = substr_replace($cursub, $makett, $tagoff + $curoffset, $taglength);
                                $curoffset += strlen($makett) - $taglength;
                                $kfcount++;
                            }
                            $output['content']['taggedsub'] = $cursub;
                        }
                    }
                }



    echo "\n";

        usort($allimagetags, function($a, $b) {
            return $a['prob'] - $b['prob'];
        });
        $allimagetags = array_reverse($allimagetags);
        foreach ($allimagetags as $allimagetag)
        {
            $createstr = $allimagetag['tag']  . "_###_"; //. "__" . $allimagetag['source'] . "__" . sprintf("%.3f",$allimagetag['prob'])
            echo $createstr;
        }
        echo "\n";
        print_r($output);



        return View::make('media.processvideo.pages.taskpreview')->with('data', $output);
    }

    public function postCreateSet()
    {
        if (!Input::has('keyframes')) {
            return Redirect::to('media/search');
        }

        $keyframes = Input::get('keyframes');
        $videounit = Input::get('videounit');
        $outarray = Array();


        $curvid = Entity::where('_id',$videounit)->get()->first()->toArray();
        $description = str_replace("\n"," ",trim($curvid['content']['description']));


        foreach($keyframes as $keyframe)
        {
            $curkf = Entity::where('_id',$keyframe)->get()->first()->toArray();

            $addrow = Array();
            $addrow['ctunitid'] = $curkf['_id'];
            $addrow['description'] = $description;
            if (isset($curkf['content']['subtitles']))
            {
                $addrow['subtitles'] = str_replace("\""," ",implode(" ",$curkf['content']['subtitles']));
            } else {
                $addrow['subtitles'] = "";
            }
            $splitid = explode("/",$curkf['_id']);
            $unitnumber = array_pop($splitid);
            $addrow['imagelocation'] = "https://joran.org/ct/".str_replace("/",".",$videounit)."/".$unitnumber.".png";
            $addrow['videolocation'] = "https://joran.org/ct/".str_replace("/",".",$videounit)."/".$unitnumber.".mp4";


            array_push($outarray,$addrow);
        }
        echo "ctunitid,description,subtitles,imagelocation,videolocation\n";
        foreach ($outarray as $currow)
        {
            echo "\"".$currow['ctunitid']."\",";
            echo "\"".$currow['description']."\",";
            echo "\"".$currow['subtitles']."\",";
            echo "\"".$currow['imagelocation']."\",";
            echo "\"".$currow['videolocation']."\"\n";

        }

    }

    public function postCreateSet56()
    {
        if (!Input::has('keyframes')) {
            return Redirect::to('media/search');
        }

        $limitperimageclassifier = 5;

        $keyframes = Input::get('keyframes');
        $videounit = Input::get('videounit');
        $outarray = Array();


        $curvid = Entity::where('_id',$videounit)->get()->first()->toArray();
        $descriptiontags = "";
        if (isset($curvid['content']['tags']))
        {
            foreach ($curvid['content']['tags'] as $curtag)
            {
                if ($curtag['source'] != 'nerd') continue;

                foreach($curtag['tags'] as $tagfoundkey => $tagfound)
                {
                    $curtagid = $tagfoundkey;
                    $curtagstr = $tagfound['label'];

                    $descriptiontags .= $curtagstr . "__" . $curtagid . "_###_";
                }
            }
        }


        foreach($keyframes as $keyframe)
        {
            $curkf = Entity::where('_id',$keyframe)->get()->first()->toArray();

            $addrow = Array();
            $addrow['ctunitid'] = $curkf['_id'];
            $addrow['descriptiontags'] = $descriptiontags;
            $addrow['subtitletags'] = "";
            $addrow['imagetags'] = "";


            $clarifaicounter = 0;
            $imaggacounter = 0; //bug avoidance;
            if (isset($curkf['content']['tags']))
            {
                foreach ($curkf['content']['tags'] as $curtag)
                {
                    if ($curtag['source'] == 'clarifai' && $clarifaicounter == 1) continue;
                    if ($curtag['source'] == 'imagga' && $imaggacounter == 1) continue;
                    if ($curtag['source'] == 'clarifai') $clarifaicounter = 1;
                    if ($curtag['source'] == 'imagga') $imaggacounter = 1;
                    switch($curtag['source'])
                    {
                        case "nerd":
                            foreach ($curtag['tags'] as $tagfoundkey => $tagfound)
                            {
                            //    if (!isset( $tagfound['tags'])) continue;

                                $curtagid = $tagfoundkey;
                                $curtagstr = $tagfound['label'];

                                $addrow['subtitletags'] .= $curtagstr . "__" . $curtagid . "_###_";
                            }

                            break;
                        case "clarifai":
                        case "imagga":
                                $cursrc = substr($curtag['source'], 0 ,1);
                                foreach ($curtag['tags'] as $tagfoundkey => $tagfound)
                                {
                                    if ($tagfoundkey >= $limitperimageclassifier) continue;
                                    $curtagid = $tagfoundkey;
                                    $curtagstr = $tagfound['tag'];

                                    $addrow['imagetags'] .= $curtagstr ."__". $cursrc . $curtagid . "_###_";

                                }


                            break;
                    }
                }
            }

            $splitid = explode("/",$curkf['_id']);
            $unitnumber = array_pop($splitid);
            $addrow['imagelocation'] = "https://joran.org/ct/".str_replace("/",".",$videounit)."/".$unitnumber.".png";
            $addrow['videolocation'] = "https://joran.org/ct/".str_replace("/",".",$videounit)."/".$unitnumber.".mp4";


            array_push($outarray,$addrow);
        }
        echo "ctunitid,descriptiontags,subtitletags,imagetags,imagelocation,videolocation\n";
        foreach ($outarray as $currow)
        {
            echo "\"".$currow['ctunitid']."\",";
            echo "\"".$currow['descriptiontags']."\",";
            echo "\"".$currow['subtitletags']."\",";
            echo "\"".$currow['imagetags']."\",";
            echo "\"".$currow['imagelocation']."\",";
            echo "\"".$currow['videolocation']."\"\n";

        }

    }

    public function postStepTwo()
    {
        if (!Input::has('videofile'))
        {
            return Redirect::to('media/search');
        }

        $videofile = Input::get('videofile');
        $output = Entity::where('_id',$videofile)->get()->first()->toArray();
        $kfdata = Entity::where('documentType','keyframe')->whereIn('parents',[$videofile])->get()->sortBy('content.scenestart')->toArray();
        if (count($kfdata) > 0) {
            $output['keyframes'] = $kfdata; }

        if (isset($output['keyframes'])) {

            foreach ($output['keyframes'] as $curkfkey => $curkf) {


                if (isset($curkf['content']['tags'])) {
                    foreach ($curkf['content']['tags'] as $curtagkey => $curtag) {
                        if ($curtag['source'] == "imagga") continue;
                        if ($curtag['source'] == "clarifai") continue;
                        if (isset($curkf['content']['subtitles'])) {

                            if ($curtag['source'] == "nerd") {
                                $cursub = implode(" ", $curkf['content']['subtitles']);
                                $tt_part1 = "<span class=\"vidtooltip\">";
                                $tt_part2 = "<span class=\"vidtooltiptext\">";
                                $tt_part3 = "</span></span>";
                                $curoffset = 0;
                                foreach ($curtag['tags'] as $subtag) {
                                    $tagstr = $subtag['label'];
                                    $tagoff = (Int)$subtag['startChar'];
                                    $tagcon = (Float)$subtag['confidence'];
                                    $tagcon = sprintf("%.03f", $tagcon);
                                    $tagurl = "Source: NerdML<br> Conf: $tagcon<br>" . $subtag['uri'];

                                    $taglength = strlen($tagstr);

                                    $makett = $tt_part1 . $tagstr . $tt_part2 . $tagurl . $tt_part3;

                                    $cursub = substr_replace($cursub, $makett, $tagoff + $curoffset, $taglength);
                                    $curoffset += strlen($makett) - $taglength;
                                }
                                $output['keyframes'][$curkfkey]['content']['taggedsub'] = $cursub;


                            }
                        }
                    }
                }
            }
        }

        return View::make('media.processvideo.pages.steptwo')->with('data',$output);
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

        $kfdata = Entity::where('documentType','keyframe')->whereIn('parents',[$videofile])->get()->sortBy('content.scenestart')->toArray();
        if (count($kfdata) > 0) {
            $output['keyframes'] = $kfdata; }


        if (isset($output['content']['description']) && isset($output['content']['tags']))
        {
            $curdesc = $output['content']['description'];
            $curoffset = 0;

            foreach($output['content']['tags'] as $curtag)
            {
                if ($curtag['source'] != "nerd") continue;
                foreach($curtag['tags'] as $curactualtag) {
                    $tt_part1 = "<span class=\"vidtooltip\">";
                    $tt_part2 = "<span class=\"vidtooltiptext\">";
                    $tt_part3 = "</span></span>";

                    $tagstr = $curactualtag['label'];
                    $tagoff = (Int)$curactualtag['startChar'];
                    $tagcon = (Float)$curactualtag['confidence'];
                    $tagcon = sprintf("%.03f",$tagcon);
                    $tagurl = "Source: NerdML<br> Conf: $tagcon<br>" . $curactualtag['uri'];

                    $taglength = strlen($tagstr);

                    $makett = $tt_part1 . $tagstr . $tt_part2 . $tagurl . $tt_part3;

                    $curdesc = substr_replace($curdesc, $makett, $tagoff + $curoffset, $taglength);
                    $curoffset += strlen($makett) - $taglength;
                }
            }
            $output['content']['taggeddesc'] = $curdesc;
        }


        if (isset($output['keyframes'])) {
            $output['doneclarifai'] = "1";
            $output['doneimagga'] = "1";

            $output['donenerd'] = "0";
            $nerdswitch = false;
            foreach ($output['keyframes'] as $curkfkey => $curkf) {

                $clarifaiswitch = false;
                $imaggaswitch = false;

                if (isset($curkf['content']['tags']))
                {
                foreach ($curkf['content']['tags'] as $curtagkey => $curtag) {
                    if ($curtag['source'] == "imagga") $imaggaswitch = true;
                    if ($curtag['source'] == "clarifai") $clarifaiswitch = true;
                    if (isset($curkf['content']['subtitles'])) {
                        if ($curtag['source'] == "nerd") {
                            $nerdswitch = true;
                            $cursub = implode(" ", $curkf['content']['subtitles']);
                            $tt_part1 = "<span class=\"vidtooltip\">";
                            $tt_part2 = "<span class=\"vidtooltiptext\">";
                            $tt_part3 = "</span></span>";
                            $curoffset = 0;
                            foreach ($curtag['tags'] as $subtag) {
                                $tagstr = $subtag['label'];
                                $tagoff = (Int)$subtag['startChar'];
                                $tagcon = (Float)$subtag['confidence'];
                                $tagcon = sprintf("%.03f",$tagcon);
                                $tagurl = "Source: NerdML<br> Conf: $tagcon<br>" . $subtag['uri'];

                                $taglength = strlen($tagstr);

                                $makett = $tt_part1 . $tagstr . $tt_part2 . $tagurl . $tt_part3;

                                $cursub = substr_replace($cursub, $makett, $tagoff + $curoffset, $taglength);
                                $curoffset += strlen($makett) - $taglength;
                            }
                            $output['keyframes'][$curkfkey]['content']['taggedsub'] = $cursub;

                            $dbpediaswitch = true;
                        }
                    } else {
                        $dbpediaswitch = true;
                    }
                }
                }
                if (!$clarifaiswitch) $output['doneclarifai'] = "0";
                if (!$imaggaswitch) $output['doneimagga'] = "0";
                if ($nerdswitch) $output['donenerd'] = "1";

              //  if ($output['doneclarifai'] == "0" && $output['doneimagga'] == "0" && $output['donenerd'] == "0") break;
            }
        } else {
            $output['doneclarifai'] = "0";
            $output['doneimagga'] = "0";
            $output['donedbpedia'] = "0";
            $output['donenerd'] = "0";
        }
        if (count(Unit::where('documentType', 'subtitlefile')->whereIn('parents',[$videofile])->get()->toArray()) > 0) $output['subtitles'] = 'true';
        //$temp = Unit::where('documentType', 'subtitlefile')->whereIn('parents',[$videofile])->get()->toArray();
        //print_r($temp);


        return View::make('media.processvideo.pages.index')->with('data',$output);


    }

    public function postDownloadAllFiles()
    {
        ini_set('memory_limit','256M');
        $getunit = Input::get('videofile');

        $successcount = 0;
        $totalcount = count($getunit);
        foreach ($getunit as $currentunit)
        {
            $videounit = Unit::where('_id',$currentunit)->first();
            $videocontent = $videounit->content;
            $videourl = $videocontent['url'];
            if (strstr($videourl,"youtube.com")) {


                $extension = "mp4";
                $targetfilename = str_replace("/", ".", $currentunit);
                $storagedir = 'videostorage/fullvideos/' . $targetfilename . '.' . $extension;
                $targetdownload = storage_path($storagedir);

                $buildcmd = "youtube-dl -o " . $targetdownload . " \"" . $videourl . "\"";

                $ytreturn = shell_exec($buildcmd);
                if (strstr($ytreturn,"Error")) continue;
            } else {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $videourl,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_FOLLOWLOCATION => 1,));

                $extension = explode(".", $videourl);
                $extension = array_pop($extension);

                $targetfilename = str_replace("/", ".", $currentunit);
                $storagedir = 'videostorage/fullvideos/' . $targetfilename . '.' . $extension;
                $targetdownload = storage_path($storagedir);

                $curldata = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($httpcode != '200') {continue;}

                $fwh = @fopen($targetdownload, "w");
                @fwrite($fwh, $curldata);
                @fclose($fwh);
                curl_close($curl);
                if (!$fwh) {continue;}
            }
            $videocontent['downloadedvideo'] = $storagedir;

            $videounit->content = $videocontent;
            $videounit->save();
            $successcount++;

        }

        if ($successcount == $totalcount)
        {
             $this->echoSuccess( "Successfully downloaded $successcount videos!");

        } elseif ($successcount == 0) {
           $this->echoError("Couldn't download any of the $totalcount videos!");
        } else {
            $this->echoError("Downloaded $successcount of $totalcount videos!");
        }

      //  return Redirect::to('media/search');

    }

    public function getSplitVideo()
    {
        set_time_limit(0);
        $ffmpegbinary = app_path('ffmpeg');

        $videofile = Input::get('videofile');

        $videounit = Unit::where('_id',$videofile)->first()->toArray();
        $kfdata = Entity::where('documentType','keyframe')->whereIn('parents',[$videofile])->get()->sortBy('content.scenestart')->toArray();


        $newkfdata = Array();
        foreach($kfdata as $oldkey => $curkf)
        {
            $newkfdata[] = $curkf;
        }
        $kfdata = $newkfdata;

        if (count($kfdata) < 1)
        {
            $this->echoError("No keyframes found for $videofile");
        }

        $videopath = storage_path($videounit['content']['downloadedvideo']);

        $getcmd = $ffmpegbinary . " -i " . $videopath . " 2>&1 1>/dev/null | grep Duration: | cut -d \" \" -f 4 | cut -d \",\" -f 1";
        $duration = shell_exec($getcmd);
        $expl2 = explode(":",$duration);
        $duration = (Float)((3600 * (Int)$expl2[0]) + (60 * (Int)$expl2[1]) + ((Float)$expl2[2]));

        //echo storage_path('videostorage/segmentvideos/' . str_replace(".","/",$videofile));
        @mkdir(storage_path('videostorage/segmentvideos/' . str_replace(".","/",$videofile)),0777,true);

        foreach($kfdata as $kfkey => $curkf)
        {
            $curstart = $curkf['content']['scenestart'];
            if ($kfkey == (count($kfdata) -1)) {
                $curstop = $duration;
            } else {
                $curstop = $kfdata[$kfkey + 1]['content']['scenestart'];
            }

            $savestring = 'videostorage/segmentvideos/' . str_replace(".","/",$videofile) . "/" . $kfkey . ".mp4";
            $splitdestination = storage_path($savestring);
            $splitcmd = $ffmpegbinary . " -i " . $videopath . " -ss " . $curstart . " -t " . ($curstop - $curstart) . " -vcodec libx264 " . $splitdestination;
            $execcmd = shell_exec($splitcmd);

            $saveunit = Unit::where('_id',$curkf['_id'])->first();
            $oldcontent = (Array)$saveunit->content;

            $oldcontent['cliplocation'] = $savestring;
            $saveunit->content = $oldcontent;
            $saveunit->save();

        }

        $this->echoSuccess("Done splitting into ".count($kfdata). " video files");


    }

    public function getDownloadFile()
    {

        ini_set('memory_limit','256M');
        $getunit = Input::get('videounit');

        $videounit = Unit::where('_id',$getunit)->first();

        $videocontent = $videounit->content;
        $videourl = $videocontent['url'];
        if (strstr($videourl,"youtube.com")) {


            $extension = "mp4";
            $targetfilename = str_replace("/", ".", $getunit);
            $storagedir = 'videostorage/fullvideos/' . $targetfilename . '.' . $extension;
            $targetdownload = storage_path($storagedir);

            $buildcmd = "youtube-dl -o " . $targetdownload . " \"" . $videourl . "\"";

            $ytreturn = shell_exec($buildcmd);
        } else {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $videourl,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => 1,));

            $extension = explode(".", $videourl);
            $extension = array_pop($extension);

            $targetfilename = str_replace("/", ".", $getunit);
            $storagedir = 'videostorage/fullvideos/' . $targetfilename . '.' . $extension;
            $targetdownload = storage_path($storagedir);

            $curldata = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpcode != '200') $this->echoError("The file could not be downloaded.");

            $fwh = @fopen($targetdownload, "w");
            @fwrite($fwh, $curldata);
            @fclose($fwh);
            curl_close($curl);
            if (!$fwh) $this->echoError("The file could not be saved to local storage.");
        }
        $videocontent['downloadedvideo'] = $storagedir;

        $videounit->content = $videocontent;
        $videounit->save();


        if (isset($ytreturn))
        {
            $this->echoSuccess("Successfully downloaded the YouTube video to local storage: <br> $ytreturn");
        } else {
            $this->echoSuccess("Successfully downloaded the file to local storage.");
        }
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

        $cleandb = Unit::whereIn('parents', [$getunit])->where('documentType', 'keyframe')->get();
        foreach ($cleandb as $currec)
        {
            $workon = $currec->toArray();

            if (isset($workon['content']['subtitles']))
            {

                unset($workon['content']['subtitles']);


                $currec->content = $workon['content'];
                $currec->save();

            }

            if (isset($workon['content']['tags'])) //remove subtitle tags as well.
            {
                foreach($workon['content']['tags'] as $tagkey => $tagvalue)
                {
                    if ($tagvalue['source'] != 'nerd') continue;
                    unset ($workon['content']['tags'][$tagkey]);
                }

                $currec->content = $workon['content'];
                $currec->save();
            }

        }
        


        if (substr($file->getClientOriginalName(),-4,4) == ".xml") {
            $subsxml = simplexml_load_file(storage_path($targetpath . $file->getClientOriginalName()));
            $base = $subsxml->body->div->p; //this is where the subs start

            foreach ($base as $subdata) {

                $subdata = (Array)$subdata;
                $time = explode(":", $subdata['@attributes']['begin']); //hours:minutes:seconds.millis
                $seconds = (float)((($time[0] * 3600) + ($time[1] * 60) + explode(".", $time[2])[0]) . "." . (explode(".", $time[2])[1]));
                $findframe = Unit::whereIn('parents', [$getunit])->where('documentType', 'keyframe')->where('content.scenestart', '<', $seconds)->get()->sortBy('content.scenestart')->last();


                $newcontent = $findframe->content;
                if (is_array($subdata['span'])) {
                    foreach ($subdata['span'] as $cursub) {
                        $newcontent['subtitles'][] = trim($cursub);
                    }

                } else {
                    $newcontent['subtitles'][] = trim($subdata['span']);
                }

                $findframe->content = $newcontent;

                $findframe->save();


            }
        } else if(substr($file->getClientOriginalName(),-4,4) == ".srt") {


            $fh = fopen(storage_path($targetpath . $file->getClientOriginalName()),"r");
            $newline = true;
            $tempsavestring = Array();
            $curtime = "";

            while (($curline = fgetss($fh)) !== false)
            {
                if ($newline)
                {
                    $newline = false; //skip the identifier.
                    continue;
                }
                if (strstr($curline,"-->"))
                {

                    $expl1 = explode(" --> ",$curline);
                    $expl2 = explode(",",$expl1[0]);
                    $timemillis = $expl2[1];
                    $expl3 = explode(":",$expl2[0]); // hours:minutes:seconds
                    $timehours = $expl3[0];
                    $timeminutes = $expl3[1];
                    $timeseconds = (Int)$expl3[2];

                    $timeseconds += ((Int)$timehours * 3600);
                    $timeseconds += ((Int)$timeminutes * 60);
                    $curtime = (float)($timeseconds."."."$timemillis");

                    //echo "$curline became $curtime\n";
                    continue;
                }

                if (ord($curline) == 13)
                {
                    $findframe = Unit::whereIn('parents', [$getunit])->where('documentType', 'keyframe')->where('content.scenestart', '<', $curtime)->get()->sortBy('content.scenestart')->last();

                    if(!isset($findframe->content)) { //when frame does not exist, add to next one;
                        $curtime = "";
                        $newline = true;
                        continue;
                    }

                    //echo "Still doing $curtime for ". $findframe->_id ."\n";

                    //if (!mb_check_encoding($tempsavestring,'UTF-8')) $tempsavestring = mb_convert_encoding($tempsavestring,'UTF-8'); //Bug when a non utf-8 char is used.
                    $newcontent = $findframe->content;
                    if (isset($newcontent['subtitles']))
                    {
                        $newcontent['subtitles'] = array_merge($newcontent['subtitles'],$tempsavestring);
                    } else {
                        $newcontent['subtitles'] = $tempsavestring;
                    }
                    $findframe->content = $newcontent;

                    $findframe->save();

                    $tempsavestring = Array();
                    $curtime = "";
                    $newline = true;
                    continue;
                }

                if (!mb_check_encoding($curline,'UTF-8')) $curline = mb_convert_encoding($curline,'UTF-8'); //Bug when a non utf-8 char is used.
                $tempsavestring[] = trim($curline);

            }


        } else {
            $this->echoError("Format not recognized: " . $file->getClientOriginalName());
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
        $images = Array(); //Array of strings containing mongo-id's of keyframes to be classified
        if (Input::has("all")) //all keyframes with a certain video as a parent.
        {
            $videofile = Input::get('videoid');
            $allkfs = Entity::where('documentType','keyframe')->whereIn('parents',[$videofile])->get()->sortBy('content.scenestart')->toArray();

            foreach($allkfs as $currentkeyf)
            {
                $images[] = $currentkeyf['_id'];
            }
        } else { //single keyframe
            $images[] = Input::get("keyframeid");
        }
        $successcount = 0;
        foreach ($images as $image) {


            $clarifai_accesstoken = $this->getClarifaiKey(); //They have a limited lifetime

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

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //Remove this if your SSL works properly
            $output = curl_exec($curl);

            $cldata = json_decode($output, false, 512, JSON_BIGINT_AS_STRING); //JSON_BIGINT_AS_STRING is needed and only provided in newer json-php libraries
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

            $imagga_api_id = Config::get('config.imagga_api_key');
            $imagga_api_secret = Config::get('config.imagga_api_secret');

            // I tried to do this in PHP, I really did.
            $curlcmd = "curl --user \"$imagga_api_id:$imagga_api_secret\" -F \"image=@$inputfile\" $curlurl"; //save the file on the imagga server
            $output = shell_exec($curlcmd);

            $output = json_decode($output, true);
            $uploadid = $output['uploaded'][0]['id']; //get the id of the uploaded file

            $curlurl = "https://api.imagga.com/v1/tagging?content=$uploadid"; //ask imagga to annotate the file and get the tags
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
            $classarray['timestamp'] = new MongoDate();
            foreach ($results as $index => $result) {
                $classarray['tags'][$index]['tag'] = $result['tag'];
                $classarray['tags'][$index]['prob'] = ($result['confidence'] / 100); //imagga does 0-100, so we normalize to 0-1;
            }

            if (!isset($newcontent['tags'])) {
                $newcontent['tags'] = Array();
            }


            $newarray = $newcontent['tags'];
            $newarray[] = $classarray;

            $newcontent['tags'] = $newarray;

            $updateframe->content = $newcontent;
            $updateframe->save();
            $successcount++;
        }
        $this->echoSuccess("Successfully processed $successcount images through Imagga!");



    }

    public function getDBPediaSpotlight_allSubtitles()
    {
        $unitid = Input::get('unitid');
        $allkf = Entity::where('documentType','keyframe')->whereIn('parents',[$unitid])->get()->sortBy('content.scenestart')->toArray();
        $donecounter = 0;

        foreach ($allkf as $curkf)
        {

            if (!isset($curkf['content']['subtitles']))
            {
                continue;
            } else {
                if (!isset($curkf['content']['tags'])) continue;
                foreach($curkf['content']['tags'] as $curtag)
                {
                    if ($curtag['source'] == "dbpedia") continue 2;
                }
            }

            $data = implode(" ", $curkf['content']['subtitles']);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = urlencode($data);
            $getdata = "text=$data&confidence=0.35";
            curl_setopt($curl, CURLOPT_URL, 'http://spotlight.sztaki.hu:2222/rest/annotate?' . $getdata);
            $getheader = Array("Accept: application/json");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $getheader);
            $returndata = curl_exec($curl);

            $updateunit = Unit::where('_id',$curkf['_id'])->get()->first();
            $newcontent = $updateunit->content;
            $results = (Array)json_decode($returndata);
            //print_r($results);
            $newtags = Array();
            $newtags['source'] = 'dbpedia';
            $newtags['confidence'] = $results['@confidence'];
            $newtags['timestamp'] = new MongoDate();
            $newtags['tags'] = Array();
            if (isset($results['Resources']))
            {
                foreach ($results['Resources'] as $result)
                {
                    $newtags['tags'][] = (Array)$result;
                }
            }

            if (!isset($newcontent['tags'])) {
                $newcontent['tags'] = Array();
            }

            $newarray = $newcontent['tags'];
            $newarray[] = $newtags;

            $newcontent['tags'] = $newarray;

            $updateunit->content = $newcontent;
            $updateunit->save();
            $donecounter++;
        }
        $this->echoSuccess("Successfully proceesed $donecounter for $unitid");
    }

    public function getNerd_allSubtitles()
    {
        $unitid = Input::get('unitid');
        $allkf = Entity::where('documentType','keyframe')->whereIn('parents',[$unitid])->get()->sortBy('content.scenestart')->toArray();
        $donecounter = 0;
        $failarray = Array();

        $nerdapikey = Config::get('config.nerd_api_key');

        foreach ($allkf as $curkf)
        {

            if (!isset($curkf['content']['subtitles']))
            {
                continue;
            } else {
                if (!isset($curkf['content']['tags'])) continue;
                foreach($curkf['content']['tags'] as $curtag)
                {
                    if ($curtag['source'] == "nerd") continue 2;
                }
            }

            $data = implode(" ", $curkf['content']['subtitles']);

            $curlurl =  "http://nerd.eurecom.fr/api/document";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $curlurl);
            curl_setopt($curl, CURLOPT_POST, 2);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $postdata = 'text=' . urlencode($data) . '&key=' . urlencode($nerdapikey);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$postdata);
            $returndata = curl_exec($curl);
            $returndata = json_decode($returndata);
            if (!isset($returndata->idDocument))
            {
                $curcount = count($failarray);
                $failarray[$curcount]['unit'] = $curkf['_id'];
                $failarray[$curcount]['msg'] = "1: " . json_encode($returndata);
                continue;
            }
            $documentid = $returndata->idDocument;
            curl_close($curl);

            $curlurl = "http://nerd.eurecom.fr/api/annotation";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $curlurl);
            curl_setopt($curl, CURLOPT_POST, 5);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $postdata = 'key=' . $nerdapikey . '&idDocument=' . $documentid . '&extractor=nerdml&ontology=extended&timeout=10';
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            $returndata = curl_exec($curl);
            $returndata = json_decode($returndata);
            if (!isset($returndata->idAnnotation))
            {
                $curcount = count($failarray);
                $failarray[$curcount]['unit'] = $curkf['_id'];
                $failarray[$curcount]['msg'] = "2: " . json_encode($returndata);
                continue;
            }
            $annotationid = $returndata->idAnnotation;
            curl_close($curl);

            $curlurl = "http://nerd.eurecom.fr/api/entity?key=" . $nerdapikey . "&idAnnotation=" . $annotationid . "&granularity=oen";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $curlurl);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $getheader = Array("Accept: application/json");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $getheader);
            $returndata = curl_exec($curl);
            $returndata = (Array)json_decode($returndata);
            if (count($returndata) == 0)
            {
                $curcount = count($failarray);
                $failarray[$curcount]['unit'] = $curkf['_id'];
                $failarray[$curcount]['msg'] = "3: " . json_encode($returndata);
                continue;
            }
            curl_close($curl);

            $updateunit = Unit::where('_id',$curkf['_id'])->get()->first();
            $newcontent = $updateunit->content;

            //print_r($results);
            $newtags = Array();
            $newtags['source'] = 'nerd';

            $newtags['timestamp'] = new MongoDate();
            $newtags['tags'] = Array();

                foreach ($returndata as $result)
                {
                    $newtags['tags'][] = (Array)$result;
                }


            if (!isset($newcontent['tags'])) {
                $newcontent['tags'] = Array();
            }

            $newarray = $newcontent['tags'];
            $newarray[] = $newtags;

            $newcontent['tags'] = $newarray;

            $updateunit->content = $newcontent;
            $updateunit->save();
            $donecounter++;
        }
        print_r($failarray);
        $this->echoSuccess("Successfully proceesed $donecounter for $unitid");


    }

    public function getNerd()
    {
        $nerdapikey = Config::get('config.nerd_api_key');
        $gettype = Input::get('type');
        $unitid = Input::get('unitid');
        $data = "";
        if ($gettype == "subtitles") {
            $unit = Unit::where('_id', $unitid)->first()->toArray();
            if (!isset($unit['content']['subtitles'])) {
                $this->echoError("$unitid has no subtitles.");
            }

            $data = implode(" ", $unit['content']['subtitles']);

        } else if ($gettype == "description")
        {
            $unit = Unit::where('_id', $unitid)->first()->toArray();
            if (!isset($unit['content']['description']))
            {
                $this->echoError("$unitid has no description.");
            }

            $data = $unit['content']['description'];
        }

        $curlurl =  "http://nerd.eurecom.fr/api/document";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curlurl);
        curl_setopt($curl, CURLOPT_POST, 2);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $postdata = 'text=' . urlencode($data) . '&key=' . urlencode($nerdapikey);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$postdata);
        $returndata = curl_exec($curl);
        $returndata = json_decode($returndata);
        $documentid = $returndata->idDocument;
        curl_close($curl);

        $curlurl = "http://nerd.eurecom.fr/api/annotation";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curlurl);
        curl_setopt($curl, CURLOPT_POST, 5);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $postdata = 'key=' . $nerdapikey . '&idDocument=' . $documentid . '&extractor=nerdml&ontology=extended&timeout=10';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $returndata = curl_exec($curl);
        $returndata = json_decode($returndata);
        $annotationid = $returndata->idAnnotation;
        curl_close($curl);

        $curlurl = "http://nerd.eurecom.fr/api/entity?key=" . $nerdapikey . "&idAnnotation=" . $annotationid . "&granularity=oen";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curlurl);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $getheader = Array("Accept: application/json");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $getheader);
        $returndata = curl_exec($curl);
        $returndata = (Array)json_decode($returndata);
        curl_close($curl);


        $updateframe = Unit::where('_id', $unitid)->get()->first();
        $newcontent = $updateframe->content;

        //print_r($results);
        $newtags = Array();
        $newtags['source'] = 'nerd';

        $newtags['timestamp'] = new MongoDate();
        $newtags['tags'] = Array();
        foreach ($returndata as $result)
        {
            $newtags['tags'][] = (Array)$result;
        }


        if (!isset($newcontent['tags'])) {
            $newcontent['tags'] = Array();
        }

        $newarray = $newcontent['tags'];
        $newarray[] = $newtags;

        $newcontent['tags'] = $newarray;

        $updateframe->content = $newcontent;
        $updateframe->save();
        $this->echoSuccess("Succesfully processed $gettype of $unitid with NERD");

    }

    public function getDBPediaSpotlight()
    {
        $gettype = Input::get('type');
        $unitid = Input::get('unitid');
        $data = "";
        if ($gettype == "subtitles") {
            $unit = Unit::where('_id', $unitid)->first()->toArray();
            if (!isset($unit['content']['subtitles'])) {
                $this->echoError("$unitid has no subtitles.");
            }

            $data = implode(" ", $unit['content']['subtitles']);

        } else if ($gettype == "description")
        {
            $unit = Unit::where('_id', $unitid)->first()->toArray();
            if (!isset($unit['content']['description']))
            {
                $this->echoError("$unitid has no description.");
            }

            $data = $unit['content']['description'];
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = urlencode($data);
        $getdata = "text=$data&confidence=0.35";
        //print_r($getdata);
        curl_setopt($curl, CURLOPT_URL, 'http://spotlight.sztaki.hu:2222/rest/annotate?' . $getdata);
        $getheader = Array("Accept: application/json");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $getheader);

        $returndata = curl_exec($curl);


        $updateframe = Unit::where('_id', $unitid)->get()->first();
        $newcontent = $updateframe->content;
        $results = (Array)json_decode($returndata);
        //print_r($results);
        $newtags = Array();
        $newtags['source'] = 'dbpedia';
        $newtags['confidence'] = $results['@confidence'];
        $newtags['timestamp'] = new MongoDate();
        $newtags['tags'] = Array();
        foreach ($results['Resources'] as $result)
        {
            $newtags['tags'][] = (Array)$result;
        }


        if (!isset($newcontent['tags'])) {
            $newcontent['tags'] = Array();
        }

        $newarray = $newcontent['tags'];
        $newarray[] = $newtags;

        $newcontent['tags'] = $newarray;

        $updateframe->content = $newcontent;
        $updateframe->save();
        $this->echoSuccess("Succesfully processed $gettype of $unitid with DBPedia Spotlight");
        //print_r($updateframe);

   }

    public function postAddDescription()
    {
        $unitid = Input::get('unitid');
        $desc = Input::get('description');
        $getunit = Unit::where('_id',$unitid)->get()->first()->toArray();

        if (isset($getunit['content']['description']))
        {
            $this->echoError("Description for $unitid already set.");
        }

        $newcontent = $getunit['content'];
        $newcontent['description'] = $desc;

        $updateunit = Unit::where('_id',$unitid)->get()->first();
        $updateunit->content = $newcontent;
        $updateunit->save();

        $this->echoSuccess("Succesfully saved description for $unitid");

    }

    private function getClarifaiKey()
    {
        //Setting:: is provided by "anlutro/l4-settings": "^0.4.8" in composer.json
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