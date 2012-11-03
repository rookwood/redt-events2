<?php
/**
 * Extending auth class to provide visitor support
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
abstract class Auth extends Kohana_Auth
{
	static $salt = NULL;

	/**
	 * Gets the currently logged in user from the session.
	 * Creates a non-saved user object no user is currently logged in.
	 *
	 * @return Model_User
	 */
	public function get_user($default = NULL)
	{
		$status = $this->_session->get($this->_config['session_key'], FALSE);

		if ( ! $status)
		{
			$user = new Model_User;
			$this->_session->set($this->_config['session_key'], $user);
			return $user;
		}

		return $status;
	}

	/**
	 * Check if there is an active session. Optionally allows checking for a
	 * specific role.
	 *
	 * @param   string   role name
	 * @return  mixed
	 */
	public function logged_in($role = NULL)
	{
		return $this->get_user()->loaded();
	}

	/**
	 * Overload auth's hashing with Bcrypt
	 *
	 * @return string
	 */
	public function hash($password, $salt = NULL)
	{
		return Bcrypt::hash($password);
	}

	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_password($password)
	{
		die('hash check');
		
		if (Kohana::$environment > Kohana::TESTING)
			ProfilerToolbar::addData('checking hash', 'password');

		$user = $this->get_user();

		if ($user === FALSE)
		{
			// nothing to compare
			return FALSE;
		}
		
		$status = Bcrypt::check($password, $user->password);
		
		if (Kohana::$environment > Kohana::TESTING)
			ProfilerToolbar::addData($status, 'password');
		
		return $status;
	}
}