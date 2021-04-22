<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Events Model */

class Events_model extends Application_model {

    function __construct() {
        parent::__construct('events', 'Event');
    }

	public function getByUser(User $user, $additional_conditions = null, $type = 'INNER') {

		$event_users_table =  $this->EventUsers->getTableName(true);
		$events_table = $this->getTableName(true);

		$arguments = array('joins' => array('table' => $event_users_table, 'cond' => $event_users_table.'.`event_id` = '.$events_table.'.`id` 
		AND '.$event_users_table.'.`user_id` = '.$user->getId(), 'type' => $type));
		
		if(!is_null($additional_conditions) && is_array($additional_conditions)) {
			$arguments['conditions'] = $additional_conditions;
		}
		
		$arguments['order'] = '`start` ASC';
		return $this->find($arguments);
		
	}
	

	public function getAll($additional_conditions = null) {
		
		$arguments = array('order' => '`start` ASC');
		
		if(!is_null($additional_conditions) && is_array($additional_conditions)) {
			$arguments['conditions'] = $additional_conditions;
		}
	
		return $this->find($arguments);

	}

}

/* Event Object */

class Event extends Application_object {
	
	function __construct() {
		parent::__construct('events');
	}

	public function clearEventUsers() {
		return $this->CI_instance()->EventUsers->clearByEvent($this);
	}

	public function delete() {
		
		$this->clearEventUsers();		
		parent::delete();
		
	}

	/* Static URLs */

	public function getObjectURL() {
		return 'calendar/view/'.$this->getId();
	}

    function getEditURL() {
		return 'calendar/edit/'.$this->getId();
	}

    function getDeleteURL() {
		return 'calendar/delete_event/'.$this->getId();
	}
	
}