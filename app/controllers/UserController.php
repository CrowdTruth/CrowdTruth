<?php

use \Auth as Auth;
use \Jenssegers\Mongodb\Sentry\Group as SentryGroup;
use \Security\ProjectHandler as ProjectHandler;
use \Security\PermissionHandler as PermissionHandler;
use \Security\Permissions as Permissions;
use \Security\Roles as Roles;

/**
 * This controller performs all actions for handling Users.
 */
class UserController extends BaseController {
	/**
	 * Create controller. Apply Cross-site request and authentication filters
	 */
	public function __construct() {
		$this->beforeFilter('csrf', [ 'on'=>'post' ]);
		$this->beforeFilter('auth', [ 'except' => [ 'login', 'postLogin', 'register', 'postRegister' ] ]);
	}

	/**
	 * Redirect to login page.
	 */
	public function getIndex() {
		return Redirect::to('login');
	}
	
	/**
	 * Redirect to login page.
	 */
	public function getShow() {
		return Redirect::to('login');
	}

	/**
	 * Display login page.
	 */
	public function login() {
		return View::make('users.login');
	}

	/**
	 * Process user login.
	 */
	public function postLogin(){
		$userdata = array(
			'username' => Input::get('username_or_email'),
			'email' => Input::get('username_or_email'),
			'password' => Input::get('password'),
		);

		if($user = UserAgent::where('_id', '=', strtolower($userdata['username']))
							->orWhere('email', '=', strtolower($userdata['email']))->first()) {
			if(Auth::attempt(array('email' => $user['email'], 'password' => $userdata['password']))) {
				return Redirect::intended('/');
			}
		}
		Session::flash('flashError', 'Invalid credentials');
		return Redirect::back();
	}

	/**
	 * Display user registration page.
	 */
	public function register(){
		return View::make('users.register');
	}

	/**
	 * Perform user registration.
	 */
	public function postRegister() {
		$userdata = [
				'_id' => strtolower(Input::get('username')),
				'firstname' => ucfirst(strtolower(Input::get('firstname'))),
				'lastname' => ucfirst(strtolower(Input::get('lastname'))),
				'email' => strtolower(Input::get('email')),
				'password' => Input::get('password'),
				'confirm_password' => Input::get('confirm_password'),
		];
	
		$rules = [
				'_id' => 'required|min:3|unique:useragents',
				'firstname' => 'required|min:3',
				'lastname' => 'required|min:1',
				'email' => 'required|email|unique:useragents',
				'password' => 'required|min:5',
				'confirm_password' => 'required|same:password'
		];
	
		$validation = Validator::make($userdata, $rules);
	
		if($validation->fails()){
			$msg = '<ul>';
			foreach ($validation->messages()->all() as $message)
				$msg .= "<li>$message</li>";
	
			Session::flash('flashError', "$msg</ul>");
			return Redirect::back()->withInput(Input::except('password', 'confirm_password'));
		}
		unset($userdata['confirm_password']);
		$user = Sentry::register($userdata);
		Auth::login($user);
	
		// Assign to groups ?
		$iCode = Input::get('invitation');
		$sentryGroup = SentryGroup::where('invite_code', '=', $iCode)->first();
		if(!is_null($sentryGroup)) {
			$user->addGroup($sentryGroup);
			Session::flash('flashSuccess', 'You have joined group: <b>'.$sentryGroup['name'].'</b>');
		}
	
		return Redirect::to('/');
	}

	/**
	 * Perform user logout.
	 */
	public function logout(){
		Cart::destroy();
		Auth::logout();
		return Redirect::to('');
	}

	/**
	 * Display user profile
	 */
	public function getProfile(UserAgent $user) {
		$projects = ProjectHandler::getUserProjects($user);
		return View::make('users.profile')
			->with('user', $user)
			->with('projects', $projects);
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
		foreach(ProjectHandler::getUserProjects($thisUser) as $group) {
			// Check if user has admin permission..
			if(PermissionHandler::checkProject($thisUser, $group['name'], Permissions::PROJECT_ADMIN)) {
				array_push($groupsManaged, $group['name']);
			}
		}

		$userGroupInfo = [];
		foreach ($userlist as $user) {
			// List of groups $user belongs to
			$usergroups = ProjectHandler::getUserProjects($user);
			$usergroupnames = array_column($usergroups, 'name');
			
			// List of groups logged in user can invite $user to join
			// and that $user is not already a member of.
			$inviteGroups = array_diff($groupsManaged, $usergroupnames);

			$belongGroups = [];
			foreach ($usergroups as $group) {
				// Can logged user assign roles for this group ?
				$canAssign = PermissionHandler::checkProject($thisUser, $group['name'], Permissions::PROJECT_ADMIN);
				// Can logged user view info for this group ?
				$canView   = PermissionHandler::checkProject($thisUser, $group['name'], Permissions::PROJECT_READ);
				
				// User cannot change his own permissions
				if($user['_id']==$thisUser['_id']) {
					$canAssign = false;
				}

				$group['canview'] = $canView;
				$group['assignrole'] = $canAssign;
				array_push($belongGroups, $group);
			}
			
			$userGroupInfo[$user['_id']] = [
				'groups' => $belongGroups,
				'tojoin' => $inviteGroups
			];
		}

		return View::make('users.list')
			->with('userlist', $userlist)
			->with('viewProfiles', $viewProfiles)
			->with('usergroups', $userGroupInfo);
	}

	/**
	 * Display user settings
	 */
	public function getSettings(UserAgent $user) {
		$groups = ProjectHandler::getUserProjects($user);
		return View::make('users.settings')
			->with('user', $user)
			->with('groups',$groups);
	}

	/**
	 * Change user profile settings. Three actions can be performed:
	 * 	- Change User information (name and email)
	 *  - Change password
	 *  - Generate API key -- used for external API data calls.
	 */
	public function postSettings(UserAgent $user) {
		$action = Input::get('action');
		if($action=='userinfo') {			// Change user details
			$user['firstname'] = Input::get('firstname');
			$user['lastname'] = Input::get('lastname');
			$user['email'] = Input::get('email');
			$user->save();
			Session::flash('flashSuccess', 'Profile information succesfully changed');
		} elseif ($action=='password') {	// Change user password
			$currPass = Input::get('oldpassword');
			if( $user->checkPassword( $currPass )) {
				$newPass1 = Input::get('newpassword1');
				$newPass2 = Input::get('newpassword2');
				if($newPass1==$newPass2) {
					$user->password = $newPass1;
					$user->save();
					Session::flash('flashSuccess', 'Password succesfully changed');
				} else {
					return Redirect::back()
						->with('flashError', 'New passwords do not match');
				}
			} else {
				return Redirect::back()
					->with('flashError', 'Incorrect password');
			}
		} elseif ($action=='apikey') {		// Generate new API key
			$user['api_key'] = hash('sha256',Str::random(10),false);
			$user->save();
			Session::flash('flashSuccess', 'New API key generated');
		}

		return Redirect::back();
	}

	/**
	 * Display current user activity
	 */
	public function getActivity(UserAgent $user) {
		$activities = Activity::getActivitiesForUser($user['_id']);
		return View::make('users.activity')
			->with('activities', $activities)
			->with('user',$user);
	}
}
