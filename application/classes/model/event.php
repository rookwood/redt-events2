<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Event extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'location'  => array(),
		'user'      => array(),
		'status'    => array(),
		'character' => array(),
	);
	
	protected $_has_many   = array(
		'enrollment'  => array('model' => 'enrollment'),
		'characters'  => array(
			'model'    => 'character',
			'through'  => 'enrollment',
		),
	);
	
	/**
	 * @var   array  Validation rules
	 */
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
			'player_limit' => array(
				array('digit'),
				array('range', array(':value', 1, 255)),
			),
			'time' => array(
				// Temp fix to validate time + date combo
				array('range', array(':value', time(), 9999999999)),
			),
		);
	}
	
	public function filters()
	{
		return array(
			'title' => array(array('trim')),
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
			'time'             => 'Start time',
			'title'            => 'Event title',
		);
	}
	
	/**
	 * Used to create new events
	 *
	 * @param   array    Event data
	 * @param   array    Names of expected fields
	 * @return  ORM      Newly created event object
	 */
	public function create_event(Model_ACL_User $user, $values)
	{
		// Get location id
		$location_id = Model_Location::to_id($values['location']);
		
		// Get leading character's id
		$character_id = ORM::factory('character', array('name' => $values['character']))->id;
		
		// Set status to scheduled
		$status_id = Model_Status::SCHEDULED;
		
		$values['location_id']  = $location_id;
		$values['character_id'] = $character_id;
		$values['user_id']      = $user->id;
		$values['status_id']    = $status_id;
		$values['time']         = Date::from_local_time($values['time'] ." ". $values['date'], $values['timezone']);
		
		// Sanitize user text
		$values['description'] = HTML::chars($values['description']);
		$values['title']       = HTML::chars($values['title']);
		
		// Fields expected for validation
		$expected = array(
			'time',
			'location_id',
			'description',
			'status_id',
			'user_id',
			'title',
			'character_id',
			'user_id',
			'player_limit',
		);
		
		return $this->values($values, $expected)->create();
	}
	
	/**
	 * Used to change event details
	 *
	 * @param   array    Event data
	 * @param   array    Names of expected fields
	 * @return  ORM      Edited event object
	 */
	public function edit_event($values)
	{
		// Get location id
		$location_id = Model_Location::to_id($values['location']);
		
		// Get leading character's id
		if (isset($values['character']))
		{
			$character_id = ORM::factory('character', array('name' => $values['character']))->id;
		}
		// Use existing value if not provided (e.g. administrative edit)
		else
		{
			$character_id = $this->character_id;
		}
		
		// Set status to scheduled
		$status_id = Model_Status::SCHEDULED;
		
		$values['location_id']  = $location_id;
		$values['character_id'] = $character_id;
		$values['time']         = Date::from_local_time($values['time'] ." ". $values['date'], $values['timezone']);
		
		// Sanitize user text
		$values['description'] = HTML::chars($values['description']);
		$values['title']       = HTML::chars($values['title']);
		
		$expected = array(
			'time',
			'location_id',
			'description',
			'title',
			'player_limit',
		);
		
		return $this->values($values, $expected)->save();
	}
	
	/**
	 * Helper method to cancel event
	 *
	 * @return  ORM  Event object
	 */
	public function cancel_event()
	{
		// Change event status to cancelled
		$this->status_id = Model_Status::CANCELLED;
		return $this->save();
	}
	
	public function uncancel_event()
	{
		$this->status_id = Model_Status::SCHEDULED;
		return $this->save();
	}
	
	/**
	 * Administrative function used to change event ownership
	 *
	 * @param   string    New leader's character
	 * @return  void
	 */
	public function reassign_owner($character)
	{
		$character = ORM::factory('character', array('name' => $character));
		
		if ( ! $character->loaded())
			throw new Kohana_Exception('Character not found.');
		
		$new_owner = $character->user->id;
		
		$this->user_id = $new_owner;
		$this->character_id = $character->id;
		$this->save();
	}
	
	public function enroll(Model_ACL_User $user, $character, $status, $comment)
	{
		// Make sure we have a character object
		if ( ! $character instanceOf ORM)
			$character = ORM::factory('character', array('name' => $character));
			
		try
		{
			// Create relation between this event and player's character record
			$this->add('characters', $character);
		}
		catch(Database_Exception $e)
		{
			// This exception indicates that the user has already enrolled but cancelled.
			// We can continue safely without intervention.
		}
		
		// Load enrollment record to add details
		$enrollment = ORM::factory('enrollment', array('event_id' => $this->id, 'character_id' => $character->id));
		
		// If no record found, an error has occured
		if ( ! $enrollment->loaded())
			throw new Kohana_Exception('No enrollment record was created.');
		
		// Add details to enrollment record
		$enrollment->details($status, $comment)->save();
		
		return $this;
	}
	
	/**
	 * Used to withdraw from an event
	 *
	 * @param   string   The character withdrawing
	 * @return  ORM      Event object
	 */
	public function withdraw($character)
	{
		if ( ! $character instanceOf ORM)
			$character = ORM::factory('character', array('name' => $character));
		
		// Load enrollment record
		$enrollment = ORM::factory('enrollment', array('event_id' => $this->id, 'character_id' => $character->id));
		
		// Change status to cancelled
		$enrollment->cancel();
		
		// Check to see if we can move someone from standby to active
		Model_Enrollment::check_bump_list($this->id);
		
		return $this;
	}
	
	/**
	 * Returns a list of players (who are not on stand-by) attending this event
	 *
	 * @return  Array
	 */
	public function active_attendee_list()
	{
		return $list = ORM::factory('enrollment')
			->where('event_id', '=', $this->id)
			->and_where('status_id', '=', Model_Status::READY)
			->find_all();
	}
	
	/**
	 * Returns count of attending players who are not on stand-by
	 *
	 * @return  int
	 */
	public function active_attendee_count()
	{
		return count($this->active_attendee_list());
	}
	
	/**
	 * Returns a list of players (who are stand-by) attending this event
	 *
	 * @return  Array
	 */
	public function standby_attendee_list()
	{
		return $list = $this->enrollment
			->where('status_id', '=', Model_Status::STANDBY_VOLUNTARY)
			->or_where('status_id', '=', Model_Status::STANDBY_FORCED)
			->find_all();
	}
	
	/**
	 * Returns count of attending players who are not on stand-by
	 *
	 * @return  int
	 */
	public function standby_attendee_count()
	{
		return count($this->standby_attendee_list());
	}
	
	/**
	 * Returns a collection of event objects matching pre-defined filters
	 *
	 * @param    string    Filter to be used
	 * @param    object    User to test against
	 * @param    int       ID of location used for specific filter
	 * @return   ORM       Collection of matching events
	 */
	public static function filtered_list($filter, Model_ACL_User $user = NULL, $id = NULL)
	{
		switch ($filter)
		{
			// Show events that the user has ever signed up for.
			case 'mine':
				if ( ! Auth::instance()->logged_in())
					throw new Kohana_Exception('Must be logged in to search your events.');

				// Build sub queries to join current user -> characters -> enrollment -> events
				$query_sub1 = DB::select('characters.id')
					->from('characters')
					->join('users')
					->on('characters.user_id', '=', 'users.id')
					->where('users.id', '=', $user->id);
				$query_sub2 = DB::select('enrollment.event_id')
					->from('enrollment')
					->join(array($query_sub1, 'characters'))
					->on('characters.id', '=', 'enrollment.character_id')
					->where('enrollment.status_id', '!=', Model_Status::CANCELLED);
				$query_sub3 = DB::select('events.id')
					->from('events')
					->join(array($query_sub2, 'enrollment'))
					->on('enrollment.event_id', '=', 'events.id');

				// Execute our query
				$events = $query_sub3->execute();

				// Build array of event IDs
				foreach ($events as $event)
				{
					$ids[] = $event['id'];
				}

				if (empty($ids))
					return array();

				// Pass event object data to the view
				$events = ORM::factory('event')
					->where('id', 'IN', $ids)
					->and_where('time', '>', Date::from_local_time(time(), date_default_timezone_get()))
					->order_by('time', 'ASC')
					->find_all();
			break;

			// Show all events that started before the current time
			case 'past':
				$events = ORM::factory('event')
					->where('time', '<', Date::from_local_time(time(), date_default_timezone_get()))
					->and_where('status_id', '!=', Model_Status::CANCELLED)
					->order_by('time', 'DESC')
					->find_all();
			break;

			// Show all events with given location id
			case 'location':
				$events = ORM::factory('event')
					->where('time', '>', Date::from_local_time(time(), date_default_timezone_get()) - Date::HOUR)
					->and_where('status_id', '!=', Model_Status::CANCELLED)
					->and_where('location_id', '=', $id)
					->order_by('status_id', 'ASC')
					->order_by('time', 'ASC')
					->find_all();
			break;

			// Pass through to default case 
			case 'current':
			
			// Show all events scheduled to start in the future
			default:
				$events = ORM::factory('event')
					->where('time', '>=', Date::from_server_time(time()))
					->and_where('status_id', '!=', Model_Status::CANCELLED)
					->order_by('status_id', 'ASC')
					->order_by('time', 'ASC')
					->find_all();
			break;
		}

		return $events;
	}
}