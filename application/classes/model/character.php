<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Character extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'user'       => array(),
		'profession' => array(),
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
		$profession = ORM::factory('profession', array('name' => $values['profession']));
		$values['profession_id'] = $profession->id;
		
		// Change race name to appropriate id
		$race = ORM::factory('race', array('name' => $values['race']));
		$values['profession_id'] = $profession->id;
		
		unset($values['profession']);
		unset($values['race']);
		
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
	public function edit_character($data)
	{
		// Change string values to ID
		$profession = ORM::factory('profession', array('name' => $data['profession']));
		$race       = ORM::factory('race',       array('name' => $data['race']));

		$data['profession_id'] = $profession->id;
		$data['race_id']       = $race->id;
				
		$expected = array(
			'name',
			'profession_id',
			'race_id',
		);
		
		// Save values to model
		$this->values($data, $expected)->save();
		
		return $this;
		
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
	 * Check if profession provided is present in the game
	 *
	 * @param  string  Name of profession to be checked
	 * @return bool
	 */
	public function valid_profession($profession)
	{
		return in_array($profession, Model_Profession::profession_list());
	}	
}