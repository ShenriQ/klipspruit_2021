<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isOwner()) redirect('dashboard');
				
	}
	
	public function index() {
		
		$transaction_logs = $this->TransactionLogs->find(array('conditions' => array('`credit_account_id` = ? OR `debit_account_id` = ?', 
		logged_user()->getId(), logged_user()->getId()), 'order' => '`created_at` DESC'));
		
		tpl_assign('transaction_logs', $transaction_logs);
		
	}

	public function create($transaction_type, $reference_id = 0) {
		
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('transactions/_'.$transaction_type.'_form');
		
		if($reference_id > 0) {
			
			if($transaction_type == "payment") {
			
				$invoice = $this->Invoices->findById($reference_id);
				if(is_null($invoice)) set_flash_error(lang('e_3'), true);
		
				tpl_assign('invoice', $invoice);
			
			}
	
		}
		
		$transaction_log = new TransactionLog();
		tpl_assign('transaction_log', $transaction_log);

		$amount = input_post_request('amount');
		tpl_assign("amount", $amount);

		$project_id = input_post_request('project_id', 0);
		tpl_assign("project_id", $project_id);

		$description = input_post_request('description');
		tpl_assign("description", $description);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('description', lang('c_48'), 'trim|required');
			$this->form_validation->set_rules('amount', lang('c_152'), 'required|greater_than[0]',  array('greater_than' => lang('c_370')));
					
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$transaction_log = new TransactionLog();

					$transaction_log->setAmount($amount);
					$transaction_log->setTransactionType($transaction_type);
					$transaction_log->setDescription($description);
					
					if($transaction_type == 'payment') {
					
						$transaction_log->setCreditAccountId($invoice->getClientId());
						$transaction_log->setReferenceId($reference_id);
						$transaction_log->setDebitAccountId(logged_user()->getId());
					
					} else {

						// Mark as expense ..
						$transaction_log->setCreditAccountId(logged_user()->getId());
						$transaction_log->setReferenceId($project_id);
						$transaction_log->setDebitAccountId(0);

					}
					
					$transaction_log->save();
					
					if($reference_id > 0) {

						if(!$invoice->getStatus()) $invoice->setStatus(true); // Reopen
	
						$total_paid_amount = $invoice->getPaidAmount() + $amount;
						$invoice->setPaidAmount($total_paid_amount);
						
						$invoice->save();
					
					}
										
					$this->db->trans_commit();
										
					set_flash_success(sprintf(lang('c_79'), ucfirst($transaction_type)));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}

		}
			
	}
	
	public function edit($transaction_type, $id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('transactions/_'.$transaction_type.'_form');

		$transaction_log = $this->TransactionLogs->findById($id);
		if(is_null($transaction_log)) set_flash_error(lang('e_3'), true);
		
		tpl_assign('transaction_log', $transaction_log);
		$reference_id = $transaction_log->getReferenceId();

		if($reference_id > 0) {
			
			if($transaction_type == "payment") {
			
				$invoice = $this->Invoices->findById($reference_id);
				if(is_null($invoice)) set_flash_error(lang('e_3'), true);
					
			} 
	
		}

		$amount = input_post_request('amount', $transaction_log->getAmount());
		tpl_assign("amount", $amount);

		if($transaction_type == "expense") {

			$project_id = input_post_request('project_id', $reference_id);
			tpl_assign("project_id", $project_id);
		
		}

		$description = input_post_request('description', $transaction_log->getDescription());
		tpl_assign("description", $description);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('description', lang('c_48'), 'trim|required');
			$this->form_validation->set_rules('amount', lang('c_152'), 'required|greater_than[0]',  array('greater_than' => lang('c_370')));
					
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					// Save invoice info ..
					if($reference_id > 0 && $transaction_type == "payment") {

						$last_paid_amount = $invoice->getPaidAmount() - $transaction_log->getAmount();
						$total_paid_amount = $last_paid_amount + $amount;
					
						$invoice->setPaidAmount($total_paid_amount);					
						$invoice->save();
					
					} elseif($transaction_type == "expense") {
						$transaction_log->setReferenceId($project_id);
					}
										
					// Save transaction info ..
					$transaction_log->setAmount($amount);
					$transaction_log->setDescription($description);

					$transaction_log->save();
					
					$this->db->trans_commit();
										
					set_flash_success(sprintf(lang('c_57'), ucfirst($transaction_type)));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}

		}
			
	}

	public function remove($id) {

		$transaction_log = $this->TransactionLogs->findById($id);
		if(isset($transaction_log)) {

			try {
			
				$this->db->trans_begin();

				$invoice = $this->Invoices->findById($transaction_log->getReferenceId());
				if(isset($invoice)) {
					
					$last_paid_amount = $invoice->getPaidAmount() - $transaction_log->getAmount();
					
					$invoice->setPaidAmount($last_paid_amount);
					$invoice->save();
					
				}
								
				$transaction_log->delete();

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_56'), lang('c_371')));

			} catch(Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}
			
		}
		
		$ref = input_get_request('ref');
		$redirect_url = $ref != '' ? base64_decode($ref) : '';
		
		redirect($redirect_url);

	}
	
}
