<?php

class SoftwareAgentSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		$this->command->info('Create software agents');
		
		// Initialize file creation
		$this->createIfNotExist(
				'filecreator',
				'This component is used for creating files in the database'
		);
	
		// Initialize batch creation
		$this->createIfNotExist(
				'batchcreator',
				'This component is used for creating batches in the database'
		);
	
		// Initialize unit creation
		$this->createIfNotExist(
				'unitcreator',
				'This component is used for creating units in the database'
		);

		// Initialize job creation
		$this->createIfNotExist(
				'jobcreator',
				'This component is used for creating jobs in the database'
		);

		// Initialize template creation
		$this->createIfNotExist(
				'templatecreator',
				'This component is used for creating templates in the database'
		);
	}

	/**
	 * Creates a new software agent, if it does not already exist in the SoftwareAgents 
	 * Collection.
	 * 
	 * @param $name Name of the software agent to be created
	 * @param $label Label providing once sentence description of the agent 
	 * @param $creator a function which configures any agent specific settings. 
	 */
	private function createIfNotExist($name, $label) {
		$sc = SoftwareAgent::find($name);
		
		if( is_null($sc) ) {
			$this->command->info('...Initializing: ' . $name);
			$agent = new SoftwareAgent($name, $label);	// Create generic agent
			$agent->save();		// And save the agent
		}
	}
}