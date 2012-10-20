<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Event_View extends Policy {

	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		// We're rather permissive about who can look at our junk
		return TRUE;
	}
}