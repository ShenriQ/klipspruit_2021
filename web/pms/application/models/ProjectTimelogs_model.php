<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ProjectTimelogs Model */

class ProjectTimelogs_model extends Application_model {

    function __construct() {
        parent::__construct('project_timelogs', 'ProjectTimelog');
    }

	public function getAll($include_trashed = false, $status = true) {

		return $this->find(array('conditions' => array('`is_timer_started` = ? AND `is_trashed` <= ? AND `is_approved` = ?', false, $include_trashed, $status), 
		'order' => '`id` DESC'));

	}

	public function getApprovedBillableByIds($ids) {

		$invoice_item_project_timelogs_table = $this->InvoiceItemProjectTimelogs->getTableName(true);
		$project_timelogs_table = $this->getTableName(true);

		$arguments = array('joins' => array('table' => $invoice_item_project_timelogs_table, 'cond' => $invoice_item_project_timelogs_table.'.`project_timelog_id` = '.$project_timelogs_table.'.`id`', 'type' => 'LEFT'), 
		'conditions' => array($invoice_item_project_timelogs_table.'.`project_timelog_id` IS NULL AND '.$project_timelogs_table.'.`is_approved` = ? AND '.$project_timelogs_table.'.`is_billable` = ? AND '.$project_timelogs_table.'.`id` IN (?)', true, true, $ids));

		return $this->find($arguments);
						
	}

	public function getByProjects($project_ids, $include_trashed = false, $start = null, $end = null) {

		if(logged_user()->isOwner() || logged_user()->isAdmin() || !logged_user()->isMember()) {
			$conditions = array('`is_timer_started` = ? AND `is_trashed` <= ? AND `project_id` IN (?)', 
			false, $include_trashed, $project_ids);
		} else {
			$conditions = array('`is_timer_started` = ? AND `is_trashed` <= ? AND `project_id` IN (?) AND `member_id` = ?', 
			false, $include_trashed, $project_ids, logged_user()->getId());
		}

		if(isset($start) && isset($end)) {
			$conditions[0] .= " AND (DATE(`created_at`) BETWEEN ? AND ?)";
			array_push($conditions, $start, $end);
		}

		return $this->find(array('conditions' => $conditions, 'order' => '`id` DESC'));

	}

	public function getStartedTimerByUser(User $member) {

		return $this->find(array('conditions' => 
			array('`member_id` = ? AND `is_timer` = ? AND `is_timer_started` = ?', 
			$member->getId(), true, true), 'one' => true
		));
						
	}

	public function getByUser(User $member, $include_trashed = false, $status = true) {

		return $this->find(array('conditions' => array('`is_timer_started` = ? AND `is_trashed` <= ? AND `member_id` = ? AND `is_approved` = ?', false, $include_trashed, $member->getId(), $status), 
		'order' => '`id` DESC'));
						
	}

	public function getTimelogStats($user_ids, $start = null, $end = null) {
	
		$project_timelogs_table = $this->getTableName(true);
		
		$query = $this->db->query("SELECT SUM(`total_hours`) AS `summary`, `member_id`, `project_id` FROM ".$project_timelogs_table."
		WHERE `is_approved` = 1".(isset($start) && isset($end) ? " AND DATE(`created_at`) BETWEEN '$start' AND '$end'" : "")." 
		AND `member_id` IN (".implode(",", $user_ids).") AND `target_source_id` = ".get_target_source_id()." GROUP BY `member_id`, `project_id` HAVING `summary` > 0");
		
		return $query->result();

	}

	public function getTimelogAllStats() {
	
		$project_timelogs_table = $this->getTableName(true);
		
		$query = $this->db->query("SELECT SUM(`total_hours`) AS `summary` FROM ".$project_timelogs_table." 
		WHERE `is_approved` = 1 AND `target_source_id` = ".get_target_source_id());
		
		return $query->result();

	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', true), 'order' => '`id`'));
	}

}

/* ProjectTimelog Object */

class ProjectTimelog extends Application_object {

	private $member;
	private $project;
	private $task;
	private $invoice_item;

	function __construct() {
		parent::__construct('project_timelogs');
	}

	public function getName() {
		return shorter($this->getMemo(), 65);
	}

	public function getMember() {

		if(is_null($this->member)) {
			$this->member = $this->CI_instance()->Users->findById($this->getMemberId());	
		}
		
		return $this->member;
		
	}

	public function getProject() {

		if(is_null($this->project)) {
			$this->project = $this->CI_instance()->Projects->findById($this->getProjectId());	
		}
		
		return $this->project;
		
	}


	public function getInvoiceItem() {

		if(is_null($this->invoice_item)) {
			$this->invoice_item = $this->CI_instance()->InvoiceItems->getByPorjectTimeLog($this);	
		}
		
		return $this->invoice_item;
		
	}

	public function getInvoiceStatusCode() {
		$status_code = 0;
		$invoice_item = $this->getInvoiceItem();
		if(!is_null($invoice_item)) {
			$invoice = $invoice_item->getInvoice();
			if(isset($invoice)) {
				$status_code = ($invoice->getPaidAmount() < $invoice->getTotalAmount()) ? 1 : 2;
			}
		}
		return $status_code;
	}

	public function getIsPaid() {
		return $this->getInvoiceStatusCode() == 2;
	}
	
	public function getTask() {

		if(is_null($this->task)) {
			$this->task = $this->CI_instance()->ProjectTasks->findById($this->getTaskId());
		}
		
		return $this->task;
		
	}

	public function moveToTrash() {
		
		$this->setIsTrashed(true);
		$this->save();
			
	}

	public function restoreFromTrash() {
		
		$this->setIsTrashed(false);
		$this->save();

	}

	public function clearInvoiceItems() {
		return $this->CI_instance()->InvoiceItemProjectTimelogs->clearByProjectTimelog($this); 
	}

	public function delete() {
		
		$this->clearInvoiceItems();
		parent::delete();

	}

	/* Static URLs */

    function getObjectURL() {
		return false;
	}

    function getEditURL() {
		return 'timesheet/edit/'.$this->getId();
	}

}