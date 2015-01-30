<?php
namespace MongoDB\Security;

/**
 * This class defines a list of constants used to identify the permissions used 
 * on the platform.
 */
class Permissions {
	// Admin permissions
	const ALLOW_ALL = 'allow.all';	// Allowed everything
	
	// Ct-Group permissions
	const GROUP_ADMIN = '#.admin';
	const GROUP_WRITE = '#.write';
	const GROUP_READ  = '#.read';
}
