<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companies extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
		if(!logged_user()->isOwner()) redirect('dashboard');

	}

	public function add() {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('companies/_company_form');

		$company = new Company();
		tpl_assign('company', $company);

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$address = input_post_request('address');
		tpl_assign("address", $address);

		$vat_no = input_post_request('vat_no');
		tpl_assign("vat_no", $vat_no);

		$phone_number = input_post_request('phone_number');
		tpl_assign("phone_number", $phone_number);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_76'), 'trim|required|max_length[50]');
			$this->form_validation->set_rules('address', lang('c_77'), 'trim|required|max_length[200]');
			$this->form_validation->set_rules('phone_number', lang('c_78'), 'trim|required|min_length[10]|max_length[30]');
		
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$company->setName($name);
					$company->setParentId(owner_company()->getId());
					if($address != '') $company->setAddress($address);
					if($vat_no != '') $company->setVatNo($vat_no);
					if($phone_number != '') $company->setPhoneNumber($phone_number);
					$company->setCreatedById(logged_user()->getId());
					
					$company->save();

					set_flash_success(sprintf(lang('c_79'), lang('c_80')));
					
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}
					
	}

	public function edit($id) {
		
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('companies/_company_form');

		$company = $this->Companies->findById($id);
		if(is_null($company) || $company->getIsTrashed() || !$company->getIsActive()) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('company', $company);

		$name = input_post_request('name', $company->getName());
		tpl_assign("name", $name);

		$address = input_post_request('address', $company->getAddress());
		tpl_assign("address", $address);

		$vat_no = input_post_request('vat_no', $company->getVatNo());
		tpl_assign("vat_no", $vat_no);

		$phone_number = input_post_request('phone_number', $company->getPhoneNumber());
		tpl_assign("phone_number", $phone_number);
		
		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {
			
			$this->form_validation->set_rules('name', lang('c_76'), 'trim|required|max_length[50]');
			$this->form_validation->set_rules('address', lang('c_77'), 'trim|required|max_length[200]');
			$this->form_validation->set_rules('phone_number', lang('c_78'), 'trim|required|min_length[10]|max_length[30]');
		
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$company->setName($name);
					$company->setAddress($address);
					$company->setVatNo($vat_no);
					$company->setPhoneNumber($phone_number);
					
					$company->save();

					set_flash_success(sprintf(lang('c_81'), lang('c_80')));
					
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		
			
		}		
	
	}
	
}
