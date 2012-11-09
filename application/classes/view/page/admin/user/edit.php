<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_User_Edit extends Abstract_View_Admin_Layout {

	public $page_title = 'User Editor';
		
	public function form_action_admin_user_edit()
	{		
		return Route::url('admin group', array('controller' => 'user', 'action' => 'edit', 'id' => $this->user_data->id, 'name' => $this->user->username));
	}

	public function roles()
	{		
		$role_list = array();

		foreach ($this->role_data as $role)
		{
			$role_list[] = array(
				'id'          => $role->id,
				'name'        => $role->name,
				'description' => $role->description,
				'owned'       => (bool) $this->user_data->is_a($role),
			);
		}

		return $role_list;
	}		
}