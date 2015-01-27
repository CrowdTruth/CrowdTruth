<?php
namespace MongoDB\Security;

/**
 * Class that defines the various roles in a CT-group.
 */
class Roles {
	// System administrator permisions
	const PLATFORM_ADMIN = 'SYS_ADMIN';
	
	// Group role permissions
	const GROUP_ADMIN  = '#:admin';
	const GROUP_MEMBER = '#:member';
	const GROUP_GUEST  = '#:guest';
	
	// Names of group roles
	public static $GROUP_ROLE_NAMES = [ 'admin', 'member', 'guest' ];
	
	// Name -> Role mapping
	public static $GROUP_ROLES = [ 
			'admin' => Roles::GROUP_ADMIN,
			'member'=> Roles::GROUP_MEMBER, 
			'guest' => Roles::GROUP_GUEST
	];
	
	// User friendly labels
	public static $GROUP_ROLES_LABELS = [
			'admin' => 'Full access',
			'member'=> 'Read/write',
			'guest' => 'Read'
	];
	
	/**
	 * Retrieve a Role constant by using the role name for that Role.
	 * 
	 * @param $rolename Name of the role to be used: 'admin', 'member', 'guest'
	 * 
	 * @return a Role constant: Roles::GROUP_ADMIN, Roles::GROUP_MEMBER, Roles::GROUP_GUEST
	 */
	public static function getRoleByName($rolename) {
		return Roles::$GROUP_ROLES[$rolename];
	}
	
	/**
	 * Retrieve the user friendly name for a given Role.
	 * 
	 * @param $rolename Name of the role to be used: 'admin', 'member', 'guest'
	 * 
	 * @return The user friendly name of the role: 'Full access', 'Read/write', 'Read'
	 */
	public static function getRoleLabel($rolename) {
		return Roles::$GROUP_ROLES_LABELS[$rolename];
	}
}
