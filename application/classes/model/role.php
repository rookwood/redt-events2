<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model for roles used as part of the ACL system
 *
 * All roles to be used as part of message-based policy checks (in lieu
 * of writting full policy classes) must be listed as constants in this class.
 */
class Model_Role extends ORM {

	// Roles used in the application
	const LOGIN      = 1;
	const ADMIN      = 2;
	const VERIFIED   = 3;
	const OFFICER    = 4;
	const LEADERSHIP = 5;
	
	// Relationships
	protected $_has_many = array(
		'users' => array('model' => 'user', 'through' => 'roles_users'),
	);
}