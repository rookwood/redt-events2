<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => '',
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Set cookie and session properties
 */
$cookie_config = Kohana::$config->load('cookie');

Cookie::$salt = $cookie_config->get('salt');

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// 'profiler'   => MODPATH.'profilertoolbar', // Alert's Profiler Toolbar
	'assets'        => MODPATH.'assets',          // Synapse Studio's asset manager
	'auth'          => MODPATH.'auth',            // Basic authentication
	// 'cache'      => MODPATH.'cache',           // Caching with multiple backends
	'database'      => MODPATH.'database',        // Database access
	'email'         => MODPATH.'email',           // Kohana wrapper for SwiftMailer
	'kostache'      => MODPATH.'kostache',        // Class-based views / logicless templates
	'minion'        => MODPATH.'minion',          // Tool for creating shell-based interaction
	'migrations'    => MODPATH.'migrations',      // Minion task for database migrations
	'notices'       => MODPATH.'notices',         // Synapse Studios - user notification
	'orm'           => MODPATH.'orm',             // Object Relationship Mapping
	'vendo-acl'     => MODPATH.'acl',             // Vendo's policy-based authorization system
));

/**
 * Attach a database reader to config
 */
Kohana::$config->attach(new Config_Database);

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('user', '<action>(/<name>)', array('action' => 'register|resetpw|email|check|login|logout'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'profile',
	));
	
Route::set('profile edit', 'profile/edit')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'edit',
	));
	
Route::set('profile', 'profile(/<name>)')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'profile',
	));

Route::set('event', 'event(/<action>(/<id>))(/<title>)', array('action' => 'display|add|edit|remove|withdraw|enroll'))
	->defaults(array(
		'controller' => 'event',
		'action'     => 'index',
	));
	
Route::set('static', '<action>', array('action' => 'news|faq|shenanigans'))
	->defaults(array(
		'controller' => 'static',
		'action'     => 'news',
	));
 
Route::set('character', 'character(/<action>(/<id>(/<name>)))', array('action' => 'add|edit|remove'))
	->defaults(array(
		'controller' => 'character',
		'action'     => 'add',
	));
Route::set('default', '')
	->defaults(array(
		'controller' => 'static',
		'action'     => 'news',
	));

Route::set('admin dashboard', 'admin(/<action>)', array('action' => 'settings'))
	->defaults(array(
		'directory'  => 'admin',
		'controller' => 'dashboard',
		'action'     => 'index',
	));
		
Route::set('admin group', 'admin/<controller>(/<action>(/<id>(/<name>)))', array('controller' => 'user|event'))
	->defaults(array(
		'directory'  => 'admin',
		'controller' => 'user',
		'action'     => 'index',
	));