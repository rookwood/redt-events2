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
		);
	}
	
	/**
	 * Creates a new profile associated with the specified user
	 *
	 * @param object User object
	 * @param array  Values to be inserted
	 * @param array  List of expected values
	 */
	public function create_profile(Model_ACL_User $user, $values)
	{
		// Add the user id to the profile and list of expected values
		$values['user_id'] = $user->id;
		
		// Expected values for profile creation
		$expected = array(
			'user_id',
			'first_name',
			'last_name',
			'birthdate',
		);
		
		// Create the profile record
		print "creating profile...";
		ob_flush();
		
		return $this->values($values, $expected)->create();
	}
	
	public function edit_profile($values)
	{
		$expected = array(
			'first_name',
			'last_name',
			'birthdate',
		);
		
		// Create the profile record
		return $this->values($values, $expected)->save();
	}
}