<?php

use \Auth as Auth;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\UserAgent as UserAgent;
use \MongoDB\Security\GroupHandler as GroupHandler;
use \MongoDB\Security\PermissionHandler as PermissionHandler;
use \MongoDB\Security\Permissions as Permissions;
use \MongoDB\Security\Roles as Roles;

class UserController extends BaseController {

	public function __construct() {
		$this->beforeFilter('csrf', array('on'=>'post'));
	}

	public function getIndex(){
		return Redirect::to('login');
	}
	
	public function getShow() {
		return Redirect::to('login');
	}

	public function login(){
		if(Auth::check())
			return Redirect::to('/');

		return View::make('user/login');
	}

	public function register(){
		if(Auth::check())
			return Redirect::to('/');
					
		return View::make('user/register');
	}

	public function logout(){
		Cart::destroy();
		Auth::logout();
		return Redirect::to('');
	}

	/**
	 * Display user profile
	 */
	public function getProfile(UserAgent $user) {
		// redirect if user is not logged in
		if(!Auth::check()) {
			return Redirect::to('/');
		}
		
		return View::make('user.profile')
			->with('user', $user);
	}

	/**
	 * Display list of all users
	 */
	public function getUserlist() {
		$userlist = UserAgent::getUserlist();
		
		// Logged in user can view other user's profiles
		$viewProfiles = PermissionHandler::checkAdmin(Auth::user(), Permissions::ALLOW_ALL);
		
		$myGroup = 'crowdtruth';
		
		return View::make('user.userlist')
			->with('userlist', $userlist)
			->with('viewProfiles', $viewProfiles)
			->with('myGroup', $myGroup);
	}

	/**
	 * Change user settings
	 */
	public function getSettings(UserAgent $user) {
		// redirect if user is not logged in
		if(!Auth::check()) {
			return Redirect::to('/');
		}
		
		$groups = GroupHandler::getUserGroups($user);
		return View::make('user.settings')
			->with('user', $user)
			->with('groups',$groups);
	}

	/**
	 * Display current user activity
	 */
	public function getActivity(UserAgent $user) {
	
		// redirect if user is not logged in
		if(!Auth::check()) {
			return Redirect::to('/');
		}

		$activities = Activity::getActivitiesForUser($user['_id']);
		return View::make('user.activity')
			->with('activities', $activities)
			->with('user',$user);
	}
	
	public function getGroupDetails($groupname) {
		$sentryGroups = [];
		foreach(Roles::$GROUP_ROLES as $role) {
			$sentryGroups[$role] = Sentry::findGroupByName($groupname.':'.$role);
		}
		
		$groupUsers = [];
		foreach(Roles::$GROUP_ROLES as $role) {
			$groupUsers[$role] = $sentryGroups[$role]['user_agent_ids'];
		}
		
		$groupInviteCodes = [];
		foreach(Roles::$GROUP_ROLES as $role) {
			$groupInviteCodes[$role] = $sentryGroups[$role]['invite_code'];
		}
		
		$canEditGroup = PermissionHandler::checkGroup(Auth::user(), $groupname, Permissions::GROUP_ADMIN);
		
		return View::make('user.group')
			->with('groupName', $groupname)
			->with('groupUsers', $groupUsers)
			->with('inviteCodes', $groupInviteCodes)
			->with('canEditGroup', $canEditGroup);
	}
	
	// Ajax call...
	public function addUserToGroup() {
		$userName = Input::get('newUserName');
		$groupName = Input::get('groupName');
		
		$user = UserAgent::find($userName);
		if($user) {
			$userRole = GroupHandler::grantUser($user, $groupName, Roles::GROUP_GUEST);
			return [
				'status' => 'ok',
				'user' => $userName,
				'role' => 'guest'
			];			
		} else {
			return [
				'status' => 'error',
				'message' => 'User not found',
			];
		}
	}

	public function postLogin(){
		$userdata = array(
			'username' => Input::get('username_or_email'),
			'email' => Input::get('username_or_email'),
			'password' => Input::get('password'),
		);

		if($user = UserAgent::where('_id', '=', strtolower($userdata['username']))->orWhere('email', '=', strtolower($userdata['email']))->first())
			if(Auth::attempt(array('email' => $user['email'], 'password' => $userdata['password'])))
				return Redirect::intended('/');

		Session::flash('flashError', 'Invalid credentials');
		return Redirect::back();
	}

	public function postRegister(){
		$role = 'user';
		// Check if demo account
		if (Hash::check(Input::get('invitation'), Config::get('config.demoInvitationCode'))) {
			$role = 'demo';
		// Check if normal account	
		} elseif (!Hash::check(Input::get('invitation'), Config::get('config.invitationCode')) && Config::get('config.invitationCode') != ''){
			Session::flash('flashError', 'Wrong invite code : )');
			return Redirect::back()
				->withInput(Input::except('password', 'confirm_password'));
		}

		$userdata = array(
			'_id' => strtolower(Input::get('username')),
			'firstname' => ucfirst(strtolower(Input::get('firstname'))),
			'lastname' => ucfirst(strtolower(Input::get('lastname'))),
			'email' => strtolower(Input::get('email')),
			'password' => Input::get('password'),
			'confirm_password' => Input::get('confirm_password'),
			'role' => $role
		);

		$rules = array(
			'_id' => 'required|min:3|unique:useragents',
			'firstname' => 'required|min:3',
			'lastname' => 'required|min:1',
			'email' => 'required|email|unique:useragents',
			'password' => 'required|min:5',
			'confirm_password' => 'required|same:password'
		);

		$validation = Validator::make($userdata, $rules);

		if($validation->fails()){

			$msg = '<ul>';
			foreach ($validation->messages()->all() as $message)
				$msg .= "<li>$message</li>";

			Session::flash('flashError', "$msg</ul>");
			return Redirect::back()->withInput(Input::except('password', 'confirm_password'));
		}

		unset($userdata['confirm_password']);
		$userdata['password'] = Hash::make($userdata['password']);
		$user = new UserAgent($userdata); 

		try {
			$this->createCrowdWatsonUserAgent();
			$user->save();
		} catch (Exception $e) {
			return Redirect::back()->with('flashError', $e->getMessage())->withInput(Input::except('password', 'confirm_password'));
		}

		Auth::login($user);
		return Redirect::to('/');
	}

	public function createCrowdWatsonUserAgent(){
		if(!UserAgent::find('crowdwatson'))
		{
			$softwareAgent = new UserAgent;
			$softwareAgent->_id = "crowdwatson";
			$softwareAgent->firstname = "Crowd";
			$softwareAgent->lastname = "Watson";
			$softwareAgent->email = "crowdwatson@gmail.com";
			$softwareAgent->save();
		}
	}
}

?>