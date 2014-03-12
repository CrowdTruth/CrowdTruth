<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{
	Route::get('/', function()
	{

		// $results = \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->max('created_at');

		// dd($results);

        // $result = \MongoDB\Entity::where('format', 'text')->where('domain', 'medical')->where("documentType", 'twrex-structured-sentence')->orderBy('natural', 'asc')->get(array("_id"));

        // return $result;
		// echo "<pre>";

		// User::whereIn('age', array(16, 18, 20))->get();


		// $entities = \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')
		// ->where('content.properties.sentenceWordCount', '<', 20)
		// ->get(array('_id', 'user_id'));

		// foreach($entities as $entity){
		// 	print_r($entity->wasAttributedToUserAgent->getAttributes());
		// }

		// exit;

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
	Route::get('postprocess/createdBy/{term?}', 'PostProcessController@createdBy');
	Route::get('postprocess/sort/{method?}/{sort?}', 'PostProcessController@sortModel');
	Route::controller('postprocess', 'PostProcessController');
	
});

Route::any('cfwebhook.php', function(){
	$cfwebhook = new crowdwatson\CFWebhook();
	$cfwebhook->getSignal();
});
Route::resource('api/v1/', '\Api\v1\apiController', array('only' => array('index', 'show')));
Route::controller('api/v2', '\Api\v2\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));


Route::controller('user', 'UserController');