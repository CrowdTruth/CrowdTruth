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
	Route::controller('files', 'FilesController');
	Route::controller('preprocess/chang', 'preprocess\ChangController');
	Route::controller('preprocess', 'PreprocessController');
	Route::controller('selection', 'SelectionController');
	Route::controller('api', 'apiController');
	Route::controller('process', 'ProcessController');

	Route::get('postprocess/sort/{method?}', 'PostProcessController@sortModel');
	Route::controller('postprocess', 'PostProcessController');
	// Route::get('whatever/asdf/{method?}', function($method) { return $method; });


    	
	// });

});



Route::controller('user', 'UserController');