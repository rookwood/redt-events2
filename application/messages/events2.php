<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'user' => array(
		'registration' => array(
			'not_allowed'       => 'Registration is currently closed.  Please speak to your system administrator for an account.',
			'completed'         => 'You have already completed the registration process.',
			'password'          => 'Passwords did not match.',
			'invalid_post'      => 'This form has expired.  Please reload and try again.',
		),
		'login' => array(
			'already_logged_in' => 'You are already logged in.',
			'failed'            => 'Invalid username or password.',
			'banned'            => 'This account has been disabled.  Please speak to your system administrator for details.',
		),
		'logout' => array(
			'success'           => 'You have been logged out.',
		),
		'edit' => array(
			'not_logged_in'     => 'Please log in to edit your profile.',
			'not_allowed'       => 'You are not permitted to make changes to your profile at this time.',
			'success'           => 'Profile updated.',
			'fail'              => 'There was an error saving your information.  Please ensure that you completed the form correctly.',
		),
		'registration_email' => array(
			'subject'           => 'Registration conformation email',
			'sender'            => 'noreply@koreg_domain.com',
			'bad_key'           => 'Invalid key submitted.  Please check the email you received during registration.',
			'completed'         => 'This key has already been used.',
			'success'           => 'Your email has been verified.  Thanks!',
			'banned'            => 'Your account has been deavtivated by an admin and cannot be reactivated via this means.',
			'not_required'      => 'Email verification is not required at this time.',
		),
		'username_email' => array(
			'not_found'         => 'There is no account associated with that email address.',
			'subject'           => 'Username for your account at out site',
			'sender'            => 'noreply@koreg_domain.com',
			'success'           => 'Your username has been sent to your email address',
		),
		'password_email' => array(
			'not_found'         => 'There is no account associated with that email address.',
			'subject'           => 'Username for your account at out site',
			'sender'            => 'noreply@mistveterans.com',
			'success'           => 'Check your email for instructions on how to reset your password',
			'reset'             => 'Your password has been reset to :password.  We suggest changing this to something more easily remembered.',
		),
	),
	'admin' => array(
		'user' => array(
			'edit' => array(
				'denied'        => 'You are not allowed to edit other users\' profiles.',
				'success'       => 'User profile information saved',
			),
			'create' => array(
				'denied'        => 'You are not allowed to create new users.',
				'success'       => 'New user created.',
				'password'      => 'Passwords did not match.',
			),
			'disable' => array(
				'denied'        => 'You are not allowed to disable user accounts.',
				'success'       => 'User disabled',
			),
			'search' => array(
				'denied'        => 'You do not have permission to search through the user listing.',
			),
		),
		'role' => array(
			'edit' => array(
				'denied'        => 'You are not allowed to edit roles.',
				'success'       => 'Role info updated',
			),
			'remove' => array(
				'denied'        => 'You are not allowed to remove roles.',
				'success'       => 'Role removed',
			),
			'create' => array(
				'denied'        => 'You are not allowed to create new roles.',
				'success'       => 'Role added',
			),
		),
		'settings' => array(
			'set' => array(
				'success'       => 'Settings saved.',
			),
		),
	),
	'registration' => array(
			'subject'           => 'Registration conformation email',
			'sender'            => 'amperialmusic@hotmail.com',
			'bad_key'           => 'Invalid key submitted.  Please check the email you received during registration.',
			'completed'         => 'This key has already been used.',
			'success'           => 'Your email has been verified.  Thanks!',
			'banned'            => 'Your account has been deavtivated by an admin and cannot be reactivated via this means.',
			'not_required'      => 'Email verification is not required at this time.',
	),
	'generic' => array(
		'validation'            => 'There were errors on the form, please correct the highlighted fields.',
	),
	'character' => array(
		'add'    => array(
			'not_logged_in'     => 'You must be logged in to add new characters',
			'not_allowed'       => 'You may not add new characters at this time',
			'success'           => 'Character added successfully',
		),
		'edit'   => array(
			'not_allowed'       => 'You may not edit your characters at this time',
			'not_owner'         => 'You may only edit your own characters',
		),
		'remove' => array(
			'not_allowed'       => 'You may not remove characters at this time',
			'not_owner'         => 'You may only remove characters that you own',
			'success'           => 'Character removed successfully',
		),
	),
	'event' => array(
		'view'   => array(
			'not_allowed'       => 'You are not authorized to view details for this event.',
		),
		'add'    => array(
			'not_allowed'       => 'You are not authorized to create new events.',
			'fail'              => 'There was an error creating the event.  Please check the highlighted fields.',
			'success'           => 'Event created.  Make sure that you sign up for a slot!',
		),
		'edit'   => array(
			'not_allowed'       => 'You do not have permission to edit this event.',
			'not_owner'         => 'You may only edit events that you created.',
		),
		'remove' => array(
			'not_allowed'       => 'You are not allowed to cancel this event.',
			'not_owner'         => 'You may only cancel events that you created.',
			'start_time_passed' => 'You may not cancel events once their start time has passed.',
			'success'           => 'This event has ben cancelled.',
		),
		'signup' => array(
			'not_allowed'       => 'You do not currently have permission to sign-up for this event.',
			'success'           => 'You are now signed-up for this event.',
			'failed'            => 'There was an error in your information.  Please check the highlighted fields.',
			'already_enrolled'  => 'You are already signed-up for this event.  If you with to change to another character, cancel your current spot first.',
			'standby_forced'    => 'The role you selected has already been filled, so you were placed on stand-by.  If someone withdraws, you will be bumped up automatically.',
			'need_character'    => 'You must add at least one character before you may sign-up for events.',
		),
		'withdraw' => array(
			'success'           => 'You are no longer signed-up for this event.',
			'failed'            => 'There was an error cancelling your spot.  Please try again.',
			'not_signed_up'     => 'You cannot withdraw from an event for which you did not sign-up.',
			'start_time_passed' => 'You cannot withdraw from events once their start time has passed.',
		),
	),
	'location' => array(
		'add' => array(
			'not_allowed'       => 'You may not add new dungeons at this time.',
			'not_logged_in'     => 'You must be logged in to perform that action.',
		),
		'edit' => array(
			'locked'            => 'This dungeon is locked and may not be edited.',
			'not_allowed'       => 'You may not edit this dungeon at this time.',
			'not_owner'         => 'You may not edit dungeon that you did not create.',
			'not_logged_in'     => 'You must be logged in to perform that action.',
		),
		'remove' => array(
			'locked'            => 'This dungeon is locked and may not be removed.',
			'not_allowed'       => 'You may not remove this dungeon at this time.',
			'not_owner'         => 'You may not remove dungeon that you did not create.',
			'not_logged_in'     => 'You must be logged in to perform that action.',
		),
	),
	'filter' => array(
		'mine'                  => 'Events for which you have registered',
		'dungeon'               => 'Events sorted by selected dungeon',
		'past'                  => 'Showing completed events',
		'default'               => FALSE,
	),
	
);