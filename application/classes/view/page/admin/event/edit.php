<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Admin_Event_Edit extends Abstract_View_Admin_Layout {

	public $page_title = 'Edit event details';
	
	public $event_data;

	public function form_action_admin_event_edit()
	{
		return Route::url('admin group', array('controller' => 'event', 'action' => 'edit', 'id' => $this->event_data->id));
	}

	public function scheduled_time()
	{
		return date('g:i a', Date::to_local_time($this->event_data->time, $this->user->timezone));
	}

	public function scheduled_date()
	{
		return date('Y-m-d', Date::to_local_time($this->event_data->time, $this->user->timezone));
	}

	public function location_list()
	{
		if ( ! isset($this->event_data))
			$this->event_data = new Model_Event;
			
		foreach (Model_Location::$locations as $location => $id)
		{
			if ($id == $this->event_data->location_id)
			{
				$out[] = array('value' => $location, 'name' => $location, 'selected' => TRUE);
			}
			else
			{
				$out[] = array('value' => $location, 'name' => $location);
			}
		}
		return $out;
	}

	public function characters()
	{
		return Model_Character::list_all_by_user($this->user);
	}
}