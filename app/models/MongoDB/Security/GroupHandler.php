<?php
namespace MongoDB\Security;

use \Sentry as Sentry;

/**
 * Class for handling Groups. Group creation, assigning and removing permissions
 * and listing user groups are functions of this class.
 */
class GroupHandler {
	// Group role permissions
	const CF_USER  = 'cfUsername';
	const CF_PASS = 'cfPassword';
	const ADMIN_USER = 'admin';
	
	/**
	 * Creates a new CT-group -- all required sentryGroups are created for the CT-group.
	 * When a new CT-group is created, roles for the CT-group are created as follows, 
	 * with their corresponding permissions:
	 * 
	 *  - Roles::GROUP_ADMIN, with Permissions::GROUP_ADMIN, Permissions::GROUP_WRITE and 
	 *  	Permissions::GROUP_READ;
	 *  - Roles::GROUP_MEMBER, with Permissions::GROUP_WRITE, and Permissions::GROUP_READ;
	 *  - Roles::GROUP_GUEST, with Permissions::GROUP_READ,
	 * 
	 * Default invitation codes are also created for the CT-group.
	 * 
	 * Account credentials for CrowdFlower are also stored as part of the CT-group information.
	 * 
	 * @param $groupName Name of the CT-group to be created.
	 */
	public static function createGroup($groupName) {
		// CT-Group credentials are stored in the Admin Sentry-group
		Sentry::createGroup([
			'name'        => str_replace('#', $groupName, Roles::GROUP_ADMIN),
			'permissions' => [
				str_replace('#', $groupName, Permissions::GROUP_ADMIN) => 1,
				str_replace('#', $groupName, Permissions::GROUP_WRITE) => 1,
				str_replace('#', $groupName, Permissions::GROUP_READ)  => 1,
			],
			'invite_code' => $groupName.'_invitation_admin',
			'credentials' => [ 
				GroupHandler::CF_USER => '',
				GroupHandler::CF_PASS => '' 
			],
		]);
		Sentry::createGroup([
			'name'        => str_replace('#', $groupName, Roles::GROUP_MEMBER),
			'permissions' => [
				str_replace('#', $groupName, Permissions::GROUP_ADMIN) => 0,
				str_replace('#', $groupName, Permissions::GROUP_WRITE) => 1,
				str_replace('#', $groupName, Permissions::GROUP_READ)  => 1,
			],
			'invite_code' => $groupName.'_invitation_member'
		]);
		Sentry::createGroup([
			'name'        => str_replace('#', $groupName, Roles::GROUP_GUEST),
			'permissions' => [
				str_replace('#', $groupName, Permissions::GROUP_ADMIN) => 0,
				str_replace('#', $groupName, Permissions::GROUP_WRITE) => 0,
				str_replace('#', $groupName, Permissions::GROUP_READ)  => 1,
			],
			'invite_code' => $groupName.'_invitation_guest'
		]);
		
		// Assign user admin to group admin role.
		GroupHandler::grantUser(Sentry::findUserByLogin(GroupHandler::ADMIN_USER), $groupName, Roles::GROUP_ADMIN);
	}
	
	/**
	 * Grants users permissions for the given role on the given group.
	 * 
	 * @param $user UserAgent instance for the user to be assigned permissions.
	 * @param $groupName name of the CT-group the user is being assigned to.
	 * @param $role Role constant defining the role assigned to the user.
	 */
	public static function grantUser($user, $groupName, $role) {
		// Remove from other Sentry-groups on the same CT-group
		GroupHandler::revokeUser($user, $groupName);
		
		$sentryGroup = Sentry::findGroupByName(str_replace('#', $groupName, $role));
		$user->addGroup($sentryGroup);
	}
	
	/**
	 * Revoke all permissions to the given user on a given group.
	 * 
	 * @param $user UserAgent instance of the user whos permissions are to be revoked.
	 * @param $groupName Group name of CT-group where permissions are to be revoked.
	 */
	public static function revokeUser($user, $groupName) {
		foreach(Roles::$GROUP_ROLES as $role) {
			$user->removeGroup(Sentry::findGroupByName(str_replace('#', $groupName, $role)));
		}
	}
	
	/**
	 * Generate a list of CT-groups a given user belongs to.
	 * 
	 * @param $user UserAgent of the user whose groups should be listed.
	 * @return List of containing the name and role of CT-groups the user belongs to.
	 */
	public static function getUserGroups($user) {
		$sentryGroups = $user->getGroups();
		
		// List Sentry-groups and build list of CT-groups
		$ctGroups = [];
		foreach ($sentryGroups as $sentryGroup) {
			$parts = explode(':', $sentryGroup->name);
			array_push($ctGroups, [
				'name' => $parts[0],
				'role' => $parts[1]
			]);
		}
		return $ctGroups;
	}
	
	/**
	 * List the names of all existing CT-groups.
	 * 
	 * Return a list of existing CT-groups.
	 * 
	 * @return List of CT-group names.
	 */
	public static function listGroups() {
		$sentryGroups = Sentry::findAllGroups();
		$ctGroups = [];

		foreach ($sentryGroups as $sentryGroup) {
			$parts = explode(':', $sentryGroup->name);
			array_push($ctGroups, $parts[0]);
		}
		return array_unique($ctGroups);
	}
	
	/**
	 * Check if a user is in a group
	 */
	public static function inGroup($userName, $groupName) {

		$user = Sentry::findUserByID($userName);
		$group = Sentry::findGroupByName(str_replace('#', $groupName, Roles::GROUP_MEMBER));

		// Check if the user is in the group
		if ($user->inGroup($group)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	
	
	/**
	 * Retrieve the external account credentials for a given group
	 * 
	 * @param $groupName Name of the desired group.
	 * @return An array with credentials information for the desired group.
	 */
	public static function getCredentials($groupName) {
		$sentryAdminGroup = Sentry::findGroupByName(str_replace('#', $groupName, Roles::GROUP_ADMIN));
		return $sentryAdminGroup['credentials'];
	}
	
	/**
	 * Update the external account credentials of a given group.
	 * 
	 * @param $groupName Name of the desired group.
	 * @param $newValues An array of key/value pairs containing the account's
	 * 		credentials to be updated (e.g. GroupHandler::CF_USER, for CrowdFlower username, 
	 * 		GroupHandler::CF_PASS for CrowdFlower password) and the value to be set.
	 */
	public static function changeCredentials($groupName, $newValues) {
		$sentryAdminGroup = Sentry::findGroupByName(str_replace('#', $groupName, Roles::GROUP_ADMIN));
		$credentials = $sentryAdminGroup['credentials'];
		foreach ($newValues as $key => $value) {
			$credentials[$key] = $value;
		}
		$sentryAdminGroup['credentials'] = $credentials;
		$sentryAdminGroup->save();
	}
}
