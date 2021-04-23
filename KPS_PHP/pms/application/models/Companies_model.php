<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Companies Model */

class Companies_model extends Application_model {

    function __construct() {
        parent::__construct('companies', 'Company');
    }
	
	public function getOwnerCompany() {
		return $this->find(array('conditions' => array('parent_id = ?', 0), 'one' => true));
	}

	public function getClients(Company $company) {
		return $this->find(array('conditions' => array('parent_id = ? AND is_trashed = ? AND is_active = ?', $company->getId(), false, true), 'order' => 'name'));
	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('is_trashed = ?', true), 'order' => 'name'));
	}

	public function getByArchived($is_trashed = false) {
		return $this->find(array('conditions' => array('is_trashed = ? AND is_active = ?', $is_trashed, false), 'order' => 'name'));
	}
		
}

/* Company Object */

class Company extends Application_object {

	function __construct() {
		parent::__construct('companies');
	}

	public function getUsers($is_trashed = false, $is_active = null) {
    	
		if(isset($is_active)) {
			$arguments = array('conditions' => array('company_id = ? AND is_trashed = ? AND is_active = ?', $this->getId(), $is_trashed, $is_active));
		} else {
			$arguments = array('conditions' => array('company_id = ? AND is_trashed = ?', $this->getId(), $is_trashed));
		}
			
		return $this->CI_instance()->Users->find($arguments);
	
	}	

	public function getAllUsers() {
		return $this->CI_instance()->Users->find(array('conditions' => array('company_id = ?', $this->getId())));
	}
	
	public function moveToTrash() {

		$this->setIsTrashed(true);
		$this->save();
		
		$users = $this->getUsers();
		if(isset($users) && is_array($users) && count($users)) {
			foreach($users as $user) $user->moveToTrash(true);
		}
			
	}

	public function restoreFromTrash() {

		$this->setIsTrashed(false);
		$this->save();

		$users = $this->getUsers(true);
		if(isset($users) && is_array($users) && count($users)) {
			foreach($users as $user) $user->restoreFromTrash(true);
		}				
				
	}

	public function moveToArchive() {

		$this->setIsActive(false);
		$this->save();
		
		$users = $this->getUsers(false, true);
		if(isset($users) && is_array($users) && count($users)) {
			foreach($users as $user) $user->moveToArchive();
		}
			
	}

	public function restoreFromArchive() {

		$this->setIsActive(true);
		$this->save();

		$users = $this->getUsers(false, false);
		if(isset($users) && is_array($users) && count($users)) {
			foreach($users as $user) $user->restoreFromArchive();
		}				
				
	}

	/* Static URLs */

	public function getEditURL() {
		return 'companies/edit/'.$this->getId();
	}

	public function getObjectURL() {
		return 'companies/view/'.$this->getId();
	}

}