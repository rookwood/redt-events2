<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Enroll extends Abstract_View_Page {

	public $page_title = 'Enrollment';

	public function characters()
	{
		return Model_Character::list_all_by_user($this->user);
	}
	
	public function form_action_event_enroll()
	{
		return Route::url('event', array('action' => 'enroll', 'id' => $this->event_data->id));
	}
}