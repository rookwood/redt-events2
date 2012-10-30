<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_User_Login extends Abstract_View_Page {

	public $page_title = 'Login';
	
	public function form_action_login()
	{
		return Route::url('user', array('action' => 'login'));
	}
	
	public function url_password_reset()
	{
		return Route::url('user', array('action' => 'resetpw'));
	}
	
	public function retrieval()
	{
		// Set as class property for later usage in retrieval_links()
		$this->config = Kohana::$config->load('lost_data');

		return $this->config->get('password_reset');
	}

	public function retrieval_links()
	{
		$links = array();

		if ($this->config->get('reset_password'))
		{
			$links[] = array(
				'url'  => $this->url_password_reset(),
				'text' => 'Forgot your password?',
			);
		}
		
		// Add more retrevial options here as needed
		
		return $links;
	}
}