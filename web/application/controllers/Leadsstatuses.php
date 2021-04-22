<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leadsstatuses extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) redirect('dashboard');
		
	}
	
	public function index() {
		tpl_assign('statuses', $this->LeadsStatuses->getAll());
	}
	
	public function create() {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('leadsstatuses/_leadsstatus_form');

		$status = new LeadsStatus();
		tpl_assign('status', $status);

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_465'), 'trim|required|max_length[100]');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$status->setName($name);
					$status->save();

					set_flash_success(sprintf(lang('c_54'), lang('c_465')));
															
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
		$this->setTemplate('leadsstatuses/_leadsstatus_form');

		$status = $this->LeadsStatuses->findById($id);
		if(is_null($status)) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('status', $status);

		$name = input_post_request('name', $status->getName());
		tpl_assign("name", $name);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_465'), 'trim|required|max_length[100]');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$status->setName($name);
					$status->save();

					set_flash_success(sprintf(lang('c_57'), lang('c_465')));
															
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}

	public function delete($id) {

		$status = $this->LeadsStatuses->findById($id);
		if(is_null($status)) {
			set_flash_error(lang('e_3'));
		} else {

			try {
			
				$status->delete();
				set_flash_success(sprintf(lang('c_56'), lang('c_465')));

			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}
		
		}
							
		redirect('leadsstatuses');

	}
						
}
