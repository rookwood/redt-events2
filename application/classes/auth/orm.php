<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Modificaiton of login to support Bcrypt hashing.  By default
 * Kohana assumes that Auth::hash() returns the same value every
 * time.  With Bcrypt generating its own salt, this is not the case.
 */
class Auth_ORM extends Kohana_Auth_ORM {
	
	/**
	 * Logs a user in.
	 *
	 * @param   string   $username
	 * @param   string   $password
	 * @param   boolean  $remember  enable autologin
	 * @return  boolean
	 */
	protected function _login($user, $password, $remember)
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = ORM::factory('user');
			$user->where($user->unique_key($username), '=', $username)->find();
			
			$this->user = $user;
		}
	
		// If the passwords match, perform a login
		if ($user->has('roles', ORM::factory('role', array('name' => 'login'))) AND $this->check_password($password))
		{
			if ($remember === TRUE)
			{
				// Token data
				$data = array(
					'user_id'    => $user->pk(),
					'expires'    => time() + $this->_config['lifetime'],
					'user_agent' => sha1(Request::$user_agent),
				);

				// Create a new autologin token
				$token = ORM::factory('user_token')
							->values($data)
							->create();

				// Set the autologin cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_password($password)
	{		
		$user = $this->user;

		if ($user === FALSE)
		{
			// nothing to compare
			return FALSE;
		}
		
		$status = Bcrypt::check($password, $user->password);
		
		return $status;
	}

}