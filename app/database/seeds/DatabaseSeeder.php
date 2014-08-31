<?php

use MongoDB\SoftwareComponent as SoftwareComponent;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// Initialize file uploader
		$id = 'textsentencepreprocessor';
		$label = 'This component is used for storing files as documents within MongoDB';
		$fileUploader = new SoftwareComponent($id, $label);
		$fileUploader['domains'] = [];
		$fileUploader->save();
		
		// $this->call('UserTableSeeder');
	}

}