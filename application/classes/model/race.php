<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Race extends ORM {

	// These MUST correspond to their record IDs in the database
	const HUMAN   = 1;
	const SYLVARI = 2;
	const CHAR    = 3;
	const ASURA   = 4;
	const NORN    = 5;
	
	// Relations
	protected $_has_many = array('character' => array());
	
	/**
	 * List of races and their database IDs
	 *
	 * return  Array
	 */
	public static $race_list = array(
		'human'   => 1,
		'sylvari' => 2,
		'charr'    => 3,
		'asura'   => 4,
		'norn'    => 5,
	);
}