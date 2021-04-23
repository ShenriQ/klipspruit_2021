<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* EventUsers Model */

class EventUsers_model extends Application_model {

    function __construct() {
        parent::__construct('event_users', 'EventUser');
    }

	public function clearByEvent(Event $event) {
		return $this->db->simple_query('DELETE FROM ' . $this->getTableName(true) . ' WHERE event_id = '.$event->getId());
	}

	public function clearByUser(User $user) {
		return $this->db->simple_query('DELETE FROM ' . $this->getTableName(true) . ' WHERE user_id = '.$user->getId());
	}

}

/* EventUser Object */

class EventUser extends Application_object {

	function __construct() {
		parent::__construct('event_users');
	}
	
}