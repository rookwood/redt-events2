<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Event_Add extends Policy {

	const NOT_ALLOWED   = 1;
	const NO_CHARACTERS = 2;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// Admins, guild leadership, and event coordinators can all create events
		if ($user->is_a('verified'))
		{
			return TRUE;
		}
		else if($user->characters->count_all() == 0)
		{
			return self::NO_CHARACTERS;
		}
		else
		{
			return self::NOT_ALLOWED;
		}
	}
}