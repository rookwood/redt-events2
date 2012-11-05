<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Status extends ORM {

	// These MUST correspond to their record IDs in the database
	const SCHEDULED         = 1;
	const CANCELLED         = 2;
	const READY             = 3;
	const STANDBY_FORCED    = 4;
	const STANDBY_VOLUNTARY = 5;
	
	public static $status_list = array(
		'scheduled'         => 1,
		'cancelled'         => 2,
		'ready'             => 3,
		'standby_forced'    => 4,
		'standby_voluntary' => 5,
	);
	
	// Relations
	protected $_has_many = array('character' => array());
	
	public static function to_status_code($status)
	{
		return self::$status_list[$status];
	}
}