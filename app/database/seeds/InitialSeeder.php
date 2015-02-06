<?php

use MongoDB\SoftwareComponent as SoftwareComponent;

class InitialSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// Initialize text sentence preprocessor component
		$id = 'textsentencepreprocessor';
		$label = 'This component is used for transforming text files';
		$txtPreprocessor = new SoftwareComponent($id, $label);
		$txtPreprocessor['domains'] = [];
		$txtPreprocessor['configurations'] = [];
		$txtPreprocessor->save();
		
		// Initialize file uploader component
		$id = 'fileuploader';
		$label = 'This component is used for storing files as documents within MongoDB';
		$txtPreprocessor = new SoftwareComponent($id, $label);
		$txtPreprocessor['domains'] = [];
		$txtPreprocessor->save();
		
		// Initialize media search component
		$id = 'mediasearchcomponent';
		$label = 'This component is used for searching media in MongoDB';
		$txtPreprocessor = new SoftwareComponent($id, $label);
		$txtPreprocessor['keys'] = [];
		$txtPreprocessor->save();
	}
}
