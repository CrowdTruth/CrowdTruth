<?php

use MongoDB\SoftwareComponent as SoftwareComponent;
use MongoDB\Activity as Activity;
use MongoDB\Entity as Entity;

/**
 * Seed the Counters collection with existing Activities and Entities.
 * This seeder looks at the _id's of existing Activities and Entities 
 * and creates a counter with the maximum value for existing 
 * Activity/Entity types.
 * 
 * For instance, if entities entity/foo/bar/1, entity/foo/bar/2, 
 * entity/foo/bar/3, ..., entity/foo/bar/20 are already exist in the 
 * database, the seeder will create a counter called entity/foo/bar/
 * with value 20. In this way, the next Entity of type entity/foo/bar/
 * which gets created, will use this counter to generate its 
 * corresponding ID entity/foo/bar/21.
 * 
 * @author carlosm
 */
class CountersSeeder extends Seeder {

	/**
	 * Run the database counter seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->command->info('Seeding all activities...');
		$seeds = $this->getSeeds(Activity::distinct('_id')->get());
		foreach($seeds as $name => $seed) {
			$this->command->info('Seed: '.$name.' = '.$seed);
			$counter = new Counter;
			$counter['_id'] = $name;
			$counter['seq'] = $seed;
			$counter->save();
		}
		
		$this->command->info('Seeding all entities...');
		$seeds = $this->getSeeds(Entity::distinct('_id')->get());
		foreach($seeds as $name => $seed) {
			$this->command->info('Seed: '.$name.' = '.$seed);
			$counter = new Counter;
			$counter['_id'] = $name;
			$counter['seq'] = $seed;
			$counter->save();
		}
	}

	/**
	 * Extract the names of seeds to be created from a list of all existing ID's.
	 * Seeds are created with the highest existing index for the .
	 * 
	 * @param $items list of existing ID's
	 * @return A key => value array, where the keys are the names of the seeds to 
	 * 		be created and the value is the value of the seed.
	 */
	private function getSeeds($items) {
		$seeds = [];
		foreach($items as $item) {
			// Split _id into base and inc
			// fullId: sample/uri/foo/bar/1
			// base  : sample/uri/foo/bar
			// inc   : 1
			$fullId = $item[0];
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
