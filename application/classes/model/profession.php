<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Profession extends ORM {

	// These MUST correspond to their record IDs in the database
	const WARRIOR      = 1;
	const GUARDIAN     = 2;
	const THIEF        = 3;
	const ENGINEER     = 4;
	const RANGER       = 5;
	const NECROMANCER  = 6;
	const MESMER       = 7;
	const ELEMENTALIST = 8;
	
	// Relations
	protected $_has_many = array('character' => array());
	
	/**
	 * List of professions and their database IDs
	 *
	 * return  Array
	 */
	public static function profession_list()
	{
		return array
		(
			'warrior'      => 1,
			'guardian'     => 2,
			'thief'        => 3,
			'engineer'     => 4,
			'ranger'       => 5,
			'necromancer'  => 6,
			'mesmer'       => 7,
			'elementalist' => 8,
		);
	}
}