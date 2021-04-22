<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'libraries/Stripe/lib/Stripe.php');

class Invoices extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(! (logged_user()->isOwner() || !logged_user()->isMember()) ) redirect('dashboard');
				
	}
	
	public function validate_date($date) {
		return $date != '' ? validate_date($date) : true;
	}

	public function compare_dates() {
	
		$start = strtotime($this->input->post('issue_date'));
		$end = strtotime($this->input->post('due_date'));
	
		if($start > $end) {
			$this->form_validation->set_message('compare_dates',lang('c_191'));
			return false;
		}

	}

	public function index() {

		$sort_by = input_get_request('sort_by');
		tpl_assign('sort_by', $sort_by);

		$current_page_base_url = get_page_base_url('invoices');
		tpl_assign('current_page_base_url', $current_page_base_url);

		if(logged_user()->isOwner()) {
			
			switch($sort_by) {

				case 'recurring' : 
					$invoices = $this->Invoices->getRecurring();
					break;
				
				case 'paid_cancelled':
					$invoices = $this->Invoices->getPaidAndCancelled();
					break;

				default:
					$invoices = $this->Invoices->getAvailable();
					break;

			}
			
		} else {
			
			switch($sort_by) {
				
				case 'paid_cancelled':
					$invoices = $this->Invoices->getPaidAndCancelledClientOnly(logged_user());
					break;

				default:
					$invoices = $this->Invoices->getAvailableByClientOnly(logged_user());
					break;

			}

		}
		
		tpl_assign('invoices', $invoices);
	
	}

	private function validate($access_key) {

		$invoice = $this->Invoices->getByAccessKey($access_key);
		if(is_null($invoice) || (!logged_user()->isMember() 
		&& logged_user()->getId() != $invoice->getClientId()) ) {
		
			set_flash_error(lang('e_3'));
			redirect('dashboard');
		
		}
		
		return $invoice;
	
	}
	
	public function view($access_key) {

		$this->setLayout("page_dialog");
		
		$invoice = $this->validate($access_key);		
		tpl_assign('invoice', $invoice);

		$paymentmode = input_post_request('paymentmode');
		tpl_assign("paymentmode", $paymentmode);

		$amount = (float) input_post_request('amount');
		tpl_assign("amount", $amount);

		$is_submited = input_post_request('submited') ==  'submited';
		tpl_assign("is_submited", $is_submited);

		if($is_submited && $amount <= 0) {

			set_flash_error(lang('c_370'));
			redirect($invoice->getObjectURL());

		}

	}

	public function payment($gateway, $access_key) {

		$this->setLayout("page_dialog");
		$invoice = $this->validate($access_key);
				
		switch($gateway) {
		
			case 'stripe' :

				$amount = (float) input_post_request('amount');
				$access_token = input_post_request('stripeToken');

				$stripe_secret_key = config_option('stripe_secret_key'); 
				$stripe_currency_code = config_option('stripe_currency_code'); 

				try {
				
					Stripe::setApiKey($stripe_secret_key);
					$charge = Stripe_Charge::create(array(
								"amount" => round(($amount)*100),
								"currency" => $stripe_currency_code,
								"card" => $access_token,
								"description" => "Payment for Invoice ".$invoice->getInvoiceNo()
					));

					try{
						
						$this->db->trans_begin();
	
						$transaction_log = new TransactionLog();
	
						$transaction_log->setAmount($amount);
						$transaction_log->setTransactionType("payment");
						$transaction_log->setReferenceId($invoice->getId());
						$transaction_log->setDescription("Stripe payment (".$charge->id.")");
						
						$transaction_log->setCreditAccountId($invoice->getClientId());
						$transaction_log->setDebitAccountId(owner_company()->getCreatedById());
						
						$transaction_log->setTargetSourceId($invoice->getTargetSourceId());

						$transaction_log->save();
							
						$total_paid_amount = $invoice->getPaidAmount() + $amount;
						$invoice->setPaidAmount($total_paid_amount);
							
						$invoice->save();
																	
						$this->db->trans_commit();

						set_flash_success(sprintf(lang('c_79'), lang('c_449')));
														
					} catch (Exception $e) {
					
						set_flash_error(lang('e_3'));
						$this->db->trans_rollback();
					
					}

				} catch (Stripe_CardError $e) {
					set_flash_error(STRIPE_FAILED);
				} catch (Stripe_InvalidRequestError $e) {
					set_flash_error($e->getMessage());
				} catch (Stripe_AuthenticationError $e) {
					set_flash_error(AUTHENTICATION_STRIPE_FAILED);
				} catch (Stripe_ApiConnectionError $e) {
					set_flash_error(NETWORK_STRIPE_FAILED);
				} catch (Stripe_Error $e) {
					set_flash_error(STRIPE_FAILED);
				} catch (Exception $e) {
					set_flash_error(STRIPE_FAILED);
				}
			
			break;
			
			default:
				
				set_flash_error(lang('e_3'));
				
			break;		

		}		

		redirect($invoice->getObjectURL());
		
	}
		
	public function create($request_project_id = null) {

		if(!logged_user()->isOwner()) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('invoices/_invoice_form');

		$invoice = new Invoice();
		tpl_assign('invoice', $invoice);

		$client_users = $this->Users->getByClients();
		tpl_assign('client_users', $client_users);

		$projects = $this->Projects->getAll();
		tpl_assign('projects', $projects);

		$subject = input_post_request('subject');
		tpl_assign("subject", $subject);

		$client_id = input_post_request('client_id');
		tpl_assign("client_id", $client_id);

		$project_id = input_post_request('project_id', $request_project_id);
		tpl_assign("project_id", $project_id);

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

		$reference = input_post_request('reference');
		tpl_assign("reference", $reference);

		$note = input_post_request('note');
		tpl_assign("note", $note);

		$private_note = input_post_request('private_note');
		tpl_assign("private_note", $private_note);

		$is_online_payment_disabled = input_post_request('is_online_payment_disabled') == 'on';
		tpl_assign("is_online_payment_disabled", $is_online_payment_disabled);
 
		$issue_date = input_post_request('issue_date');
		tpl_assign("issue_date", $issue_date);

		$due_date = input_post_request('due_date');
		tpl_assign("due_date", $due_date);

		$is_recurring = input_post_request('is_recurring') == 'on';
		tpl_assign("is_recurring", $is_recurring);

		$recurring_value = input_post_request('recurring_value');
		tpl_assign("recurring_value", $recurring_value);
		
		$recurring_type = input_post_request('recurring_type');
		tpl_assign("recurring_type", $recurring_type);
		
		$no_of_cycles = input_post_request('no_of_cycles');
		tpl_assign("no_of_cycles", $no_of_cycles);

		$items = input_post_request('items_count', 0);
		$names = input_post_request("name");
		$quantities = input_post_request("quantity");
		$amounts = input_post_request("amount");
		
		$timelog_ids = input_post_request("timelog_ids");
		$timelog_ids_data = [];
		if(isset($timelog_ids) && is_array($timelog_ids) && count($timelog_ids)) {
			foreach($timelog_ids as $key => $timelog_ids_value) {
				$timelog_ids_data["item_" . $key] = json_decode($timelog_ids_value);
			}
		}

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('subject', lang('c_126'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('client_id', lang('c_29'), 'required|greater_than[0]',  array('greater_than' => lang('c_129')));

			$this->form_validation->set_rules('issue_date', lang('c_523.92'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_523.92')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'required|callback_validate_date',  
			array('validate_due_date' => lang('c_322')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'trim|callback_compare_dates');

			if($is_recurring) {
				$this->form_validation->set_rules('recurring_value', lang('c_523.80'), 'required|greater_than[0]',  array('greater_than' => lang('c_523.86')));
			}

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$invoice->setClientId($client_id);
					$invoice->setProjectId($project_id);

					$invoice->setSubject($subject);

					if(!$is_recurring) {

						$invoice->setDueDate($due_date);
						$invoice->setIssueDate($issue_date);
				
					} else {
						$date_diff_object = date_diff(date_create($issue_date), date_create($due_date));
						$invoice->setDueAfterDays($date_diff_object->days);
						$invoice->setNextRecurringDate($issue_date);
					}				

					$invoice->setTax($tax);
					$invoice->setTaxRate($tax_rate);

					$invoice->setTax2($tax2);
					$invoice->setTaxRate2($tax_rate2);

					$invoice->setDiscountAmount($discount_amount);
					$invoice->setDiscountAmountType($discount_amount_type);

					$invoice->setReference($reference);

					$invoice->setNote($note);
					$invoice->setPrivateNote($private_note);

					$invoice->setIsRecurring($is_recurring);
					$invoice->setRecurringValue($recurring_value);
					$invoice->setRecurringType($recurring_type);
					if(isset($no_of_cycles) && $no_of_cycles > 0) {
						$invoice->setNoOfCycles($no_of_cycles);
					} else {
						$invoice->setNoOfCycles(NULL);
					}

					$invoice->setIsOnlinePaymentDisabled($is_online_payment_disabled);

					$invoice->setCreatedById(logged_user()->getId());

					$invoice->save();
					
					$sub_total=0;
					$o_key = 0;

					for ($i=0;$i<$items;$i++) {
					
						$name = array_key_value($names, $i);
						$quantity = abs(array_key_value($quantities, $i));
						$amount = abs(array_key_value($amounts, $i));
				
						if ($quantity > 0 && $amount > 0 && $name != "") {
							
							$invoice_item = new InvoiceItem();

							$o_key++;
							$invoice_item->setOKey($o_key);
							
							$item_hash_id = sha1(uniqid(rand(), true).$invoice->getId().
							$quantity.$amount.$name.$o_key.time());
							
							$invoice_item->setUKey($item_hash_id);

							$invoice_item->setInvoiceId($invoice->getId());
							$invoice_item->setQuantity($quantity);
							$invoice_item->setAmount($amount);
							$invoice_item->setDescription($name);
											
							$invoice_item->save();

							// Save billable entries
							$timelog_ids = (array) array_key_value($timelog_ids_data, "item_" . $i);
							if(count($timelog_ids)) {
								$billable_timelogs = $this->ProjectTimelogs->getApprovedBillableByIds($timelog_ids);
								if(isset($billable_timelogs) && is_array($billable_timelogs) && count($billable_timelogs)) {
									foreach($billable_timelogs as $billable_timelog) {

										$invoice_item_project_timelog = new InvoiceItemProjectTimelog();
										
										$invoice_item_project_timelog->setInvoiceUKey($invoice_item->getUKey());
										$invoice_item_project_timelog->setProjectTimelogId($billable_timelog->getId());
										$invoice_item_project_timelog->save();

									}
								}
							}

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
					$invoice->setTotalAmount($total_amount);

					$to_client = $invoice->getClient();
					$to_client_company = $to_client->getCompany();

					if(isset($to_client_company)) {

						$invoice->setCompanyId($to_client_company->getId());
						$invoice->setCompanyName($to_client_company->getName());

						$complate_company_address  = $to_client_company->getAddress() . ($to_client_company->getVatNo() != "" ? " (".lang('c_523.118').") " . $to_client_company->getVatNo() : "");
						$invoice->setCompanyAddress($complate_company_address);
					
					} else {

						// Indiviual client ..
						$invoice->setCompanyId(0);
						$invoice->setCompanyName(null);
						$invoice->setCompanyAddress($to_client->getAddress());
					
					}

					$invoice->setInvoiceNo("INV-".str_pad($invoice->getId(), 6, '0', STR_PAD_LEFT));
					$invoice->setAccessKey(sha1(uniqid(rand(), true).$invoice->getId().time()));
					
					$invoice->save();

					$this->db->trans_commit();
					
					set_flash_success(sprintf(lang('c_128'), lang('c_173')));
		
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
		$this->setTemplate('invoices/_invoice_form');

		$invoice = $this->Invoices->findById($id);
		if(is_null($invoice) || $invoice->getIsCancelled()) set_flash_error(lang('e_3'), true);
		
		tpl_assign('invoice', $invoice);

		$client_users = $this->Users->getByClients();
		tpl_assign('client_users', $client_users);

		$projects = $this->Projects->getAll();
		tpl_assign('projects', $projects);

		$subject = input_post_request('subject', $invoice->getSubject());
		tpl_assign("subject", $subject);

		$client_id = input_post_request('client_id', $invoice->getClientId());
		tpl_assign("client_id", $client_id);

		$project_id = input_post_request('project_id', $invoice->getProjectId());
		tpl_assign("project_id", $project_id);

		$tax = input_post_request('tax', $invoice->getTax());
		tpl_assign("tax", $tax);

		$tax_rate = input_post_request('tax_rate', $invoice->getTaxRate());
		tpl_assign("tax_rate", $tax_rate);

		$tax2 = input_post_request('tax2', $invoice->getTax2());
		tpl_assign("tax2", $tax2);

		$tax_rate2 = input_post_request('tax_rate2', $invoice->getTaxRate2());
		tpl_assign("tax_rate2", $tax_rate2);

		$discount_amount = (float) input_post_request('discount_amount', $invoice->getDiscountAmount());
		tpl_assign("discount_amount", $discount_amount);
		
		$discount_amount_type = input_post_request('discount_amount_type', $invoice->getDiscountAmountType());
		tpl_assign("discount_amount_type", $discount_amount_type);

		$reference = input_post_request('reference', $invoice->getReference());
		tpl_assign("reference", $reference);

		$note = input_post_request('note', $invoice->getNote());
		tpl_assign("note", $note);

		$private_note = input_post_request('private_note', $invoice->getPrivateNote());
		tpl_assign("private_note", $private_note);

		$issue_date_timestamp = $invoice->getIsRecurring() ? $invoice->getNextRecurringDate() : $invoice->getIssueDate();
		$formatted_issue_date = $issue_date_timestamp ? format_date($issue_date_timestamp, 'm/d/Y') : null;

		$issue_date = input_post_request('issue_date', $formatted_issue_date);
		tpl_assign("issue_date", $issue_date);

		$due_date = input_post_request('due_date');
		tpl_assign("due_date", $due_date);

		$recurring_value = input_post_request('recurring_value', $invoice->getRecurringValue());
		tpl_assign("recurring_value", $recurring_value);
		
		$recurring_type = input_post_request('recurring_type', $invoice->getRecurringType());
		tpl_assign("recurring_type", $recurring_type);
		
		$no_of_cycles = (int) input_post_request('no_of_cycles', $invoice->getNoOfCycles());
		tpl_assign("no_of_cycles", $no_of_cycles);

		$items = input_post_request('items_count', 0);
		$names = input_post_request("name");
		$quantities = input_post_request("quantity");
		$amounts = input_post_request("amount");
		
		$timelog_ids = input_post_request("timelog_ids");
		$timelog_ids_data = [];
		if(isset($timelog_ids) && is_array($timelog_ids) && count($timelog_ids)) {
			foreach($timelog_ids as $key => $timelog_ids_value) {
				$timelog_ids_data["item_" . $key] = json_decode($timelog_ids_value);
			}
		}

		$is_submited = input_post_request('submited') ==  'submited';

		$is_online_payment_disabled = $is_submited ? input_post_request('is_online_payment_disabled') == 'on' : $invoice->getIsOnlinePaymentDisabled();
		tpl_assign("is_online_payment_disabled", $is_online_payment_disabled);

		$is_recurring = $is_submited ? input_post_request('is_recurring') == 'on' : $invoice->getIsRecurring();
		tpl_assign("is_recurring", $is_recurring);

		if ($is_submited) {

			$this->form_validation->set_rules('subject', lang('c_126'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('client_id', lang('c_29'), 'required|greater_than[0]',  array('greater_than' => lang('c_129')));

			$this->form_validation->set_rules('issue_date', lang('c_523.92'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_523.92')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'required|callback_validate_date',  
			array('validate_due_date' => lang('c_322')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'trim|callback_compare_dates');

			if($is_recurring) {
				$this->form_validation->set_rules('recurring_value', lang('c_523.80'), 'required|greater_than[0]',  array('greater_than' => lang('c_523.86')));
			}

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$invoice->setClientId($client_id);
					$invoice->setProjectId($project_id);

					$invoice->setSubject($subject);

					if(!$is_recurring) {
						
						$invoice->setDueDate($due_date);
						$invoice->setIssueDate($issue_date);
				
					} else {
						$date_diff_object = date_diff(date_create($issue_date), date_create($due_date));
						$invoice->setDueAfterDays($date_diff_object->days);
						$invoice->setNextRecurringDate($issue_date);
					}				

					$invoice->setTax($tax);
					$invoice->setTaxRate($tax_rate);

					$invoice->setTax2($tax2);
					$invoice->setTaxRate2($tax_rate2);

					$invoice->setDiscountAmount($discount_amount);
					$invoice->setDiscountAmountType($discount_amount_type);
					
					$invoice->setReference($reference);

					$invoice->setNote($note);
					$invoice->setPrivateNote($private_note);

					// $invoice->setIsRecurring($is_recurring);
					$invoice->setRecurringValue($recurring_value);
					$invoice->setRecurringType($recurring_type);
					$invoice->setNoOfCycles($no_of_cycles);

					$invoice->setIsOnlinePaymentDisabled($is_online_payment_disabled);
					
					$invoice->clearInvoiceItems();

					$sub_total=0;
					$o_key = 0;
					
					for ($i=0;$i<$items;$i++) {
					
						$name = array_key_value($names, $i);
						$quantity = abs(array_key_value($quantities, $i));
						$amount = abs(array_key_value($amounts, $i));

						if ($quantity > 0 && $amount > 0 && $name != "") {
							
							$invoice_item = new InvoiceItem();

							$o_key++;
							$invoice_item->setOKey($o_key);
							
							$item_hash_id = sha1(uniqid(rand(), true).$invoice->getId().
							$quantity.$amount.$name.$o_key.time());
							
							$invoice_item->setUKey($item_hash_id);

							$invoice_item->setInvoiceId($invoice->getId());
							$invoice_item->setQuantity($quantity);
							$invoice_item->setAmount($amount);
							$invoice_item->setDescription($name);
											
							$invoice_item->save();

							// Save billable entries
							$timelog_ids = (array) array_key_value($timelog_ids_data, "item_" . $i);
							if(count($timelog_ids)) {
								$billable_timelogs = $this->ProjectTimelogs->getApprovedBillableByIds($timelog_ids);
								if(isset($billable_timelogs) && is_array($billable_timelogs) && count($billable_timelogs)) {
									foreach($billable_timelogs as $billable_timelog) {

										$invoice_item_project_timelog = new InvoiceItemProjectTimelog();
										
										$invoice_item_project_timelog->setInvoiceUKey($invoice_item->getUKey());
										$invoice_item_project_timelog->setProjectTimelogId($billable_timelog->getId());
										$invoice_item_project_timelog->save();

									}
								}
							}
							
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
					$invoice->setTotalAmount($total_amount);

					$to_client = $invoice->getClient();
					$to_client_company = $to_client->getCompany();

					if(isset($to_client_company)) {

						$invoice->setCompanyId($to_client_company->getId());
						$invoice->setCompanyName($to_client_company->getName());

						$complate_company_address  = $to_client_company->getAddress() . ($to_client_company->getVatNo() != "" ? " (".lang('c_523.118').") " . $to_client_company->getVatNo() : "");
						$invoice->setCompanyAddress($complate_company_address);

					} else {

						// Indiviual client ..
						$invoice->setCompanyId(0);
						$invoice->setCompanyName(null);
						$invoice->setCompanyAddress($to_client->getAddress());
					
					}

					$invoice->save();

					$this->db->trans_commit();
					
					set_flash_success(sprintf(lang('c_57'), lang('c_173')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}
			
		} 
			
	}
	
	public function cancel($id) {

		if(!logged_user()->isOwner()) die();
		only_ajax_request_allowed();
	
		$this->setLayout('modal');
	
		$invoice = $this->Invoices->findById($id);

		if(is_null($invoice) || $invoice->getIsCancelled() || $invoice->getIsRecurring() ) {
			set_flash_error(lang('e_3'), true);
		}
		
		tpl_assign('invoice', $invoice);
		$is_submited = input_post_request('submited') ==  'submited';		

		if ($is_submited) {

			try {

				$this->db->trans_begin();
				
				$invoice->setIsCancelled(true);
				$invoice->setPaidAmount(0);
				$invoice->save();

				$invoice->clearPayments();

				$this->ActivityLogs->create($invoice, lang('c_523.72'), lang('c_37'));

				$this->db->trans_commit();
				
				set_flash_success(lang('c_523.73'));
				
			} catch(Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}

			$this->renderText(output_ajax_request(true));
			
		}

	}

	public function download($access_key) {

		$this->setAutoRender(false);

		$invoice = $this->Invoices->getByAccessKey($access_key);
		if(is_null($invoice) || (!logged_user()->isMember() 
		&& logged_user()->getId() != $invoice->getClientId()) ) {
			
			set_flash_error(lang('e_3'));
			redirect('dashboard');
		
		}

		$html = $this->load->view("invoices/pdf", ["invoice" => $invoice], true);
		$pdf_file_name = lang("c_173") . "-" . $invoice->getId() . ".pdf";

		download_pdf_file($html, $pdf_file_name);

	}

	public function send_notification($access_key) {

		$invoice = $this->Invoices->getByAccessKey($access_key);
		if(isset($invoice) && logged_user()->isOwner()) {

			try {

				// Send notification ..
				$notify_subject = lang('c_171');
				$notify_message = $this->load->view("emails/invoice", 
				array("invoice_no" => $invoice->getInvoiceNo(), 
				"invoice_link" => get_page_base_url($invoice->getObjectURL())), true);

				$notify_user_object = $invoice->getClient();
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

	public function clone_entry($access_key) {

		$invoice = $this->Invoices->getByAccessKey($access_key);
		if(isset($invoice) && logged_user()->isOwner()) {

			try {

				$this->db->trans_begin();

				clone_invoice($invoice);

				$this->db->trans_commit();
						
				set_flash_success(lang('c_523.100'));

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
