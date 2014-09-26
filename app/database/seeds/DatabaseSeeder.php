<?php

class DatabaseSeeder extends Seeder {

	public function run()
	{
		// $this->call('InitialSeeder');
		// $this->command->info('Initial framework tables seeded!');
		
		$this->call('CountersSeeder');
		$this->command->info('Counters table seeded!');
	}

}