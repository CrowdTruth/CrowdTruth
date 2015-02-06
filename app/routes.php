<?php
use \MongoDB\Security\Permissions as Permissions;

Route::group(array('before' => 'auth'), function()
{
	Route::controller('media/preprocess/fullvideo', 'preprocess\FullvideoController');
	Route::controller('media/preprocess/relex', 'preprocess\RelexController');
	Route::controller('media/preprocess/text', 'preprocess\TextController');
	Route::controller('media/preprocess/CSVresultController', 'preprocess\CSVresultController');
	Route::controller('media/preprocess/metadatadescription', 'preprocess\MetadatadescriptionController');
	Route::controller('media', 'MediaController');

	Route::controller('jobs', 'JobsController');
	Route::controller('jobs2', 'JobsController2');
	Route::controller('workers', 'WorkersController');
	Route::controller('analyze','AnalyticsController');
	Route::controller('onlinesource', 'OnlineSourceController');
});

Route::get('/', function()
{
	Session::reflash();
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

// define routes
Route::get('home', 'PagesController@index');
Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');
Route::controller('api/analytics', '\Api\analytics\apiController');

Route::get('login', 'UserController@login');
Route::get('register', 'UserController@register');
Route::post('register', 'UserController@postRegister');
Route::get('logout', 'UserController@logout');
Route::get('users', 'UserController@getUserlist');
Route::get('user/{user}', 'UserController@getProfile');
Route::get('user/{user}/activity', 'UserController@getActivity');
Route::get('user/{user}/settings', 'UserController@getSettings');
Route::model('user', '\MongoDB\UserAgent');

Route::get('projects/', 'ProjectController@getGroupList');
Route::post('projects/create', [ 'before' => 'adminPermission', 'uses' => 'ProjectController@createGroup' ]);
Route::get('project/{projectname}', 'ProjectController@getGroupDetails');

Route::group([ 'before' => 'permission:'.Permissions::PROJECT_ADMIN ], function()
{
	Route::post('project/{projectname}/invitations', 'ProjectController@updateInviteCodes');
	Route::post('project/{projectname}/credentials', 'ProjectController@updateAccountCredentials');
	Route::get('project/{projectname}/actions', 'ProjectController@groupActions');
});

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');
