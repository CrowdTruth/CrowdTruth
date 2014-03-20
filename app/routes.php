<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{
	Route::get('/', function()
	{
	    return Redirect::to('home');
	});

	Route::get('home', 'PagesController@index');
	Route::get('files/upload', array('as' => 'fileuploader', 'uses' => 'FilesController@getUpload'));
	Route::controller('files', 'FilesController');
	Route::controller('preprocess/twrex', 'preprocess\TwrexController');
	Route::controller('preprocess', 'PreprocessController');
	Route::controller('selection', 'SelectionController');
	// Route::controller('api', 'apiController');
	Route::controller('process', 'ProcessController');
	Route::controller('jobs', 'JobsController');
	
});


Route::any('cfwebhook.php', function(){
	$cfwebhook = new crowdwatson\CFWebhook();
	$cfwebhook->getSignal();
});

Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::resource('api/v1/', '\Api\v1\apiController', array('only' => array('index', 'show')));
Route::controller('api/v2', '\Api\v2\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));


Route::controller('user', 'UserController');