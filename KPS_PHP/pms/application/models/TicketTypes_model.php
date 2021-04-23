<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* TicketTypes Model */

class TicketTypes_model extends Application_model {

    function __construct() {
        parent::__construct('ticket_types', 'TicketType');
    }

	public function getAll() {
		return $this->find(array('order' => 'name ASC'));
	}

	public function getAllActive() {

		return $this->find(array('conditions' => array('is_active = ?', true), 
		'order' => 'name ASC'));
						
	}

}

/* TicketType Object */

class TicketType extends Application_object {
	
	function __construct() {
		parent::__construct('ticket_types');
	}

	
	/* Static URLs */

	public function getEditURL() {
		return 'departments/edit/'.$this->getId();
	}

}