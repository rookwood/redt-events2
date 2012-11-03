<?php defined('SYSPATH') or die('No direct script access.');

class Policy_User_Register extends Policy {

	const LOGGED_IN              = 1;
	const REGISTRATION_COMPLETED = 2;
	const REGISTRATION_CLOSED    = 3;
	
	public function execute(Model_ACL_User $user, array $array = NULL)
{
		// If already logged in, you obviously can't do it again
		if ( ! Auth::instance()->logged_in())
		{
			return TRUE;
		}
		else
		{
			return self::LOGGED_IN;
		}
		
		return FALSE;
	}
}