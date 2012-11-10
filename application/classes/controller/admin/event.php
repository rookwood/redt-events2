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
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		if ( ! $event->loaded())
			throw new HTTP_Exception_404;
		
		if ($this->valid_post())
		{
			$post = Arr::get($this->request->post(), 'reassign', array());
			
			try
			{
				$event->reassign_owner($post['character']);
			}
			catch (Kohana_Exception $e)
			{
				Notices::error('admin.event.reassign.character');
			}

				$this->request->redirect(Route::url('admin group', array('controller' => 'event')));
		}
		
		$this->view->user = $this->user;
		$this->view->event_data = $event;
	}
	
	public function action_user()
	{
		if ( ! $this->request->is_ajax())
			throw new HTTP_Exception_404;
		
		$this->view = json_encode(Search::user($this->request->query('q')));
	}
	
	public function action_character()
	{
		if ( ! $this->request->is_ajax())
			throw new HTTP_Exception_404;
		
		$this->view->data = Search::character($this->request->query('q'));
		$this->view->user = $this->user;
	}
	
	public function action_cancel()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		if ( ! $event->loaded())
			throw new HTTP_Exception_404;
			
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
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		if ( ! $event->loaded())
			throw new HTTP_Exception_404;
		
		// Valid csrf, etc.
		if ($this->valid_post())
		{
			// Get event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			try
			{
				// Save data to event object
				$event->edit_event($event_post);
				
				Notices::success('event.edit.success');
				
				// Display edited event
				$this->request->redirect(Route::url('admin group', array('controller' => 'event')));
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->view->errors = $e->errors('event');
				$this->view->values = $event_post;
			}
		}
		else
		{
			$this->view->values = $event->as_array();
		}
		
		// Pass event object to the view class
		$this->view->event_data = $event;
		$this->view->user = $this->user;
		
	}

}