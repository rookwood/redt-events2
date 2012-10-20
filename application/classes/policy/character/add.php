<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Character_Add extends Policy {

	const NOT_LOGGED_IN = 1;
	
	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		if (Auth::instance()->logged_in())
		{
			return TRUE;
		}
		
		return self::NOT_LOGGED_IN;
	}
}