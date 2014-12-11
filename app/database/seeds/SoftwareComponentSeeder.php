<?php

use MongoDB\SoftwareComponent as SoftwareComponent;

class SoftwareComponentSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		$this->command->info('Create software components');
		
		// Initialize file uploader
		$this->createIfNotExist(
				'textsentencepreprocessor',
				'Process data',
				function( &$component ) { 
					$txtPreprocessor['domains'] = [];
				}
		);
		
		$this->createIfNotExist(
				'fileuploader',
				'This component is used for storing files as documents within MongoDB',
				function( &$component ) {
					$component['domains'] = [];
				}
		);
		
		$this->createIfNotExist(
				'mediasearchcomponent',
				'Searches through media',
				function( &$component ) {
					$component['keys'] = [];
					$component['keyLabels'] = [];
				}
		);
		}

	/**
	 * Creates a new software component, if it does not already exist in the SoftwareComponents 
	 * Collection.
	 * 
	 * @param $name Name of the software component to be created
	 * @param $label Label providing once sentence description of the component 
	 * @param $creator a function which configures any component specific settings. 
	 */
	private function createIfNotExist($name, $label, $creator) {
		$sc = SoftwareComponent::find($name);
		
		if( is_null($sc) ) {
			$this->command->info('...Initializing: ' . $name);
			$component = new SoftwareComponent($name, $label);	// Create generic component
			$creator($component);	// Customize anything specific for this component
			$component->save();		// And save the component
		}
	}
}
