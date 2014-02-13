<?php

namespace MongoDB;

use Moloquent, URL, File, Exception, Auth, User;

class CrowdAgent extends Moloquent {

	protected $collection = 'crowdagents';
	protected $softDelete = true;
	protected static $unguarded = true;
	 
}