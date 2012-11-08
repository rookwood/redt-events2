<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Contains elements to be displayed on the page
 */
class Abstract_View_Page extends Abstract_View_Layout {
	
	/**
	 * @var    string  Site title to be appended to all page titles
	 */
	public $site_title = '[redt]Events 2';
	
	/**
	 * @var    string  Default page title
	 */
	public $page_title = '';
	
	/**
	 * @var  object  Current user
	 */
	public $user;
	
	protected $_partials = array(
		'header'          => 'partials/header',
		'main_navigation' => 'partials/main_navigation',
	);
	
	/**
	 * Displayed page title
	 *
	 * @return  string  Page title
	 */
	public function title()
	{
		return __($this->page_title).' &#8226; '.__($this->site_title);
	}
	
	/**
	 * Display notifications to the user, most commonly errors
	 *
	 * @return  string  html data for notice display
	 */
	public function notices()
	{
		$notices = array();
		
		$config = Kohana::$config->load('notices');
		
		if ($config->get('output') == 'json')
		{
			$notices = Notices::get(TRUE, TRUE);
			
			return (empty($notices)) ? FALSE : json_encode($notices);
		}
		else
		{
			// Get all current notifications
			foreach (Notices::get(TRUE, TRUE) as $notice)
			{			
				// Build our data array
				$notices[] = (object) array(
					'type'          => $notice['type'],
					'message'       => $notice['values']['message'],
					'hash'          => $notice['values']['hash'],
					'is_persistent' => $notice['values']['is_persistent'],
					'key'           => $notice['key']
				);
			}
		
			$output = '';
			
			foreach ($notices as $notice)
			{
				// Build notice html
				$output .= View::factory('notices/notice')->set('notice', $notice);
			}
			
			return $output;
		}
	}
	
	/**
	 * List of navigation links to be used in menu bar
	 * Child classes should set their own and Arr::merge() with 
	 * parent::links() as needed.
	 *
	 * The links array is indexed with each element being an associative
	 * array with this structure:
	 * 'location' => URL for link
	 * 'text'     => Anchor text
	 * 'icon'     => [optional] Image prepended to link text
	 *
	 * @return  Array  List of links
	 */
	public function links()
	{
		if ( ! $this->user)
		{		
			$this->user = Auth::instance()->get_user();
		}
		
		$links[] = array(
			'location' => Route::url('event'),
			'text'     => 'Events',
		);

		// Link to resend verification email
		if (Auth::instance()->logged_in())
		{
			$links[] = array(
				'location' => Route::url('profile edit'),
				'text'     => 'My Profile'
			);
		}
		
		$links[] = array(
			'location' => Route::url('static', array('action' => 'shenanigans')),
			'text'     => 'Shenanigans Night',
		);
		
		$links[] = array(
			'location' => Route::url('static', array('action' => 'faq')),
			'text'     => 'FAQs',
		);
		
		return $links;
	}
	
	public function account_links()
	{
		// Check for administrative access
		if ($this->user->can('admin_access'))
		{
			$links[] = array(
				'location' => Route::url('admin dashboard'),
				'text'     => 'Admin dashboard',
			);
		}
		
		// New account registration link
		if ($this->user->can('user_register'))
		{
			$links[] = array(
				'location' => Route::url('user', array('action' => 'register')),
				'text'     => 'Create an account',
			);
		}
		
		// Login link
		if ($this->user->can('login'))
		{
			$links[] = array(
				'location' => Route::url('user', array('action' => 'login')),
				'text'     => 'Log in'
			);
		}
		else
		{
			$links[] = array(
				'location' => Route::url('user', array('action' => 'logout')),
				'text'     => 'Log out'
			);
		}
		
		return $links;
	}
	
	/**
	 * Uses Kohana's Security class to generate tokens
	 * providing basic protection agasint cross-site 
	 * request forgery.
	 *
	 * @return  string  HTML for hidden form element with CSRF token
	 */
	public function csrf()
	{
		return Form::hidden('csrf', Security::token());
	}
	
	/**
	 * Displays debug / profiling information in development environments
	 *
	 * @see     APPPATH./classes/abstract/controller/website.php line 80
	 * @return  string  HTML data for toolbar
	 */
	public function stats()
	{
		if ($this->profiler)
			return ProfilerToolbar::render(FALSE);
	}
	
	/**
	 * Set assets group to be used
	 */
	public function assets($assets)
	{
		$assets->group('default');
		return parent::assets($assets);
	}
	
	/**
	 * @return  array   Formatted list of timezones for use in <select>
	 */
	public function timezone_list()
	{
		$current_timezone = $this->user->timezone;
		
		foreach (Date::$timezone_list as $value => $name)
		{
		
			if ($value == $current_timezone)
			{
				$out[] = array('value' => $value, 'name' => $name, 'selected' => TRUE);
			}
			else
			{
				$out[] = array('value' => $value, 'name' => $name);
			}
		}
		
		return $out;		
	}	

}