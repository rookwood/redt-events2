<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Event_Remove extends Policy {

	const START_TIME_PASSED = 1;
	const NOT_OWNER         = 2;

	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// No cancelling past events
		if ($extras['event']->time < Date::from_local_time(time(), date_default_timezone_get()))
		{
			return self::START_TIME_PASSED;
		}

		// Can cancel your own events
		if ($user->owns($extras['event']))
		{
			return TRUE;
		}
		else
		{
			// Admins and guild leadership can cancel all events
			if ($user->is_an('officer') OR $user->is_an('admin'))
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