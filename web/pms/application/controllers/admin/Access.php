<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends Admin_controller {
	
	function __construct() {
		parent::__construct();
		$this->setLayout("dialog");
	}
	
	public function login() {
		
		if(logged_admin_user() instanceof IUser) redirect('admin/dashboard');

		$email = input_post_request('email');
		tpl_assign("email", $email);

		$password = input_post_request('password');

		$remember = input_post_request('remember') == 'on';
		tpl_assign("remember", $remember);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('email', "Email", 'trim|valid_email|required');
			$this->form_validation->set_rules('password', "Password", 'trim|required');
		
			if ($this->form_validation->run() == FALSE) {
				$error_msg = validation_errors();
			} else {
										
				$user = $this->IUsers->getByEmail($email);

				if($user instanceof IUser) {
					
					if($user->getIsActive()) {
						if($user->isValidPassword($password)) {
							try {
								
								Initial_Admin_Data::instance()->setLoggedUser($user, $remember, true);
								
								$ref = input_get_request('ref');
								$redirect_url = $ref != '' ? base64_decode($ref) : 'admin/dashboard';
								
								redirect($redirect_url);
		
							} catch(Exception $e) {
							}
						
						}
					
					}
										
				}
				
				$error_msg = "Email and password do not match.";
			
			}	
			
			tpl_assign('error', $error_msg);
			
		}

	}

	public function logout() {
		
		Initial_Admin_Data::instance()->logUserOut();

		$ref = input_get_request('ref');
		$param = $ref != '' ? '?ref='.$ref : '';

		redirect('admin/access/login'.$param);
		
	}
	
	function forgot_password($token = null) {

		if(logged_admin_user() instanceof IUser) redirect('admin/dashboard');

		tpl_assign("token", $token);
		$is_submited = input_post_request('submited') ==  'submited';
				
		if(is_null($token)) {
		
			$user_email = input_post_request('user_email');
			tpl_assign("user_email", $user_email);

			if ($is_submited) {
			
				$this->form_validation->set_rules('user_email', "Email Address", 'required|valid_email');

				if ($this->form_validation->run() == FALSE) {
					tpl_assign("error", validation_errors());
				} else {

					$user = $this->IUsers->getByEmail($user_email);

					if(isset($user) && $user instanceof IUser && $user->getIsActive() ) {
					
						// Send reset password email here ..
						$notify_subject = "Request for Reset Password";
						$notify_message = $this->load->view("emails/admin_forgot_password", array(
						"reset_link" => get_page_base_url($user->getForgotPasswordURL()), "user_name" => $user->getName()), true);

						send_i_mail_SMTP($user->getEmail(), $user->getName(), $notify_subject, $notify_message);
					
					}
			
					$this->session->set_flashdata("success", "<b>Email sent!</b> If the email address you entered is registered, you'll receive an email with a link to a page where you can easily create a new password.");
					redirect('admin/access/forgot_password');
														
				}
				
			}
					
		} else {
		
			$user = $this->IUsers->getByToken($token);
			if(is_null($user)) redirect('admin/dashboard');

			$password = input_post_request('new_password');
		
			if ($is_submited) {

				$this->form_validation->set_rules('new_password', "Password", 'trim|required|min_length[8]|max_length[20]');
				$this->form_validation->set_rules('confirm_password', "Retype password", 'trim|required|matches[new_password]');

				if ($this->form_validation->run() == FALSE) {
					tpl_assign("error", validation_errors());	
				} else {
				
					try { 
					
						$user->setPassword($password);
						$user->save();
	
						$this->session->set_flashdata("success", "Your password has been changed successfully.");
		
					} catch(Exception $e) {
						$this->session->set_flashdata("error", "An error was encountered.");
					}
			
					redirect('admin/access/login');
		
				}
				
				if(isset($error_msg)) tpl_assign("error", $error_msg);

			}

		}
		
	}
		
}