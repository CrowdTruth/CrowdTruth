<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{
	Route::get('/', function()
	{
		// $units = \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->where('activity_id', 'like', '%twrexstructurer%')->get();

		// // $units = \MongoDB\Entity::where('content.sentence.text', 'like', '%antigen permits%')->get();

		// $twrexStructurer = \App::make('\preprocess\TwrexStructurer');


		// foreach($units as $unit)
		// {
		// 	$content = $unit->toArray()['content'];
		// 	$properties = $content['properties'];

		// 	$properties['relationOutsideTerms'] = $twrexStructurer->relationOutsideTerms($content);
		// 	$properties['relationBetweenTerms'] = $twrexStructurer->relationBetweenTerms($content);
		// 	$properties['semicolonBetweenTerms'] = $twrexStructurer->semicolonBetweenTerms($content);
		// 	$properties['commaSeparatedTerms'] = $twrexStructurer->commaSeparatedTerms($content);

		// 	$content['properties'] = $properties;

		// 	$unit->content = $content;

		// 	$unit->update();			
		// }

		// return $units;

		// foreach($units as $unit){
		// 	$unit->user_id = "khamkham";
		// 	$unit->update();
		// }

		// return \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->where('user_id', 'khamkham')->count();


        //$result = \MongoDB\Entity::where('documentType', 'job')->whereIn('softwareAgent_id', ['cf', 'amt'])->count();
        //return $result;
        
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
        Route::controller('analyze','AnalyticsController');
	


});

Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');
