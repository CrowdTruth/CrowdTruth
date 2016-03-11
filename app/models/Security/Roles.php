<?php
namespace Security;

/**
 * Class that defines the various roles in a CT-group.
 */
class Roles {
	// System administrator permisions
	const PLATFORM_ADMIN = 'SYS_ADMIN';
	
	// Project role permissions
	const PROJECT_ADMIN  = '#:admin';
	const PROJECT_MEMBER = '#:member';
	const PROJECT_GUEST  = '#:guest';
	
	// Names of group roles
	public static $PROJECT_ROLE_NAMES = [ 'admin', 'member', 'guest' ];
	
	// Name -> Role mapping
	public static $PROJECT_ROLES = [ 
			'admin' => Roles::PROJECT_ADMIN,
			'member'=> Roles::PROJECT_MEMBER, 
			'guest' => Roles::PROJECT_GUEST
	];
	
	// User friendly labels
	public static $PROJECT_ROLES_LABELS = [
			'admin' => 'Full access',
			'member'=> 'Read/write',
			'guest' => 'Read'
	];
	
	/**
	 * Retrieve a Role constant by using the role name for that Role.
	 * 
	 * @param $rolename Name of the role to be used: 'admin', 'member', 'guest'
	 * 
	 * @return a Role constant: Roles::PROJECT_ADMIN, Roles::PROJECT_MEMBER, Roles::PROJECT_GUEST
	 */
	public static function getRoleByName($rolename) {
		return Roles::$PROJECT_ROLES[$rolename];
	}
	
	/**
	 * Retrieve the user friendly name for a given Role.
	 * 
	 * @param $rolename Name of the role to be used: 'admin', 'member', 'guest'
	 * 
	 * @return The user friendly name of the role: 'Full access', 'Read/write', 'Read'
	 */
	public static function getRoleLabel($rolename) {
		return Roles::$PROJECT_ROLES_LABELS[$rolename];
	}
}
