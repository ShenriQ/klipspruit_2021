<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
	}

	public function project_tasks_json($project_id) {

		only_ajax_request_allowed();

		$tasks = array();		
		$project = $this->Projects->findById($project_id);

		if(isset($project) && (logged_user()->isOwner() || (logged_user()->isMember() 
		&& logged_user()->isProjectUser($project))) ) {

			$project_tasks = $project->getTasks();
			if(isset($project_tasks) && is_array($project_tasks) && count($project_tasks)) {
			
				foreach($project_tasks as $project_task) {
					array_push($tasks, array('id' => $project_task->getId(), 'name' => $project_task->getName()));
				}
			
			}
			
		}

		$this->renderText(json_encode($tasks));

	}

	public function sort_tasks(){

		only_ajax_request_allowed();
		
		$list_id = input_post_request('tasklist_id');
		$add_or_remove = input_post_request('add_or_remove');
		
		$task_list = $this->ProjectTaskLists->findById($list_id);
		if(isset($task_list) && (logged_user()->isOwner() || (logged_user()->isMember() 
		&& logged_user()->isProjectUser($task_list->getProject()))) ) {
				
			$list_tasks = $task_list->getTasks(true);
			if(isset($list_tasks) && is_array($list_tasks) && count($list_tasks)) {
				$list_tasks_ids = get_objects_ids($list_tasks);
			}

			parse_str(input_post_request('items'), $items);
			$task_ids = $items['task'];

			if(isset($task_ids) && is_array($task_ids) && count($task_ids)) {

				try {

					$this->db->trans_begin();
				
					$order_count = 1;
					foreach($task_ids as $task_id) {
								
						if(isset($task_id) && $task_id > 0) {
							
							$object = $this->ProjectTasks->findById($task_id);
							if(isset($object)) {

								if(isset($list_tasks_ids) && is_array($list_tasks_ids) 
								&& in_array($object->getId(), $list_tasks_ids)) {

									$object->setSortOrder($order_count);
									$object->save();
	
									$order_count++;
								
								} else {

									if($add_or_remove == "add") {
										
										$object->setTaskListId($task_list->getId());
										$object->setSortOrder($order_count);
										$object->save();
	
										$order_count++;
	
									}
								
								}
								
							}

						} 
												
					}
	
					$this->db->trans_commit();
				
				}catch(Exception $e){
					$this->db->trans_rollback();
				}
			
			}
		
				
		}

		$this->renderText(output_ajax_request(true));
			
	}
		
	public function sort_tasklists($id){

		only_ajax_request_allowed();

		$project = $this->Projects->findById($id);
		if(isset($project) && (logged_user()->isOwner() || logged_user()->isProjectUser($project)) ) {
			
			$include_private = logged_user()->isMember();
			$project_task_lists = $project->getTaskLists($include_private);
			if(isset($project_task_lists) && is_array($project_task_lists) && count($project_task_lists)) {

				$project_task_lists_ids = get_objects_ids($project_task_lists);

				parse_str(input_post_request('lists'), $lists);
				$lists_ids = $lists['list'];

				if(isset($lists_ids) && is_array($lists_ids) && count($lists_ids)) {

					try {

						$this->db->trans_begin();
					
						$order_count = 1;
						foreach($lists_ids as $list_id) {
									
							if(isset($list_id) && $list_id > 0 && in_array($list_id, $project_task_lists_ids)) {
	
								$object = $this->ProjectTaskLists->findById($list_id);
								$object->setSortOrder($order_count);
								$object->save();

								$order_count++;
	
							}						
						}
		
						$this->db->trans_commit();
					
					}catch(Exception $e){
						$this->db->trans_rollback();
					}
														
				} 
			}
			
		}

		$this->renderText(output_ajax_request(true));
			
	}
		
	public function validate_date($date) {
		return $date != '' ? validate_date($date) : true;
	}
	
	public function compare_dates() {
	
		$start = strtotime($this->input->post('start_date'));
		$end = strtotime($this->input->post('due_date'));
	
		if($start > $end) {
			$this->form_validation->set_message('compare_dates',lang('c_191'));
			return false;
		}

	}

	public function create_task($task_list_id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('tasks/_task_form');

		$task_list = $this->ProjectTaskLists->findById($task_list_id);
		if(is_null($task_list) || !(logged_user()->isOwner() || (logged_user()->isMember() && logged_user()->isProjectUser($task_list->getProject()))) ) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('task_list', $task_list);
		tpl_assign('project', $task_list->getProject());

		$task = new ProjectTask();
		tpl_assign('task', $task);

		$description = input_post_request('description');
		tpl_assign("description", $description);

		$assignee_id = input_post_request('assignee_id');
		tpl_assign("assignee_id", $assignee_id);

		$due_date = input_post_request('due_date');
		tpl_assign("due_date", $due_date);

		$start_date = input_post_request('start_date');
		tpl_assign("start_date", $start_date);

		$is_high_priority = input_post_request('is_high_priority') == 'on';
		tpl_assign("is_high_priority", $is_high_priority);

		$default_label = $this->GlobalLabels->getDefaultByType('TASK');
		$default_label_id = isset($default_label) ? $default_label->getId() : 0;

		$label_id = input_post_request('label_id', $default_label_id);
		tpl_assign("label_id", $label_id);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('description', lang('c_321'), 'trim|required');
			
			$this->form_validation->set_rules('start_date', lang('c_192'), 'callback_validate_date',  
			array('validate_date' => lang('c_193')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'callback_validate_date',  
			array('validate_date' => lang('c_322')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'trim|callback_compare_dates');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$this->db->trans_begin();

					$task->setDescription($description);
					$task->setProjectId($task_list->getProjectId());
					$task->setTaskListId($task_list->getId());
					$task->setAssigneeId($assignee_id);
					$task->setLabelId($label_id);
					$task->setStartDate($start_date);
					$task->setDueDate($due_date);

					$task->setIsHighPriority($is_high_priority);
					
					$task->setCreatedById(logged_user()->getId());
					$task->save();

					$this->ActivityLogs->create($task, lang('c_323'), 'add', $task_list->getIsPrivate());

					$this->db->trans_commit();

					// Send notification ..
					if($assignee_id > 0) {

						$notify_subject = lang('c_523.51');
						$notify_message = $this->load->view("emails/assign_task", 
						array("task_subject" => $task->getDescription(), "task_id" => $task->getId(), 
						"task_link" => get_page_base_url($task->getObjectURL())), true);

						$notify_user_object = $this->Users->findById($assignee_id);
						if(isset($notify_user_object)) {
							try {
								$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
								send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
							} catch (Exception $e){}
						}
												
					}
					
					set_flash_success(sprintf(lang('c_54'), lang('c_183')));
															
				}catch(Exception $e){
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
		
	public function edit_task($id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('tasks/_task_form');

		$task = $this->ProjectTasks->findById($id);
		if(!is_null($task)) $task_list = $task->getTaskList();
		
		if(is_null($task_list) || !(logged_user()->isOwner() || (logged_user()->isMember() && logged_user()->isProjectUser($task_list->getProject()))) ) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('task', $task);
		tpl_assign('task_list', $task_list);
		tpl_assign('project', $task_list->getProject());

		$description = input_post_request('description', $task->getDescription());
		tpl_assign("description", $description);

		$assignee_id = input_post_request('assignee_id', $task->getAssigneeId());
		tpl_assign("assignee_id", $assignee_id);

		$label_id = input_post_request('label_id', $task->getLabelId());
		tpl_assign("label_id", $label_id);
		
		$start_date_timestamp = $task->getStartDate();
		$formatted_start_date = $start_date_timestamp ? format_date($start_date_timestamp, 'm/d/Y') : null;
		
		$start_date = input_post_request('start_date', $formatted_start_date);
		tpl_assign("start_date", $start_date);

		$due_date_timestamp = $task->getDueDate();
		$formatted_due_date = $due_date_timestamp ? format_date($due_date_timestamp, 'm/d/Y') : null;
		
		$due_date = input_post_request('due_date', $formatted_due_date);
		tpl_assign("due_date", $due_date);

		$is_submited = input_post_request('submited') ==  'submited';

		$is_high_priority = $is_submited ? input_post_request('is_high_priority') == 'on' : $task->getIsHighPriority();
		tpl_assign("is_high_priority", $is_high_priority);

		if ($is_submited) {

			$this->form_validation->set_rules('description', lang('c_321'), 'trim|required');
			
			$this->form_validation->set_rules('start_date', lang('c_192'), 'callback_validate_date',  
			array('validate_date' => lang('c_193')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'callback_validate_date',  
			array('validate_date' => lang('c_322')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'trim|callback_compare_dates');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				$old_assignee = $task->getAssigneeId();

				try{

					$this->db->trans_begin();

					$task->setDescription($description);
					$task->setAssigneeId($assignee_id);
					$task->setLabelId($label_id);
					$task->setStartDate($start_date);
					$task->setDueDate($due_date);

					$task->setIsHighPriority($is_high_priority);

					$task->save();

					$this->ActivityLogs->create($task, lang('c_324'), 'edit', $task_list->getIsPrivate());
				
					$this->db->trans_commit();

					// Send notification ..
					if($assignee_id > 0 && $assignee_id != $old_assignee){

						$notify_subject = lang('c_523.51');
						$notify_message = $this->load->view("emails/assign_task", 
						array("task_subject" => $task->getDescription(), "task_id" => $task->getId(), 
						"task_link" => get_page_base_url($task->getObjectURL())), true);

						$notify_user_object = $this->Users->findById($assignee_id);
						if(isset($notify_user_object)) {
							try {
								$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
								send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
							} catch (Exception $e){}
						}
					
					}
					
					set_flash_success(sprintf(lang('c_81'), lang('c_183')));
															
				}catch(Exception $e){
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
		
	public function create_task_list($project_id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('tasks/_task_list_form');

		$project = $this->Projects->findById($project_id);
		if(!(isset($project) && !$project->getIsTrashed() &&
		(logged_user()->isOwner() || (logged_user()->isProjectUser($project) && logged_user()->isMember())))) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('project', $project);

		$task_list = new ProjectTaskList();
		tpl_assign('task_list', $task_list);

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$description = input_post_request('description');
		tpl_assign("description", $description);

		$start_date = input_post_request('start_date');
		tpl_assign("start_date", $start_date);

		$due_date = input_post_request('due_date');
		tpl_assign("due_date", $due_date);

		$is_private = input_post_request('is_private') == 'on';
		tpl_assign("is_private", $is_private);

		$is_high_priority = input_post_request('is_high_priority') == 'on';
		tpl_assign("is_high_priority", $is_high_priority);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_325'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('description', lang('c_326'), 'trim|required');

			$this->form_validation->set_rules('start_date', lang('c_192'), 'callback_validate_date',  
			array('validate_due_date' => lang('c_327')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'callback_validate_date',  
			array('validate_due_date' => lang('c_322')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'trim|callback_compare_dates');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$task_list->setProjectId($project->getId());

					$task_list->setName($name);
					$task_list->setDescription($description);
					
					if(logged_user()->isMember()) $task_list->setIsPrivate($is_private);
					$task_list->setIsHighPriority($is_high_priority);

					$task_list->setStartDate($start_date);
					$task_list->setDueDate($due_date);
					
					$task_list->setCreatedById(logged_user()->getId());
					$task_list->save();

					$this->ActivityLogs->create($task_list, lang('c_328'), 'add', $task_list->getIsPrivate());
						
					set_flash_success(sprintf(lang('c_54'), lang('c_184')));
															
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}

	public function edit_task_list($id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('tasks/_task_list_form');

		$task_list = $this->ProjectTaskLists->findById($id);
		if(is_null($task_list) || !(logged_user()->isOwner() || (logged_user()->isMember() && logged_user()->isProjectUser($task_list->getProject()))) ) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('task_list', $task_list);
		tpl_assign('project', $task_list->getProject());

		$name = input_post_request('name', $task_list->getName());
		tpl_assign("name", $name);

		$description = input_post_request('description', $task_list->getDescription());
		tpl_assign("description", $description);

		$start_date_timestamp = $task_list->getStartDate();
		$formatted_start_date = $start_date_timestamp ? format_date($start_date_timestamp, 'm/d/Y') : null;
		
		$start_date = input_post_request('start_date', $formatted_start_date);
		tpl_assign("start_date", $start_date);

		$due_date_timestamp = $task_list->getDueDate();
		$formatted_due_date = $due_date_timestamp ? format_date($due_date_timestamp, 'm/d/Y') : null;
		
		$due_date = input_post_request('due_date', $formatted_due_date);
		tpl_assign("due_date", $due_date);

		$is_submited = input_post_request('submited') ==  'submited';

		$is_private = $is_submited ? input_post_request('is_private') == 'on' : $task_list->getIsPrivate();
		tpl_assign("is_private", $is_private);

		$is_high_priority = $is_submited ? input_post_request('is_high_priority') == 'on' : $task_list->getIsHighPriority();
		tpl_assign("is_high_priority", $is_high_priority);

		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_325'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('description', lang('c_326'), 'trim|required');

			$this->form_validation->set_rules('start_date', lang('c_192'), 'callback_validate_date',  
			array('validate_due_date' => lang('c_327')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'callback_validate_date',  
			array('validate_due_date' => lang('c_322')));

			$this->form_validation->set_rules('due_date', lang('c_138'), 'trim|callback_compare_dates');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$task_list->setName($name);
					$task_list->setDescription($description);

					if(logged_user()->isMember()) $task_list->setIsPrivate($is_private);
					$task_list->setIsHighPriority($is_high_priority);
					
					$task_list->setStartDate($start_date);
					$task_list->setDueDate($due_date);
										
					$task_list->save();

					$this->ActivityLogs->create($task_list, lang('c_329'), 'edit', $task_list->getIsPrivate());
						
					set_flash_success(sprintf(lang('c_81'), lang('c_184')));
															
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}

	public function complete_task($id) {

		$task = $this->ProjectTasks->findById($id);
		if(!is_null($task)) $task_list = $task->getTaskList();
		
		if(is_null($task_list) || !(logged_user()->isOwner() || (logged_user()->isMember() && logged_user()->isProjectUser($task_list->getProject()))) ) {

			set_flash_error(lang('e_3'));
			redirect('projects');

		} else {
	
			try {
				
				$task->setCompletedAt(time());
				$task->setCompletedById(logged_user()->getId());
				
				$task->save();

				$this->ActivityLogs->create($task, lang('c_330'), 'close', $task_list->getIsPrivate());
				
				set_flash_success(sprintf(lang('c_168'), lang('c_183')));
				
			} catch(Exception $e) {
	
				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));
	
			}

			redirect($task_list->getObjectURL());
	
		}
		
	}

	public function reopen_task($id) {

		$task = $this->ProjectTasks->findById($id);
		if(!is_null($task)) $task_list = $task->getTaskList();
		
		if(is_null($task_list) || !(logged_user()->isOwner() || (logged_user()->isMember() && logged_user()->isProjectUser($task_list->getProject()))) ) {

			set_flash_error(lang('e_3'));
			redirect('projects');

		} else {

			try {
				
				$task->setCompletedAt(null);
				$task->setCompletedById(NULL);
				
				$task->save();

				$this->ActivityLogs->create($task, lang('c_331'), 'close', $task_list->getIsPrivate());
				
				set_flash_success(sprintf(lang('c_41'), lang('c_183')));
				
			} catch(Exception $e) {
	
				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));
	
			}

			redirect($task_list->getObjectURL());
		
		}
	
	}	
			
}
