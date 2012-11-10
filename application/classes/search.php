<?php defined('SYSPATH') or die('No direct script access.');

class Search {

	public static function user($query)
	{
		return ORM::factory('user')->where('username', 'LIKE', '%'.$query.'%')->find_all();
	}
	
	public static function character($query)
	{
		return ORM::factory('character')->where('name', 'LIKE', '%'.$query.'%')->find_all();
	}
}