<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_admin_data_contoller($this);
	}

	public function get_orders_json() {
		
		$data = array();
		$packages = get_packages();

		$search = input_get_request('search');
		$search = isset($search['value']) ? $search['value'] : null;

		$draw = input_get_request('draw');
		$start = input_get_request('start', 0);
		$length = input_get_request('length', 10);
		
		$selected_target = input_get_request('target');
		list($orders, $total_items) = $this->IOrders->getPaginate($length, $start, $selected_target);
			
		if(isset($orders) && is_array($orders) && count($orders)) {
		
			foreach($orders as $order) {
				
				$user = $this->Users->findById($order->getCreatedById());
				$user_email = (isset($user)) ? $user->getEmail() : null;

				$data[] = array(
				'Date' => format_date($order->getCreatedAt()),
				'Transaction' => $order->getOrderMessage(),
				'Customer' => (isset($user_email) ? '<a href="'.base_url('admin/orders?target='.$user_email).'">'.$user_email.'</a>' : 'n/a'),
				'PayerEmail' => $order->getPayerEmail(),
				'TXNId' => $order->getTxnId(),
				'Status' => $order->getPaymentStatus(),
				'Amount' => '$'.$order->getSendAmount()
				);
				
			}
					
		}
		
		$json_data = array(
			"draw"            => intval($draw),   
			"recordsTotal"    => $total_items,  
			"recordsFiltered" => $total_items,
			"data"            => $data
		);

		header('Content-Type: application/json');		
		die(json_encode($json_data));
	
	}
	
	public function index() {
	}
		
}
