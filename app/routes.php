<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{
	Route::controller('media/preprocess/fullvideo', 'preprocess\FullvideoController');
	Route::controller('media/preprocess/relex', 'preprocess\RelexController');
	Route::controller('media/preprocess/text', 'preprocess\TextController');
	Route::controller('media/preprocess/CSVresultController', 'preprocess\CSVresultController');
  Route::controller('media/preprocess/metadatadescription', 'preprocess\MetadatadescriptionController');
	Route::controller('media', 'MediaController');

	Route::controller('jobs', 'JobsController');
	Route::controller('jobs2', 'JobsController2');
	Route::controller('workers', 'WorkersController');
  Route::controller('analyze','AnalyticsController');
  Route::controller('onlinesource', 'OnlineSourceController');
	
});



Route::get('/', function()
{

/*  $units = \MongoDB\Entity::where('documentType', 'metadatadescription')->get();

  foreach ($units as $unit) {
    $unit["totalNoOfFeatures"] = count($unit["content"]["features"]["cleanedUpEntities"]) + count($unit["content"]["features"]["automatedEvents"]);
    $words = explode(" ", $unit["content"]["description"]);
    $unit["wordCount"] = count($words);

    $unit->update();

  }
*/ 
//  dd("here");
//set_time_limit(5200);
/*$id=  'entity/text/medical/relex-structured-sentence/2203';
    $avg_clarity = \MongoDB\Entity::where('metrics.units.withoutSpam.'.$id, 'exists', 'true')->avg('metrics.units.withoutSpam.'.$id.'.avg.max_relation_Cos');
    dd($avg_clarity);*/
/*    set_time_limit(6000000);
    //dd(array_flatten(\MongoDB\Entity::where('documentType','workerunit')->where('unit_id', "entity/text/medical/twrex-structured-sentence/1128")->distinct('job_id')->get()->toArray()));
    $units = \MongoDB\Entity::where('documentType', 'metadatadescription')->where('cache.softwareAgent.cf2', 'exists', false)->get(['_id'])->toArray();
  //  dd($units);
    // $units = \MongoDB\Entity::where('tags', 'unit')->where('_id',"entity/text/medical/twrex-structured-sentence/1128")->get(['_id'])->toArray();
    foreach($units as $unit)
    {
        $jobField = array();
        $jobField['count'] = count(array_flatten(\MongoDB\Entity::where('documentType','workerunit')->where('unit_id', $unit['_id'])->distinct('job_id')->get()->toArray()));
        $jobTypes = array_flatten(\MongoDB\Entity::where('documentType','workerunit')->distinct('type')->get()->toArray());
        $jobField['distinct'] = count(array_flatten(\MongoDB\Entity::where('documentType','workerunit')->where('unit_id', $unit['_id'])->distinct('type')->get()->toArray()));
        $jobField['types'] = array();
        foreach($jobTypes as $type) {
            $jobField['types'][$type] = array();
            $jobField['types'][$type]['count'] = count(array_flatten(\MongoDB\Entity::where('documentType','workerunit')->where('unit_id', $unit['_id'])->where('type',$type)->distinct('job_id')->get()->toArray()));
            $unitJobs = array_flatten(\MongoDB\Entity::where('documentType','workerunit')->where('type',$type)->where('unit_id', $unit['_id'])->distinct('job_id')->get()->toArray());
            $jobTemplates = array_flatten(\MongoDB\Entity::whereIn('_id', $unitJobs)->distinct('template')->get()->toArray());
            $jobField['types'][$type]['distinct'] = count($jobTemplates);

            $jobField['types'][$type]['templates'] = array();
          //  foreach($jobTemplates as $template){
          //      $countTemplateJobs = count(\MongoDB\Entity::whereIn('_id', $unitJobs)->where('template',$template)->lists('template'));
          //      $jobField['types'][$type]['templates'][$template] = $countTemplateJobs;
          //  }
        }

        $annotationField = array();
        $annotationField['count'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->get()->toArray());
        $annotationField['spam'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', true)->get()->toArray());
        $annotationField['nonSpam'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', false)->get()->toArray());


        $platformField = array();
        $platformField['cf2'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('softwareAgent_id','cf2' )->get()->toArray());
        $platformField['amt'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('softwareAgent_id','amt' )->get()->toArray());


        $workerField = array();
        $workerField['count'] = count(array_flatten(\MongoDB\Entity::where('unit_id', $unit['_id'])->distinct('crowdAgent_id')->get()->toArray()));

        $spamWorkers = array_flatten(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', true)->distinct('crowdAgent_id')->get()->toArray());
        $nonSpamWorkers = array_flatten(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', false)->distinct('crowdAgent_id')->get()->toArray());
        $potentialSpam = array_intersect($spamWorkers, $nonSpamWorkers);
        $workerField['spam'] = count($spamWorkers) - count($potentialSpam);
        $workerField['nonSpam'] = count($nonSpamWorkers) - count($potentialSpam);
        $workerField['potentialSpam'] = count($potentialSpam);

        //filtered
        $filteredField = array();
        $filteredField['job_ids'] = array_flatten(\MongoDB\Entity::where('documentType', 'job')->where('metrics.filteredUnits.list','all', array($unit['_id']))->get(['_id'])->toArray());
        $filteredField['count'] = count($filteredField['job_ids']);

        //batches
        $batchesField = array();
        $batchesField['count'] = count(\MongoDB\Entity::where('documentType', 'batch')->where('parents', 'all', array($unit['_id']))->get()->toArray());

        $mongoUnit = \MongoDB\Entity::where('_id',$unit['_id'])->first();
        $derivatives = \MongoDB\Entity::whereIn('parents', array($unit['_id']))->lists('_id');

        $children["count"] = count($derivatives);
        $children["list"] = $derivatives;

        $mongoUnit->cache = ["jobs" => $jobField,
            "workers" => $workerField,
            "softwareAgent" => $platformField,
            "workerunits" => $annotationField,
            "filtered" => $filteredField,
            "batches" => $batchesField,
            "children" => $children];
        $mongoUnit->update();

        //dd($unit['_id']);
        $avg_clarity = \MongoDB\Entity::where('metrics.units.withoutSpam.'.$unit['_id'], 'exists', 'true')->avg('metrics.units.withoutSpam.'.$unit['_id'].'.avg.max_relation_Cos');
        if (!isset($avg_clarity)) $avg_clarity = 0;
        $mongoUnit->avg_clarity = $avg_clarity;

        $mongoUnit->update();

    }

    return "done";
*/

//	$metadatadescriptions = \MongoDB\Entity::where("documentType", "metadatadescription")->get()->toArray();
//	foreach ($metadatadescriptions as $metad) {
//		createStatisticsForMetadatadescriptionCache("entity/text/cultural/metadatadescription/574");
//	}

/*	$metadatadescriptions = \MongoDB\Entity::where("documentType", "metadatadescription")->get()->toArray();
		foreach ($metadatadescriptions as $metad) {
			$metadcontent = \MongoDB\Entity::where("_id", $metad["_id"])->first();
			$proc = $metadcontent["preprocessed"];
			if (isset($metadcontent["content"]["features"])) {
				$proc["automatedEntities"] = true;
				$metadcontent->preprocessed = $proc;
				$metadcontent->update();
			}
		}
*/	
    /*$id=  'entity/text/medical/relex-structured-sentence/2203';
   $avg_clarity = \MongoDB\Entity::where('metrics.units.withoutSpam.'.$id, 'exists', 'true')->avg('metrics.units.withoutSpam.'.$id.'.avg.max_relation_Cos');
   dd($avg_clarity);*/
   /*   set_time_limit(60000);
       //dd(array_flatten(\MongoDB\Entity::where('documentType','workerunit')->where('unit_id', "entity/text/medical/twrex-structured-sentence/1128")->distinct('job_id')->get()->toArray()));
       $units = \MongoDB\Entity::where('tags', 'unit')->get(['_id'])->toArray();
       // $units = \MongoDB\Entity::where('tags', 'unit')->where('_id',"entity/text/medical/twrex-structured-sentence/1128")->get(['_id'])->toArray();
       foreach($units as $unit)
       {
           $distinctJob = count(array_flatten(\MongoDB\Entity::where('documentType','workerunit')->where('unit_id', $unit['_id'])->distinct('type')->get()->toArray()));

           $mongoUnit = \MongoDB\Entity::where('_id',$unit['_id'])->first();
           $cache = $mongoUnit->cache;
           $cache['jobs']['distinct'] = $distinctJob;
           $mongoUnit->cache = $cache;


           $mongoUnit->update();


           $mongoUnit->update();

       }

       return "done";*/


    /*$template = 'entity/text/medical/FactSpan/Factor_Span/0';
    $jobs = array('entity/text/medical/job/5');//,'entity/text/medical/job/5', 'entity/text/medical/job/4', 'entity/text/medical/job/3'*/
    /*$template = 'entity/text/medical/RelDir/Relation_Direction/0';
    $jobs = array('entity/text/medical/job/13'); //,'entity/text/medical/job/11', 'entity/text/medical/job/12', 'entity/text/medical/job/13', 'entity/text/medical/job/14'*/
    /*$template = 'entity/text/medical/RelExt/Relation_Extraction/0';
    $jobs = array('entity/text/medical/job/10')*/;//,'entity/text/medical/job/6', 'entity/text/medical/job/7', 'entity/text/medical/job/8', 'entity/text/medical/job/9', 'entity/text/medical/job/10'*/
    /*foreach ($jobs as $job) {
        $j = \MongoDB\Entity::where('_id',$job)->first();
        exec('/usr/bin/python2.7 ' . base_path()  . '/app/lib/generateMetrics.py \''.$job.'\' \''.$template.'\'', $output, $error);

        $response = json_decode($output[0],true);
        $j->metrics = $response['metrics'];
        $r = $j->results;
        $r['withoutSpam'] = $response['results']['withoutSpam'];
        $j->results = $r;
        $j->save();
    }

*/

    return Redirect::to('home');
});

Route::get('/urlsurls', function()
{
	echo '-------- paintings -------' . PHP_EOL;
	$results = \MongoDB\Entity::whereIn('documentType', ['painting'])->get(['content.url']);
	$results2 = \MongoDB\Entity::whereIn('documentType', ['drawing'])->get(['content.url']);

	foreach($results as $result)
	{	

		echo $result['content']['url'] . ' ';
		echo $result['_id'] . PHP_EOL;
	}
	echo PHP_EOL . PHP_EOL;
	echo '-------- drawings-------' . PHP_EOL;
	foreach($results2 as $result)
	{
		echo $result['content']['url'] . PHP_EOL;
	    echo $result['_id'] . PHP_EOL;
	}
    echo PHP_EOL . PHP_EOL . "[";
	$results = \MongoDB\Entity::whereIn('documentType', ['painting','drawing'])->get();
	foreach($results as $result)
	{	
		echo $result . ",". PHP_EOL;

	}
    echo "]";

	exit;

    return Redirect::to('home');
});

// define routes
Route::get('home', 'PagesController@index');
Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');
Route::controller('api/analytics', '\Api\analytics\apiController');

Route::get('login', 'UserController@login');
Route::get('register', 'UserController@register');
Route::get('logout', 'UserController@logout');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');
