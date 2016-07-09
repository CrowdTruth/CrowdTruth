<?php

use \Template as Template;

class EnrichmentAPISeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$username = 'Clariah';
		$firstname = 'Clariah';
		$lastname = 'Clariah';
		$email = 'clariah@crowdtruth.org';
		$password = 'clariah';

		$registration = UserController::doRegister($username, $firstname, $lastname,
																								$email, $password, $password);
		if($registration['status']=='fail') {
			$this->command->error('Failed to create CLARIAH user');
			foreach ($registration['messages'] as $message)
				$this->command->error(' > '.$message);
		}
		$user = $registration['user'];

		$te = new Template;
		$te['platform'] = 'cf2';
		$te['cml'] = '<div class="html-element-wrapper"><br /><span>{{snippet}}</span></div><cml:radios label="Question" validates="required" gold="true"><cml:radio label="First option" value="first_option" /><cml:radio label="Second option" value="second_option" /></cml:radios>';
		$te['css'] = '';
		$te['instructions'] = '<p><strong>1. Summarize Goal: 1</strong></p><p><strong>2. Define Rules: 2</strong></p><p><strong>3. Provide Examples: 3</strong></p>';
		$te['js'] = '';
		$te['version'] = 0;
		$te['type'] = 'TestTemplate';
		$te['user_id'] = is_null($user) ? $username : $user->_id;

		$te['parameters'] = [
        'input' => [
            [
                'type' => '?imageURL',
                'name' => 'imageURL',
                'description' => 'An Image'
            ]
        ],
        'output' => [
            [
                'type' => '?actor',
                'name' => 'actor',
                'description' => 'The name of the person'
            ]
        ]
    ];
		$te['description'] = 'Identify actors in an image';
		$te->save();

		$this->command->info('EnrichmentAPI seeder completed');
	}
}
