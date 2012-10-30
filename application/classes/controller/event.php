<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Event extends Abstract_Controller_Website {

	public function action_index()
	{
		$filter = Arr::get($this->request->query(), 'filter', 'default');
		$id     = Arr::get($this->request->query(), 'id',     FALSE);
			
		if ($filter == 'mine' AND ! Auth::instance()->logged_in())
		{
			Session::instance()->set('follow_login', Route::url('event').URL::query(array('filter' => $filter)));
			$this->request->redirect(Route::url('user', array('action' => 'login')));
		}
		
		// Pass events to the view class
		$this->view->event_data = Model_Event::filtered_list($filter, $this->user, $id);
		$this->view->filter_message = Kohana::message('gw', 'filter.'.$filter);
	}
	
	public function action_display()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
	
		if ( ! $event->loaded())
		{
			Notices::error('event.view.not_found');
			$this->request->redirect(Route::url('event'));
		}
		
		// Can user view this event?
		if ( ! $this->user->can('event_view', array('event' => $event)))
		{
			// Error notification
			Notices::error('event.view.not_allowed');
			$this->request->redirect(Route::url('event'));
		}
		
		// Pass event data to the view class
		$this->view->event_data = $event;
		$this->view->user = $this->user;
	}
	
	public function action_add()
	{
		// Can this user add new events?
		if ( ! $this->user->can('event_add'))
		{
			// Error notification
			Notices::error('event.add.not_allowed');
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf
		if ($this->valid_post())
		{
			// Extract event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			try
			{
				// Create new event object
				$event = ORM::factory('event')->create_event($this->user, $event_post);
				
				// Notification
				Notices::success('event.add.success');
				
				$event->enroll($this->user, $event_post['character'], Model_Status::SCHEDULED, 'Event leader');
				Notices::success('event.enroll.success');
				
				// Display created event
				$this->request->redirect(Route::url('event').'#'.$event->id);
				
			}
			catch(ORM_Validation_Exception $e)
			{
				if ( ! $event->loaded())
				{
					Notices::error('event.add.fail');
					
					$this->view->errors = $e->errors('validation');
					$this->view->values = $event_post;
				}
				else
				{
					Notices::error('event.enroll.on_create_failed');
					$this->request->redirect(Route::url('event').'#'.$event->id);
				}
			}
		}
		
		// Pass user to view object for character list, etc.
		$this->view->user = $this->user;
	}
	
	public function action_edit()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		if ( ! $event->loaded())
			throw new HTTP_Exception_404;
		
		// Can user edit this event's details?
		if ( ! $this->user->can('event_edit', array('event' => $event)))
		{
			// Error notification
			Notices::error('event.edit.not_allowed');
			$this->request->redirect(Route::url('event'));
		}
		
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
				$this->request->redirect(Route::url('event').'#'.$event->id);
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
		
	}
	
	public function action_remove()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user remove this event?
		if ( ! $this->user->can('event_remove', array('event' => $event)))
		{
			// Error notification
			Notices::error('event.remove.not_allowed');
			$this->request->redirect(Route::url('event'));
		}
		
		// Cancel the event (will be hidden from view)
		$event->cancel_event();
		
		Notices::success('event.remove.success');
		
		// Show event list
		$this->request->redirect(Route::url('event'));
	}
	
	public function action_enroll()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user sign-up for this event?
		if ( ! $this->user->can('event_enroll', array('event' => $event)))
		{
			// Error notification
			Notices::error('event.enroll.not_allowed');
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf, etc
		if ($this->valid_post())
		{
			// Extract event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			// Load character object
			$character = ORM::factory('character', array('name' => $event_post['character']));
			
			// Confirm that character exists
			if ( ! $character->loaded())
			{
				Notices::error('event.enroll.character_failed');
				$this->request->redirect(Route::url('character', array('action' => 'add')));
			}
			
			// Ensure that status is numeric for comparisons
			if ( ! is_numeric($event_post['status']))
			{
				$event_post['status'] = Model_Status::to_status_code($event_post['status']);
			}
			
			// Is the event full?
			if ($event_post['status'] === Model_Status::READY AND $event->active_attendee_count() >= $event->player_limit)
			{
				// Force player to standby list
				$event_post['status'] = Model_Status::STANDBY_FORCED;
			}
			
			$event->enroll($this->user, $character, $event_post['status'], $event_post['comment']);
		}
		else
		{
			// Not a valid post (came to this url directly or bad data)
			$this->request->redirect(Route::url('event'));
		}
	}

	public function action_withdraw()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Get all of the user's characters that might have signed up
		$characters = $this->user->characters->find_all();
		
		if ( ! $this->user->can('event_withdraw', array('event' => $event, 'characters' => $characters)))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// User wasn't actually signed-up for this event
			if ($status === Policy_Event_Withdraw::NOT_SIGNED_UP)
			{
				Notices::error('event.withdraw.not_signed_up');
				$this->request->redirect(Route::url('event'));
			}
			// Tried to cancel after event had started
			elseif ($status === Policy_Event_Withdraw::START_TIME_PASSED)
			{
				Notices::error('event.withdraw.start_time_passed');
				$this->request->redirect(Route::url('event'));
			}
			// Unspecified policy failure... this shouldn't really happen
			else
			{
				Notices::error('event.withdraw.failed');
				$this->request->redirect(Route::url('event'));
			}
		}
		// User may cancel - now we have to find where they signed-up and remove it
		else
		{
			// Get enrolled character
			$character = Model_Enrollment::is_enrolled($event, $characters);
			
			// Withdraw enrollment
			$event->withdraw($character);
			
			Notices::success('event.withdraw.success');
		}

		$this->request->redirect(Route::url('event'));
	}
}