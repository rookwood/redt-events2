<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_Dashboard_Index extends Abstract_View_Admin_Layout {

	public $page_title = 'Administrative Dashboard';
	
	/**
	 * @var  array  List of users to be displayed
	 */
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
				'edit_route'       => Route::url('admin group', array('controller' => 'user', 'action' => 'edit',    'name' => $user->username)),
				'deactivate_route' => Route::url('admin group', array('controller' => 'user', 'action' => 'disable', 'name' => $user->username)),
			);
		}
		
		return $user_list;
	}
	
	/**
	 * Gets data on all roles for display in table format
	 */	
	public function roles()
	{
		$role_list = array();

		foreach ($this->role_data as $role)
		{
			$role_list[] = array(
				'id'           => $role->id,
				'name'         => $role->name,
				'description'  => $role->description,
				'edit_route'   => Route::url('admin group', array('controller' => 'role', 'action' => 'edit', 'id' => $role->id, 'name' => $role->name)),
				'remove_route' => Route::url('admin group', array('controller' => 'role', 'action' =>'remove', 'id' => $role->id, 'name' => $role->name)),
			);
		}

		return $role_list;
	}
}