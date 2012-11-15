<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Enrollment extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'events'     => array(),
		'character'  => array(),
		'status'     => array(),
	);

	protected $_table_name = 'enrollment';
	
	public function filters()
	{
		return array(
			'description' => array(
				array('trim'),
				array('Security::xss_clean'),
			),
		);
	}
	
	/**
	 * Bumps the first person on forced stand-by to the active list
	 *
	 * @param    int    The id of the event to check
	 * @return   bool   True if someone was moved
	 */
	public static function check_bump_list($event_id)
	{
		// Check forced stand-by list
		$forced_standby = ORM::factory('enrollment')
			->where('event_id', '=', $event_id)
			->and_where('status_id', '=', Model_Status::STANDBY_FORCED)
			->order_by('timestamp', 'DESC')
			// Get only the record for the first person to be put on stand-by
			->find();
		
		// If an eligible person was found
		if ($forced_standby->loaded())
		{
			// Change from stand-by to active
			$forced_standby->status_id = Model_Status::READY;
			$forced_standby->save();
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Helper method used in adding details to newly created enrollment record
	 *
	 * @param   int      Status of new record (e.g. ready, stand-by (forced), etc)
	 * @param   string   Enrollment comment left by user
	 * @return  ORM      Enrollment object
	 */
	public function details($status, $comment)
	{
		if ( ! is_numeric($status))
			$status = Model_Status::to_status_code($status);
			
		// Set status
		$this->status_id = $status;
		
		// Filter out html special characters
		$this->comment = HTML::chars($comment);
		
		return $this;
	}
	
	/**
	 * Helper method used to cancel enrollment
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$this->status_id = Model_Status::CANCELLED;
		$this->save();
	}

	/**
	 * Tests if a user is enrolled for a given event on any of their characters
	 *
	 * @param   object  Model_ACL_User - the user to test against
	 * @param   objecvt Model_Event    - the event to test against
	 * @return  bool
	 */
	public static function is_enrolled(Model_ACL_User $user, Model_Event $event, $characters = NULL)
	{
		static $events = array();
		
		// This function can be very expensive in terms of database usage, so check for cached results
		if (array_key_exists($event->id, $events))
		{
			if (array_key_exists($user->id, $events[$event->id]))
			{
				return $events[$event->id][$user->id];
			}
		}
		
		// Get character list if not provided
		if ( ! $characters)
			$characters = $user->characters->find_all();
		
		// See if any character is enrolled
		foreach ($characters as $character)
		{
			// If the character has 
			if ($character->has('events', $event->id))
			{
				$enrollment = ORM::factory('enrollment', array('event_id' => $event->id, 'character_id' => $character->id));
				if ($enrollment->status_id != Model_Status::CANCELLED)
				{	
					$events[$event->id][$user->id] = $character;
					return $character;
				}
			}
		}
		
		// Nothing found; no sign-up present
		return FALSE;
	}	

}