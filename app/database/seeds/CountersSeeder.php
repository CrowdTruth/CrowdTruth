<?php

use MongoDB\SoftwareComponent as SoftwareComponent;

class CountersSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$counter = new Counter;
		$counter['_id'] = 'seeded';
		$counter['seq'] = 1;
		$counter->save();

	}

}