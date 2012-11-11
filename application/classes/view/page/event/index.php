<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Index extends Abstract_View_Page {

	public $page_title = 'Event listing';
	
	/**
	 * @var   object  All event data to be displayed on the page
	 */
	public $event_data;
	
	/**
	 * Builds an array of data for all events to be listed
	 *
	 * @return  mixed  (array) Event data or (bool) FALSE
	 */
	public function events()
	{	
		static $event_list;
		
		if ( ! empty($event_list))
			return $event_list;
		
		foreach($this->event_data as $event)
		{
			// Get number of enrolled players
			$player_count = $event->active_attendee_count();
			
			// Build event array
			$out[] = array(
				'details_link'  => Route::url('event', array('action' => 'display', 'id' => $event->id, 'title' => URL::title($event->title))),
				'date'          => date('F d, Y', Date::to_local_time($event->time, $this->user->timezone)),
				'time'          => date('g:i A ', Date::to_local_time($event->time, $this->user->timezone)).Date::timezone_abbr($this->user->timezone),
				'time_full'     => date('c',  Date::to_local_time($event->time, $this->user->timezone)),
				'title'         => $event->title,
				'status'        => $event->status->name,
				'host'          => ORM::factory('character', $event->character_id)->name,
				'location'      => $event->location->name,
				'player_count'  => $player_count,
				'player_total'  => $event->player_limit,
				'signup_status' => $this->player_count_status($player_count, $event->player_limit),
				'id'            => $event->id,
			);
		}
		
		return isset($out) ? $event_list = $out : FALSE;
	}
	
	/**
	 * URL pointing to form to add new event
	 *
	 * @return  mixed  (array) Event data or (bool) FALSE
	 */
	public function url_event_add()
	{
		if ($this->user->can('event_add'))
		{
			return Route::url('event', array('action' => 'add'));
		}
		
		return FALSE;
	}
	
	public function filters()
	{
		$filter_key = $this->filter;
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'time')),
			'text' => 'Start time',
			'key'  => 'time',
		);
				
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'mine')),
			'text' => 'My events',
			'key'  => 'mine',
		);
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'dungeon')),
			'text' => 'Dungeon',
			'key'  => 'dungeon',
		);
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'past')),
			'text' => 'Past events',
			'key'  => 'past',
		);
		
		foreach ($out['bottom'] as $filter)
		{
			if (array_search($filter_key, $filter) !== FALSE)
			{
				$out['top'] = $filter;
			}
		}

		// Reindex for mustache... not sure why this is necessary
		$out['bottom'] = array_values($out['bottom']);
		
		return $out;
	}
	
	protected function player_count_status($active, $total)
	{
		switch (TRUE)
		{
			case $active == 0:
				return 'empty';
			break;
			case ($active / $total) <= 0.5:
				return 'low';
			break;
			case $active >= $total:
				return 'full';
			break;
			default:
				return 'high';
			break;
		}
	}
}