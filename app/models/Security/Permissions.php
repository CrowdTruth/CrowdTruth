<?php
namespace Security;

/**
 * This class defines a list of constants used to identify the permissions used 
 * on the platform.
 */
class Permissions {
	// Admin permissions
	const ALLOW_ALL = 'allow.all';	// Allowed everything
	
	// Project permissions
	const PROJECT_ADMIN = '#.admin';
	const PROJECT_WRITE = '#.write';
	const PROJECT_READ  = '#.read';
}
