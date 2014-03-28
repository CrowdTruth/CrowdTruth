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
	// Route::controller('api', 'ApiController');
	Route::controller('preprocess/twrex', 'preprocess\TwrexController');
	Route::controller('preprocess/csvresult', 'preprocess\CSVresultController');
	Route::controller('preprocess', 'PreprocessController');
	Route::controller('selection', 'SelectionController');
	Route::controller('process', 'ProcessController');
	Route::controller('jobs', 'JobsController');
	Route::controller('workers', 'WorkersController');

	Route::controller('temp', 'TempController');
	
});

Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');