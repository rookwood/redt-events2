<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Admin_Manage_Events extends Policy {

	const NOT_ALLOWED = 1;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		if ($user->is_an('admin') or $user->is_an('officer'))
		{
			return TRUE;
		}
		
		return self::NOT_ALLOWED;
	}

}