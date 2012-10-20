<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Event_Reassign extends Policy {

	const NOT_ALLOWED       = 1;
	const START_TIME_PASSED = 2;
	
	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		// No modifying past events
		if ($extras['event']->time < Date::from_local_time(time(), date_default_timezone_get()))
		{
			return self::START_TIME_PASSED;
		}
		
		if ($user->is_an('officer') OR $user->is_an('admin'))
		{
			return TRUE;
		}
		
		return self::NOT_ALLOWED;
	}
}