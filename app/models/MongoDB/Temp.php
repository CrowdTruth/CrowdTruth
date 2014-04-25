<?php

namespace MongoDB;

use Moloquent;

class Temp extends Moloquent {

	protected $collection = 'temp';
	protected static $unguarded = true;
    public static $snakeAttributes = false;
}