<?php defined('SYSPATH') or die('No direct script access.');

class View_Email_Resetpw extends Abstract_View_Email {

	public function link()
	{
		$route = Route::url('user', array('aciton' => 'resetpw')).URL::query(array('key' => $this->key));
		
		return HTML::anchor($route, URL::base(TRUE, TRUE).$route);
	}
}