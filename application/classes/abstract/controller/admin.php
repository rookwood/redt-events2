<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Abstract_Controller_Admin extends Abstract_Controller_Website {
	
	public function before()
	{
		// Save any retrun value from the parent
		$parent = parent::before();
		
		if ( ! $this->user->is_an('officer'))
		{
			// throw new HTTP_Exception_403('Not authorized to access this section');
			Notices::error('You are not authorized for that action', FALSE);
			
			// Go to login if needed, otherwise simply return to home
			$this->request->redirect(Auth::instance()->logged_in() ? 
				Route::url('user', array('action' => 'welcome')) :
				Route::url('user', array('action' => 'login')));
		}
		
		return $parent;
	}
}