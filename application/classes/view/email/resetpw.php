<?php defined('SYSPATH') or die('No direct script access.');

class View_Email_Resetpw extends Abstract_View_Email {

	public function link()
	{
		$route = Route::url('user', array('action' => 'resetpw')).URL::query(array('key' => $this->key));
		
		return HTML::anchor(URL::base('http', FALSE).$route, URL::base('http', FALSE).$route);
	}
}