<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* InvoiceItems Model */

class InvoiceItems_model extends Application_model {

    function __construct() {
        parent::__construct('invoice_items', 'InvoiceItem');
    }

	public function getItems(Invoice $invoice) {
	
		return $this->find(array('conditions' => array('invoice_id = ?', $invoice->getId()), 
		'order' => 'o_key ASC'));
	
	}

	public function getByPorjectTimeLog(ProjectTimelog $project_timelog) {

		$invoice_item_project_timelogs_table = $this->InvoiceItemProjectTimelogs->getTableName(true);
		$invoice_items_table = $this->getTableName(true);

		$arguments = array('joins' => array('table' => $invoice_item_project_timelogs_table, 'cond' => $invoice_item_project_timelogs_table.'.invoice_u_key = '.$invoice_items_table.'.u_key 
		AND '.$invoice_item_project_timelogs_table.'.project_timelog_id = '.$project_timelog->getId(), 'type' => 'INNER'), 'one' => true);
				
		return $this->find($arguments);

	}

	public function countItems(Invoices $invoice) {
		return $this->count(array('invoice_id = ?', $invoice->getId()));
	}

}

/* InvoiceItem Object */

class InvoiceItem extends Application_object {

	private $invoice;

	function __construct() {
		parent::__construct('invoice_items');
	}
	
	public function getName($max_length = 65) {
		return shorter($this->getDescription(), $max_length);
	}

	public function getInvoice() {

		if(is_null($this->invoice)) {
			$this->invoice = $this->CI_instance()->Invoices->findById($this->getInvoiceId());
		}
		
		return $this->invoice;

	}

	public function getProjectTimelogs() {
		return $this->CI_instance()->InvoiceItemProjectTimelogs->getByInvoiceItem($this); 
	}

	public function clearProjectTimelogs() {
		return $this->CI_instance()->InvoiceItemProjectTimelogs->clearByInvoiceItem($this); 
	}

	public function delete() {
		
		$this->clearProjectTimelogs();
		parent::delete();

	}
}