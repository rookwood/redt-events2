<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_Event_Reassign extends Abstract_View_Admin_Layout {

	public $page_title = 'Reassign owner';

	public function form_action_admin_event_reassign()
	{
		return Route::url('admin group', array('controller' => 'event', 'action' => 'reassign', 'id' => $this->event_data->id));
	}
}