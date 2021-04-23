<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_admin_data_contoller($this);
				
	}
	
	public function index() {
	}
	
	public function edit_profile() {

		only_ajax_request_allowed();
		$this->setLayout('modal');
		
		$user = logged_admin_user();
		tpl_assign('user', $user);
				
		$name = input_post_request('name', $user->getName());
		tpl_assign("name", $name);

		$email = input_post_request('email', $user->getEmail());
		tpl_assign("email", $email);

		$password = input_post_request('password');

		$address = input_post_request('address', $user->getAddress());
		tpl_assign("address", $address);

		$phone_number = input_post_request('phone_number', $user->getPhoneNumber());
		tpl_assign("phone_number", $phone_number);

		$is_submited = input_post_request('submited') ==  'submited';
				
		if ($is_submited) {

			$this->form_validation->set_rules('name', "Display Name", 'trim|required|max_length[30]');
			$this->form_validation->set_rules('password', "Password", 'trim|min_length[8]|max_length[20]');
			
			$this->form_validation->set_rules('address', "Address", 'trim|max_length[200]');
			$this->form_validation->set_rules('phone_number', "Phone Number", 'trim|min_length[10]|max_length[30]');
		
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$user->setName($name);
					if($password != '') $user->setPassword($password);

					if($address != '') $user->setAddress($address);
					if($phone_number != '') $user->setPhoneNumber($phone_number);
									
					$user->save();
					
					set_flash_success("Profile has been saved successfully.");
					
				}catch(Exception $e){
					set_flash_error("error", "An error was encountered.");
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
	
}
