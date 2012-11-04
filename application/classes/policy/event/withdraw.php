<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Event_Withdraw extends Policy {

	const NOT_ENROLLED      = 1;
	const START_TIME_PASSED = 2;
	
	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		// No modifying past events
		if ($extras['event']->time < Date::from_local_time(time(), date_default_timezone_get()))
		{
			return self::START_TIME_PASSED;
		}		
		
		if (Model_Enrollment::is_enrolled($user, $extras['event']))
		{
			return TRUE;
		}
		
		return self::NOT_ENROLLED;
	}
}