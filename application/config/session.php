<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'native' => array(
		'name' => 'redt_events',
		'lifetime' => Date::WEEK,
	),
	'database' => array(
		'name'      => 'redt_events',
		'encrypted' => TRUE,
		'group'     => 'default',
		'table'     => 'sessions',
		'lifetime'  => Date::WEEK,
	),
	'cookie' => array(
		'encrypted' => TRUE,
		'lifetime' => Date::WEEK,
	),
);