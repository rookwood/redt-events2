<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_User_Create extends Abstract_View_Admin_Layout {

	public $page_title = 'Create user';
	
	public function form_action_user_create()
	{
		return Route::url('admin group', array('controller' => 'user', 'action' => 'create'));
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
			);
		}

		return $role_list;
	}
}