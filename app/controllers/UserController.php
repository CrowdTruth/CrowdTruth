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
		
		$thisUser = Auth::user();
		
		// List of groups this user can invite people to
		$groupsManaged = [];
		// For each group logged in user belongs to
		foreach(GroupHandler::getUserGroups($thisUser) as $group) {
			// Check if user has admin permission..
			if(PermissionHandler::checkGroup($thisUser, $group['name'], Permissions::GROUP_ADMIN)) {
				array_push($groupsManaged, $group['name']);
			}
		}
		
		$userGroupInfo = [];
		foreach ($userlist as $user) {
			// List of groups $user belongs to
			$usergroups = GroupHandler::getUserGroups($user);
			$usergroupnames = array_column($usergroups, 'name');
			
			// List of groups logged in user can invite $user to join
			// and that $user is not already a member of.
			$inviteGroups = array_diff($groupsManaged, $usergroupnames);
			
			$belongGroups = [];
			foreach ($usergroups as $group) {
				// Can logged user assign roles for this group ?
				$canAssign = PermissionHandler::checkGroup($thisUser, $group['name'], Permissions::GROUP_ADMIN);
				// Can logged user view info for this group ?
				$canView   = PermissionHandler::checkGroup($thisUser, $group['name'], Permissions::GROUP_READ);
				
				$group['canview'] = $canView;
				$group['assignrole'] = $canAssign;
				array_push($belongGroups, $group);
			}
			
			$userGroupInfo[$user['_id']] = [
				'groups' => $belongGroups,
				'tojoin' => $inviteGroups
			];
		}
		
		return View::make('user.userlist')
			->with('userlist', $userlist)
			->with('viewProfiles', $viewProfiles)
			->with('usergroups', $userGroupInfo);
	}

	/**
	 * Perform actions triggered from the user list page (/users). Actions performed:
	 * addGroup    - Adds a given user to a given CT-group
	 * assignRole  - Assigns the given role to a given user on the given CT-group.
	 * removeGroup - Removes the given user from the given CT-group.
	 * 
	 * Browser is redirected to calling page (hopefully /users), with a flashError or 
	 * flashSuccess message indicating the result.
	 */
	public function groupActions() {
		$thisUser = Auth::user();
		
		$targetUserName = Input::get('usedId');
		$groupName = Input::get('group');
		$targetUser = UserAgent::find($targetUserName);
		
		if(!$targetUser) {
			return Redirect::back()
			->with('flashError', 'User does not exist: '.$targetUserName);
		}
		
		$isAdmin = PermissionHandler::checkGroup($thisUser, $groupName, Permissions::GROUP_ADMIN);
		if(!$isAdmin) {
			return Redirect::back()
			->with('flashError', 'You do not have permission to perform selected action');
		}
		
		$action = Input::get('action');
		if($action=='addGroup') {
			$userRole = GroupHandler::grantUser($targetUser, $groupName, Roles::GROUP_GUEST);
			
			return Redirect::back()
				->with('flashSuccess', 'User '.$targetUserName.' added to group '.$groupName);
		} elseif($action=='assignRole') {
			$roleName = Input::get('role');
			$role = Roles::getRoleByName($roleName);
			$userRole = GroupHandler::grantUser($targetUser, $groupName, $role);
			
			return Redirect::back()
				->with('flashSuccess', 'User '.$targetUserName.' assigned role '.$roleName.' on group '.$groupName);
		} elseif($action=='removeGroup') {
			GroupHandler::revokeUser($targetUser, $groupName);
			
			return Redirect::back()
				->with('flashSuccess', 'User '.$targetUserName.' removed from group '.$groupName);
		} else {
			return Redirect::back()
				->with('flashError', 'Invalid action selected: '.$action);
		}
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
		foreach(Roles::$GROUP_ROLE_NAMES as $role) {
			$sentryGroups[$role] = Sentry::findGroupByName($groupname.':'.$role);
		}
		
		$groupUsers = [];
		foreach(Roles::$GROUP_ROLE_NAMES as $role) {
			$groupUsers[$role] = $sentryGroups[$role]['user_agent_ids'];
		}
		
		$groupInviteCodes = [];
		foreach(Roles::$GROUP_ROLE_NAMES as $role) {
			$groupInviteCodes[$role] = $sentryGroups[$role]['invite_code'];
		}
		
		$canEditGroup = PermissionHandler::checkGroup(Auth::user(), $groupname, Permissions::GROUP_ADMIN);
		
		return View::make('user.group')
			->with('groupName', $groupname)
			->with('groupUsers', $groupUsers)
			->with('inviteCodes', $groupInviteCodes)
			->with('canEditGroup', $canEditGroup);
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