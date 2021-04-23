<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* EstimateItems Model */

class EstimateItems_model extends Application_model {

    function __construct() {
        parent::__construct('estimate_items', 'EstimateItem');
    }

	public function getItems(Estimate $estimate) {
	
		return $this->find(array('conditions' => array('estimate_id = ?', $estimate->getId()), 
		'order' => 'o_key ASC'));
	
	}

	public function countItems(Estimates $estimate) {
		return $this->count(array('estimate_id = ?', $estimate->getId()));
	}

}

/* EstimateItem Object */

class EstimateItem extends Application_object {
	
	function __construct() {
		parent::__construct('estimate_items');
	}
	
	public function getName($max_length = 65) {
		return shorter($this->getDescription(), $max_length);
	}

}