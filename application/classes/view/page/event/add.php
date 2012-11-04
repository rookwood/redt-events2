<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Add extends Abstract_View_Page {

	public $page_title = 'Host new event';
	
	public function form_action_event_add()
	{
		return Route::url('event', array('action' => 'add'));
	}
	
	public function location_list()
	{
		if ( ! isset($this->event_data))
			$this->event_data = new Model_Event;
			
		foreach (Model_Location::$locations as $location => $id)
		{
			if ($id === $this->event_data->location_id)
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