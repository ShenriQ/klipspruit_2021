<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Leads Model */

class Leads_model extends Application_model {

    function __construct() {
        parent::__construct('leads', 'Lead');
    }

	public function getAll() {
		return $this->find(array('order' => 'id DESC'));
	}

	public function getLeads(LeadForm $lead_form) {
	
		return $this->find(array('conditions' => array('form_id = ?', $lead_form->getId()), 
		'order' => 'id DESC'));
	
	}

	public function getByUser(User $user, $limit = null, $offset = null) {

		return $this->find(array('conditions' => array('assigned_id = ?', $user->getId()),
		'order' => 'id DESC',
		'limit' => $limit,
        'offset' => $offset
		));
						
	}
	
}

/* Lead Object */

class Lead extends Application_object {

	private $client;
	private $project;
	private $assignee;
	private $source;
	private $status;
	private $form;

	function __construct() {
		parent::__construct('leads');
	}

	public function getClient() {
		
		if(is_null($this->client)) {
			$this->client = $this->CI_instance()->Users->findById($this->getClientId());	
		}
		
		return $this->client;
		
	}

	public function getProject() {
		
		if(is_null($this->project)) {
			$this->project = parent::getProject();
		}
		
		return $this->project;
		
	}

	public function getAssignee() {
		
		if(is_null($this->assignee)) {
			$this->assignee = $this->CI_instance()->Users->findById($this->getAssignedId());	
		}
		
		return $this->assignee;
		
	}

	public function getSource() {
		
		if(is_null($this->source)) {
			$this->source = $this->CI_instance()->LeadsSources->findById($this->getSourceId());	
		}
		
		return $this->source;
		
	}

	public function getStatus() {
		
		if(is_null($this->status)) {
			$this->status = $this->CI_instance()->LeadsStatuses->findById($this->getStatusId());	
		}
		
		return $this->status;
		
	}

	public function getForm() {
		
		if(is_null($this->form)) {
			$this->form = $this->CI_instance()->LeadForms->findById($this->getFormId());	
		}
		
		return $this->form;
		
	}

	public function getElements() {
		return $this->CI_instance()->LeadFormElementValues->getElements($this);
	}

	public function clearLeadElements() {
		
		$elements = $this->getElements();
		if(isset($elements) && is_array($elements) && count($elements)) {
			foreach($elements as $element) $element->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearLeadElements();		
		parent::delete();
		
	}
	

	/* Static URLs */

    function getEditURL() {
		return 'leads/edit/'.$this->getId();
	}

    function getEditNotesURL() {
		return 'leads/editnotes/'.$this->getId();
	}

    function getCreateClientURL() {
		return 'leads/createclient/'.$this->getId();
	}

    function getDeleteURL() {
		return 'leads/delete/'.$this->getId();
	}

    function getObjectURL() {
		return 'leads/view/'.$this->getId();
	}

}