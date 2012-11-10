<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_Event_Character extends Abstract_View_Admin_Layout {

	public $data;
	
	public function result()
	{
		if ( ! empty($this->data))
		{
			foreach ($this->data as $row)
			{
				$array[] = array('name' => $row->name, 'profession' => $row->profession->name);
			}
			
			return json_encode($array);
		}
		return 'None found.';
	}

}