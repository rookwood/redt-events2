<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Event extends Abstract_Controller_Admin {

	public function action_index()
	{
		$this->view->event_data = ORM::factory('event')
			->where('time', '>', Date::from_server_time(time()) - Date::HOUR)
			->find_all();
		
		$this->view->user = $this->user;
	}
	
	public function action_reassign()
	{
	
	}
	
	public function action_cancel()
	{
	
	}
	
	public function action_edit()
	{
	
	}

}