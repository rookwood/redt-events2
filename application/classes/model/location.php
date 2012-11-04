<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Location extends ORM {

	public static $locations = array
	(
		'Ascalonian Catacombs (story)'         => 1,
		'Ascalonian Catacombs (explorable)'    => 2,
		'Caudecus\'s Manor (story)'            => 3,
		'Caudecus\'s Manor (explorable)'       => 4,
		'Twilight Arbor (story)'               => 5,
		'Twilight Arbor (explorable)'          => 6,
		'Sorrow\'s Embrace (story)'            => 7,
		'Sorrow\'s Embrace (explorable)'       => 8,
		'Citadel of Flame (story)'             => 9,
		'Citadel of Flame (explorable)'        => 10,
		'Honor of the Waves (story)'           => 11,
		'Honor of the Waves (explorable)'      => 12,
		'Crucible of Eternity (story)'         => 13,
		'Crucible of Eternity (explorable)'    => 14,
		'The Ruined City of Arah (story)'      => 15,
		'The Ruined City of Arah (explorable)' => 16,
		'Eternal Battlegrounds'                => 17,
		'Red Borderlands'                      => 18,
		'Green Borderlands'                    => 19,
		'Blue Borderlands'                     => 20,
		'WvW Location TBD'                     => 21,
		'Heart of the Mists'                   => 22,
		'Misc World PvE Zone'                  => 23,
		'Shenanigans Night'                    => 24,
	);
	
	// Relationships
	protected $_has_many = array('events' => array());

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 30)),
				array(array($this, 'unique'), array('name', ':value')),
			),
		);
	}
	
	public function add_location($data)
	{
		return $this->values($data, array('name'))->create();
	}
	
	public static function to_id($location)
	{
		return self::$locations[$location];
	}
}