<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tickets extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
				
	}
	
	public function index() {

 		if(!(logged_user()->isOwner() || logged_user()->isAdmin() || !logged_user()->isMember())) redirect('dashboard');
 
		$sort_by = input_get_request('sort_by');
		tpl_assign('sort_by', $sort_by);
		
		$tickets_status = $sort_by == "closed" ? 0 : 1;

        if(logged_user()->isOwner()) {
			$tickets = $this->Tickets->getAll($tickets_status);
		} else {

			$project_ids = get_objects_ids(logged_user()->getActiveProjects());
			$tickets = empty($project_ids) ? null : $this->Tickets->getByProjects($project_ids, $tickets_status);
		}
		
		tpl_assign('tickets', $tickets);
	
	}

	public function create() {

 		if(!(logged_user()->isOwner() || logged_user()->isAdmin() || !logged_user()->isMember())) die();

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('tickets/_ticket_form');

		$ticket = new Ticket();
		tpl_assign('ticket', $ticket);

		$subject = input_post_request('subject');
		tpl_assign("subject", $subject);

		$description = input_post_request('description');
		tpl_assign("description", $description);

		$default_label = $this->GlobalLabels->getDefaultByType('TICKET');
		$default_label_id = isset($default_label) ? $default_label->getId() : 0;

		$label_id = input_post_request('label_id', $default_label_id);
		tpl_assign("label_id", $label_id);

		$projects = logged_user()->getActiveProjects();
		tpl_assign('projects', $projects);

		$ticket_types = $this->TicketTypes->getAllActive();
		tpl_assign('ticket_types', $ticket_types);

        if(!logged_user()->isMember()) {
			$assignee_id = 0;
		} else {

			$assignees = $this->Users->getAllMembers();
			tpl_assign('assignees', $assignees);
			
			$assignee_id = (int) input_post_request('assignee_id');
			tpl_assign("assignee_id", $assignee_id);
		
		}
		
		$project_id = (int) input_post_request('project_id');
		tpl_assign("project_id", $project_id);
	
		$type_id = (int) input_post_request('type_id');
		tpl_assign("type_id", $type_id);

		$priority = input_post_request('priority');
		tpl_assign("priority", $priority);

		$status = input_post_request('status', 1);
		tpl_assign("status", $status);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('subject', lang('c_335'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('project_id', lang('c_23'), 'required|greater_than[0]',  array('greater_than' => lang('c_336')));
			$this->form_validation->set_rules('type_id', lang('c_105'), 'required|greater_than[0]',  array('greater_than' => lang('c_337')));
							
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$ticket->setSubject($subject);
					$ticket->setDescription($description);

					$ticket->setProjectId($project_id);
					$ticket->setTypeId($type_id);
					$ticket->setLabelId($label_id);
					$ticket->setAssigneeId($assignee_id);

					$ticket->setPriority($priority);
					$ticket->setIsOpen($status == 1);
					
					$ticket->setCreatedById(logged_user()->getId());
					$ticket->save();

					$ticket->setTicketNo("TCK-".str_pad($ticket->getId(), 6, '0', STR_PAD_LEFT));
					$ticket->setAccessKey(sha1(uniqid(rand(), true).$ticket->getId().time()));

					$ticket->save();

					$this->ActivityLogs->create($ticket, lang('c_338'), 'add');

					$this->db->trans_commit();
					
					// Send notification ..
					if($assignee_id > 0) {
 
						$notify_subject = lang('c_339');
						$notify_message = $this->load->view("emails/assign_ticket", 
						array("ticket_subject" => $ticket->getSubject(), "description" => $ticket->getDescription(), 
						"ticket_no" => $ticket->getTicketNo(), "ticket_link" => get_page_base_url($ticket->getObjectURL())), true);

						$notify_user_object = $this->Users->findById($assignee_id);
						if(isset($notify_user_object)) {
							try {
								$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
								send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
							} catch (Exception $e){}
						}
							
					}

					set_flash_success(sprintf(lang('c_54'), lang('c_186')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}

		}
			
	}

	public function edit($id) {

        if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('tickets/_ticket_form');
		
		$active_projects = logged_user()->getActiveProjects();
		$project_ids = get_objects_ids($active_projects);

		$ticket = $this->Tickets->findById($id);

		if(is_null($ticket) || !in_array($ticket->getProjectId(), $project_ids) ) {
			set_flash_error(lang('e_3'), true);
		}
		
		tpl_assign('ticket', $ticket);

		$assignees = $this->Users->getAllMembers();
		tpl_assign('assignees', $assignees);

		tpl_assign('projects', $active_projects);

		$ticket_types = $this->TicketTypes->getAllActive();
		tpl_assign('ticket_types', $ticket_types);

		$subject = input_post_request('subject', $ticket->getSubject());
		tpl_assign("subject", $subject);

		$description = input_post_request('description', $ticket->getDescription());
		tpl_assign("description", $description);

		$assignee_id = (int) input_post_request('assignee_id', $ticket->getAssigneeId());
		tpl_assign("assignee_id", $assignee_id);

		$project_id = (int) input_post_request('project_id', $ticket->getProjectId());
		tpl_assign("project_id", $project_id);
	
		$type_id = (int) input_post_request('type_id', $ticket->getTypeId());
		tpl_assign("type_id", $type_id);

		$label_id = (int) input_post_request('label_id', $ticket->getLabelId());
		tpl_assign("label_id", $label_id);

		$priority = input_post_request('priority', $ticket->getPriority());
		tpl_assign("priority", $priority);

		$status = input_post_request('status', (int) $ticket->getIsOpen());
		tpl_assign("status", $status);

		$is_submited = input_post_request('submited') ==  'submited';

		if ($is_submited) {

			$this->form_validation->set_rules('subject', lang('c_335'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('type_id', lang('c_23'), 'required|greater_than[0]',  array('greater_than' => lang('c_336')));
					
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
				
				$old_assignee = $ticket->getAssigneeId();
						
				try{

					$this->db->trans_begin();
					
					$ticket->setSubject($subject);
					$ticket->setDescription($description);

					$ticket->setTypeId($type_id);
					$ticket->setLabelId($label_id);
					$ticket->setAssigneeId($assignee_id);

					$ticket->setPriority($priority);
					$ticket->setIsOpen($status == 1);
					
					$ticket->setCreatedById(logged_user()->getId());

					$ticket->save();

					$this->ActivityLogs->create($ticket, lang('c_340'), lang('c_220'));

					$this->db->trans_commit();

					// Send notification ..
					if($assignee_id > 0 && $assignee_id != $old_assignee){

						$notify_subject = lang('c_339');
						$notify_message = $this->load->view("emails/assign_ticket", 
						array("ticket_subject" => $ticket->getSubject(), "description" => $ticket->getDescription(), 
						"ticket_no" => $ticket->getTicketNo(), "ticket_link" => get_page_base_url($ticket->getObjectURL())), true);

						$notify_user_object = $this->Users->findById($assignee_id);
						if(isset($notify_user_object)) {
							try {
								$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
								send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
							} catch (Exception $e){}
						}
					
					}
										
					set_flash_success(sprintf(lang('c_57'), lang('c_186')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}
			
		} 
			
	}

	public function close($id) {

		only_ajax_request_allowed();
	
		$this->setLayout('modal');
	
		$active_projects = logged_user()->getActiveProjects();
		$project_ids = get_objects_ids($active_projects);

		$ticket = $this->Tickets->findById($id);

		if(is_null($ticket) || !in_array($ticket->getProjectId(), $project_ids) ) {
			set_flash_error(lang('e_3'), true);
		}
		
		tpl_assign('ticket', $ticket);

		$is_submited = input_post_request('submited') ==  'submited';		

		if ($is_submited) {

			try {

				$this->db->trans_begin();
				
				$ticket->setIsOpen(false);
				$ticket->save();

				$this->ActivityLogs->create($ticket, lang('c_341'), lang('c_37'));

				$this->db->trans_commit();
				
				set_flash_success(sprintf(lang('c_342'), lang('c_186')));
				
			} catch(Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}

			$this->renderText(output_ajax_request(true));
			
		}

	}
		
}
