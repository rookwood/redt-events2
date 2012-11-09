<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_User_Index extends Abstract_View_Admin_Layout {

	public $page_title = 'User List';
	
	public $user_data;
	
	public function users()
	{
		foreach ($this->user_data as $user)
		{
			$roles = array();

			// Get all of a user's roles (necessary step for any $_has_many relationship)
			foreach ($user->roles->find_all() as $role)
			{
				$roles[] = $role->name;
			}

			// Populate the array with any data we want to display
			$user_list[] = array(
				'id'               => $user->id,
				'username'         => $user->username,
				'name'             => $user->profile->first_name.' '.$user->profile->last_name,
				'email'            => $user->email,
				'timezone'         => $user->timezone,
				'roles'            => implode(', ', $roles),
				'edit_route'       => Route::url('admin group', array('controller' => 'user', 'action' => 'edit', 'id' => $user->id, 'name' => $user->username)),
				'deactivate_route' => (in_array('login', $roles)) ? Route::url('admin group', array('controller' => 'user', 'action' => 'disable', 'id' => $user->id, 'name' => $user->username)) : FALSE,
				'activate_route'   => ( ! in_array('login', $roles)) ? Route::url('admin group', array('controller' => 'user', 'action' => 'enable', 'id' => $user->id, 'name' => $user->username)) : FALSE,
			);
		}
		
		return $user_list;
	}

}