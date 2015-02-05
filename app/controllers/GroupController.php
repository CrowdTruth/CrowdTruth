<?php

use \Auth as Auth;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\UserAgent as UserAgent;
use \MongoDB\Security\GroupHandler as GroupHandler;
use \MongoDB\Security\PermissionHandler as PermissionHandler;
use \MongoDB\Security\Permissions as Permissions;
use \MongoDB\Security\Roles as Roles;

/**
 * Controll actions related to Group management.
 */
class GroupController extends BaseController {

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

		$groups = GroupHandler::listGroups();
		$groupInfo = [];
		foreach ($groups as $group) {
			$canView   = PermissionHandler::checkGroup($thisUser, $group, Permissions::GROUP_READ);
			
			array_push($groupInfo, [
				'name' => $group,
				'canview' => $canView
			]);
		}
		
		return View::make('projects.list')
			->with('groupInfo', $groupInfo);
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
	 * Display view with details for a specified group.
	 * 
	 * @param $groupname Name of the group to be displayed.
	 */
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
		
		$credentials = GroupHandler::getCredentials($groupname);
		
		return View::make('projects.profile')
			->with('groupName', $groupname)
			->with('groupUsers', $groupUsers)
			->with('inviteCodes', $groupInviteCodes)
			->with('canEditGroup', $canEditGroup)
			->with('credentials', $credentials);
	}

	/**
	 * Process POST requests for changing group invitation codes on a specified group.
	 * Permissions::GROUP_ADMIN on the specified group is required to perform this action.
	 */
	public function updateInviteCodes($groupName) {
		$thisUser = Auth::user();
		// Check permissions
		$isAdmin = PermissionHandler::checkGroup($thisUser, $groupName, Permissions::GROUP_ADMIN);
		if(!$isAdmin) {
			return Redirect::back()
				->with('flashError', 'You do not have permission to perform selected action');
		}
		
		$codes = [
			Roles::GROUP_ADMIN => Input::get('adminsICode'),
			Roles::GROUP_MEMBER => Input::get('membersICode'),
			Roles::GROUP_GUEST => Input::get('guestsICode')
		];
		
		foreach(Roles::$GROUP_ROLES as $role) {
			$sentryGroup = Sentry::findGroupByName(str_replace('#', $groupName, $role));
			$sentryGroup['invite_code'] = $codes[$role];
			$sentryGroup->save();
		}
		
		return Redirect::back()
			->with('flashSuccess', 'Invitation code successfully updated.');
	}
	
	/**
	 * Perform the POST action for changing account credentials for a given group.
	 * Permissions::GROUP_ADMIN on the specified group is required to perform this action.
	 */
	public function updateAccountCredentials($groupName) {
		$thisUser = Auth::user();
		// Check permissions
		$isAdmin = PermissionHandler::checkGroup($thisUser, $groupName, Permissions::GROUP_ADMIN);
		if(!$isAdmin) {
			return Redirect::back()
			->with('flashError', 'You do not have permission to perform selected action');
		}
		
		$cfUser = Input::get('cfUsername');
		$cfPass = Input::get('cfPassword');
		
		$newValues = [
			GroupHandler::CF_USER => $cfUser,
			GroupHandler::CF_PASS => $cfPass
		];
		
		GroupHandler::changeCredentials($groupName, $newValues);
		
		return Redirect::back()
			->with('flashSuccess', 'Invitation code successfully updated.');
	}
}
