<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* LeadForms Model */

class LeadForms_model extends Application_model {

    function __construct() {
        parent::__construct('lead_forms', 'LeadForm');
    }

	public function getAll() {
		return $this->find(array('order' => 'id DESC'));
	}

	public function getByAssignee(User $assignee) {
		
		if($assignee->isMember()) {

			return $this->find(array('conditions' => array('assigned_id = ?', $assignee->getId()),
			'order' => 'id DESC'));
		}
		
		return null;		
	}

	public function getByAccessKey($access_key) {
		return  $this->find(array('conditions' => array('access_key = ?', $access_key), 'one' => true));
	}


}

/* LeadForm Object */

class LeadForm extends Application_object {

	private $assignee;

	function __construct() {
		parent::__construct('lead_forms');
	}

	public function getAssignee() {
		
		if(is_null($this->assignee)) {
			$this->assignee = $this->CI_instance()->Users->findById($this->getAssignedId());	
		}
		
		return $this->assignee;
		
	}

	public function getElements() {
		return $this->CI_instance()->LeadFormElements->getElements($this);
	}

	public function getLeads() {
		return $this->CI_instance()->Leads->getLeads($this);
	}

	public function clearLeads() {
		
		$leads = $this->getLeads();
		if(isset($leads) && is_array($leads) && count($leads)) {
			foreach($leads as $lead) $lead->delete();	
		}
			
	}

	public function clearLeadFormElements() {
		
		$elements = $this->getElements();
		if(isset($elements) && is_array($elements) && count($elements)) {
			foreach($elements as $element) $element->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearLeadFormElements();		
		$this->clearLeads();
				
		parent::delete();
		
	}
	

	/* Static URLs */

    function getEditURL() {
		return 'forms/edit/'.$this->getId();
	}

    function getDeleteURL() {
		return 'forms/delete/'.$this->getId();
	}

    function getFullViewURL() {
		return 'forms/full_view/'.$this->getAccessKey();
	}

    function getObjectURL() {
		return 'forms/view/'.$this->getAccessKey();
	}

}