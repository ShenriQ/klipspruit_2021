<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isOwner()) redirect('dashboard');
		
	}
	
	public function index() {

		$by = input_get_request('by');
		$by = $by != "" ? $by : "system";

		tpl_assign('by', $by);
		
		$options = $this->Configurations->getByCategory($by);
		tpl_assign('options', $options);
		
	}
	
	public function edit($id){

		only_ajax_request_allowed();
		$this->setLayout('modal');

		$option = $this->Configurations->findById($id);
		if(is_null($option)) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('option', $option);

		$name = input_post_request('name', $option->getName());
		tpl_assign("name", clean_config_option($name));

		$value = input_post_request('value', $option->getValue());
		tpl_assign("value", $value);

		$is_submited = input_post_request('submited') ==  'submited';
	
		if ($is_submited) {
	
			$this->form_validation->set_rules('value', lang('c_315'), 'trim|required|max_length[100]');
		
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {

				try{

					$option->setValue($value);
					$option->save();
					
					set_flash_success(sprintf(lang('c_81'), lang('c_316')));
					
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));
	
			}
		
		}
		
	}

}
