<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Edit extends View_Page_Event_Add {

	public $page_title = 'Edit event details';
	
	public $event_data;

	public function form_action_event_edit()
	{
		return Route::url('event', array('action' => 'edit', 'id' => $this->event_data->id));
	}


	public function scheduled_time()
	{
		return date('g:i a', Date::to_local_time($this->event_data->time, $this->user->timezone);
	}

	public function scheduled_date()
	{
		return date('Y-m-d', Date::to_local_time($this->event_data->time, $this->user->timezone);
	}
}