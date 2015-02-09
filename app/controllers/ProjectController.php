<?php

use \Auth as Auth;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\UserAgent as UserAgent;
use \MongoDB\Security\ProjectHandler as ProjectHandler;
use \MongoDB\Security\PermissionHandler as PermissionHandler;
use \MongoDB\Security\Permissions as Permissions;
use \MongoDB\Security\Roles as Roles;

/**
 * Controll actions related to Group management.
 */
class ProjectController extends BaseController {

	public function __construct() {
		$this->beforeFilter('csrf', array('on'=>'post'));
	}

	/**
	 * Display list of all groups.
	 */
	public function createProject(){
		if(!Auth::check())
			return Redirect::to('/');

		return View::make('projects.create');
	}
	
	/**
	 * Display list of all groups.
	 */
	public function getGroupList() {
		$thisUser = Auth::user();

		$groups = ProjectHandler::listGroups();
		$groupInfo = [];
		foreach ($groups as $group) {
			$canView = PermissionHandler::checkGroup($thisUser, $group, Permissions::PROJECT_READ);
			
			array_push($groupInfo, [
				'name' => $group,
				'canview' => $canView
			]);
		}
		$isAdmin = PermissionHandler::checkAdmin($thisUser, Permissions::ALLOW_ALL);
		return View::make('projects.list')
			->with('groupInfo', $groupInfo)
			->with('isAdmin', $isAdmin);
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
	public function groupActions($groupName) {
		$targetUserName = Input::get('usedId');
		$targetUser = UserAgent::find($targetUserName);
		
		if(!$targetUser) {
			return Redirect::back()
			->with('flashError', 'User does not exist: '.$targetUserName);
		}
		
		$action = Input::get('action');
		if($action=='addGroup') {
			$userRole = ProjectHandler::grantUser($targetUser, $groupName, Roles::PROJECT_GUEST);
			
			return Redirect::back()
				->with('flashSuccess', 'User '.$targetUserName.' added to group '.$groupName);
		} elseif($action=='assignRole') {
			$roleName = Input::get('role');
			$role = Roles::getRoleByName($roleName);
			$userRole = ProjectHandler::grantUser($targetUser, $groupName, $role);
			
			return Redirect::back()
				->with('flashSuccess', 'User '.$targetUserName.' assigned role '.$roleName.' on group '.$groupName);
		} elseif($action=='removeGroup') {
			ProjectHandler::revokeUser($targetUser, $groupName);
			
			return Redirect::back()
				->with('flashSuccess', 'User '.$targetUserName.' removed from group '.$groupName);
		} else {
			return Redirect::back()
				->with('flashError', 'Invalid action selected: '.$action);
		}
	}

	/**
	 * Display view with details for a specified group.
	 * 
	 * @param $groupname Name of the group to be displayed.
	 */
	public function getGroupDetails($groupname) {
		$sentryGroups = [];
		foreach(Roles::$PROJECT_ROLE_NAMES as $role) {
			$sentryGroups[$role] = Sentry::findGroupByName($groupname.':'.$role);
		}
		
		$groupUsers = [];
		foreach(Roles::$PROJECT_ROLE_NAMES as $role) {
			// List userts with $role in this group -- make [] when none
			$users = $sentryGroups[$role]['user_agent_ids'];
			$groupUsers[$role] = is_null($users)?[]:$users;
		}
		
		$groupInviteCodes = [];
		foreach(Roles::$PROJECT_ROLE_NAMES as $role) {
			$groupInviteCodes[$role] = $sentryGroups[$role]['invite_code'];
		}
		
		$canEditGroup = PermissionHandler::checkGroup(Auth::user(), $groupname, Permissions::PROJECT_ADMIN);
		$credentials = ProjectHandler::getCredentials($groupname);
		
		return View::make('projects.profile')
			->with('groupName', $groupname)
			->with('groupUsers', $groupUsers)
			->with('inviteCodes', $groupInviteCodes)
			->with('canEditGroup', $canEditGroup)
			->with('credentials', $credentials);
	}

	/**
	 * Process POST requests for changing group invitation codes on a specified group.
	 * Permissions::PROJECT_ADMIN on the specified group is required to perform this action.
	 */
	public function updateInviteCodes($groupName) {
		$codes = [
			Roles::PROJECT_ADMIN => Input::get('adminsICode'),
			Roles::PROJECT_MEMBER => Input::get('membersICode'),
			Roles::PROJECT_GUEST => Input::get('guestsICode')
		];
		
		foreach(Roles::$PROJECT_ROLES as $role) {
			$sentryGroup = Sentry::findGroupByName(str_replace('#', $groupName, $role));
			$sentryGroup['invite_code'] = $codes[$role];
			$sentryGroup->save();
		}
		
		return Redirect::back()
			->with('flashSuccess', 'Invitation code successfully updated.');
	}
	
	/**
	 * Perform the POST action for changing account credentials for a given group.
	 * Permissions::PROJECT_ADMIN on the specified group is required to perform this action.
	 */
	public function updateAccountCredentials($groupName) {
		$cfUser = Input::get('cfUsername');
		$cfPass = Input::get('cfPassword');
		
		$newValues = [
			ProjectHandler::CF_USER => $cfUser,
			ProjectHandler::CF_PASS => $cfPass
		];
		
		ProjectHandler::changeCredentials($groupName, $newValues);
		
		return Redirect::back()
			->with('flashSuccess', 'Invitation code successfully updated.');
	}
	
	/**
	 * Handle POST requests to create a new group. 
	 */
	public function createGroup() {
		$groupName = Input::get('addGrp');
		try{
			ProjectHandler::createGroup($groupName);
			return Redirect::back()
				->with('flashSuccess', 'Group <b>'.$groupName.'</b> succesfully created!');			
		} catch(\Cartalyst\Sentry\Groups\GroupExistsException $e) {
			return Redirect::back()
			->with('flashError', 'Group <b>'.$groupName.'</b> already exists!');
		}
	}
}
