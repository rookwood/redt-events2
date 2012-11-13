<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Reset user's password manually
 *
 * There are no options needed.  All data will be grabbed from your configuration files.
 * If your config files are incomplete, finish those before attempting installation.
 */
class Minion_Task_Resetpw extends Minion_Task {
	
	protected $_defaults = array();
	
	public function execute(array $params)
	{
		Minion_CLI::write('Password reset utility'.PHP_EOL);
		Minion_CLI::write('======================');
		Minion_CLI::write();

		$credential = Minion_CLI::read('Username or email')

		// test if email given
		if (Valid::email($credential))
		{
			$user = ORM::factory('user', array('email' => $credential));
		}
		// else assume username
		else
		{
			$user = ORM::factory('user', array('username' => $credential));
		}

		if ( ! $user->loaded())
		{
			Minion_CLI::write('No user found for provided credentials.'.PHP_EOL);
			exit(0);
		}

		$password = Minion_CLI::read('Enter new temporary password');

		$user->password = $password;

		$user->save();

		Minion_CLI::write('Password updated.');
		exit(0);
	}
}
