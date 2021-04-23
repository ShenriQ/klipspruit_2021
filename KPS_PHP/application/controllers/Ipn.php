<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ipn extends Application_controller {
	
	function __construct() {
	
		parent::__construct();
		$this->load->model("IpnLogs");		
	
	}
	
	public function process() {
		
		// Config vars ..
		$paypal_currency_code = config_option('paypal_currency_code', 'USD');
		$paypal_email = config_option('paypal_email');
		$paypal_sandbox = config_option('paypal_sandbox', 'yes');
		
		$paypal_host = $paypal_sandbox == 'no' ? 'www.paypal.com' : 'www.sandbox.paypal.com';
		
		$paypal_path = '/cgi-bin/webscr';
		$ipn_data = array ();
		$ipn_response = '';
		
		$post_string = '';
		foreach ($_POST as $field => $value){
		
			$ipn_data[$field] = $value;
			$post_string     .= $field.'='.urlencode ($value).'&';
		
		} 
		
		$post_string .= "cmd=_notify-validate"; // append ipn command
		
		// Open the connection to paypal
		if($paypal_sandbox == 'no') {
			$fp = @ fsockopen ($paypal_host, '80', $err_num, $err_str, 30);
		} else {
			$fp = @ fsockopen ("ssl://".$paypal_host, '443', $err_num, $err_str, 30);		
		}
				
		if (!$fp) {
		
			// Could not open the connection.
			exit();
			
		}else{
			
			// Post the data back to paypal
			@ fputs ($fp, "POST {$paypal_path} HTTP/1.1\n");
			@ fputs ($fp, "Host: {$paypal_host}\n");
			@ fputs ($fp, "Content-type: application/x-www-form-urlencoded\n");
			@ fputs ($fp, "Content-length: ".strlen ($post_string)."\n");
			@ fputs ($fp, "Connection: close\n\n");
			@ fputs ($fp, $post_string."\n\n");
		
			// Loop through the response from the server and append to variable
			while (!feof ($fp))
				$ipn_response .= @ fgets ($fp, 1024);
		
		   // Close connection
		   @ fclose ($fp);
		   
		} // if
		
		if (stripos ($ipn_response, "VERIFIED") === false){
			
			// Invalid IPN transaction.
			exit();
		
		}else{
			
			// Valid IPN transaction.
				
			// Timestamp
			$raw = "[".date ('m/d/Y g:i A')."] - IPN POST Vars from Paypal:\n";
			
			// Log the POST variables
			foreach ($ipn_data as $key=>$value){
			  $raw .= "{$key}\t{$value}\n";
			}
			
			$raw .= "\nIPN Response from Paypal Server:\n ".$ipn_response;

			$process_success = false;																																				

			if ($ipn_data['payment_status'] == "Completed") {

				$item_number    = $ipn_data['item_number'];
				$access_key     = $ipn_data['custom'];
				$txn_id         = $ipn_data['txn_id'];		
				
				$amount       = $ipn_data['payment_gross'];
				$mc_currency    = $ipn_data['mc_currency'];

				$receiver_email = $ipn_data['receiver_email'];
				$payer_email    = $ipn_data['payer_email'];


				$invoice = $this->Invoices->getByAccessKey($access_key);
			
				if(isset($invoice) && $invoice->getId() == $item_number 
				&& $receiver_email == $paypal_email && $mc_currency == $paypal_currency_code){
					
					try{
						
						$this->db->trans_begin();
	
						$transaction_log = new TransactionLog();
	
						$transaction_log->setAmount($amount);
						$transaction_log->setTransactionType("payment");
						$transaction_log->setReferenceId($item_number);
						$transaction_log->setDescription("PayPal payment from ".$payer_email." (".$txn_id.")");
						
						$transaction_log->setCreditAccountId($invoice->getClientId());
						$transaction_log->setDebitAccountId(owner_company()->getCreatedById());
						
						$transaction_log->setTargetSourceId($invoice->getTargetSourceId());

						$transaction_log->save();
							
						$total_paid_amount = $invoice->getPaidAmount() + $amount;
						$invoice->setPaidAmount($total_paid_amount);
							
						$invoice->save();
																	
						$this->db->trans_commit();
											
						$process_success = true;
			
					} catch (Exception $e) {
						$this->db->trans_rollback();
					}
					
				}
				
			}

			// Save IPN Log ..
			$this->IpnLogs->create($raw, $process_success);
		
		}

		// Finally, exit the process ..
		exit();

	}
	
}