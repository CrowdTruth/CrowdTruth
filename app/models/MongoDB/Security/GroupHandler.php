<?php
namespace MongoDB\Security;

use \Sentry as Sentry;

class GroupHandler {
	// Creates a new CT-group -- all required sentryGroups are created for the CT-group
	public static function createGroup($groupName) {
		Sentry::createGroup([
			'name'        => str_replace('#', $groupName, Roles::GROUP_ADMIN),
			'permissions' => [
				str_replace('#', $groupName, Permissions::GROUP_ADMIN) => 1,
				str_replace('#', $groupName, Permissions::GROUP_WRITE) => 1,
				str_replace('#', $groupName, Permissions::GROUP_READ)  => 1,
			],
			'invite_code' => $groupName.'_invitation'
		]);
		Sentry::createGroup([
			'name'        => str_replace('#', $groupName, Roles::GROUP_MEMBER),
			'permissions' => [
				str_replace('#', $groupName, Permissions::GROUP_ADMIN) => 0,
				str_replace('#', $groupName, Permissions::GROUP_WRITE) => 1,
				str_replace('#', $groupName, Permissions::GROUP_READ)  => 1,
			],
			'invite_code' => $groupName.'_invitation'
		]);
		Sentry::createGroup([
			'name'        => str_replace('#', $groupName, Roles::GROUP_GUEST),
			'permissions' => [
				str_replace('#', $groupName, Permissions::GROUP_ADMIN) => 0,
				str_replace('#', $groupName, Permissions::GROUP_WRITE) => 0,
				str_replace('#', $groupName, Permissions::GROUP_READ)  => 1,
			],
			'invite_code' => $groupName.'_invitation'
		]);
	}
	
	public static function grantUser($user, $groupName, $role) {
		GroupHandler::revokeUser($user, $groupName);
		
		$sentryGroup = Sentry::findGroupByName(str_replace('#', $groupName, $role));
		$user->addGroup($sentryGroup);
	}
	
	public static function revokeUser($user, $groupName) {
		// Remove from other Sentry-groups on the same CT-group
		$user->removeGroup(Sentry::findGroupByName(str_replace('#', $groupName, Roles::GROUP_ADMIN)));
		$user->removeGroup(Sentry::findGroupByName(str_replace('#', $groupName, Roles::GROUP_MEMBER)));
		$user->removeGroup(Sentry::findGroupByName(str_replace('#', $groupName, Roles::GROUP_GUEST)));
	}
	
	public static function getUserGroups($user) {
		$sentryGroups = $user->getGroups();
		
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
}
