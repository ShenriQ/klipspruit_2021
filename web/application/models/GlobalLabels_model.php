<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* GlobalLabels Model */

class GlobalLabels_model extends Application_model {

    function __construct() {
        parent::__construct('global_labels', 'GlobalLabel');
    }

	public function getByType($type, $exclude_inactive = true) {
		return $this->find(array('conditions' => array('`type` = ? AND `is_active` >= ?', $type, $exclude_inactive), 'order' => '`name`'));
	}
	
	public function getDefaultByType($type, $exclude_inactive = true) {
		return $this->find(array('conditions' => array('`is_default` = ? AND `type` = ? AND `is_active` >= ?', true, $type, $exclude_inactive), 'one' => true));
	}
	
}

/* GlobalLabel Object */

class GlobalLabel extends Application_object {

	function __construct() {
		parent::__construct('global_labels');
	}
	
}