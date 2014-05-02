<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{

	// \DB::collection('entities')->whereIn('documentType', ['twrex-structured-sentence'])->limit(1)->push('tags', ['unit']);

	// return \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->limit(1)->get();

	// \DB::collection('entities')->whereIn('documentType', ['twrex', 'twrex-structured-sentence', 'fullvideo'])->push('tags', ['unit']);



	// set_time_limit(1200);

 //    \Session::flash('rawArray', 1);
 //    $db = \DB::getMongoDB();
 //    $db = $db->temp;
    
 //    $result = \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->with('wasAttributedToUserAgent', 'wasGeneratedBy')->get()->toArray();

 //    array_push($result, [
 //        "_id" => "unitsCache",
 //        "created_at" => new \MongoDate(time()),
 //    ]);

 //    try {
 //        $db->batchInsert(
 //            $result,
 //            array('continueOnError' => true)
 //        );             
 //    } catch (Exception $e) {
 //    // ContinueOnError will still throw an exception on duplication, even though it continues, so we just move on.
 //    }

 //    \Session::forget('rawArray');

 //    dd('done');

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

	// \MongoDB\Temp::whereIn('_id', ['mainSearchFilters', 'jobCache'])->forceDelete();

	// dd('done');

	\DB::collection('entities')->unset('tags');
	\DB::collection('entities')->whereIn('documentType', ['twrex-structured-sentence', 'twrex'])->push('tags', 'unit', true);

	// return \MongoDB\Entity::where('documentType', 'twrex-structured-sentence')->limit(1)->get();

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