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
			'_id' => 'admin',
			'password' => 'admin',
			'email' => 'admin@crowdtruth.org',
			'firstname' => 'Admin',
			'lastname' => 'Crowdtruth'
		]);
		
		Sentry::createGroup([
			'name'        => 'admin:admin',
			'permissions' => [
				Permissions::ALLOW_ALL => 1,	// Allowed everything !
			],
		]);
		
		$root = Sentry::findUserByLogin('admin');
		$adminGroup = Sentry::findGroupByName('admin:admin');
		$root->addGroup($adminGroup);

		// DEBUG:
		GroupHandler::createGroup('crowdwatson');
		GroupHandler::createGroup('nlesc');

		$carlos = Sentry::findUserByLogin('carlosm');
		$benjamin = Sentry::findUserByLogin('benjamin');
		$arne = Sentry::findUserByLogin('harriette');
		
		GroupHandler::grantUser($carlos, 'crowdwatson', Roles::GROUP_ADMIN);
		GroupHandler::grantUser($benjamin, 'crowdwatson', Roles::GROUP_MEMBER);
		GroupHandler::grantUser($arne, 'crowdwatson', Roles::GROUP_GUEST);
		
		GroupHandler::grantUser($carlos, 'nlesc', Roles::GROUP_ADMIN);
	}
}
