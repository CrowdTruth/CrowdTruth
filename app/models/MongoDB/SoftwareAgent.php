<?php

namespace MongoDB;

use Moloquent, URL, File, Exception, Auth, User;

class SoftwareAgent extends Moloquent {

	protected $collection = 'softwareagents';
	protected $softDelete = true;
	protected static $unguarded = true;   

}