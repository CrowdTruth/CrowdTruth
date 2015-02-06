<?php

use \MongoDB\UserAgent as UserAgent;
use \MongoDB\Security\Permissions as Permissions;
use \MongoDB\Security\PermissionHandler as PermissionHandler;
use \MongoDB\Security\GroupHandler as GroupHandler;
use \MongoDB\Security\Roles as Roles;

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
			'_id' => GroupHandler::ADMIN_USER,
			'password' => 'admin',
			'email' => 'admin@crowdtruth.org',
			'firstname' => 'Admin',
			'lastname' => 'Crowdtruth'
		]);
		
		// Create the admin group with special permission Permissions::ALLOW_ALL
		GroupHandler::createGroup('admin');
		$adminGroup = Sentry::findGroupByName('admin:admin');
		$permissions = $adminGroup->permissions;
		$permissions[Permissions::ALLOW_ALL] = 1;	// Allowed everything !
		$adminGroup->permissions = $permissions;
		$adminGroup->save();
		
		// Assign user admin to group admin.
		$root = Sentry::findUserByLogin(GroupHandler::ADMIN_USER);
		$root->addGroup($adminGroup);
	}
}
