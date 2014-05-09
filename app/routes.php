<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{


	Route::controller('media/preprocess/fullvideo', 'preprocess\FullvideoController');
	Route::controller('media/preprocess/twrex', 'preprocess\TwrexController');
	Route::controller('media/preprocess/CSVresultController', 'preprocess\CSVresultController');
	Route::controller('media', 'MediaController');

	Route::controller('selection', 'SelectionController');
	Route::controller('process', 'ProcessController');
	Route::controller('jobs', 'JobsController');
	Route::controller('workers', 'WorkersController');
    Route::controller('analyze','AnalyticsController');
    Route::controller('onlinesource', 'OnlineSourceController');
	


});

Route::get('/', function()
{

	// $parents = \MongoDB\Entity::where('documentType', 'painting')->get()->toArray();

	// $children = \MongoDB\Entity::whereIn('parents', [$parents[0]['_id']])->get(['content.features']);

	// $featuresdirty = array();
	// foreach($children as $child){
	// 	array_push($featuresdirty, $child['content']['features']);
	// }
	
	// $features = array();
	// foreach($featuresdirty as $key=>$value){
	// 	dd($value);
	// 	array_push($features, $value);
	// }
	// return $features;

	// $parents[0]['content']['features'] = $features;

	// return $parents[0];

    return Redirect::to('home');
});

Route::get('home', 'PagesController@index');
Route::get('info', 'PagesController@info');
Route::get('papers', 'PagesController@papers');

Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');
Route::controller('api/analytics', '\Api\analytics\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');

