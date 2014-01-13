<?php

Route::group(array('before' => 'auth'), function()
{

	Route::get('/', function()
	{
	    return Redirect::to('home');
	});

	Route::get('home', 'PagesController@index');
	Route::controller('files', 'FilesController');
	Route::controller('preprocess/chang', 'preprocess\ChangController');
	Route::controller('preprocess', 'PreprocessController');
	Route::controller('selection', 'SelectionController');
	Route::controller('process', 'ProcessController');
	Route::get('resource/{collection}/{category}/{document}', 'ResourceController@getDocument');
	Route::get('resource/{collection}/{category}', 'ResourceController@getCategory');
	Route::get('resource/{collection}', 'ResourceController@getCollection');
	Route::get('resource', 'ResourceController@index');

	//Route::controller('resource', 'ResourceController');

});


Route::controller('user', 'UserController');