<?php
namespace MongoDB\Security;

class Roles {
	const PLATFORM_ADMIN = 'SYS_ADMIN';
	const GROUP_ADMIN  = '#:admin';
	const GROUP_MEMBER = '#:member';
	const GROUP_GUEST  = '#:guest';
	
	public static $GROUP_ROLE_NAMES = [ 'admin', 'member', 'guest' ];
	public static $GROUP_ROLES = [ 
			'admin' => Roles::GROUP_ADMIN,
			'member'=> Roles::GROUP_MEMBER, 
			'guest' => Roles::GROUP_GUEST
	];
	public static $GROUP_ROLES_LABELS = [
			'admin' => 'Full access',
			'member'=> 'Read/write',
			'guest' => 'Read'
	];
	
	public static function getRoleByName($rolename) {
		return Roles::$GROUP_ROLES[$rolename];
	}
	
	public static function getRoleLabel($rolename) {
		return Roles::$GROUP_ROLES_LABELS[$rolename];
	}
}
