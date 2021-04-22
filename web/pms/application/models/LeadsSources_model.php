<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* LeadsSources Model */

class LeadsSources_model extends Application_model {

    function __construct() {
        parent::__construct('leads_sources', 'LeadsSource');
    }

	public function getAll() {
		return $this->find(array('order' => '`name` ASC'));
	}

	public function getAllActive() {

		return $this->find(array('conditions' => array('`is_active` = ?', true), 
		'order' => '`name` ASC'));
						
	}

}

/* LeadsSource Object */

class LeadsSource extends Application_object {
	
	function __construct() {
		parent::__construct('leads_sources');
	}

	
	/* Static URLs */

	public function getEditURL() {
		return 'leadssources/edit/'.$this->getId();
	}

	public function getDeleteURL() {
		return 'leadssources/delete/'.$this->getId();
	}

}