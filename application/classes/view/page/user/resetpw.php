<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_User_Resetpw extends Abstract_View_Page {

	public $page_title = 'Password reset request';
	
	public function form_action_password_reset()
	{
		return Route::url('user', array('action' => 'resetpw'));
	}

}