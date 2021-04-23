<?php defined('BASEPATH') OR exit('No direct script access allowed');

class People extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
	}

	public function companies_json() {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) exit(null);
		only_ajax_request_allowed();
		$companies = array();
		
		$owner_company = owner_company();
		array_push($companies, array('id' => $owner_company->getId(), 'name' => $owner_company->getName().' ['.lang('c_213').']'));
		
		$client_companies = $this->Companies->getClients($owner_company);
		if(isset($client_companies) && is_array($client_companies) && count($client_companies)) {

			foreach($client_companies as $client_company) {
				array_push($companies, array('id' => $client_company->getId(), 'name' => $client_company->getName()));
			}

		}
		
		$this->renderText(json_encode($companies));
		
	}
	
	public function users_json($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin()))  exit(null);
		only_ajax_request_allowed();
		$users = array();

		if($id == 0) {
			$company_users = $this->Users->getByWithoutCompany();
		} else {
		
			$company = $this->Companies->findById($id);
			if(isset($company)) $company_users = $company->getUsers();
		
		}

		if(isset($company_users) && is_array($company_users) && count($company_users)) {
		
			foreach($company_users as $company_user) {
				
				if(!$company_user->isOwner()) {
					array_push($users, array('id' => $company_user->getId(), 'name' => $company_user->getName(), 'is_member' => $company_user->isMember()));
				}
		
			}
		
		}

		$this->renderText(json_encode($users));

	}	
	
	public function members() {
		if(!logged_user()->isOwner()) redirect('dashboard');
	}

	public function clients() {
		if(!logged_user()->isOwner()) redirect('dashboard');

		tpl_assign('client_companies', $this->Companies->getClients(owner_company()));
		tpl_assign('without_company_clients', $this->Users->getByWithoutCompany());
	}

	public function add($user_group) {

		if(!logged_user()->isOwner()) exit(null);
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('people/_user_form');

		if($user_group != 'client' && $user_group != 'member') {
			set_flash_error(lang('e_3'), true);
		}
		
		tpl_assign('user_group', $user_group);
	
		if($user_group == 'member') {
			
			$member_type = input_post_request('member_type');
			$member_type = $member_type == 0 ? 0 : 1;
			
			tpl_assign("member_type", $member_type);
			$company_id = owner_company()->getId();
			
		} else {
		
			$company_id = (int) input_post_request('company_id');
			if($company_id > 0) {
			
				$company = $this->Companies->findById($company_id);
				$company_id = is_null($company) || $company->getId() == owner_company()->getId() ? 0 : $company_id;
				
			} else {
				$company_id = 0;
			}
			
			tpl_assign("company_id", $company_id);
			
		}
				
		$user = new User();
		tpl_assign('user', $user);

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$email = input_post_request('email');
		tpl_assign("email", $email);

		$password = input_post_request('password');

		$address = input_post_request('address');
		tpl_assign("address", $address);

		$phone_number = input_post_request('phone_number');
		tpl_assign("phone_number", $phone_number);

		$notes = input_post_request('notes');
		tpl_assign("notes", $notes);

		$can_access_invoices_estimates = input_post_request('can_access_invoices_estimates') == 'on';
		tpl_assign("can_access_invoices_estimates", $can_access_invoices_estimates);
			
		$hourly_rate = (int) input_post_request('hourly_rate');
		tpl_assign("hourly_rate", $hourly_rate);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$target_source = logged_user()->getTargetSource();
			if($target_source->getUsersCreated() >= $target_source->getUsersLimit()) {
				$this->renderText(output_ajax_request(false, lang('c_539')));
			} else {

				$this->form_validation->set_rules('name', lang('c_85'), 'trim|required|max_length[30]');
				$this->form_validation->set_rules('email', lang('c_1'), 'trim|valid_email|required|max_length[100]|is_unique['.$this->Users->getTableName().'.email]', 
				array('is_unique' => lang('c_214')));
				$this->form_validation->set_rules('password', lang('c_2'), 'trim|required|min_length[8]|max_length[20]');
				
				$this->form_validation->set_rules('address', lang('c_77'), 'trim|max_length[200]');
				$this->form_validation->set_rules('phone_number', lang('c_78'), 'trim|min_length[10]|max_length[30]');
			
				if ($this->form_validation->run() == FALSE) {
					$this->renderText(output_ajax_request(false, validation_errors()));
				} else {
			
					try{
						
						$user->setName($name);
						$user->setCompanyId($company_id);
						$user->setEmail($email);
						$user->setPassword($password);
	
						$user->setAddress($address);
						$user->setPhoneNumber($phone_number);
						$user->setNotes($notes);
					
						$user->setHourlyRate($hourly_rate);
						// $user->setCanAccessInvoicesEstimates($can_access_invoices_estimates);

						if($user_group == 'member' && $member_type == 1) {
							$user->setIsAdmin(true);
						}
						
						$user->setIsActive(true);
						$user->setCreatedById(logged_user()->getId());

						$user->save();
						
						$this->Users->updateUsersCache($target_source);

						set_flash_success(sprintf(lang('c_79'), ucfirst($user_group)));
						
					}catch(Exception $e){
						set_flash_error(lang('e_1'));
					}

					$this->renderText(output_ajax_request(true));

				}		

			}

		}

	}

	public function edit($id) {

		if(!logged_user()->isOwner()) exit(null);
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('people/_user_form');

		$user = $this->Users->findById($id);
		if(is_null($user) || $user->getIsTrashed() || !$user->getIsActive()) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('user', $user);
		$user_group = $user->isMember() ? 'member' : 'client';
		
		tpl_assign('user_group', $user_group);
		if($user_group == 'member') {
			
			if(!$user->isOwner()) {

				$member_type = input_post_request('member_type', ($user->getIsAdmin() ? 1 : 0));
				$member_type = $member_type == 0 ? 0 : 1;
				
				tpl_assign("member_type", $member_type);
			} 
						
		} else {
		
			$company_id = (int) input_post_request('company_id', $user->getCompanyId());
			if($company_id > 0) {
			
				$company = $this->Companies->findById($company_id);
				$company_id = is_null($company) || $company->getId() == owner_company()->getId() ? 0 : $company_id;
				
			} else {
				$company_id = 0;
			}
			
			tpl_assign("company_id", $company_id);
			
		}
				
		$name = input_post_request('name', $user->getName());
		tpl_assign("name", $name);

		$email = input_post_request('email', $user->getEmail());
		tpl_assign("email", $email);

		$password = input_post_request('password');

		$address = input_post_request('address', $user->getAddress());
		tpl_assign("address", $address);

		$phone_number = input_post_request('phone_number', $user->getPhoneNumber());
		tpl_assign("phone_number", $phone_number);

		$notes = input_post_request('notes', $user->getNotes());
		tpl_assign("notes", $notes);

		$can_access_invoices_estimates = input_post_request('can_access_invoices_estimates') == 'on';
		tpl_assign("can_access_invoices_estimates", $can_access_invoices_estimates);

		$hourly_rate = (int) input_post_request('hourly_rate', $user->getHourlyRate());
		tpl_assign("hourly_rate", $hourly_rate);

		$is_submited = input_post_request('submited') ==  'submited';

		$can_access_invoices_estimates = $is_submited ? input_post_request('can_access_invoices_estimates') == 'on' : $user->getCanAccessInvoicesEstimates();
		tpl_assign("can_access_invoices_estimates", $can_access_invoices_estimates);

				
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_85'), 'trim|required|max_length[30]');
			$this->form_validation->set_rules('email', lang('c_1'), 'trim|valid_email|required|max_length[100]'.($user->getEmail() != $email ? '|is_unique['.$this->Users->getTableName().'.email]' : ''), 
			array('is_unique' => lang('c_214')));
			$this->form_validation->set_rules('password', lang('c_2'), 'trim|min_length[8]|max_length[20]');
			
			$this->form_validation->set_rules('address', lang('c_77'), 'trim|max_length[200]');
			$this->form_validation->set_rules('phone_number', lang('c_78'), 'trim|min_length[10]|max_length[30]');
		
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$user->setName($name);
					if($user_group == 'client') $user->setCompanyId($company_id);
					$user->setEmail($email);
					if($password != '') $user->setPassword($password);

					$user->setAddress($address);
					$user->setPhoneNumber($phone_number);

					$user->setHourlyRate($hourly_rate);
					// $user->setCanAccessInvoicesEstimates($can_access_invoices_estimates);
					
					$user->setNotes($notes);
				
					if($user_group == 'member' && !$user->isOwner()) {

						$is_admin_flag = $member_type == 1 ? true : false;
						$user->setIsAdmin($is_admin_flag);

					}
					
					$user->save();
					
					set_flash_success(sprintf(lang('c_81'), ucfirst($user_group)));
					
				}catch(Exception $e){
					set_flash_error("error", lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
	
	public function add_to_project($id) {
		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) exit(null);
		only_ajax_request_allowed();
		$this->setLayout('modal');

		$user = $this->Users->findById($id);
		if(is_null($user) || $user->getIsTrashed() || !$user->getIsActive() || $user->isOwner()) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('user', $user);

		$project_ids = input_post_request('projects');		
		$is_submited = input_post_request('submited') ==  'submited';

		if ($is_submited) {

			if (isset($project_ids) && is_array($project_ids) && count($project_ids)) {

				try{
					
					$this->db->trans_begin();
							
					foreach($project_ids as $project_id) {				

						$project = $this->Projects->findById($project_id);
						if(isset($project) && !$project->getIsTrashed() 
						&& !$project->isCompleted()) {

							$project_user = new ProjectUser();

							$project_user->setProjectId($project->getId());
							$project_user->setUserId($user->getId());
						
							$project_user->save();
						
						}
													
					}

					$this->db->trans_commit();
					set_flash_success(sprintf(lang('c_215'), lang('c_216')));
					
				}catch(Exception $e){
	
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));
	
				}
	
				$this->renderText(output_ajax_request(true));

			} else {
				$this->renderText(output_ajax_request(false, lang('c_217')));
			}
					
		}
			
	}

	public function view($id) {

		if(!logged_user()->isOwner()) redirect('dashboard');

		$user = $this->Users->findById($id);
		if(isset($user) && logged_user()->isMember()) {
			tpl_assign('user', $user);	
		}else {
			set_flash_error(lang('e_3'));
			redirect("dashboard");		
		}
	}
	
}
