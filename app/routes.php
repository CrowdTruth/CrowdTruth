<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{
	Route::controller('media/preprocess/fullvideo', 'preprocess\FullvideoController');
	Route::controller('media/preprocess/relex', 'preprocess\RelexController');
	Route::controller('media/preprocess/CSVresultController', 'preprocess\CSVresultController');
	Route::controller('media', 'MediaController');

	Route::controller('jobs', 'JobsController');
	Route::controller('jobs2', 'JobsController2');
	Route::controller('workers', 'WorkersController');
    Route::controller('analyze','AnalyticsController');
    Route::controller('onlinesource', 'OnlineSourceController');
	
});

Route::get('/', function()
{
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

Route::get('home', 'PagesController@index');
Route::get('info', 'PagesController@info');
Route::get('papers', 'PagesController@papers');
Route::get('team', 'PagesController@team');
Route::get('api/examples', 'PagesController@apiExamples');
Route::get('templates/examples', 'PagesController@templatesExamples');
Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');
Route::controller('api/analytics', '\Api\analytics\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');

