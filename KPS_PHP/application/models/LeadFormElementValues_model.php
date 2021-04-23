<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* LeadFormElementValues Model */

class LeadFormElementValues_model extends Application_model {

    function __construct() {
        parent::__construct('lead_form_element_values', 'LeadFormElementValue');
    }

	public function getElements(Lead $lead) {
		return $this->find(array('conditions' => array('form_id = ? AND lead_id = ?', $lead->getFormId(), $lead->getId())));
	}

	public function getElementsByObject(LeadFormElement $element) {
		return $this->find(array('conditions' => array('element_id = ?', $element->getId())));
	}

	public function getElementByLeadElement(LeadFormElement $element, Lead $lead) {
		return $this->find(array('conditions' => array('element_id = ? AND lead_id = ?', $element->getId(), $lead->getId()), 'one' => true));
	}

}

/* LeadFormElementValue Object */

class LeadFormElementValue extends Application_object {

	private $element;
	
	function __construct() {
		parent::__construct('lead_form_element_values');
	}

	public function getElement() {
		
		if(is_null($this->element)) {
			$this->element = $this->CI_instance()->LeadFormElements->findById($this->getElementId());
		}
		
		return $this->element;
		
	}
	
}