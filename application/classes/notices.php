<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Extension of Kohana Notices by Synapse Studios
 * Adds extra static shortcut methods for php installations on < 5.3
 */
class Notices extends Kohana_Notices {

	/**
	 * @var   string  Filename containing messages
	 */
	public static $message_file = 'events2';

	/**
	 * Error message shortcut
	 *
	 * @param   string  Message or message key to be displayed
	 * @param   bool    Expand key to message
	 * @return  void
	 */
	public static function error($message, $expand = TRUE)
	{
		if ($expand)
		{
			$message = self::expand($message);
		}
		
		return Notices::add('error', 'msg_error', array('message' => __($message), 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}
	
	/**
	 * Unauthorized message shortcut
	 *
	 * @param   string  Message or message key to be displayed
	 * @param   bool    Expand key to message
	 * @return  void
	 */
	public static function denied($message, $expand = TRUE)
	{
		if ($expand)
		{
			$message = self::expand($message);
		}
		
		return Notices::add('denied', 'msg_error', array('message' => __($message), 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}
	
	/**
	 * Success message shortcut
	 *
	 * @param   string  Message or message key to be displayed
	 * @param   bool    Expand key to message
	 * @return  void
	 */
	public static function success($message, $expand = TRUE)
	{
		if ($expand)
		{
			$message = self::expand($message);
		}
		
		return Notices::add('success', 'msg_success', array('message' => __($message), 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}

	/**
	 * Warning message shortcut
	 *
	 * @param   string  Message or message key to be displayed
	 * @param   bool    Expand key to message
	 * @return  void
	 */
	public static function warning($message, $expand = TRUE)
	{
		if ($expand)
		{
			$message = self::expand($message);
		}
		
		return Notices::add('warning', 'msg_warning', array('message' => __($message), 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}

	/**
	 * Informational message shortcut
	 *
	 * @param   string  Message or message key to be displayed
	 * @param   bool    Expand key to message
	 * @return  void
	 */
	public static function info($message, $expand = TRUE)
	{
		if ($expand)
		{
			$message = self::expand($message);
		}
		
		return Notices::add('info', 'msg_info', array('message' => __($message), 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}

	/**
	 * Shortcut method for Kohana::message
	 *
	 * @param  string  Message key
	 */
	public static function expand($message)
	{
		return Kohana::message(self::message_file, $message);
	}
	
}
