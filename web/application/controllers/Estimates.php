<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Estimates extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(! (logged_user()->isOwner() || !logged_user()->isMember()) ) redirect('dashboard');
				
	}

	public function validate_date($date) {
		return $date != '' ? validate_date($date) : true;
	}	
	
	public function index() {

		if(logged_user()->isOwner()) {
			$estimates = $this->Estimates->getAll();
		} else {
			$estimates = $this->Estimates->getByClientOnly(logged_user());
		}
		
		tpl_assign('estimates', $estimates);
	
	}

	public function view($access_key) {

		$this->setLayout("page_dialog");
				
		$estimate = $this->Estimates->getByAccessKey($access_key);
		if(is_null($estimate) || (!logged_user()->isMember() 
		&& logged_user()->getId() != $estimate->getClientId()) ) {
		
			set_flash_error(lang('e_3'));
			redirect('dashboard');
		
		}

		tpl_assign('estimate', $estimate);
		
	}
	
	public function create() {

		if(!logged_user()->isOwner()) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('estimates/_estimate_form');

		$estimate = new Estimate();
		tpl_assign('estimate', $estimate);

		$client_users = $this->Users->getByClients();
		tpl_assign('client_users', $client_users);

		$projects = $this->Projects->getAll();
		tpl_assign('projects', $projects);

		$subject = input_post_request('subject');
		tpl_assign("subject", $subject);

		$client_id = input_post_request('client_id');
		tpl_assign("client_id", $client_id);

		$project_id = input_post_request('project_id');
		tpl_assign("project_id", $project_id);
	
		$status = input_post_request('status', 1);
		tpl_assign("status", $status);

		$currency = input_post_request('currency');
		tpl_assign("currency", $currency);

		$due_date = input_post_request('due_date');
		tpl_assign("due_date", $due_date);

		$tax = input_post_request('tax');
		tpl_assign("tax", $tax);

		$tax_rate = input_post_request('tax_rate');
		tpl_assign("tax_rate", $tax_rate);

		$tax2 = input_post_request('tax2');
		tpl_assign("tax2", $tax2);

		$tax_rate2 = input_post_request('tax_rate2');
		tpl_assign("tax_rate2", $tax_rate2);

		$discount_amount = (float) input_post_request('discount_amount');
		tpl_assign("discount_amount", $discount_amount);
		
		$discount_amount_type = input_post_request('discount_amount_type');
		tpl_assign("discount_amount_type", $discount_amount_type);

		$note = input_post_request('note');
		tpl_assign("note", $note);

		$private_note = input_post_request('private_note');
		tpl_assign("private_note", $private_note);

		$is_online_payment_disabled = input_post_request('is_online_payment_disabled') == 'on';
		tpl_assign("is_online_payment_disabled", $is_online_payment_disabled);

		$items = input_post_request('items_count', 0);
		$names = input_post_request("name");
		$quantities = input_post_request("quantity");
		$amounts = input_post_request("amount");

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('subject', lang('c_126'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('client_id', lang('c_29'), 'required|greater_than[0]',  array('greater_than' => lang('c_129')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_138')));

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$estimate->setClientId($client_id);
					$estimate->setProjectId($project_id);

					$estimate->setStatus($status == 1);

					$estimate->setSubject($subject);
					$estimate->setDueDate($due_date);

					$estimate->setTax($tax);
					$estimate->setTaxRate($tax_rate);

					$estimate->setTax2($tax2);
					$estimate->setTaxRate2($tax_rate2);

					$estimate->setDiscountAmount($discount_amount);
					$estimate->setDiscountAmountType($discount_amount_type);

					$estimate->setNote($note);
					$estimate->setPrivateNote($private_note);

					$estimate->setIsOnlinePaymentDisabled($is_online_payment_disabled);

					$estimate->setCreatedById(logged_user()->getId());

					$estimate->save();
					
					$sub_total=0;
					$o_key = 0;

					for ($i=0;$i<$items;$i++) {
					
						$name = array_key_value($names, $i);
						$quantity = abs(array_key_value($quantities, $i));
						$amount = abs(array_key_value($amounts, $i));

						if ($quantity > 0 && $amount > 0 && $name != "") {
							
							$estimate_item = new EstimateItem();

							$o_key++;
							$estimate_item->setOKey($o_key);
							
							$item_hash_id = sha1(uniqid(rand(), true).$estimate->getId().
							$quantity.$amount.$name.$o_key.time());
							
							$estimate_item->setUKey($item_hash_id);

							$estimate_item->setEstimateId($estimate->getId());
							$estimate_item->setQuantity($quantity);
							$estimate_item->setAmount($amount);
							$estimate_item->setDescription($name);
											
							$estimate_item->save();
											
							$sub_total += $amount*$quantity;
						
						}
							
					}

					$total_amount = $sub_total;

					if ($tax_rate > 0) {
					
						$tax_amount = abs($sub_total/100*$tax_rate);
						$total_amount += $tax_amount;

					}					

					if ($tax_rate2 > 0) {
					
						$tax2_amount = abs($sub_total/100*$tax_rate2);
						$total_amount += $tax2_amount;
					
					}					

					$calculated_discount_amount = $discount_amount;
					if($discount_amount > 0) {
						if($discount_amount_type == 'percentage') {
							$calculated_discount_amount = abs($discount_amount/100*$total_amount);
						}
					}

					$total_amount = ($total_amount - $calculated_discount_amount);
					$estimate->setTotalAmount($total_amount);
					
					$to_client = $estimate->getClient();
					$to_client_company = $to_client->getCompany();

					if(isset($to_client_company)) {

						$estimate->setCompanyId($to_client_company->getId());
						$estimate->setCompanyName($to_client_company->getName());

						$complate_company_address  = $to_client_company->getAddress() . ($to_client_company->getVatNo() != "" ? " (".lang('c_523.118').") " . $to_client_company->getVatNo() : "");
						$estimate->setCompanyAddress($complate_company_address);
					
					} else {

						// Indiviual client ..
						$estimate->setCompanyId(0);
						$estimate->setCompanyName(null);
						$estimate->setCompanyAddress($to_client->getAddress());
					
					}

					$estimate->setEstimateNo("EST-".str_pad($estimate->getId(), 6, '0', STR_PAD_LEFT));
					$estimate->setAccessKey(sha1(uniqid(rand(), true).$estimate->getId().time()));
					
					$estimate->save();

					$this->db->trans_commit();
					
					// // Send notification ..
					// $email_to_client = intval(input_post_request("email_to_client"));
					// if($email_to_client) {
 
					// 	$notify_subject = lang('c_127');
					// 	$notify_message = $this->load->view("emails/estimate", 
					// 	array("estimate_subject" => $estimate->getSubject(), "estimate_no" => $estimate->getEstimateNo(), 
					// 	"estimate_link" => get_page_base_url($estimate->getObjectURL())), true);

					// 	$notify_user_object = $this->Users->findById($client_id);
					// 	if(isset($notify_user_object)) {
					// 		try {
					// 			$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
					// 			send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
					// 		} catch (Exception $e){}
					// 	}
							
					// }
					
					set_flash_success(sprintf(lang('c_128'), lang('c_130')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}

		}
			
	}

	public function edit($id) {

		if(!logged_user()->isOwner()) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('estimates/_estimate_form');

		$estimate = $this->Estimates->findById($id);
		if(is_null($estimate)) set_flash_error(lang('e_3'), true);
		
		tpl_assign('estimate', $estimate);

		$client_users = $this->Users->getByClients();
		tpl_assign('client_users', $client_users);

		$projects = $this->Projects->getAll();
		tpl_assign('projects', $projects);

		$subject = input_post_request('subject', $estimate->getSubject());
		tpl_assign("subject", $subject);

		$client_id = input_post_request('client_id', $estimate->getClientId());
		tpl_assign("client_id", $client_id);

		$project_id = input_post_request('project_id', $estimate->getProjectId());
		tpl_assign("project_id", $project_id);
	
		$status = input_post_request('status', (int) $estimate->getStatus());
		tpl_assign("status", $status);

		$due_date_timestamp = $estimate->getDueDate();
		$formatted_due_date = $due_date_timestamp ? format_date($due_date_timestamp, 'm/d/Y') : null;
		
		$due_date = input_post_request('due_date', $formatted_due_date);
		tpl_assign("due_date", $due_date);

		$tax = input_post_request('tax', $estimate->getTax());
		tpl_assign("tax", $tax);

		$tax_rate = input_post_request('tax_rate', $estimate->getTaxRate());
		tpl_assign("tax_rate", $tax_rate);

		$tax2 = input_post_request('tax2', $estimate->getTax2());
		tpl_assign("tax2", $tax2);

		$tax_rate2 = input_post_request('tax_rate2', $estimate->getTaxRate2());
		tpl_assign("tax_rate2", $tax_rate2);

		$discount_amount = (float) input_post_request('discount_amount', $estimate->getDiscountAmount());
		tpl_assign("discount_amount", $discount_amount);
		
		$discount_amount_type = input_post_request('discount_amount_type', $estimate->getDiscountAmountType());
		tpl_assign("discount_amount_type", $discount_amount_type);

		$note = input_post_request('note', $estimate->getNote());
		tpl_assign("note", $note);

		$private_note = input_post_request('private_note', $estimate->getPrivateNote());
		tpl_assign("private_note", $private_note);

		$items = input_post_request('items_count', 0);
		$names = input_post_request("name");
		$quantities = input_post_request("quantity");
		$amounts = input_post_request("amount");

		$is_submited = input_post_request('submited') ==  'submited';

		$is_online_payment_disabled = $is_submited ? input_post_request('is_online_payment_disabled') == 'on' : $estimate->getIsOnlinePaymentDisabled();
		tpl_assign("is_online_payment_disabled", $is_online_payment_disabled);

		if ($is_submited) {

			$this->form_validation->set_rules('subject', lang('c_126'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('client_id', lang('c_29'), 'required|greater_than[0]',  array('greater_than' => lang('c_129')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_138')));

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$estimate->setClientId($client_id);
					$estimate->setProjectId($project_id);

					$estimate->setStatus($status == 1);

					$estimate->setSubject($subject);
					$estimate->setDueDate($due_date);

					$estimate->setTax($tax);
					$estimate->setTaxRate($tax_rate);

					$estimate->setTax2($tax2);
					$estimate->setTaxRate2($tax_rate2);

					$estimate->setDiscountAmount($discount_amount);
					$estimate->setDiscountAmountType($discount_amount_type);

					$estimate->setNote($note);
					$estimate->setPrivateNote($private_note);

					$estimate->setIsOnlinePaymentDisabled($is_online_payment_disabled);

					$estimate->clearEstimateItems();

					$sub_total=0;
					$o_key = 0;
					
					for ($i=0;$i<$items;$i++) {
					
						$name = array_key_value($names, $i);
						$quantity = abs(array_key_value($quantities, $i));
						$amount = abs(array_key_value($amounts, $i));

						if ($quantity > 0 && $amount > 0 && $name != "") {
							
							$estimate_item = new EstimateItem();

							$o_key++;
							$estimate_item->setOKey($o_key);
							
							$item_hash_id = sha1(uniqid(rand(), true).$estimate->getId().
							$quantity.$amount.$name.$o_key.time());
							
							$estimate_item->setUKey($item_hash_id);

							$estimate_item->setEstimateId($estimate->getId());
							$estimate_item->setQuantity($quantity);
							$estimate_item->setAmount($amount);
							$estimate_item->setDescription($name);
											
							$estimate_item->save();
											
							$sub_total += $amount*$quantity;
						
						}
							
					}

					$total_amount = $sub_total;

					if ($tax_rate > 0) {
					
						$tax_amount = abs($sub_total/100*$tax_rate);
						$total_amount += $tax_amount;

					}					

					if ($tax_rate2 > 0) {
					
						$tax2_amount = abs($sub_total/100*$tax_rate2);
						$total_amount += $tax2_amount;
					
					}					

					$calculated_discount_amount = $discount_amount;
					if($discount_amount > 0) {
						if($discount_amount_type == 'percentage') {
							$calculated_discount_amount = abs($discount_amount/100*$total_amount);
						}
					}

					$total_amount = ($total_amount - $calculated_discount_amount);
					$estimate->setTotalAmount($total_amount);

					$to_client = $estimate->getClient();
					$to_client_company = $to_client->getCompany();
					
					if(isset($to_client_company)) {

						$estimate->setCompanyId($to_client_company->getId());
						$estimate->setCompanyName($to_client_company->getName());

						$complate_company_address  = $to_client_company->getAddress() . ($to_client_company->getVatNo() != "" ? " (".lang('c_523.118').") " . $to_client_company->getVatNo() : "");
						$estimate->setCompanyAddress($complate_company_address);

					} else {

						// Indiviual client ..
						$estimate->setCompanyId(0);
						$estimate->setCompanyName(null);
						$estimate->setCompanyAddress($to_client->getAddress());
					
					}

					$estimate->save();

					$this->db->trans_commit();
					
					// // Send notification ..
					// $email_to_client = intval(input_post_request("email_to_client"));
					// if($email_to_client) {
 
					// 	$notify_subject = lang('c_127');
					// 	$notify_message = $this->load->view("emails/estimate", 
					// 	array("estimate_subject" => $estimate->getSubject(), "estimate_no" => $estimate->getEstimateNo(), 
					// 	"estimate_link" => get_page_base_url($estimate->getObjectURL())), true);

					// 	$notify_user_object = $this->Users->findById($client_id);
					// 	if(isset($notify_user_object)) {
					// 		try {
					// 			$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
					// 			send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
					// 		} catch (Exception $e){}
					// 	}
							
					// }
					
					set_flash_success(sprintf(lang('c_57'), lang('c_130')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}
			
		} 
			
	}

	public function convert($id) {

		if(!logged_user()->isOwner()) redirect("dashboard");
		
		$estimate = $this->Estimates->findById($id);
		if(isset($estimate)) {

			try {
			
				$this->db->trans_begin();

				$invoice = new Invoice();
				
				$invoice->setClientId($estimate->getClientId());
				$invoice->setProjectId($estimate->getProjectId());
				$invoice->setSubject($estimate->getSubject());

				$invoice->setStatus($estimate->getStatus());
				$invoice->setDueDate($estimate->getDueDate());

				$invoice->setTax($estimate->getTax());
				$invoice->setTaxRate($estimate->getTaxRate());

				$invoice->setTax2($estimate->getTax2());
				$invoice->setTaxRate2($estimate->getTaxRate2());

				$invoice->setDiscountAmount($estimate->getDiscountAmount());
				$invoice->setDiscountAmountType($estimate->getDiscountAmountType());

				$invoice->setNote($estimate->getNote());
				$invoice->setPrivateNote($estimate->getPrivateNote());

				$invoice->setIsOnlinePaymentDisabled($estimate->getIsOnlinePaymentDisabled());

				$invoice->setCompanyId($estimate->getCompanyId());
				$invoice->setCompanyName($estimate->getCompanyName());
				$invoice->setCompanyAddress($estimate->getCompanyAddress());

				$invoice->setCreatedById($estimate->getCreatedById());
				$invoice->save();

				$sub_total=0;
				$o_key = 0;
				
				$estimate_items = $estimate->getItems();
				if(isset($estimate_items) && is_array($estimate_items) && count($estimate_items)) {

					foreach($estimate_items as $estimate_item) {
					
						$invoice_item = new InvoiceItem();
	
						$o_key++;
						$invoice_item->setOKey($o_key);
						
						$item_hash_id = sha1(uniqid(rand(), true).$invoice->getId().
						$estimate_item->getQuantity().$estimate_item->getAmount().
						$estimate_item->getDescription().$o_key.time());
						
						$invoice_item->setUKey($item_hash_id);
	
						$invoice_item->setInvoiceId($invoice->getId());
						$invoice_item->setQuantity($estimate_item->getQuantity());
						$invoice_item->setAmount($estimate_item->getAmount());
						$invoice_item->setDescription($estimate_item->getDescription());
										
						$invoice_item->save();
										
						$sub_total += $estimate_item->getAmount()*$estimate_item->getQuantity();
					
					}
				
				}


				$total_amount = $sub_total;

				if ($estimate->getTaxRate() > 0) {
				
					$tax_amount = abs($sub_total/100*$estimate->getTaxRate());
					$total_amount += $tax_amount;

				}

				if ($estimate->getTaxRate2() > 0) {
				
					$tax2_amount = abs($sub_total/100*$estimate->getTaxRate2());
					$total_amount += $tax2_amount;
				
				}

				$calculated_discount_amount = $estimate->getDiscountAmount();
				if($calculated_discount_amount > 0) {
					if($estimate->getDiscountAmountType() == 'percentage') {
						$calculated_discount_amount = abs($calculated_discount_amount/100*$total_amount);
					}
				}

				$total_amount = ($total_amount - $calculated_discount_amount);
				$invoice->setTotalAmount($total_amount);
				
				$invoice->setInvoiceNo("INV-".str_pad($invoice->getId(), 6, '0', STR_PAD_LEFT));
				$invoice->setAccessKey(sha1(uniqid(rand(), true).$invoice->getId().time()));
				
				$invoice->save();
				
				// finished ..
				$estimate->delete();

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_131'), lang('c_130')));

			} catch(Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}
			
		}
				
		redirect("invoices");

	}

	public function download($access_key) {

		$this->setAutoRender(false);

		$estimate = $this->Estimates->getByAccessKey($access_key);
		if(is_null($estimate) || (!logged_user()->isMember() 
		&& logged_user()->getId() != $estimate->getClientId()) ) {
		
			set_flash_error(lang('e_3'));
			redirect('dashboard');
		
		}

		$html = $this->load->view("estimates/pdf", ["estimate" => $estimate], true);
		$pdf_file_name = lang("c_130") . "-" . $estimate->getId() . ".pdf";

		download_pdf_file($html, $pdf_file_name);

	}

	public function send_notification($access_key) {

		$estimate = $this->Estimates->getByAccessKey($access_key);
		if(isset($estimate) && logged_user()->isOwner()) {

			try {

				// Send notification ..
				$notify_subject = lang('c_171');
				$notify_message = $this->load->view("emails/invoice", 
				array("invoice_no" => $estimate->getInvoiceNo(), 
				"invoice_link" => get_page_base_url($estimate->getObjectURL())), true);

				$notify_user_object = $estimate->getClient();
				if(isset($notify_user_object)) {
					$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
					send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
				}
						
				set_flash_success(lang('c_523.98'));

			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}
			
		}
		
		$ref = input_get_request('ref');
		$redirect_url = $ref != '' ? base64_decode($ref) : '';
		
		redirect($redirect_url);

	}


}
