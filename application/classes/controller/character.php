<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Character extends Abstract_Controller_Website {

	public function action_add()
	{
		// Can user add new characters at this time?
		if ( ! $this->user->can('character_add'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// Must be logged in to add a character
			if ($status === Policy_Character_Add::NOT_LOGGED_IN)
			{			
				Notices::info('character.add.not_logged_in');
				
				// Redirect to login screen; come back once finished
				$this->session->set('follow_login', $this->request->url());
				$this->request->redirect(Route::url('user', array('controller' => 'user', 'action' => 'login')));
			}
			// Unspecified reason for denial
			else if ($status === Policy_Character_Add::NOT_ALLOWED)
			{
				Notices::denied('character.add.not_allowed');
				
				$this->request->redirect(Route::url('profile'));
			}
		}
		
		// Alias for user and profile
		$user = $this->user;
		
		// Is the form submitted correctly w/ CSRF token?
		if ($this->valid_post())
		{
			// Submitted data
			$character_post = Arr::get($this->request->post(), 'character', array());
						
			// Create the character
			try
			{
				$character = ORM::factory('character')->create_character($user, $character_post);
				
				Notices::success('character.add.success');
				
				$this->request->redirect(Route::url('profile edit'));
			}
			catch(ORM_Validation_Exception $e)
			{			
				$this->view->errors = $e->errors('character');
				
				// We have no valid Model_Character, so we have to pass the form values back directly
				$this->view->values = $character_post;
			}
		}
	}
	
	public function action_remove()
	{
		// Load character model
		$character = ORM::factory('character', array('name' => $this->request->param('character')));
		
		if ( ! $this->user->can('character_remove', array('character' => $character)))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// Unspecified reason for denial
			if ($status === Policy_Remove_Character::NOT_ALLOWED)
			{			
				Notices::denied('character.remove.not_allowed');

				$this->request->redirect(Route::url('profile'));
			}
			elseif ($status === Policy_Remove_Character::NOT_OWNER)
			{
				Notices::denied('character.remove.not_owner');
				
				$this->request->redirect(Route::url('profile'));
			}
		}
				
		// Remove
		$character->remove_character();
		
		Notices::success('character.remove.success');
		
		$this->request->redirect(Route::url('profile'));
	}
	
	public function action_edit()
	{
		// Load character model
		$character = ORM::factory('character', array('name' => $this->request->param('character')));
		
		if ( ! $character->loaded())
			throw new HTTP_Exception_404;
		
		// Is user allowed to edit this character?
		if ( ! $this->user->can('character_edit', array('character' => $character)))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// Unspecified reason for denial
			if ($status === Policy_Character_Edit::NOT_ALLOWED)
			{			
				Notices::info('character.edit.not_allowed');

				$this->request->redirect(Route::url('profile'));
			}
			// Non-administrator tried to edit another user's character
			elseif ($status === Policy_Edit_Character::NOT_OWNER)
			{
				Notices::info('character.edit.not_owner');
				
				$this->request->redirect(Route::url('profile'));
			}
			// Other denial reason
			else
			{
				Notices::info('character.edit.not_allowed');
				
				$this->request->redirect(Route::url('profile'));
			}
		}
		
		// Valid csrf, etc
		if ($this->valid_post())
		{
			// Extract character data from $_POST
			$character_post = Arr::get($this->request->post(), 'character', array());
			
			try
			{
				// Set data to character model and save
				$character->edit_character($character_post);
				
				Notices::success('character.edit.success');
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->view->errors = $e->errors('character');
				
				Notices::error('character.edit.failed');
			}
		}
		
		// Pass character data to view class
		$this->view->character_data = $character;
	}}