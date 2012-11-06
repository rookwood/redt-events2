<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Character_Edit extends View_Page_Character_Add {

	/**
	 * @var  object  Character
	 */
	public $character;
	
	public $page_title = 'Edit character';
	
	public function form_action_character_edit()
	{
		return Route::url('character', array('action' => 'edit'));
	}

}