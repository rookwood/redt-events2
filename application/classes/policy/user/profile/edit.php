<?php defined('SYSPATH') or die('No direct script access.');

class Policy_User_Profile_Edit extends Policy {

	const NOT_OWNER = 1;
	
	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		/*
		if ($user->owns($extras['profile']))
		{
			return TRUE;
		}
		else if ($user->is_an('officer') OR $user->is_an('admin'))
		{
			return TRUE;
		}
		
		return self::NOT_OWNER;
		*/
		return TRUE;
	}
}