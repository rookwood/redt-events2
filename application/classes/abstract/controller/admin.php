<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Abstract_Controller_Admin extends Abstract_Controller_Website {
	
	public function before()
	{
		// Save any retrun value from the parent
		// This function also sets $this->user
		$parent = parent::before();
		
		if ( ! $this->user->can('admin_access'))
		{
			throw new HTTP_Exception_404;
		}
		
		return $parent;
	}
}