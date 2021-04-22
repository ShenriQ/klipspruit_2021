<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* LeadsStatuses Model */

class LeadsStatuses_model extends Application_model {

    function __construct() {
        parent::__construct('leads_statuses', 'LeadsStatus');
    }

	public function getAll() {
		return $this->find(array('order' => '`name` ASC'));
	}

	public function getAllActive() {

		return $this->find(array('conditions' => array('`is_active` = ?', true), 
		'order' => '`name` ASC'));
						
	}

}

/* LeadsStatus Object */

class LeadsStatus extends Application_object {
	
	function __construct() {
		parent::__construct('leads_statuses');
	}

	
	/* Static URLs */

	public function getEditURL() {
		return 'leadsstatuses/edit/'.$this->getId();
	}

	public function getDeleteURL() {
		return 'leadsstatuses/delete/'.$this->getId();
	}

}