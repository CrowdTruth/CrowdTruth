<?php

namespace MongoDB;

use Moloquent, URL, File, Exception, Auth;

class SoftwareAgent extends Moloquent {

	protected $collection = 'softwareagents';
	protected $softDelete = true;
	protected static $unguarded = true;   

}