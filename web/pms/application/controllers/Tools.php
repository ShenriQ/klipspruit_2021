<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isOwner()) redirect('dashboard');
		
	}
	
	public function index() {
	}
	
	public function test_email(){

        only_ajax_request_allowed();
        $this->setLayout('modal');
        
		$test_recepient_email = input_post_request('test_recepient_email');
		tpl_assign("test_recepient_email", $test_recepient_email);

		$test_recepient_subject = input_post_request('test_recepient_subject');
		tpl_assign("test_recepient_subject", $test_recepient_subject);

		$test_recepient_message = input_post_request('test_recepient_message');
		tpl_assign("test_recepient_message", $test_recepient_message);

		$is_submited = input_post_request('submited') ==  'submited';
	
		if ($is_submited) {
    
            $this->form_validation->set_rules('test_recepient_email', lang('c_518'), 'trim|valid_email|required');
			$this->form_validation->set_rules('test_recepient_subject', lang('c_519'), 'trim|required');
			$this->form_validation->set_rules('test_recepient_message', lang('c_520'), 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {

				try{

                    $mail = get_config_SMTP();
                    
                    $mail->Subject    = html_entity_decode($test_recepient_subject, ENT_NOQUOTES, 'UTF-8');
                    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
                    
                    $mail->AddAddress($test_recepient_email);                    
                    $mail->MsgHTML($test_recepient_message);

                    if($mail->Send()) {
                        set_flash_success(lang('c_514'));
                        $this->renderText(output_ajax_request(true));
                    } else {
                        $this->renderText(output_ajax_request(false, lang('c_523')));
                    }
					
				}catch(Exception $e){
                    $this->renderText(output_ajax_request(false, lang('e_1')));
				}
	
			}
		
		}
		
	}

}
