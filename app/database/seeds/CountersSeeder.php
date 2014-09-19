<?php

use MongoDB\SoftwareComponent as SoftwareComponent;
use MongoDB\Activity as Activity;
use MongoDB\Entity as Entity;

class CountersSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$seeds = $this->getSeeds(Activity::all());
		foreach($seeds as $name => $seed) {
			$this->command->info('Seed: '.$name.' = '.$seed);
			$counter = new Counter;
			$counter['_id'] = $name;
			$counter['seq'] = $seed;
			$counter->save();
		}
		
		$seeds = $this->getSeeds(Entity::all());
		foreach($seeds as $name => $seed) {
			$this->command->info('Seed: '.$name.' = '.$seed);
			$counter = new Counter;
			$counter['_id'] = $name;
			$counter['seq'] = $seed;
			$counter->save();
		}
	}
	
	private function getSeeds($items) {
		$seeds = [];
		foreach($items as $item) {
			// Split _id into base and inc
			// fullId: sample/uri/foo/bar/1
			// base  : sample/uri/foo/bar
			// inc   : 1
			$fullId = $item->_id;
			$bits = explode('/', $fullId);
			$inc = array_pop($bits);
			$inc = intval($inc);
			$base = implode('/', $bits);
		
			// Check if index exists
			if(array_key_exists($base, $seeds)) {
				if($inc > $seeds[$base]) {
					// If exists, keep the highest number
					$seeds[$base] = $inc;
				}
			} else {
				// If index doesn't exist, add it
				$seeds[$base] = $inc;
			}
		}
		return $seeds;
	}
}
