<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) redirect('dashboard');
		
	}
	
	public function index() {
		tpl_assign('ticket_types', $this->TicketTypes->getAll());
	}
	
	public function create() {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('departments/_department_form');

		$ticket_type = new TicketType();
		tpl_assign('ticket_type', $ticket_type);

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$is_active = input_post_request('is_active', 1);
		tpl_assign("is_active", $is_active);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_104'), 'trim|required|max_length[100]');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$ticket_type->setName($name);
					$ticket_type->setIsActive($is_active == 1);

					$ticket_type->save();

					$this->ActivityLogs->create($ticket_type, lang('c_106'), 'add', true, true);

					set_flash_success(sprintf(lang('c_54'), lang('c_105')));
															
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
		$this->setTemplate('departments/_department_form');

		$ticket_type = $this->TicketTypes->findById($id);
		if(is_null($ticket_type)) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('ticket_type', $ticket_type);

		$name = input_post_request('name', $ticket_type->getName());
		tpl_assign("name", $name);

		$is_active = input_post_request('is_active', (int) $ticket_type->getIsActive());
		tpl_assign("is_active", $is_active);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_104'), 'trim|required|max_length[100]');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$ticket_type->setName($name);
					$ticket_type->setIsActive($is_active == 1);

					$ticket_type->save();

					$this->ActivityLogs->create($ticket_type, lang('c_107'), 'edit', true, true);

					set_flash_success(sprintf(lang('c_57'), lang('c_105')));
															
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
						
}
