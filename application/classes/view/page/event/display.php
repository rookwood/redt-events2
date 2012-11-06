<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Display extends Abstract_View_Page {

	/**
	 * @var   object  Event that will be displayed
	 */
	public $event_data;

	/**
	 * Creates link to sign up for this event
	 *
	 * @return  string  URL
	 */
	public function url_event_enroll()
	{
		return Route::url('event', array('action' => 'enroll', 'id' => $this->event_data->id, 'title' => URL::title($this->event_data->title)));
	}
	
	/**
	 * Data to be displayed about this event
	 *
	 * @return  array  Event data
	 */
	public function event()
	{
		$event = $this->event_data;
		
		// Calculate start time using user's time offset from GMT
		$local_start_time = Date::to_local_time($event->time, $this->user->timezone);
		
		// Event leader data
		$host = ORM::factory('character', $event->character_id);
		
		return array(
			'location'     => $event->location->name,
			'host'         => $host->user->username,
			'hostas'       => $host->name,
			'date'         => date('F d, Y', $local_start_time),
			'time'         => date('g:i A ', $local_start_time).Date::timezone_abbr($this->user->timezone),
			'time_full'    => date('c', $local_start_time),
			'title'        => $event->title,
			'description'  => $event->description,
			'status'       => $event->status->name,
			'event_url'    => Route::url('event').'#'.$event->id,
		);
	}
	
	/**
	 * Data to be displayed about all event attendees
	 *
	 * @return  mixed  Multi-dimensonal array with attendees grouped as active or standby (if 1+ attendees present) or FALSE if empty
	 */
	public function attendees()
	{
		// Cache results as this function causes a lot of database hits
		static $attendee_list;
		
		// Return cached results if available
		if ( ! empty($attendee_list))
		{
			return $attendee_list;
		}
		
		foreach ($this->event_data->active_attendee_list() as $attendee)
		{
			$out['active'][] = array(
				'name'       => $attendee->character->name,
				'profession' => $attendee->character->profession->name,
				'comment'    => $attendee->comment,
			);
		}
		
		foreach ($this->event_data->standby_attendee_list() as $attendee)
		{
			$out['standby'][] = array(
				'name'       => $attendee->character->name,
				'profession' => $attendee->character->profession->name,
				'comment'    => $attendee->comment,
			);
		}
		
		// If no attendees yet, use 'no signup' message, also caches attendee list
		return isset($out) ? $attendee_list = $out : FALSE;
	}
	
	/**
	 * List of user's characters
	 *
	 * @return  mixed  Array of characters if any present, FALSE if empty
	 */
	public function characters()
	{
		if (empty($this->characters))
			$this->characters =  Model_Character::list_all_by_user($this->user);
				
		return $this->characters;
	}
	
	/**
	 * URL to edit this event
	 *
	 * @return  mixed  URL if allowed, FALSE if not
	 */
	public function url_event_edit()
	{
		if ($this->user->can('event_edit', array('event' => $this->event_data)))
		{
			return Route::url('event', array('action' => 'edit', 'id' => $this->event_data->id));
		}
		return FALSE;
	}
	
	/**
	 * URL to cancel this event
	 *
	 * @return  mixed  URL if allowed, FALSE if not
	 */	
	public function url_event_remove()
	{
		if ($this->user->can('event_remove', array('event' =>$this->event_data)))
		{
			return Route::url('event', array('action' => 'remove', 'id' => $this->event_data->id));
		}
	}
	
	/**
	 * URL to withdraw from this event
	 *
	 * @return  mixed  URL if allowed, FALSE if not
	 */	
	public function url_event_withdraw()
	{
		if ($this->user->can('event_withdraw', array('event' => $this->event_data)))
		{
			return Route::url('event', array('action' => 'withdraw', 'id' => $this->event_data->id));
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Tests if current user can see enrollment form
	 *
	 * @return  bool
	 */
	public function enroll()
	{
		return  TRUE === $this->user->can('event_enroll', array('event' => $this->event_data, 'characters' => $this->characters()));
	}
	
	/**
	 * Player count for standby tab
	 *
	 * @return int
	 */
	public function standby_count()
	{
		return $this->event_data->standby_attendee_count();
	}
	
	/**
	 * Player count for active tab
	 *
	 * @return int
	 */
	public function attendee_count()
	{
		return $this->event_data->active_attendee_count();
	}
}