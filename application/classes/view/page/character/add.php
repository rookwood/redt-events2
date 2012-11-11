<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Character_Add extends Abstract_View_Page {

	public function form_action_character_add()
	{
		return Route::url('character', array('action' => 'add'));
	}

	public function profession_list()
	{
		if (isset($this->character))
		{
			$selected_profession = $this->character->profession->name;
		}
		else
		{
			$selected_profession = FALSE;
		}
		
		foreach (Model_Profession::$profession_list as $profession => $id)
		{
			$list[] = array(
				'name'     => ucwords($profession),
				'id'       => $id,
				'selected' => $profession == $selected_profession,
			);
		}
		
		return $list;
	}
	
	public function race_list()
	{
		if (isset($this->character))
		{
			$selected_race = $this->character->race->name;
		}
		else
		{
			$selected_race = FALSE;
		}
		
		foreach (Model_Race::$race_list as $race => $id)
		{
			$list[] = array(
				'name'     => ucwords($race),
				'id'       => $id,
				'selected' => $race == $selected_race,
			);
		}
		
		return $list;
	}
}