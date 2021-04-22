<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
	}

	public function view($id) {
		
		$notification = $this->UserNotifications->findById($id);
		
		if(!(isset($notification) && $notification->getCreatedById() == logged_user()->getId())) {
			set_flash_error(lang('e_3'));
			redirect('notifications');
		}
		
		try {

			$notification->setIsRead(true);
			$notification->save();
		
		} catch (Exception $e) {}
		
		tpl_assign('notification', $notification);
	
	}
	
	public function index() {

		$delete = input_post_request('delete');
		$read = input_post_request('read');
		
		$itemids = input_post_request('itemid');
		$is_submited = input_post_request('submited') ==  'submited';

		if ($is_submited) {

			if(isset($itemids) && is_array($itemids) && count($itemids)) {
		
				try{
			
					foreach($itemids as $itemid) {
						
						$a_notification = $this->UserNotifications->findById($itemid);
						
						if(isset($a_notification) && $a_notification->getCreatedById() == logged_user()->getId()) {
							
							if($read != '') {
								
								$a_notification->setIsRead(true);
								$a_notification->save();
								
							} elseif($delete != '') {
								$a_notification->delete();
							}
					
						}	
					
					}
		
				} catch(Exception $e) {}
					
			}
		
			redirect('notifications');
			
		}
	
		tpl_assign('user_notifications', logged_user()->getMyNotifications(true));
	
	}

	public function remove($id) {

		$notification = $this->UserNotifications->findById($id);

		if(!(isset($notification) && $notification->getCreatedById() == logged_user()->getId())) {
			set_flash_error(lang('e_3'));
		} else {

			try {
			
				$notification->delete();
				set_flash_success(sprintf(lang('c_56'), lang('c_212')));
	
			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}
		
		}
							
		redirect('notifications');

	}
							
}
