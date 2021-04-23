<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* TransactionLogs Model */

class TransactionLogs_model extends Application_model {

    function __construct() {
        parent::__construct('transaction_logs', 'TransactionLog');
    }

	public function getTransactionStatsFor($transaction_type, $start, $end) {
	
		$transaction_logs_table = $this->getTableName(true);
		
		$query = $this->db->query("SELECT SUM(amount) AS summary, left(created_at, 7) AS stats_date , created_at FROM ".$transaction_logs_table."
		WHERE transaction_type = '".$transaction_type."' AND CONVERT(datetime,  created_at) BETWEEN '$start' AND '$end' AND target_source_id = ".get_target_source_id()." GROUP BY created_at");

		return $query->result();
	}

	public function getTransactionAllStats($transaction_type) {
	
		$transaction_logs_table = $this->getTableName(true);
		
		$query = $this->db->query("SELECT SUM(amount) AS summary FROM ".$transaction_logs_table."
		WHERE transaction_type = '".$transaction_type."' AND target_source_id = ".get_target_source_id());

		return $query->result();
	}

	public function getReferenceLogs($t_type, $ref_id) {
	
		return $this->find(array('conditions' => array('transaction_type = ? AND reference_id = ?', $t_type, $ref_id), 
		'order' => 'id ASC'));
	
	}

}

/* TransactionLog Object */

class TransactionLog extends Application_object {
	
	function __construct() {
		parent::__construct('transaction_logs');
	}

	function getCreditAccount() {
		return $this->CI_instance()->Users->findById($this->getCreditAccountId());
	}

	function getDebitAccount() {
		return $this->CI_instance()->Users->findById($this->getDebitAccountId());
	}

	function getReferenceInvoice() {
		return $this->CI_instance()->Invoices->findById($this->getReferenceId());
	}

	function getReferenceProject() {
		return $this->CI_instance()->Projects->findById($this->getReferenceId());
	}

	/* Static URLs */

	public function getEditURL($transaction_type) {
		return 'transactions/edit/'.$transaction_type.'/'.$this->getId();
	}
	
	public function getRemoveURL() {
		return 'transactions/remove/'.$this->getId();
	}
	
}