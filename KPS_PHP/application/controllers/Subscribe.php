<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribe extends Application_controller {
	
	function __construct() {		
		parent::__construct();
	}
	
	public function index() {

		initial_data_contoller($this);
		if(!logged_user()->isOwner()) redirect('dashboard');

	}

	public function ipn(){

		// Config vars ..
		$paypal_currency_code = 'USD';
		
		$paypal_account_query = i_config_option('paypal_account');
		$paypal_account = $paypal_account_query->row();
		$paypal_email = $paypal_account->value;
		
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
		$fp = @ fsockopen ('www.paypal.com', '80', $err_num, $err_str, 30);
				
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

				$item_number     = $ipn_data['item_number'];
				$item_name       = $ipn_data['item_name'];

				$custom_data     = explode("-", $ipn_data['custom']);
				if(isset($custom_data) && count($custom_data) == 2){
					$custom_hashcode = $custom_data[0];
					$subscription_id = $custom_data[1];
				}
				
				$txn_id         = $ipn_data['txn_id'];			
				$amount         = $ipn_data['mc_gross'];

				$mc_currency    = $ipn_data['mc_currency'];
				$mc_fee    		= $ipn_data['mc_fee'];
				$tax 			= $ipn_data['tax'];
				
				$receiver_email = $ipn_data['receiver_email'];
				$payer_email    = $ipn_data['payer_email'];
				$payer_name    = $ipn_data['first_name'] . ' ' . $ipn_data['last_name'];

				$amounts = array(1 => 4, 2 => 9, 3 => 19);

				$user_query = $this->db->query("SELECT * FROM users WHERE id = '".$item_number."' ");
				$user_record = $user_query->row();
				
				if(isset($user_record) && isset($custom_hashcode) && isset($subscription_id)
				&& $custom_hashcode == md5($user_record->token.$subscription_id) && $amount == $amounts[$subscription_id]
				&& $receiver_email == $paypal_email && $mc_currency == $paypal_currency_code) {

					try{
							
						$this->db->trans_begin();
	
						$this->db->query("INSERT INTO ipayment_orders (id, payment_method, is_verified, payer_name, payer_email, payment_status, receiver_email, send_amount, payment_fee, tax, txn_id, currency, raw_data, order_message, created_at, created_by_id, target_source_id) 
						VALUES (NULL, 'paypal', '0', ".$this->db->escape($payer_name).", '".$payer_email."', 'completed', '".$receiver_email."', '".$amount."', '".$mc_fee."', '".$tax."', '".$txn_id."', '".$mc_currency."', ".$this->db->escape($raw).", ".$this->db->escape($item_name).", GETDATE(), '".$user_record->id."', '".$user_record->target_source_id."');");

						$this->db->query("UPDATE target_sources SET 
						subscription_id = '".$subscription_id."', 
						expire_date = DATE_ADD(expire_date, INTERVAL 30 DAY),
						updated_at = GETDATE()
						WHERE id = ".$user_record->target_source_id);
																	
						$this->db->trans_commit();
			
					} catch (Exception $e) {
						$this->db->trans_rollback();
					}
						
				}

			}

		}
		
		die(); // Finished ..
		
	}
	
}