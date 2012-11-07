<?php defined('SYSPATH') or die('No direct script access.');

abstract class Abstract_Controller_Website extends Controller {

	/**
	 * @var  object  the content View object
	 */
	public $view;
	
	/**
	 * Checks user state (e.g. logged in) and attempts to set a default view based on the controller action
	 */
	public function before()
	{
		$this->session = Session::instance();
		
		// Auto login users if possible and cookie has been set
		if (! Auth::instance()->logged_in())
		{
			try {
				$auth = Auth::instance()->auto_login();
			}
			catch (Exception $e) {
				// Log error for investigation
				Kohana::$log->add(Log::ERROR, 'Error on Auth::auto_login');
			}
		}
		
		// Get our currently logged in user (or an empty user model for guest purposes)
		if (! isset($this->user))
		{
			$this->user = Auth::instance()->get_user();
			if ( ! isset($this->user->timezone))
			{
				$this->user->timezone = 'America/Chicago';
				
				$display_timezone_notice = Cookie::get('display_timezone_notice', TRUE);
				
				if ($display_timezone_notice)
				{
					// Don't spam users with this on every pageview
					Cookie::set('display_timezone_notice', FALSE);
					Notices::info('Until you log in, all times are displayed in the America/Chicago timezone.', FALSE);
				}
			}
		}
		
		// Set default title and content views (path only)
		$directory  = $this->request->directory();
		$controller = $this->request->controller();
		$action     = $this->request->action();

		// Removes leading slash if this is not a subdirectory controller
		$controller_path = trim($directory.'/'.$controller.'/'.$action, '/');

		try
		{
			$this->view = Kostache::factory('page/'.$controller_path)
				->assets(Assets::factory());
		}
		catch (Kohana_Exception $x)
		{
			// The View class could not be found, so the controller action is repsonsible for making sure this is resolved.
			$this->view = NULL;
		}

		return parent::before();
	}

	/**
	 * Ensures that a view has been set and passes it to the response object.
	 */
	public function after()
	{
		// If content is NULL, then there is no View to render
		if ($this->view === NULL)
			throw new Kohana_Exception('There was no View created for this request.');

		// Display debug information in debug and testing environments
		$this->view->profiler = Kohana::$environment > Kohana::TESTING;
		
		// Don't render layout on ajax requests
		if ($this->request->is_ajax())
			$this->view->render_layout = FALSE;
		
		$this->response->body($this->view);
	}

	/**
	 * Returns true if post request and has a valid CSRF
	 *
	 * @return  bool
	 */
	public function valid_post()
	{
		if ($this->request->method() !== HTTP_Request::POST)
			return FALSE;

		if (Request::post_max_size_exceeded())
		{
			Notices::add('error', __('Max filesize of :max exceeded.', array(':max' => ini_get('post_max_size').'B')));
			return FALSE;
		}

		$csrf = $this->request->post('csrf');
		$has_csrf = ! empty($csrf);
		$valid_csrf = $has_csrf AND Security::check($csrf);

		if ($has_csrf AND ! $valid_csrf)
		{
			// CSRF was submitted but expired
			Notices::add('error', __('This form has expired. Please try submitting it again.'));
		
			return FALSE;
		}
		
		return $has_csrf AND $valid_csrf;
	}
}