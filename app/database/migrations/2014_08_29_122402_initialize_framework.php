<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitializeFramework extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('softwarecomponents', function($collection) {});
		Schema::create('useragents', function($collection) {
			$collection->unique('email');
		});
		Schema::create('groups', function($collection) {});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('softwarecomponents');
		Schema::drop('useragents');
		Schema::drop('groups');
	}
}
