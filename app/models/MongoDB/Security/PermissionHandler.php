<?php
namespace MongoDB\Security;

/**
 * Developer notes:
 * Package 'Cartalyst/Sentry' does not support 'roles', so we will construct this artificially
 * Sentry does have 'Groups' (which here we refer to as 'Sentry-groups') but they 
 * are not analogous to our intention of Groups, (which here we refer to as 'CT-Groups').
 * For this reason we adopt the following convention:
 * 
 *  - Sentry-groups will be named as: <ct-group>:role
 *  - Permissions within a group will be named <ct-group>.permission
 *  - Within a ct-group, 3 roles exist: admin, member and guest
 *  - Each role the following permissions (within that group):
 *    + admin:  groupadmin, write, read
 *    + member: write, read
 *    + guest:  read
 * 
 * EG1:
 * User Lora is added to ct-group crowdtruth with an administrator role.
 * Thus she is added to sentry-group crowdtruth:admin
 * which gives her permissions:
 * 		crowdtruth.read       = 1
 * 		crowdtruth.write      = 1
 *      crowdtruth.groupadmin = 1
 * 
 * EG2:
 * User Benjamin is added to ct-group crowdtruth with an member role.
 * Thus he is added to sentry-group crowdtruth:member
 * which gives her permissions:
 * 		crowdtruth.read       = 1
 * 		crowdtruth.write      = 1
 *      crowdtruth.groupadmin = 0
 * 
 * EG3:
 * User Arne is added to ct-group crowdtruth with an guest role.
 * Thus he is added to sentry-group crowdtruth:guest
 * which gives her permissions:
 * 		crowdtruth.read       = 1
 * 		crowdtruth.write      = 0
 *      crowdtruth.groupadmin = 0
 */
class PermissionHandler {
	// Permission is a constant from Admin permissions
	public static function checkAdmin($user, $permission) {
		return $user->hasAccess($permission);
	}
	
	// Permission is a constant from group permissions
	public static function checkGroup($user, $group, $permission) {
		$sentryPermission = str_replace('#', $group, $permission);
		return $user->hasAccess($sentryPermission) or $user->hasAccess(Permissions::ALLOW_ALL);
	}
}
