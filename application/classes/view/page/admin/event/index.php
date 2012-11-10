<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_Event_Index extends Abstract_View_Admin_Layout {

	public $page_title = 'Manage events';
	
	public function events()
	{
		foreach ($this->event_data as $event)
		{
			$list[] = array(
				'event_title'  => $event->title,
				'location'     => $event->location->name,
				'host'         => $event->user->username,
				'character'    => $event->character->name,
				'time'         => date('r', $event->time),
				'status'       => $event->status->name,
				'url_reassign' => Route::url('admin group', array('controller' => 'event', 'action' => 'reassign', 'id' => $event->id, 'title' => URL::title($event->title))),
				'url_cancel'   => ($event->status_id == Model_Status::SCHEDULED) ? Route::url('admin group', array('controller' => 'event', 'action' => 'cancel', 'id' => $event->id, 'title' => URL::title($event->title))) : FALSE,
				'url_uncancel' => ($event->status_id == Model_Status::CANCELLED) ? Route::url('admin group', array('controller' => 'event', 'action' => 'uncancel', 'id' => $event->id, 'title' => URL::title($event->title))) : FALSE,
				'url_edit'     => Route::url('admin group', array('controller' => 'event', 'action' => 'edit', 'id' => $event->id, 'title' => URL::title($event->title))),
			);
		}

		return isset($list) ? $list : FALSE;
	}

}