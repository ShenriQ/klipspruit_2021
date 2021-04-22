<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends Application_controller {
	
	function __construct() {
	
		parent::__construct();
		$this->setLayout("dialog");
	
	}

	public function register() {

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$email = input_post_request('email');
		tpl_assign("email", $email);

		$address = input_post_request('address');
		tpl_assign("address", $address);

		$phone_number = input_post_request('phone_number');
		tpl_assign("phone_number", $phone_number);

		$company_name = input_post_request('company_name');
		tpl_assign("company_name", $company_name);

		$workspace_name = input_post_request('workspace_name');
		tpl_assign("workspace_name", $workspace_name);

		$subscribe_default = input_get_request('subscribe', 1);
		$subscription_id = input_post_request('subscription_id', $subscribe_default);
		tpl_assign("subscription_id", $subscription_id);

		$ipackages = get_packages(false);
		$packages = array();
		foreach($ipackages as $ipackage) {
			$packages[$ipackage->getId()] = $ipackage->getName();
			if($ipackage->getId() == $subscription_id) {
				$select_package = $ipackage;
			}
		}
		tpl_assign('packages_options', $packages);
		$password = input_post_request('password');

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('subscription_id', lang('c_536'), 'required|numeric');
			$this->form_validation->set_rules('name', lang('c_526'), 'trim|required|max_length[30]');
			$this->form_validation->set_rules('email', lang('c_527'), 'trim|valid_email|required|max_length[100]|is_unique['.$this->Users->getTableName().'.email]');
			$this->form_validation->set_rules('company_name', lang('c_528'), 'trim|required|max_length[50]');
			$this->form_validation->set_rules('password', lang('c_529'), 'trim|required|min_length[8]|max_length[20]');
			$this->form_validation->set_rules('workspace_name', lang('c_530'), 'trim|required|max_length[100]');
			
			if ($this->form_validation->run() == FALSE) {
				$error_msg = validation_errors();
			} else {

				if(!isset($select_package)) {
					$error_msg = lang('c_541');
				} else {

					$glabel_data = "INSERT INTO `global_labels` (`id`, `type`, `name`, `bg_color_hex`, `is_default`, `is_active`, `target_source_id`) VALUES
					(NULL, 'PROJECT', 'NEW', 'FFEB3B', 1, 1, [TARGET_SOURCE]),
					(NULL, 'PROJECT', 'CANCELED', 'FF5722', 0, 1, [TARGET_SOURCE]),
					(NULL, 'PROJECT', 'INPROGRESS', '8BC34A', 0, 1, [TARGET_SOURCE]),
					(NULL, 'PROJECT', 'PAUSED', 'FF5722', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'NEW', 'F5BA42', 1, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'CONFIRMED', 'B276D8', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'DUPLICATE', '31353C', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'WONT FIX', '7277D5', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'ASSIGNED', 'D9434E', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'BLOCKED', 'E3643E', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'IN PROGRESS', 'A5ADB8', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'FIXED', 'F59B43', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'REOPENED', '4B8CDC', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TASK', 'VERIFIED', 'B1C252', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TICKET', 'NEW', 'FFEB3B', 1, 1, [TARGET_SOURCE]),
					(NULL, 'TICKET', 'INPROGRESS', '8BC34A', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TICKET', 'PAUSED', 'FF5722', 0, 1, [TARGET_SOURCE]),
					(NULL, 'TICKET', 'DONE', 'B276D8', 0, 1, [TARGET_SOURCE]);";
	
					$config_data = "INSERT INTO `configurations` (`id`, `category_name`, `name`, `value`, `target_source_id`) VALUES
					(NULL, 'mailing', 'smtp_server', 'server@example.com', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_port', '101', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_authenticate', '1', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_username', 'username', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_password', 'password', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_secure_connection', 'no', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_reply_from_email', 'noreply@example.com', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_from_name', 'PROMS', [TARGET_SOURCE]),
					(NULL, 'mailing', 'smtp_from_email', 'support@example.com', [TARGET_SOURCE]),
					(NULL, 'system', 'site_name', 'Project Management System', [TARGET_SOURCE]),
					(NULL, 'system', 'contact_email', 'contact@example.com', [TARGET_SOURCE]),
					(NULL, 'system', 'default_currency', '$', [TARGET_SOURCE]),
					(NULL, 'system', 'items_per_page', '10', [TARGET_SOURCE]),
					(NULL, 'system', 'calendar_google_api_key', NULL, [TARGET_SOURCE]),
					(NULL, 'system', 'calendar_google_event_address', NULL, [TARGET_SOURCE]),
					(NULL, 'system', 'paypal_email', 'paypal_sandbox@example.com', [TARGET_SOURCE]),
					(NULL, 'system', 'paypal_sandbox', 'yes', [TARGET_SOURCE]),
					(NULL, 'system', 'paypal_currency_code', 'USD', [TARGET_SOURCE]),
					(NULL, 'system', 'stripe_secret_key', 'sk_live_lV5nITIlIlzvSZmkfunl5bH3', [TARGET_SOURCE]),
					(NULL, 'system', 'stripe_publishable_key', 'pk_live_ogI79OxdoS6YUGWLkkAVwbC3', [TARGET_SOURCE]),
					(NULL, 'system', 'stripe_currency_code', 'USD', [TARGET_SOURCE]),
					(NULL, 'system', 'offline_bank_name', 'Sample Bank', [TARGET_SOURCE]),
					(NULL, 'system', 'offline_bank_account', 'XXXXXX00000000', [TARGET_SOURCE]),
					(NULL, 'system', 'invoice_color', '#00A65A', [TARGET_SOURCE]),
					(NULL, 'system', 'send_due_date_invoice_reminder_before_days', '1', [TARGET_SOURCE]),
					(NULL, 'system', 'send_due_date_invoice_reminder_after_days', '1', [TARGET_SOURCE]),
					(NULL, 'system', 'logo_text', 'PROMS', [TARGET_SOURCE]);";

					$ticket_types_data = "INSERT INTO `ticket_types` (`id`, `name`, `is_active`, `target_source_id`) 
					VALUES (NULL, 'General Support', '1', [TARGET_SOURCE]);";
					
					try {
	
						$this->db->trans_begin();
	
						$this->db->query("INSERT INTO `target_sources` (id, name, subscription_id, expire_date, storage_limit, projects_limit, users_limit, created_at, updated_at) 
						VALUES (NULL, ".$this->db->escape($workspace_name).", ".$subscription_id.", (NOW() + INTERVAL 30 DAY), ".$select_package->getMaxStorage().", ".$select_package->getMaxProjects().", ".$select_package->getMaxUsers().", NOW(), NOW())");
						
						$source_id = $this->db->insert_id();
						
						$glabel_sql = str_replace("[TARGET_SOURCE]", $source_id, $glabel_data);
						$this->db->query($glabel_sql);
	
						$config_sql = str_replace("[TARGET_SOURCE]", $source_id, $config_data);
						$this->db->query($config_sql);

						$ticket_types_sql = str_replace("[TARGET_SOURCE]", $source_id, $ticket_types_data);
						$this->db->query($ticket_types_sql);
						
						$company = new Company();
						$company->setName($company_name);
						$company->setParentId(0); // Owner company ..
						$company->setTargetSourceId($source_id);
						$company->save();
						
						$user = new User();
						$user->setName($name);
						$user->setCompanyId($company->getId());
						$user->setEmail($email);
						$user->setPassword($password);
						$user->setIsActive(true);
						$user->setTargetSourceId($source_id);
						$user->save();
	
						$this->db->simple_query("UPDATE `companies` SET `created_by_id` = ".$user->getId()." WHERE `id` = ".$company->getId()." LIMIT 1");
						$this->db->simple_query("UPDATE `target_sources` SET `created_by_id` = ".$user->getId()." WHERE `id` = ".$source_id." LIMIT 1");
	
	
						$this->db->trans_commit();
						
						set_flash_success(lang('c_531'));
						redirect("access/login");
						
					} catch (Exception $e) {
		
						$this->db->trans_rollback();
						$error_msg = lang('e_1');
		
					}
	
				}
						
			}	
			
			tpl_assign('error', $error_msg);
			
		}
		
	}

	public function login() {

		if(logged_user() instanceof User) redirect('');

		$email = input_post_request('email');
		tpl_assign("email", $email);

		$password = input_post_request('password');

		$remember = input_post_request('remember') == 'on';
		tpl_assign("remember", $remember);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {
						
			$this->form_validation->set_rules('email', lang('c_1'), 'trim|valid_email|required');
			$this->form_validation->set_rules('password', lang('c_2'), 'trim|required');
		
			if ($this->form_validation->run() == FALSE) {
				$error_msg = validation_errors();
			} else {
				
				$query = $this->db->query("SELECT * FROM `users` WHERE `email` = '".$email."' LIMIT 1");
				$row = $query->row();
				
				if(isset($row)) {		

					$this->session->set_userdata('target_source_id', $row->target_source_id);
					$user = $this->Users->findById($row->id);

					if($user instanceof User) {
	
						if($user->getIsActive() && !$user->getIsTrashed()) {
	
							if($user->isValidPassword($password)) {
								
								try {
									
									Initial_Data::instance()->setLoggedUser($user, $remember, true);
									
									$ref = input_get_request('ref');
									$redirect_url = $ref != '' ? base64_decode($ref) : '';
									
									redirect($redirect_url);
			
								} catch(Exception $e) {
								}
							
							}
						
						}
											
					}

				}		

				$error_msg = lang('c_7');

			}	
			
			tpl_assign('error', $error_msg);
			
		}

	}

	public function logout() {
		
		Initial_Data::instance()->logUserOut();

		$ref = input_get_request('ref');
		$param = $ref != '' ? '?ref='.$ref : '';

		// redirect('access/login'.$param);
		redirect('');
		
	}
	
	function forgot_password($token = null) {

		if(logged_user() instanceof User) redirect('');

		tpl_assign("token", $token);
		$is_submited = input_post_request('submited') ==  'submited';
				
		if(is_null($token)) {

			$user_email = input_post_request('user_email');
			tpl_assign("user_email", $user_email);

			if ($is_submited) {

				$this->form_validation->set_rules('user_email', lang('c_14'), 'required|valid_email');

				if ($this->form_validation->run() == FALSE) {
					tpl_assign("error", validation_errors());
				} else {

					$query = $this->db->query("SELECT * FROM `users` WHERE `email` = '".$user_email."' LIMIT 1");
					$row = $query->row();
					
					if(isset($row)) {		
	
						$this->session->set_userdata('target_source_id', $row->target_source_id);
						$user = $this->Users->findById($row->id);

						if(isset($user) && $user instanceof User && 
						$user->getIsActive() && !$user->getIsTrashed()) {
						
							// Send reset password email here ..
							$notify_subject = lang('c_17');
							$notify_message = $this->load->view("emails/forgot_password", array(
							"reset_link" => get_page_base_url($user->getForgotPasswordURL()), "user_name" => $user->getName()), true);
	
							send_mail_SMTP($user->getEmail(), $user->getName(), $notify_subject, $notify_message);
						
						}
			
						$this->session->set_flashdata("success", lang('c_18'));
						redirect(get_page_base_url('access/forgot_password'));
					}
														
				}
				
			}
					
		} else {

			$query = $this->db->query("SELECT * FROM `users` WHERE `token` = '".$token."' LIMIT 1");
			$row = $query->row();

			if(isset($row)) {
				$this->session->set_userdata('target_source_id', $row->target_source_id);
			} else {
				show_404();
			}
			
			$user = $this->Users->getByToken($token);
			if(is_null($user)) show_404();

			$password = input_post_request('new_password');
		
			if ($is_submited) {

				$this->form_validation->set_rules('new_password', lang('c_2'), 'trim|required|min_length[8]|max_length[20]');
				$this->form_validation->set_rules('confirm_password', lang('c_11'), 'trim|required|matches[new_password]');

				if ($this->form_validation->run() == FALSE) {
					tpl_assign("error", validation_errors());	
				} else {
				
					try { 
					
						$user->setPassword($password);
						$user->save();
	
						$this->session->set_flashdata("success", lang('c_20'));
		
					} catch(Exception $e) {
						$this->session->set_flashdata("error", lang('e_1'));
					}
			
					redirect(get_page_base_url('access/login'));
		
				}
				
				if(isset($error_msg)) tpl_assign("error", $error_msg);

			}

		}
		
	}
	
}