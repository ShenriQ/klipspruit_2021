<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* IOrders Model */

class IOrders_model extends Application_model {

    function __construct() {
        parent::__construct('ipayment_orders', 'IOrder');
    }

	public function getPaginate($limit = null, $offset = null, $target_email = null) {
		
		$arguments = array('order' => 'id DESC');

		if(is_valid_email($target_email)){
			$user = $this->Users->getByEmail($target_email);
			if(isset($user)) {
				$arguments['conditions'] = array("created_by_id = ".$user->getId());
			}
		}
		
		return $this->paginate($arguments, $limit, $offset);
	
	}	

	public function getPaymentsStatsFor($start, $end) {
	
		$transaction_logs_table = $this->getTableName(true);
		
		$query = $this->db->query("SELECT SUM(send_amount) AS summary, SUM(payment_fee) AS fee_amount, left(created_at, 7)  AS stats_date , created_at FROM ".$transaction_logs_table."
		WHERE CONVERT(datetime,  created_at) BETWEEN '$start' AND '$end' GROUP BY created_at");

		return $query->result();
	}
	
	public function getPaymentsAllStats() {
		
		$transaction_logs_table = $this->getTableName(true);
		
		$query = $this->db->query("SELECT SUM(send_amount) AS summary, SUM(payment_fee) AS fee_amount FROM ".$transaction_logs_table);

		return $query->result();

	}

}

/* IOrder Object */

class IOrder extends Application_object {

	function __construct() {
		parent::__construct('ipayment_orders');
	}
	
}