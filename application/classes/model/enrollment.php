<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Enrollment extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'events'     => array(),
		'characters' => array(),
		'status'     => array(),
	);

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
			->where('event_id', '=', $this->id)
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
	public function details(int $status, $comment)
	{
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
	public static function is_enrolled(Model_Event $event, $characters)
	{
		foreach ($characters as $character)
		{
			// If the character has 
			if ($character->has('events', $event->id))
			{
				return $character;
			}
		}

		// Nothing found; no sign-up present
		return FALSE;
	}	

}