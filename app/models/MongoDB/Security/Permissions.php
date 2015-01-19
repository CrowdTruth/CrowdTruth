<?php
namespace MongoDB\Security;

class Permissions {
	// Admin permissions
	const ALLOW_ALL = 'allow.all';	// Allowed everything
	
	// Ct-Group permissions
	const GROUP_ADMIN = '#.admin';
	const GROUP_WRITE = '#.write';
	const GROUP_READ  = '#.read';
}
