<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Dashboard extends Abstract_Controller_Admin {

	public function action_index()
	{
		$this->view->user_data = ORM::factory('user')->order_by('id', 'desc')->limit(15)->find_all();
		$this->view->role_data = ORM::factory('role')->find_all();
	}
	
	/**
	 * Settings configurable through admin gui
	 */
	public function action_settings()
	{
		if ($this->valid_post())
		{
			$options_post = Arr::get($this->request->post(), 'options', array());
			
			foreach ($options_post as $group => $setting)
			{
				$config = Kohana::$config->load($group);
				
				foreach ($setting as $option => $value)
				{
					// Probably a better way to do this
					if (strpos($value, 'bool') !== FALSE)
					{
						$value = (bool) substr($value, 0, 1);
					}
					
					$config->set($option, $value);
				}
			}
			Notices::info('admin.settings.set.success');
		}
	}

}