<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Invoices Model */

class Invoices_model extends Application_model {

    function __construct() {
        parent::__construct('invoices', 'Invoice');
    }

	public function getAll($include_trashed = false) {

		return $this->find(array('conditions' => array('`is_trashed` <= ?', $include_trashed), 
		'order' => '`id` DESC'));
						
	}

	public function getPaidAndCancelled($project_ids = null, $include_trashed = false) {

		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND (`is_cancelled` = ? OR (`total_amount` <> 0 AND `total_amount` <= `paid_amount`)) AND `project_id` IN (?)', $include_trashed, true,  $project_ids), 
			'order' => '`updated_at` DESC'));	
		} else {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND (`is_cancelled` = ? OR (`total_amount` <> 0 AND `total_amount` <= `paid_amount`))', $include_trashed, true), 
			'order' => '`updated_at` DESC'));	
		}
						
	}

	public function getRecurring($project_ids = null, $include_trashed = false) {

		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND `is_recurring` = ? AND `project_id` IN (?)', $include_trashed, true,  $project_ids), 
			'order' => '`updated_at` DESC'));	
		} else {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND `is_recurring` = ?', $include_trashed, true), 
			'order' => '`updated_at` DESC'));	
		}
						
	}

	public function getRenewableRecurring($include_trashed = false) {
		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `is_recurring` = ?
			AND `next_recurring_date` <> \'0000-00-00\' AND `next_recurring_date` <= CURDATE() AND 
			(`no_of_cycles` < ? OR (`no_of_cycles_completed` < `no_of_cycles`))', $include_trashed, true, 1), 
			'order' => '`updated_at` DESC'));
	}

	public function getAvailable($project_ids = null, $include_trashed = false) {

		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND (`is_cancelled` = ? AND (`total_amount` = 0 OR `total_amount` > `paid_amount`)) AND `is_recurring` = ? AND `project_id` IN (?)', $include_trashed, false, false,  $project_ids), 
			'order' => '`updated_at` DESC'));	
		} else {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND (`is_cancelled` = ? AND (`total_amount` = 0 OR `total_amount` > `paid_amount`)) AND `is_recurring` = ?', $include_trashed, false, false), 
			'order' => '`updated_at` DESC'));		
		}
						
	}

	public function getByClientOnly(User $client, $include_trashed = false) {
		
		if(!$client->isMember()) {

			return $this->find(array('conditions' => array('`is_trashed` <= ? AND `client_id` = ?', $include_trashed, $client->getId()),
			'order' => '`id` DESC'));
		}
		
		return null;		
	}

	public function getAvailableByClientOnly(User $client, $project_ids = null, $include_trashed = false) {
		
		if(!$client->isMember()) {
			if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
				return $this->find(array('conditions' => array('`is_trashed` <= ? AND `client_id` = ? AND (`is_cancelled` = ? AND (`total_amount` = 0 OR `total_amount` > `paid_amount`)) AND `is_recurring` = ? AND (`issue_date` <> \'0000-00-00\' AND `issue_date` <= CURDATE()) AND `project_id` IN (?)', $include_trashed, $client->getId(), false, false, $project_ids),
				'order' => '`updated_at` DESC'));
			} else {
				return $this->find(array('conditions' => array('`is_trashed` <= ? AND `client_id` = ? AND (`is_cancelled` = ? AND (`total_amount` = 0 OR `total_amount` > `paid_amount`)) AND `is_recurring` = ? AND (`issue_date` <> \'0000-00-00\' AND `issue_date` <= CURDATE())', $include_trashed, $client->getId(), false, false),
				'order' => '`updated_at` DESC'));	
			}
		}
		
		return null;		
	}

	public function getPaidAndCancelledClientOnly(User $client, $project_ids = null, $include_trashed = false) {

		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND `client_id` = ? AND (`is_cancelled` = ? OR `total_amount` < `paid_amount`) AND `is_recurring` = ? AND (`issue_date` <> \'0000-00-00\' AND `issue_date` <= CURDATE()) AND `project_id` IN (?)', $include_trashed, $client->getId(), true, false, $project_ids), 
			'order' => '`updated_at` DESC'));	
		} else {
			return $this->find(array('conditions' => array('`is_trashed` <= ? AND `client_id` = ? AND (`is_cancelled` = ? OR `total_amount` < `paid_amount`) AND `is_recurring` = ? AND (`issue_date` <> \'0000-00-00\' AND `issue_date` <= CURDATE())', $include_trashed, $client->getId(), true, false), 
			'order' => '`updated_at` DESC'));	
		}
						
	}

	public function getByProject(Project $project, $include_trashed = false) {

		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `project_id` = ?', $include_trashed, $project->getId()), 
		'order' => '`created_at` DESC'));
						
	}

	public function getByAccessKey($access_key) {
		return  $this->find(array('conditions' => array('`access_key` = ?', $access_key), 'one' => true));
	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', true), 'order' => '`id`'));
	}

	public function getOverdues($client_ids) {
		return $this->find(array('conditions' => array('`is_trashed` = ? AND `is_recurring` = ? AND `is_cancelled` = ? AND `client_id` IN (?) AND `total_amount` > `paid_amount` AND (`issue_date` <> \'0000-00-00\' AND `issue_date` <= CURDATE()) AND (`due_date` <> \'0000-00-00\' AND `due_date` < NOW())', false, false, false, $client_ids), 'order' => '`id`'));
	}

	public function getDueReminders($date) {
		return $this->find(array('conditions' => array('`is_trashed` = ? AND `is_recurring` = ? AND `is_cancelled` = ? AND `total_amount` >= `paid_amount` AND (`due_date` <> \'0000-00-00\' AND `due_date` = ?) AND (`due_reminder_date` = \'0000-00-00\' OR `due_reminder_date` <> ?)' , false, false, false, $date, $date), 'order' => '`id`'));
	}

}

/* Invoice Object */

class Invoice extends Application_object {

	private $client;
	private $project;

	protected $is_searchable = true;
	protected $searchable_fields = array('subject');

	function __construct() {
		parent::__construct('invoices');
	}

	public function getName() {
		return $this->getInvoiceNo();
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
		return $this->CI_instance()->InvoiceItems->getItems($this);
	}

	public function getItemsCount() {
		return $this->CI_instance()->InvoiceItems->countItems($this);
	}

	public function clearInvoiceItems() {
		
		$items = $this->getItems();
		if(isset($items) && is_array($items) && count($items)) {
			foreach($items as $item) $item->delete();	
		}
			
	}

	public function getPayments() {
		return $this->CI_instance()->TransactionLogs->getReferenceLogs("payment", $this->getId());
	}

	public function clearPayments() {
		$payments = $this->getPayments();
		if(isset($payments) && is_array($payments) && count($payments)) {
			foreach($payments as $payment) $payment->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearInvoiceItems();
		$this->clearPayments();
		
		parent::delete();
		
	}
	

	/* Static URLs */

    function getCreatePaymentURL() {
		return 'transactions/create/payment/'.$this->getId();
	}

    function getEditURL() {
		return 'invoices/edit/'.$this->getId();
	}

	public function getCancelURL() {
		return 'invoices/cancel/'.$this->getId();
	}

    function getPaymentURL($gateway) {
		return 'invoices/payment/'.$gateway.'/'.$this->getAccessKey();
	}

    function getObjectURL() {
		return 'invoices/view/'.$this->getAccessKey();
	}

	function getDownloadURL() {
		return 'invoices/download/'.$this->getAccessKey();
	}

	function getSendNotificationURL() {
		return 'invoices/send_notification/'.$this->getAccessKey();
	}

	function getCloneURL() {
		return 'invoices/clone_entry/'.$this->getAccessKey();
	}

}