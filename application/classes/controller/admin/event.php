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
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Cancel the event (will be hidden from view)
		$event->cancel_event();
		
		Notices::success('event.remove.success');
		
		// Show event list
		$this->request->redirect(Route::url('admin group', array('controller' => 'event')));
	}
	
	public function action_uncancel()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Cancel the event (will be hidden from view)
		$event->uncancel_event();
		
		Notices::success('event.uncancel.success');
		
		// Show event list
		$this->request->redirect(Route::url('admin group', array('controller' => 'event')));
	}
	
	public function action_edit()
	{
	
	}

}