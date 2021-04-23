<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Estimates Model */

class Estimates_model extends Application_model {

    function __construct() {
        parent::__construct('estimates', 'Estimate');
    }

	public function getAll($include_trashed = false) {

		return $this->find(array('conditions' => array('is_trashed <= ?', $include_trashed), 
		'order' => 'id DESC'));
						
	}

	public function getByClientOnly(User $client, $include_trashed = false) {
		
		if(!$client->isMember()) {

			return $this->find(array('conditions' => array('is_trashed <= ? AND client_id = ?', $include_trashed, $client->getId()),
			'order' => 'id DESC'));
		}
		
		return null;		
	}

	public function getByProject(Project $project, $include_trashed = false) {

		return $this->find(array('conditions' => array('is_trashed <= ? AND project_id = ?', $include_trashed, $project->getId()), 
		'order' => 'created_at DESC'));
						
	}

	public function getByAccessKey($access_key) {
		return  $this->find(array('conditions' => array('access_key = ?', $access_key), 'one' => true));
	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('is_trashed = ?', true), 'order' => 'id'));
	}

	public function getOverdues($client_ids) {
		return $this->find(array('conditions' => array('is_trashed = ? AND status = ? AND client_id IN (?) AND (due_date <> \'2021/01/01\' AND due_date < GETDATE())', false, true, $client_ids), 'order' => 'id'));
	}

}

/* Estimate Object */

class Estimate extends Application_object {

	private $client;
	private $project;

	protected $is_searchable = true;
	protected $searchable_fields = array('subject');

	function __construct() {
		parent::__construct('estimates');
	}

	public function getName() {
		return $this->getEstimateNo();
	}

	public function getClient() {
		
		if(is_null($this->client)) {
			$this->client = $this->CI_instance()->Users->findById($this->getClientId());	
		}
		
		return $this->client;
		
	}

	public function getProject() {
		
		if(is_null($this->project)) {
			$this->project = $this->CI_instance()->Projects->findById($this->getProjectId());	
		}
		
		return $this->project;
		
	}

	public function moveToTrash() {
		
		$this->setIsTrashed(true);
		$this->save();
			
	}

	public function restoreFromTrash() {
		
		$this->setIsTrashed(false);
		$this->save();

	}

	public function getItems() {
		return $this->CI_instance()->EstimateItems->getItems($this);
	}

	public function getItemsCount() {
		return $this->CI_instance()->EstimateItems->countItems($this);
	}

	public function clearEstimateItems() {
		
		$items = $this->getItems();
		if(isset($items) && is_array($items) && count($items)) {
			foreach($items as $item) $item->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearEstimateItems();
		parent::delete();
		
	}
	

	/* Static URLs */

    function getConvertToInvoiceURL() {
		return 'estimates/convert/'.$this->getId();
	}

    function getEditURL() {
		return 'estimates/edit/'.$this->getId();
	}

    function getObjectURL() {
		return 'estimates/view/'.$this->getAccessKey();
	}

	function getDownloadURL() {
		return 'estimates/download/'.$this->getAccessKey();
	}

	function getSendNotificationURL() {
		return 'estimates/send_notification/'.$this->getAccessKey();
	}

}