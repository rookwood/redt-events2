<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Admin user management gui
 */
class Controller_Admin_User extends Abstract_Controller_Admin {
		
	public function before()
	{
		$parent = parent::before();
		
		// Controller specific permissions
		if ( ! $this->user->can('admin_manage_users'))
			throw new HTTP_Exception_404;
		
		return $parent;
	}
	
	public function action_index()
	{
		$this->view->user_data = ORM::factory('user')->order_by('id', 'desc')->find_all();
		$this->view->user = $this->user;
	}
	
	public function action_edit()
	{				
		// Grab the profile
		$user = ORM::factory('user', array('id' => $this->request->param('id')));
		$profile = $user->profile;
		
		if ($this->valid_post())
		{
			// Extract user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
			$role_post    = Arr::get($this->request->post(), 'role',    array());
			
			try
			{
				// Update all user data
				$user->update_user($user_post, array('username', 'email', 'password'));
				
				// Update roles
				$user->update_roles($role_post);
				
				// Update all profile data
				$profile->edit_profile($profile_post);
				
				// User notification
				Notices::info('admin.user.edit.success');
				$this->request->redirect(Route::url('admin group'));
			}
			catch (ORM_Validation_Exception $e)
			{
				// User notification
				Notices::error((string) $e);
				$this->view->errors = $e->errors();
			}
		}
		
		// Pass our user object to the view for display		
		$this->view->user_data = $user;
		$this->view->profile   = $profile;
		$this->view->role_data = ORM::factory('role')->find_all();
		$this->view->user      = $this->user;
	}
	
	public function action_create()
	{
		// Valid CSRF, etc
		if ($this->valid_post())
		{
			// Extract the user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
			$role_post    = Arr::get($this->request->post(), 'role',    array());
			
			if ($user_post['password'] === $user_post['password_confirm'])
			{
				try
				{
					// For testing completion in case of validation failure
					$user_account_created = FALSE;
					
					// Create our user
					$user = ORM::factory('user')->register($user_post);
					
					// Add the 'login' role; without this new users will be unable to log in.
					$user->update_roles($role_post);
					
					// Mark account creation complete
					$user_account_created = TRUE;
					
					// Create the user's profile
					$profile = ORM::factory('profile')->create_profile($user, $profile_post);
				}
				catch (ORM_Validation_Exception $e)
				{
					// User notification
					Notices::error('generic.validation');
					
					// Undo account creation
					if ($user_account_created)
						$user->delete();
						
					$this->view->errors = $e->errors('validation');
					
					// We have no valid Model_User, so we have to pass the form values back directly
					$this->view->values = Arr::merge($user_post, $profile_post);
				}
			}
			else 
			{
				// User notification
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.create.password'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
						
			// Redirect back to the dashboard
			$this->request->redirect(Route::url('admin group'));
		}
		else 
		{
			// Empty user object needed for correct display
			$this->view->user = $this->user;
			$this->view->role_data = ORM::factory('role')->find_all();
		}
	}
	
	public function action_disable()
	{
		// Load user
		$user = ORM::factory('user', array('username' => $this->request->param('name')));
		
		// Remove login ability
		$user->remove_role('login');
			
		// User notification
		Notices::info('admin.user.disable.success');
		
		$this->request->redirect(Route::url('admin group'));
	}
	
	public function action_enable()
	{
		// Load user
		$user = ORM::factory('user', array('username' => $this->request->param('name')));
		
		// Add login ability
		$user->add_role('login');
			
		// User notification
		Notices::info('admin.user.enable.success');
		
		$this->request->redirect(Route::url('admin group'));
	}
	
	public function action_search()
	{		
		$this->view->search_result = Search::user($this->request->query('q', FALSE));
		$this->view->user = $this->user;
	}
}