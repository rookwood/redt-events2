<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_User_Resetform extends Abstract_View_Page {

	public $page_title = 'Reset password';
	
	public function form_action_password_reset()
	{
		return Route::url('user', array('action' => 'password'));
	}
	
	public function hidden_key()
	{
		return Form::hidden('key', $this->key);
	}

}