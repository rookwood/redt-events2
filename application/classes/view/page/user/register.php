<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_User_Register extends Abstract_View_Page {

	public $page_title = 'Create account';
	
	public function form_action_register()
	{
		return Route::url('user', array('action' => 'register'));
	}
}