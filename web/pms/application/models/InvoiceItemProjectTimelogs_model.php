<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* InvoiceItemProjectTimelogs Model */

class InvoiceItemProjectTimelogs_model extends Application_model {

    function __construct() {
        parent::__construct('invoice_item_project_timelogs', 'InvoiceItemProjectTimelog');
    }

	public function getByInvoiceItem(InvoiceItem $invoice_item) {	
		return $this->find(array('conditions' => array('`invoice_u_key` = ?', $invoice_item->getUKey())));
	}
	
	public function clearByInvoiceItem(InvoiceItem $invoice_item) {
		return $this->db->simple_query('DELETE FROM ' . $this->getTableName(true) . ' WHERE `invoice_u_key` = '. db_escape_value($invoice_item->getUKey()));
	}
	
	public function clearByProjectTimelog(ProjectTimelog $project_timelog) {
		return $this->db->simple_query('DELETE FROM ' . $this->getTableName(true) . ' WHERE `project_timelog_id` = '.$project_timelog->getId());
	}

}

/* InvoiceItemProjectTimelog Object */

class InvoiceItemProjectTimelog extends Application_object {

	private $invoice_item;
	private $project_timelog;

	function __construct() {
		parent::__construct('invoice_item_project_timelogs');
	}
	
	public function getInvoiceItem() {
		if(is_null($this->invoice_item)) {
			$this->invoice_item = $this->CI_instance()->InvoiceItems->findById($this->getInvoiceItemId());
		}
		return $this->invoice_item;
	}

	public function getProjectTimelog() {
		if(is_null($this->project_timelog)) {
			$this->project_timelog = $this->CI_instance()->ProjectTimelogs->findById($this->getProjectTimelogId());
		}
		return $this->project_timelog;
	}

}