<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_User extends Abstract_Controller_Website {

	/**
	 * View and update the user's information
	 */
	public function action_edit()
	{
		// Does this user have permision to edit their profile?
		if (! $this->user->can('user_profile_edit'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			if ($status === Policy_Edit_Own_Profile::NOT_LOGGED_IN)
			{			
				Notices::error('user.edit.not_logged_in');

				$this->session->set('follow_login', $this->request->url());
				$this->request->redirect(Route::url('user', array('controller' => 'user', 'action' => 'login')));
			}
			else if ($status === Policy_Edit_Own_Profile::NOT_ALLOWED)
			{
				Notices::denied('user.edit.not_allowed');

				$this->request->redirect(Route::url('default'));
			}
		}
		
		// Is the form submitted correctly w/ CSRF token?
		if ($this->valid_post())
		{
			// Extract user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
			
			// Don't let the username change
			unset($user_post['username']);
			
			try 
			{
				// Update all user data
				$this->user->update_user($user_post, array('email', 'password', 'timezone'));
				
				// Update all profile data
				$this->user->profile->values($profile_post);
				$this->user->profile->save();
				// User notification
				Notices::info('user.edit.success');
			}
			catch (ORM_Validation_Exception $e)
			{
				// User notification
				Notices::error('generic.validation');
				
				$this->view->errors = $e->errors('validation');
				
				// We have no valid Model_User, so we have to pass the form values back directly
				$this->view->values = Arr::merge($user_post, $profile_post);
			}
		}
		
		// Pass our user object to the view for display
		$this->view->user    = $this->user;
		$this->view->profile = $this->user->profile;
	}
	
	/**
	 * User registration page and form processing
	 */
	public function action_register()
	{		
		if ( ! $this->user->can('register'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;

			if ($status === Policy_Register::REGISTRATION_COMPLETED)
			{
				Notices::info('user.registration.completed');

				$this->request->redirect(Route::url('default'));
			}
			else if ($status === Policy_Register::REGISTRATION_CLOSED)
			{
				Notices::denied('user.registration.not_allowed');

				$this->request->redirect(Route::url('default'));
			}
		}
		
		// If the form is submitted via POST and the CSRF token is valid
		if ($this->valid_post())
		{
			// Extract the user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
				
			if ($user_post['password'] === $user_post['password_confirm'])
			{
				try 
				{
					// Create our user
					$user = ORM::factory('user')->create_user($user_post, array('username', 'email', 'password', 'timezone'));
					
					// Add the 'login' role; without this new users will be unable to log in.
					$user->add('roles', ORM::factory('role')->where('name', '=', 'login')->find());
					
					// Create the user's profile
					$profile = ORM::factory('profile')->create_profile($user, $profile_post, array('first_name', 'last_name', 'birthdate'));
					
					// Creation complete, log in the user
					$login = Auth::instance()->login($user_post['username'], $user_post['password'], FALSE);
					
					// Check if email verification is required to complete registration
					$config = Kohana::$config->load('registration');
					
					if ($config->get('require_email_verification'))
					{
						$this->request->redirect(Route::url('email registration'));
					}
					else
					{
						// No email verification required
						$user->add_role('verified_user');
						
						// Redirect to the main page
						$this->request->redirect(Route::url('default'));
					}
					
				}
				catch (ORM_Validation_Exception $e)
				{
					// User notification
					Notices::error('generic.validation');
					
					$this->view->errors = $e->errors('validation');
					
					// We have no valid Model_User, so we have to pass the form values back directly
					$this->view->values = Arr::merge($user_post, $profile_post);
				}
			}
			else 
			{
				// User notification
				Notices::error('user.registration.password');
				
				// We have no valid Model_User, so we have to pass the form values back directly
				$this->view->values = Arr::merge($user_post, $profile_post);
			}
		}
		else if ($this->request->method() == HTTP_Request::POST)
		{
			// User notification
			Notices::error('user.registration.invalid_post');
		}
	}
	
	/**
	 * Log-in page and form processing
	 */
	public function action_login()
	{
		if ( ! $this->user->can('login'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// This should be the only reason one cannot attempt a login
			if ($status === Policy_Login::LOGGED_IN)
			{			
				Notices::info('user.login.already_logged_in');

				$this->request->redirect(Route::url('default'));
			}
		}
	
		if ($this->valid_post())
		{
			// Extract the user data from $_POST
			$user_post = Arr::get($this->request->post(), 'user', array());
			$remember = array_key_exists('remember', $this->request->post()) ? (bool) $this->request->post('remember') : FALSE;
			
			// Try to log in
			$user = Auth::instance()->login($user_post['username'], $user_post['password'], $remember);
			
			if ($user)
			{
				// Check to see if we had saved a user destination before forcing login
				$follow_login = $this->session->get_once('follow_login', FALSE);
				
				if ($follow_login)
				{
					$this->request->redirect($follow_login);
				}
				else
				{
					$this->request->redirect(Route::url('default'));
				}
			}
			else
			{
			// User notification
			Notices::error('user.login.failed');
			}
		}
	}
	
	/**
	 * User log-out
	 */
	public function action_logout()
	{
		// Do the logout - destroys session, etc
		Auth::instance()->logout();
		
		// User notification
		Notices::info('user.logout.success');
		
		// Redirect
		$this->request->redirect(Route::url('user', array('controller' => 'user', 'action' => 'login')));
	}

	/**
	 * Notification that the user must verify their email address
	 * Users will be automatically redirected here by any controller extended Abstract_Controller_Verefied
	 */
	public function action_jail(){}

	/**
	 * Sends registration email to the current user
	 */
	public function action_email()
	{
		// Redirect if not logged in
		if ( ! Auth::instance()->logged_in())
		{
			$this->request->redirect(Route::url('user', array('action' => 'login')));
		}
		
		// Make sure this user needs / can receive registration email
		if ($this->user->can('registration_get_email'))
		{			
			// Set up values needed by Swift Mailer
			$email = Email::factory(Kohana::message('events2', 'user.registration_email.subject'), NULL)
				->message
				(
					// Using Kostache view for our message body
					Kostache::factory('email/registration')
						->set('user', $this->user)
						->set('key', $this->user->get_key('registration')),
					// MIME type
					'text/html'
				)
				->to($this->user->email)
				->from(Kohana::message('events2', 'user.registration_email.sender'))
				->send();
		}
		else
		{
			// Check why email registration verification is denied
			if (Policy::$last_code === Policy_Get_Registration_Email::REGISTRATION_COMPLETED)
			{
				Notices::error('user.registration_email.completed');

				$this->request->redirect(Route::url('default'));
			}
			else if (Policy::$last_code === Policy_Get_Registration_Email::ACCOUNT_DEACTIVATED)
			{
				Notices::error('user.registration_email.banned');

				$this->request->redirect(Route::url('default'));
			}
			else if (Policy::$last_code === Policy_Get_Registration_Email::NOT_REQUIRED)
			{
				Notices::error('user.registration_email.not_required');

				$this->request->redirect(Route::url('default'));
			}
			else
			{
				throw new HTTP_Exception_404;
			}
		}
	}
	
	public function action_resetpw()
	{
		// If submitted via form
		if ($this->valid_post())
		{
			$check_key = Arr::get($this->request->post(), 'key', FALSE);
			
			$key = ORM::factory('key', array('key' => $check_key));
			
			if ( ! $key->loaded())
			{
				Notices::error('user.registration.bad_key');
				$this->request->redirect(Route::url('default');
			}
			
			$password   = Arr::get($this->request->post(), 'password', FALSE);
			$pw_confirm = Arr::get($this->request->post(), 'password_confirm', FALSE);
			
			if ($password === $pw_confirm AND $password !== FALSE)
			{
				$user = $key->user;
				$user->change_password($password);
				
				Notices::success('user.registration.password_changed');
				$this->request->redirect(Route::url('default'));
			}
			else
			{
				Notices::error('user.registration.password');
				$this->view->key = $key->key;
			}
		}
		// If not submitted via form, user arrived via email link
		else
		{
			$check_key = Arr::get($this->request->query(), 'key', FALSE);
			
			$key = ORM::factory('key', array('key' => $check_key));
			
			// Does the key match?
			if ( ! $key->loaded())
			{
				throw new HTTP_Exception_404;
			}
			
			$this->view->key = $key->key;
		}
	}
	
	/**
	 * Checks that registration key submitted matches the one we created for our user
	 *
	 * @deprecated
	 */
	public function action_check()
	{
		// Relevant info from query string
		$check_key  = Arr::get($this->request->query(), 'key',      FALSE);
		$check_user = Arr::get($this->request->query(), 'username', FALSE);
		$action     = Arr::get($this->request->query(), 'action',  'registration');
		
		// Find our user
		$user = ORM::factory('user', array('username' => $check_user));
				
		// Compare keys
		if ($check_key === $user->get_key($action))
		{
			// Keys match, what did we just verify?
			switch($action)
			{
				case 'registration':
					// Set user verified flag
					$user->add_role('verified_user');
					
					Notices::success('user.registration_email.success');

					break;
				
				case 'reset_password':
					// Reset password to random string
					$new_pw = $user->reset_password();
					
					Notices::success('user.password_email.reset');

					break;
				
				// Add more cases here as needed
				
				default:
					throw new HTTP_Exception_404('Invalid check case.');
			}

			$this->request->redirect(Route::url('default'));
		}
		else
		{
			Notices::error('user.registration_email.bad_key');

			$this->request->redirect(Route::url('default'));
		}
	}

	/**
	 * Sends email for password reset
	 */
	public function action_lostpw()
	{
		if ( ! $this->user->can('user_reset_password'))
		{
			throw new HTTP_Exception_404('Policy failure');
		}
		
		if ($this->valid_post())
		{
			$this->user = ORM::factory('user')->where('email', '=', $this->request->post('email'))->find();
			
			// If no user found for provided email address
			if ( ! $this->user->loaded())
			{
				Notices::error('user.password_email.not_found');
			}
			else
			{
				// Build the email
				$email = Email::factory(Kohana::message('events2', 'user.password_email.subject'), NULL)
					->message
					(
						// Using Kostache view for our message body
						Kostache::factory('email/password')
							->set('user', $this->user),
						// MIME type
						'text/html'
					)
					->to($this->user->email)
					->from(Kohana::message('koreg', 'user.username_email.sender'))
					->send();
				
				Notices::success('user.password_email.success');
			}
		}

		// Pass user object to the view
		$this->view->user = $this->user;
	}
	
	/**
	 * Sends email with lost username
	 */
	public function action_lostname()
	{
		if ( ! $this->user->can('get_lost_username'))
		{
			throw new HTTP_Exception_404;
		}
		
		if ($this->valid_post())
		{
			$this->user = ORM::factory('user')->where('email', '=', $this->request->post('email'))->find();
			
			// If no user found for provided email address
			if ( ! $this->user->loaded())
			{
				Notices::error('user.username_email.not_found');
			}
			else
			{
				// Build the email
				$email = Email::factory(Kohana::message('events2', 'user.username_email.subject'), NULL)
					->message
					(
						// Using Kostache view for our message body
						Kostache::factory('email/username')
							->set('user', $this->user),
						// MIME type
						'text/html'
					)
					->to($this->user->email)
					->from(Kohana::message('events2', 'user.username_email.sender'))
					->send();
				
				Notices::success('user.username_email.success');
			}
		}
		
		$this->view->user = $this->user;
	}
}