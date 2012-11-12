<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Character extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'user'       => array(),
		'profession' => array(),
		'race'       => array(),
	);
	
	protected $_has_many = array(
		'enrollment' => array('model' => 'enrollment'),
		'events'     => array(
			'through' => 'enrollment',
			'model'   => 'event',
		),
	);
		
	// Validation rules
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 19)),
				// To be valid names in game, must start with a letter, contain no numbers, and no more than one consecutive space
				array('regex', array(':value', '/^[a-zA-Z]+( [a-zA-Z]+)*$/')),
				array(array($this, 'unique'), array('name', ':value')),
			),
		);
	}

	public function filters()
	{
		return array(
			'name' => array(array('trim')),
		);
	}
	
	/**
	 * Create a new character
	 *
	 * @param  object  User who owns the character
	 * @param  array   Character data
	 * @param  array   Fields expected for creation
	 * @return object  ORM Character model
	 */
	public function create_character(Model_ACL_User $user, $values)
	{
		// Add user id for character relationship
		$values['user_id'] = $user->id;
		
		// Change profession name to appropriate id
		$professions = Model_Profession::$profession_list;
		$values['profession_id'] = $professions[$values['profession']];
		
		// Change race name to appropriate id
		$races = Model_Race::$race_list;
		$values['race_id'] = $races[$values['race']];
		
		// Sanitize user input
		$values['name'] = HTML::chars($values['name']);
		
		$expected = array(
			'name',
			'profession_id',
			'race_id',
			'user_id',
		);
		
		// Create the record
		return $this->values($values, $expected)->create();
	}
	
	/**
	 * Edit existing character
	 *
	 */
	public function edit_character($values)
	{
		// Change profession name to appropriate id
		$professions = Model_Profession::$profession_list;
		$values['profession_id'] = $professions[$values['profession']];
		
		// Change race name to appropriate id
		$races = Model_Race::$race_list;
		$values['race_id'] = $races[$values['race']];
		
		// Sanitize user input
		$values['name'] = HTML::chars($values['name']);
				
		$expected = array(
			'name',
			'profession_id',
			'race_id',
		);
		
		// Save values to model
		$this->values($values, $expected)->save();
		
		return $this;
	}
		
	/**
	 * Remove existing character
	 * 
	 * For reasons of maintaining historical data, character records are not deleted, 
	 * but merely made invisible in current / new listings
	 */
	public function remove_character()
	{
		if ($this->visibility == 1)
		{
			$this->visibility = 0;
			
			return $this->save();
		}
		else
		{
			throw new Kohana_Exception('Character has already been removed.');
		}
	}
	
	/**
	 * Restore previously "deleted" (i.e. hidden) characters
	 */
	public function unhide()
	{
		$this->visibility = 1;
		return $this->save();
	}
	
	/**
	 * Check if profession provided is present in the game
	 *
	 * @param  string  Name of profession to be checked
	 * @return bool
	 */
	public function valid_profession($profession)
	{
		return in_array($profession, Model_Profession::profession_list());
	}
	
	/**
	 * List all characters owned by user
	 *
	 * @param  object  Model_User
	 * @return array   List of characters and their profession
	 */
	public static function list_all_by_user(Model_ACL_User $user)
	{
		static $characters;
		
		if ( ! empty($characters))
			return $characters;
		
		foreach ($user->characters->find_all() as $character)
		{
			$characters[] = array('name' => $character->name, 'profession' => array_search($character->profession_id, Model_Profession::$profession_list));
		}
		
		return $characters;
	}
}