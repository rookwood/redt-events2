<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_User_Edit extends Abstract_View_Page {

	public $page_title = 'Profile Editor';
	
	public function form_action_profile_edit()
	{
		return Route::url('user', array('action' => 'edit'));
	}

}