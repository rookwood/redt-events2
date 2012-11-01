<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Add extends Abstract_View_Page {

	public $page_title = 'Host new event';
	
	public function location_list()
	{
		foreach (Model_Location::$locations as $location)
		{
			if ($location->name === $this->event_data->location->name)
			{
				$out[] = array('value' => $location->name, 'name' => $location->name, 'selected' => TRUE);
			}
			else
			{
				$out[] = array('value' => $location->name, 'name' => $location->name);
			}
		}
		return $out;
	}

	public function characters()
	{
		return Model_Characer::list_all_by_user($this->user);
	}
}