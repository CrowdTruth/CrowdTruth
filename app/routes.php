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
	Route::controller('workers', 'WorkersController');
    Route::controller('analyze','AnalyticsController');
    Route::controller('onlinesource', 'OnlineSourceController');
	
});

Route::get('/', function()
{

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
Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');
Route::controller('api/analytics', '\Api\analytics\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');

