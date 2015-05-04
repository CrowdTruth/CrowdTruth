<?php

/**
 * Seeds the CrowdTruth database for initial use.
 * 
 * On the command line, run:
 * 
 *     php artisan migrate --seed
 * 
 * This will create all the necessary table structure to start using the 
 * CrowdTruth framework.
 */
class DatabaseSeeder extends Seeder {

	public function run()
	{
		$this->call('InitialSeeder');
		$this->command->info('Initial framework tables seeded!');
		
		$this->call('CountersSeeder');
		$this->command->info('Counters collection seeded!');
		
		$this->call('SoftwareComponentSeeder');
		$this->command->info('SoftwareComponent collection seeded!');
		$this->command->info('Counters table seeded!');
		
		$this->call('PermissionSeeder');
		$this->command->info('Permissions tables seeded!');
	}

}
