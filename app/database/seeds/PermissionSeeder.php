<?php

use \Security\Permissions as Permissions;
use \Security\PermissionHandler as PermissionHandler;
use \Security\ProjectHandler as ProjectHandler;
use \Security\Roles as Roles;

/**
 * Create root user and basic permission structure in the database.
 */
class PermissionSeeder extends Seeder {

	/**
	 * Create root user and basic permission structure in the database.
	 * For detailed documentation on permission structure, see
	 * PermissionHandler class.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		// Create admin user with admin permisions
		Sentry::getUserProvider()->create([
			'_id' => ProjectHandler::ADMIN_USER,
			'password' => 'admin',
			'email' => 'admin@crowdtruth.org',
			'firstname' => 'Admin',
			'lastname' => 'Crowdtruth'
		]);
		
		// Create the admin group with special permission Permissions::ALLOW_ALL
		ProjectHandler::createGroup('admin');
		$adminGroup = Sentry::findGroupByName('admin:admin');
		$permissions = $adminGroup->permissions;
		$permissions[Permissions::ALLOW_ALL] = 1;	// Allowed everything !
		$adminGroup->permissions = $permissions;
		$adminGroup->save();
		
		// Assign user admin to group admin.
		$root = Sentry::findUserByLogin(ProjectHandler::ADMIN_USER);
		$root->addGroup($adminGroup);
	}
}
