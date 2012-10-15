<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Profile extends ORM {

	// Relationships
	protected $_has_one = array(
		'user' => array('model' => 'user'),
	);

	// Validation rules
	public function rules()
	{
		return array(
			'first_name' => array(
				array('alpha'),
				array('max_length', array(':value', 30)),
			),
			'last_name' => array(
				array('alpha'),
				array('max_length', array(':value', 45)),
			),
			'birthdate' => array(
				array('date'),
			),
			'from_gw1' => array(
				array('not_empty'),
			),
		);
	}
	
	/**
	 * Creates a new profile associated with the specified user
	 *
	 * @param object User object
	 * @param array  Values to be inserted
	 * @param array  List of expected values
	 */
	public function create_profile(Model_ACL_User $user, $values, $expected)
	{
		// Add the user id to the profile and list of expected values
		$values['user_id'] = $user->id;
		$expected[] = 'user_id';
		
		// Create the profile record
		return $this->values($values, $expected)->create();
	}
}