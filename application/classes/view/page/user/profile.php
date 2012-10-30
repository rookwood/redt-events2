<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_User_Profile extends Abstract_View_Page {

	public $page_title = 'Member profiler';
	
	/**
	 * @var  object  Model_User whose profile is displayed
	 */
	public $profile;
	
	/**
	 * @var  array  User's characters
	 */
	public $characters;

}