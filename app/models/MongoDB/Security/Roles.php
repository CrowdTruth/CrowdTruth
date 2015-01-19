<?php
namespace MongoDB\Security;

class Roles {
	const PLATFORM_ADMIN = 'SYS_ADMIN';

	public static $GROUP_ROLES = [ 'admin', 'member', 'guest' ];
	const GROUP_ADMIN  = '#:admin';
	const GROUP_MEMBER = '#:member';
	const GROUP_GUEST  = '#:guest';
}
