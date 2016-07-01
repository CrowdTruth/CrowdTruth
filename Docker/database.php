<?php
return array(
	'fetch' => PDO::FETCH_CLASS,
	'default' => 'CrowdWatson',
	'connections' => array(
		'CrowdWatson' => array(
			'driver'   => 'mongodb',
			'host'     => 'crowdtruth-mongo',
			'port'     => 27017,
			'username' => '',
			'password' => '',
			'database' => 'CrowdTruth'
		),
	),
	'migrations' => 'migrations',
);
