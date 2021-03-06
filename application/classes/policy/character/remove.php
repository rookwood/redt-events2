<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Character_Remove extends Policy {

	const NOT_OWNER   = 1;
	const NOT_ALLOWED = 2;
	
	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		if ($user->owns($extras['character']))
		{
			return TRUE;
		}
		else if ($user->is_an('officer') OR $user->is_an('admin'))
		{
			return TRUE;
		}
		
		return self::NOT_OWNER;
	}
}