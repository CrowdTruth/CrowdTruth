<?php
namespace MongoDB;

use \Auth as Auth;

class PermissionHandler {
	// Permission is a constant from Admin permissions
	public static function checkAdmin($permission) {
		$user = Auth::user();
		return $user->hasAccess($permission);
	}

	// Permission is a constant from group permissions
	public static function checkGroup($group, $permission) {
		$sentryPermission = str_replace('#', $group,$permission);
		$user = Auth::user();
		return $user->hasAccess($sentryPermission);
	}
	
	public static function getUserGroups() {
		$user = Auth::user();
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

class Roles {
	const PLATFORM_ADMIN = 1;
	const PLATFORM_MEMBER = 2;
	const PLATFORM_GUEST = 3;
	
	const GROUP_ADMIN  = '#:admin';
	const GROUP_MEMBER = '#:member';
	const GROUP_GUEST  = '#:guest';
}

class Permissions {
	// Admin permissions
	const GROUP_CREATE = 'group.create';	// Allowed to create
	const GROUP_MODIFY = 'group.modify';	// Allowed to modify
	const USERS_MODIFY = 'users.modify';	// Allowed to manage users	
	
	// Ct-Group permissions
	const GROUP_ADMIN = '#.admin';
	const GROUP_WRITE = '#.write';
	const GROUP_READ  = '#.read';
}
