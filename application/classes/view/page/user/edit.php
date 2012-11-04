<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_User_Edit extends Abstract_View_Page {

	public $page_title = 'Profile Editor';
	
	/**
	 * @var  array  User's characters
	 */
	public $characters;
		
	public function form_action_profile_edit()
	{		
		return Route::url('profile edit');
	}
	
	public function form_action_character_add()
	{
		return Route::url('character', array('action' => 'add'));
	}

	public function profession_list()
	{
		foreach (Model_Profession::$profession_list as $profession => $id)
		{
			$list[] = array(
				'name' => $profession,
				'id'   => $id,
			);
		}
		
		return $list;
	}
	
	public function race_list()
	{
		foreach (Model_Race::$race_list as $race => $id)
		{
			$list[] = array(
				'name' => $race,
				'id'   => $id,
			);
		}
		
		return $list;
	}
}