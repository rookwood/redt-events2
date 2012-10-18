<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Enrollment extends ORM {



	public function details(int $status, $comment)
	{
		$this->status_id = $status;
		$this->comment = HTML::chars($comment);
		
		return $this;
	}
	
	public function cancel()
	{
		$this->status_id = Model_Status::CANCELLED;
		$this->save();
	}
}