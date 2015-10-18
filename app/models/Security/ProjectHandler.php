<?php
namespace Security;

use \Sentry as Sentry;

/**
 * Class for handling Projects. Project creation, assigning and removing permissions
 * and listing user projects are functions of this class.
 */
class ProjectHandler {
	// Project role permissions
	const CF_USER  = 'cfUsername';
	const CF_PASS = 'cfPassword';
	const ADMIN_USER = 'admin';
	
	/**
	 * Creates a new Project -- all required sentryGroups are created for the Project.
	 * When a new Project is created, roles for the Project are created as follows, 
	 * with their corresponding permissions:
	 * 
	 *  - Roles::PROJECT_ADMIN, with Permissions::PROJECT_ADMIN, Permissions::PROJECT_WRITE and 
	 *  	Permissions::PROJECT_READ;
	 *  - Roles::PROJECT_MEMBER, with Permissions::PROJECT_WRITE, and Permissions::PROJECT_READ;
	 *  - Roles::PROJECT_GUEST, with Permissions::PROJECT_READ,
	 * 
	 * Default invitation codes are also created for the Project.
	 * 
	 * Account credentials for CrowdFlower are also stored as part of the Project information.
	 * 
	 * @param $projectName Name of the Project to be created.
	 */
	public static function createGroup($projectName) {
		// $groupName credentials are stored in the Admin Sentry-group
		Sentry::createGroup([
			'name'        => str_replace('#', $projectName, Roles::PROJECT_ADMIN),
			'permissions' => [
				str_replace('#', $projectName, Permissions::PROJECT_ADMIN) => 1,
				str_replace('#', $projectName, Permissions::PROJECT_WRITE) => 1,
				str_replace('#', $projectName, Permissions::PROJECT_READ)  => 1,
			],
			'invite_code' => $projectName.'_invitation_admin',
			'credentials' => [ 
				ProjectHandler::CF_USER => '',
				ProjectHandler::CF_PASS => '' 
			],
		]);
		Sentry::createGroup([
			'name'        => str_replace('#', $projectName, Roles::PROJECT_MEMBER),
			'permissions' => [
				str_replace('#', $projectName, Permissions::PROJECT_ADMIN) => 0,
				str_replace('#', $projectName, Permissions::PROJECT_WRITE) => 1,
				str_replace('#', $projectName, Permissions::PROJECT_READ)  => 1,
			],
			'invite_code' => $projectName.'_invitation_member'
		]);
		Sentry::createGroup([
			'name'        => str_replace('#', $projectName, Roles::PROJECT_GUEST),
			'permissions' => [
				str_replace('#', $projectName, Permissions::PROJECT_ADMIN) => 0,
				str_replace('#', $projectName, Permissions::PROJECT_WRITE) => 0,
				str_replace('#', $projectName, Permissions::PROJECT_READ)  => 1,
			],
			'invite_code' => $projectName.'_invitation_guest'
		]);
		
		// Assign user admin to group admin role.
		ProjectHandler::grantUser(Sentry::findUserByLogin(ProjectHandler::ADMIN_USER), $projectName, Roles::PROJECT_ADMIN);
	}
	
	/**
	 * Grants users permissions for the given role on the given project.
	 * 
	 * @param $user UserAgent instance for the user to be assigned permissions.
	 * @param $projectName name of the Project the user is being assigned to.
	 * @param $role Role constant defining the role assigned to the user.
	 */
	public static function grantUser($user, $projectName, $role) {
		// Remove from other Sentry-groups on the same Project
		ProjectHandler::revokeUser($user, $projectName);
		
		$sentryGroup = Sentry::findGroupByName(str_replace('#', $projectName, $role));
		$user->addGroup($sentryGroup);
	}
	
	/**
	 * Revoke all permissions to the given user on a given project.
	 * 
	 * @param $user UserAgent instance of the user whos permissions are to be revoked.
	 * @param $projectName Group name of Project where permissions are to be revoked.
	 */
	public static function revokeUser($user, $projectName) {
		foreach(Roles::$PROJECT_ROLES as $role) {
			$user->removeGroup(Sentry::findGroupByName(str_replace('#', $projectName, $role)));
		}
	}
	
	/**
	 * Generate a list of Projects a given user belongs to.
	 * 
	 * @param $user UserAgent of the user whose groups should be listed.
	 * @param $permission (optional) a permission required for the listed projects -- only 
	 * 			projects for which the user has the given permission will be listed.
	 * @return List of containing the name and role of Projects the user belongs to.
	 */
	public static function getUserProjects($user, $permission=null) {
		$sentryGroups = $user->getGroups();
		
		// List Sentry-groups and build list of Projects
		$projects = [];
		foreach ($sentryGroups as $sentryGroup) {
			$parts = explode(':', $sentryGroup->name);
			
			if(is_null($permission) || PermissionHandler::checkProject($user, $parts[0], $permission)) {
				array_push($projects, [
					'name' => $parts[0],
					'role' => $parts[1]
				]);
			}
		}
		return $projects;
	}

	/**
	 * List the names of all existing Projects.
	 * 
	 * Return a list of existing Projects.
	 * 
	 * @return List of Project names.
	 */
	public static function listGroups() {
		$sentryGroups = Sentry::findAllGroups();
		$project = [];

		foreach ($sentryGroups as $sentryGroup) {
			$parts = explode(':', $sentryGroup->name);
			array_push($project, $parts[0]);
		}
		return array_unique($project);
	}
	
	/**
	 * Check if a user is in a group
	 */
	public static function inGroup($userName, $groupName) {

		$user = Sentry::findUserByID($userName);
		$group = Sentry::findGroupByName(str_replace('#', $groupName, Roles::PROJECT_MEMBER));

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
	 * @param $projectName Name of the desired group.
	 * @return An array with credentials information for the desired group.
	 */
	public static function getCredentials($projectName) {
		$sentryAdminGroup = Sentry::findGroupByName(str_replace('#', $projectName, Roles::PROJECT_ADMIN));
		return $sentryAdminGroup['credentials'];
	}
	
	/**
	 * Update the external account credentials of a given group.
	 * 
	 * @param $projectName Name of the desired group.
	 * @param $newValues An array of key/value pairs containing the account's
	 * 		credentials to be updated (e.g. ProjectHandler::CF_USER, for CrowdFlower username, 
	 * 		ProjectHandler::CF_PASS for CrowdFlower password) and the value to be set.
	 */
	public static function changeCredentials($projectName, $newValues) {
		$sentryAdminGroup = Sentry::findGroupByName(str_replace('#', $projectName, Roles::PROJECT_ADMIN));
		$credentials = $sentryAdminGroup['credentials'];
		foreach ($newValues as $key => $value) {
			$credentials[$key] = $value;
		}
		$sentryAdminGroup['credentials'] = $credentials;
		$sentryAdminGroup->save();
	}
}
