<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Archive extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isOwner()) redirect('dashboard');		
				
	}
	
	public function index() {
		tpl_assign('archived_users', $this->Users->getByArchived());
		tpl_assign('archived_companies', $this->Companies->getByArchived());
	}
	
	public function move($item_name, $item_id) {

		only_ajax_request_allowed();
		$this->setLayout('modal');
		
		switch($item_name) {
			
			case 'user' : 

				if(!logged_user()->isOwner() || $item_id == logged_user()->getId()) {
					set_flash_error(lang('c_38'), true);
				}
								
				$item_object = $this->Users->findById($item_id);
				break;
			
			case 'company' : 

				if(!logged_user()->isOwner() || $item_id == owner_company()->getId()) {
					set_flash_error(lang('c_39'), true);
				}
				
				$item_object = $this->Companies->findById($item_id);
				break;
			
			default :

				set_flash_error(lang('e_3'), true);
				break;			

		}
		
		if(!(isset($item_object) && $item_object instanceof Application_object
		&& $item_object->getIsActive() && !$item_object->getIsTrashed()) ) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('item_object', $item_object);	
		
		$is_submited = input_post_request('submited') ==  'submited';		

		if ($is_submited) {
			
			try {
				
				$this->db->trans_begin();

				$item_object->moveToArchive();
				if ($this->db->trans_status() === FALSE) throw new Exception();

				if($item_object instanceof User) {
					$target_source = logged_user()->getTargetSource();
					$this->Users->updateUsersCache($target_source);
				}

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_40'), ucfirst($item_name)));
				
			} catch(Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}

			$this->renderText(output_ajax_request(true));
		
		}	

	}

	public function restore($item_name, $item_id) {

		switch($item_name) {
			
			case 'user' : 
				
				$item_object = $this->Users->findById($item_id);
				break;

			case 'company' : 
				
				$item_object = $this->Companies->findById($item_id);
				break;

		}
				
		if(!(isset($item_object) && $item_object instanceof Application_object
		&& !$item_object->getIsActive() && !$item_object->getIsTrashed()) ) {
			set_flash_error(lang('e_3'));
		} else {

			try {

				$this->db->trans_begin();
				
				$item_object->restoreFromArchive();									
				if ($this->db->trans_status() === FALSE) throw new Exception();

				if($item_object instanceof User) {
					$target_source = logged_user()->getTargetSource();
					$this->Users->updateUsersCache($target_source);
				}

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_41'), ucfirst($item_name)));
					
				
			} catch(Exception $e) {
	
				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));
	
			}
		
		}

		redirect('archive');
		
	}
	
}
