<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* LeadFormElements Model */

class LeadFormElements_model extends Application_model {

    function __construct() {
        parent::__construct('lead_form_elements', 'LeadFormElement');
    }

	public function getElements(LeadForm $form) {
	
		return $this->find(array('conditions' => array('`form_id` = ?', $form->getId()), 
		'order' => '`id` ASC'));
	
	}

}

/* LeadFormElement Object */

class LeadFormElement extends Application_object {
	
	function __construct() {
		parent::__construct('lead_form_elements');
	}

	public function getElementValues() {
		return $this->CI_instance()->LeadFormElementValues->getElementsByObject($this);
	}

	public function clearElementValues() {
		
		$element_values = $this->getElementValues();
		if(isset($element_values) && is_array($element_values) && count($element_values)) {
			foreach($element_values as $element_value) $element_value->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearElementValues();
		parent::delete();
		
	}
	
}