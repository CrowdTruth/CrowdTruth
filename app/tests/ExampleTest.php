<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$crawler = $this->client->request('GET', '/home');
		$response = $this->client->getResponse();
		$this->assertTrue($response->isOk());
	}

}