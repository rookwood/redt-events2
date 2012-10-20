<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Extension of Kohana Notices by Synapse Studios
 * Adds extra static shortcut methods for php installations on < 5.3
 *
 */
class Notices extends Kohana_Notices {

	public static function error($message)
	{
		return Notices::add('error', 'msg_error', array('message' => $message, 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}
	
	public static function success($message)
	{
		return Notices::add('success', 'msg_success', array('message' => $message, 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}

	public static function warning($message)
	{
		return Notices::add('warning', 'msg_warning', array('message' => $message, 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}

	public static function info($message)
	{
		return Notices::add('info', 'msg_info', array('message' => $message, 'is_persistent' => FALSE, 'hash' => Text::random($length = 16)));
	}

}
