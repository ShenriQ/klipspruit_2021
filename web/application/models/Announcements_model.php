<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Announcements Model */

class Announcements_model extends Application_model {

    function __construct() {
        parent::__construct('announcements', 'Announcement');
    }

	public function getAll() {
		return $this->find(array('order' => '`id` DESC'));
	}

	public function getAllActive() {
	
		$now_date = date('Y-m-d');
		return $this->find(array('conditions' => array('`start_date` <= ? AND `end_date` >= ?', $now_date, $now_date), 
		'order' => '`id` DESC'));
	
	}

}

/* Announcement Object */

class Announcement extends Application_object {
	
	function __construct() {
		parent::__construct('announcements');
	}

	/* Static URLs */

	public function getEditURL() {
		return 'noticeboard/edit/'.$this->getId();
	}
	
	public function getRemoveURL() {
		return 'noticeboard/remove/'.$this->getId();
	}

}