<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* TargetSources Model */

class TargetSources_model extends Application_model {

    function __construct() {
        parent::__construct('target_sources', 'TargetSource');
    }

	public function getPaginate($limit = null, $offset = null, $search = null, $by = "all") {
		
		$arguments = array('order' => '`id` DESC');

		if (isset($search) && $search != "") {
		
			if(is_valid_email($search)){
				$user = $this->Users->getByEmail($search);
				if($user) {
					$arguments['conditions'] = array("`created_by_id` = ".$user->getId());
				}
			} else {
				$arguments['conditions'] = array("`id` LIKE '%".$search."%'");
			}
		}
	
		switch($by) {
			case "active": 
				$arguments['conditions'] = array("`is_active` = 1");
				break;
			case "expired": 
				$arguments['conditions'] = array("`is_active` = 0");
				break;
		}

		return $this->paginate($arguments, $limit, $offset);
	
	}	

	public function countActiveOnly() {
		return $this->count(array('`is_active` = ?', true));
	}

	public function countInactiveOnly() {
		return $this->count(array('`is_active` = ?', false));
	}
}

/* TargetSource Object */

class TargetSource extends Application_object {

	function __construct() {
		parent::__construct('target_sources');
	}
		
	/* Static URLs */

	public function getEditURL() {
		return 'admin/subscriptions/edit/' . $this->getId();
	}

}