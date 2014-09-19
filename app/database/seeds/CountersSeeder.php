<?php

use MongoDB\SoftwareComponent as SoftwareComponent;
use MongoDB\Activity as Activity;
use MongoDB\Entity as Entity;

/**
 * Generate seeds for Counter collection
 * 
 * To run this seeder alone:
 *   php artisan db:seed --class=CountersSeeder
 * 
 * @author carlosm
 */
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
	
	/**
	 * Generate a list of seed values for a given list of Entity/Activity item.
	 * The _id of the given items is used to determine the existing counter names
	 * and maximum values.
	 * 
	 * @param $items   A dictionary where the keys are the name of the sequences existing 
	 *                 in the given list of items and the value is the maximum id 
	 *                 for that sequence.
	 */
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
