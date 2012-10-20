<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Event_Bump extends Policy {

	const START_TIME_PASSED = 1;
	const NOT_OWNER         = 2;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// No modifying past events
		if ($extras['event']->time < Date::from_local_time(time(), date_default_timezone_get()))
		{
			return self::START_TIME_PASSED;
		}
		
		// Can modify your own events
		if ($user->owns($extras['event']))
		{
			return TRUE;
		}
		else
		{
			// Admins and guild leadership can modify all events
			if ($user->is_a('leadership') OR $user->is_an('admin'))
			{
				return TRUE;
			}
			else
			{
				return self::NOT_OWNER;
			}
		}
		return FALSE;
	}
}