<?php

namespace mongo\text;

use Moloquent, URL, File, Exception, Auth, User;

class Agent extends Moloquent {

	protected $connection = 'mongodb_text';
	protected $collection = 'agents';
	protected $softDelete = true;
	protected static $unguarded = true;
}