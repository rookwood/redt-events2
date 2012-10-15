<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Event extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'location' => array(),
		'user'     => array(),
		'status'   => array(),
		'build'    => array(),
		'status'   => array(),
	);
	
	protected $_has_many   = array(
		'enrollments' => array('model' => 'enrollment'),
		'characters' => array(
			'model'    => 'character',
			'through'  => 'signups',
		),
	);
	
	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 50)),
			),
			'time' => array(
				array('not_empty'),
			),
		);
	}
	
	/**
	 * Labels for fields in this model
	 *
	 * @return array Labels
	 */
	public function labels()
	{
		return array(
			'time'             => 'Time',
			'title'            => 'Event title',
		);
	}
}