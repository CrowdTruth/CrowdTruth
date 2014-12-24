<?php

class PermissionSeeder extends Seeder {

	/**
	 * Create root user and basic permission structure in the database.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		User::create([
			'_id' => 'admin',
			'password' => Hash::make('admin'),
		]);
		
		// Developer note:
		// Package 'Sentry' does not support 'roles', so we will construct this artificially
		// Sentry does have 'Groups' (which here I refer to as 'Sentry-groups') but they 
		// are not analogous to our intention of Groups, (which here I refer to as 'Groups').
		// For this reason we adopt the following convention:
		// 
		// Sentry-groups will be named as: <group>:role
		// Permissions within a group will be named <group>.permission
		// EG1:
		// User Lora is added to group CrowdTruth with an administrator role.
		// Thus she is added to sentry-group crowdtruth:admin
		// which gives her permissions:
		//		crowdtruth.read       = 1
		//		crowdtruth.write      = 1
		//      crowdtruth.groupadmin = 1
		//      
		Sentry::createGroup([
			'name'        => 'admin:admin',
			'permissions' => [
				'group.create' => 1,	// Allowed to create
				'group.modify' => 1,	// Allowed to modify
				'users.modify' => 1,	// Allowed to manage users
			],
		]);
		
		Sentry::createGroup([
			'name'        => 'admin:member',
			'permissions' => [
				'group.create' => 1,	// Allowed to create
				'group.modify' => 0,	// But not to modify groups
				'users.modify' => 0,	// or users
			],
		]);
		
		$root = Sentry::findUserByLogin('admin');
		$adminGroup = Sentry::findGroupByName('admin:admin');
		$root->addGroup($adminGroup);

		// DEBUG:
		Sentry::createGroup([
			'name'        => 'crowdtruth:admin',
			'permissions' => [
				'crowdtruth.admin' => 1,
				'crowdtruth.write' => 1,
				'crowdtruth.read'  => 1,
			],
		]);

		$carlos = Sentry::findUserByLogin('carlosm');
		$ctGroup = Sentry::findGroupByName('crowdtruth:admin');
		$carlos->addGroup($ctGroup);
	}
}
