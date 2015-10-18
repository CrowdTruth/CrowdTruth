<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!Auth::check()){
		return Redirect::guest('/login');
	} 
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

use \Security\PermissionHandler as PermissionHandler;
use \Security\Permissions as Permissions;

/**
 * Require routes to have a particular Permissions for a given projectname.
 * 
 * NOTES: 
 * 
 * $projectname needs to be passed as a route parameter:
 * 
 * 		'project/{projectname}/invitations'
 * 
 * Alternatively it should be passed in as a GET/POST parameter 
 * 
 * $permission needs to be passed in as a filter parameter
 * 
 * 		'before' => 'permission:'.Permissions::PROJECT_ADMIN
 */
Route::filter('permission', function($route, $request, $permission) {
	$thisUser = Auth::user();
	$groupName = Route::input('projectname');	// Passed in as route parameter
	if(is_null($groupName)) {
		$groupName = Input::get('projectname');	// Passed in as parameter parameter
	}
	// Check permissions
	$hasPermission = PermissionHandler::checkProject($thisUser, $groupName, $permission);
	if(!$hasPermission) {
		return Redirect::back()
			->with('flashError', 'You do not have permission to perform selected action');
	}
});

/**
 * Require routes to have admin permissions.
 */
Route::filter('adminPermission', function() {
	$thisUser = Auth::user();
	// Check permissions
	$isAdmin = PermissionHandler::checkAdmin($thisUser, Permissions::ALLOW_ALL);
	if(!$isAdmin) {
		return Redirect::back()
			->with('flashError', 'You do not have permission to perform selected action');
	}
});
