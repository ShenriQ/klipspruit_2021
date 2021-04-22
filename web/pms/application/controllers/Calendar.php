<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
				
	}

	public function view($id) {
		
		$event = $this->Events->findById($id);

		$shared_user_ids = array();
		$event_object_users = isset($event) ? $this->Users->getByEvent($event, false) : null;
		
		if(isset($event_object_users) && is_array($event_object_users) && count($event_object_users)) {
			foreach($event_object_users as $event_object_user) {
				$shared_user_ids[] = $event_object_user->getId();
			}
		} 

		
		if(!((isset($event) && in_array(logged_user()->getId(), $shared_user_ids))
		|| logged_user()->isOwner() || logged_user()->isAdmin()) ) {
			set_flash_error(lang('e_3'));
			redirect('calendar');
		}
				
		tpl_assign('event', $event);
	
	}
	
	public function index() {

		$can_manage_calendar = (logged_user()->isOwner() || logged_user()->isAdmin()) ? true : false;
		$events = logged_user()->getEvents();		

		$events_list = "";
		if(isset($events) && is_array($events) && count($events)) {
		
			foreach ($events as $event) {
			
				$events_list .= "{
					  title: '".addslashes($event->getTitle())."',
					  start: '".format_date($event->getStart(), 'Y-m-d\TH:i:s')."',
					  end: '".format_date($event->getEnd(), 'Y-m-d\TH:i:s')."',
					  ".($can_manage_calendar ? "url: '".base_url("calendar/edit/".$event->getId())."'," : "") ."
					  className: '".$event->getClassname()."',
					  modal: 'true',
					  description: '".addslashes(preg_replace( "/\r|\n/", "", $event->getDescription()))."',
	
				  },";
			
			}

		}
		
		tpl_assign("events_list", $events_list);
		
	}

	public function create() {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('calendar/_event_form');

		$event = new Event();
		tpl_assign('event', $event);

		$title = input_post_request('title');	
		tpl_assign("title", $title);
	
		$start = input_post_request('start');		
		tpl_assign("start", $start);

		$end = input_post_request('end');		
		tpl_assign("end", $end);

		$classname = input_post_request('classname');		
		tpl_assign("classname", $classname);

		$description = input_post_request('description');		
		tpl_assign("description", $description);

		tpl_assign("shared_user_ids", array());
		$request_shared_user_ids = input_post_request('shared_users');

		$is_submited = input_post_request('submited') ==  'submited';

		if ($is_submited) {

			$this->form_validation->set_rules('title', lang('c_45'), 'trim|required|max_length[255]');
			$this->form_validation->set_rules('start', lang('c_46'), 'required|callback_validate_calendar_datetime');
			$this->form_validation->set_rules('end', lang('c_47'), 'required|callback_validate_calendar_datetime');
					
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$this->db->trans_begin();

					$event->setTitle($title);
					$event->setDescription($description);
					$event->setStart($start);
					$event->setEnd($end);
					$event->setClassname($classname);
					$event->setCreatedById(logged_user()->getId());

					$event->save();
					
					// Shared users ..
					if(isset($request_shared_user_ids) && is_array($request_shared_user_ids) && count($request_shared_user_ids)) {
	
						foreach($request_shared_user_ids as $request_shared_user_id){
							
							$user_object = $this->Users->findById($request_shared_user_id);
							if(isset($user_object)) {
	
								$event_user = new EventUser();
								
								$event_user->setEventId($event->getId());
								$event_user->setUserId($user_object->getId());
								
								$event_user->save();
								
							}
							
						}
	
					} 

					$this->db->trans_commit();
					set_flash_success(sprintf(lang('c_54'), lang('c_58')));
		
				} catch (Exception $e) {
	
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}
			
		} 
	
	}

	public function validate_calendar_datetime($datetime) {
		
		if (!validate_date($datetime, 'Y-m-d H:i')) {
			$this->form_validation->set_message('validate_calendar_datetime', lang('c_55'));
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function delete_event($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) redirect('dashboard');

		$event = $this->Events->findById($id);
		if(is_null($event)) {
			set_flash_error(lang('e_3'));
		} else {
		
			try { 

				$this->db->trans_begin();

				$event->delete();

				$this->db->trans_commit();

				set_flash_success(sprintf(lang('c_56'), lang('c_58')));
			
			} catch (Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}
		
		}

		redirect('calendar');
				
	}
			
	public function edit($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('calendar/_event_form');

		$event = $this->Events->findById($id);
		if(is_null($event)) {
			set_flash_error(lang('e_3'), true);
		}
		
		tpl_assign('event', $event);

		$title = input_post_request('title', $event->getTitle());	
		tpl_assign("title", $title);
	
		$start = input_post_request('start', format_date($event->getStart(), 'Y-m-d H:i'));		
		tpl_assign("start", $start);

		$end = input_post_request('end', format_date($event->getEnd(), 'Y-m-d H:i'));		
		tpl_assign("end", $end);

		$classname = input_post_request('classname', $event->getClassname());		
		tpl_assign("classname", $classname);

		$description = input_post_request('description', $event->getDescription());		
		tpl_assign("description", $description);
		
		$shared_user_ids = array();
		$event_object_users = $this->Users->getByEvent($event, false);
		
		if(isset($event_object_users) && is_array($event_object_users) && count($event_object_users)) {
			foreach($event_object_users as $event_object_user) {
				$shared_user_ids[] = $event_object_user->getId();
			}
		} 

		tpl_assign("shared_user_ids", $shared_user_ids);
		$request_shared_user_ids = input_post_request('shared_users');
		
		$is_submited = input_post_request('submited') ==  'submited';

		if ($is_submited) {

			$this->form_validation->set_rules('title', lang('c_45'), 'trim|required|max_length[255]');
			$this->form_validation->set_rules('start', lang('c_46'), 'required|callback_validate_calendar_datetime');
			$this->form_validation->set_rules('end', lang('c_47'), 'required|callback_validate_calendar_datetime');
					
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$this->db->trans_begin();

					$event->setTitle($title);
					$event->setDescription($description);
					$event->setStart($start);
					$event->setEnd($end);
					$event->setClassname($classname);
					
					$event->save();

					// Shared users ..
					$event->clearEventUsers();
					if(isset($request_shared_user_ids) && is_array($request_shared_user_ids) && count($request_shared_user_ids)) {
	
						foreach($request_shared_user_ids as $request_shared_user_id){
							
							$user_object = $this->Users->findById($request_shared_user_id);
							if(isset($user_object)) {
	
								$event_user = new EventUser();
								
								$event_user->setEventId($event->getId());
								$event_user->setUserId($user_object->getId());
								
								$event_user->save();
								
							}
							
						}
	
					} 
	
					$this->db->trans_commit();

					set_flash_success(sprintf(lang('c_57'), lang('c_58')));
		
				} catch (Exception $e) {
	
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));
	
				}

				$this->renderText(output_ajax_request(true));

			}
			
		} 
	
	}
	
	
}
