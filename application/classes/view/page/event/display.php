<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Display extends Abstract_View_Page {

	public $page_title = 'Event details';

	/**
	 * @var   object  Event that will be displayed
	 */
	public $event_data;

	/**
	 * Creates link to enroll in for this event
	 *
	 * @return  string  URL
	 */
	public function url_enroll()
	{
		return Route::url('event', array('action' => 'enroll', 'id' => $this->event_data->id));
	}
	
	/**
	 * Data to be displayed about this event
	 *
	 * @return  array  Event data
	 */
	public function event()
	{
		// Event alias
		$event = $this->event_data;
		
		// Calculate start time using user's time offset from UTC
		$local_start_time = Date::to_local_time($event->time, $this->user->timezone);
		
		// Event leader alias
		$host = $event->character;
		
		return array(
			'date'        => date('Y M d', $local_start_time),
			'time'        => date('g:i A ', $local_start_time).Date::timezone_abbr($this->user->timezone),
			'description' => $event->title,
			'status'      => $event->status->name,
			'host'        => $host->user->username,
			'host_as'     => $host->name,
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
			return $attendee_list;
		
		// Event alias
		$event = $this->event_data;
		
		$active_list  = $event->active_attendee_list();
		$standby_list = $event->standby_attendee_list();
		
		// Iterate through active attendees and pass their data to output
		foreach ($active_list as $enrollment)
		{			
			$character = $enrollment->character->find();

			$out['active'][] = array(
				'profession' => array_search($character->profession_id, Model_Profession::$profession_list),
				'name'       => $character->name,
				'comment'    => $enrollment->comment,
			);
		}
		
		// Iterate through standby attendees and pass their data to output
		foreach ($standby_list as $enrollment)
		{
			$out['standby'][] = array(
				'profession' => array_search($character->profession_id, Model_Profession::$profession_list),
				'name'       => $character->name,
				'comment'    => $enrollment->comment,
			);
		}
		
		// If no attendees yet, use 'no attendees' message, also caches attendee list
		return isset($out) ? $attendee_list = $out : FALSE;
	}
	
	/**
	 * List of user's characters
	 *
	 * @return  mixed  Array of characters if any present, FALSE if empty
	 */
	public function characters()
	{
		return Model_Character::list_all_by_user($this->user);
	}
	
	/**
	 * URL to edit this event
	 *
	 * @return  mixed  URL if allowed, FALSE if not
	 */
	public function edit_event()
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
	public function remove_event()
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
	public function withdraw()
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
	 * Tests if current user can see enrollment link
	 *
	 * @return  bool
	 */
	public function enroll()
	{
		return  TRUE === $this->user->can('event_enroll', array('event' => $this->event_data));
	}
}