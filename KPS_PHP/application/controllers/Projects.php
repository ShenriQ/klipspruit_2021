<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

	}
	
	public function index() {

		$sort_by = input_get_request('sort_by');
		tpl_assign('sort_by', $sort_by);
		
		$projects = $sort_by == "completed" ? logged_user()->getCompletedProjects() : logged_user()->getActiveProjects();
		tpl_assign('projects', $projects);
	   
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

	public function users_json($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		only_ajax_request_allowed();

		$users = array();
		$project = $this->Projects->findById($id);

		if(isset($project)) {
	
			$project_users = $this->Users->getByProject($project);	
			if(isset($project_users) && is_array($project_users) && count($project_users)) {
			
				foreach($project_users as $project_user) {
					
					if($project_user->isMember()) {
						array_push($users, array('id' => $project_user->getId(), 'name' => $project_user->getName()));
					}
			
				}
			
			}
		
		}
		
		$this->renderText(json_encode($users));

	}	

	public function add() {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('projects/_project_form');

		$project = new Project();
		tpl_assign('project', $project);

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$description = input_post_request('description');
		tpl_assign("description", $description);

		$start_date = input_post_request('start_date');
		tpl_assign("start_date", $start_date);

		$due_date = input_post_request('due_date');
		tpl_assign("due_date", $due_date);

		$company_id = input_post_request('company_id', 0);
		tpl_assign("company_id", $company_id);
		
		$default_label = $this->GlobalLabels->getDefaultByType('PROJECT');
		$default_label_id = isset($default_label) ? $default_label->getId() : 0;

		$label_id = input_post_request('label_id', $default_label_id);
		tpl_assign("label_id", $label_id);

//		$enable_timelog = input_post_request('enable_timelog') == 'on';
//		tpl_assign("enable_timelog", $enable_timelog);
//
		$is_visible_timelog = input_post_request('is_visible_timelog') == 'on';
		tpl_assign("is_visible_timelog", $is_visible_timelog);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$target_source = logged_user()->getTargetSource();
			if($target_source->getProjectsCreated() >= $target_source->getProjectsLimit()) {
				$this->renderText(output_ajax_request(false, lang('c_540')));
			} else {

				$this->form_validation->set_rules('name', lang('c_237'), 'trim|required|max_length[100]');

				$this->form_validation->set_rules('start_date', lang('c_192'), 'required|callback_validate_date',  
				array('validate_due_date' => lang('c_193')));

				$this->form_validation->set_rules('due_date', lang('c_238'), 'required|callback_validate_date',  
				array('validate_due_date' => lang('c_239')));

				$this->form_validation->set_rules('due_date', lang('c_238'), 'trim|callback_compare_dates');
			
				if ($this->form_validation->run() == FALSE) {
					$this->renderText(output_ajax_request(false, validation_errors()));
				} else {
			
					try{
						
						$this->db->trans_begin();

						$project->setName($name);
						$project->setDescription($description);

						$project->setCompanyId($company_id);
						$project->setLabelId($label_id);
						
	//					$project->setIsEnableTimelog($enable_timelog);
						$project->setIsTimelogVisible($is_visible_timelog);

						$project->setStartDate($start_date);
						$project->setDueDate($due_date);

						$project->setCreatedById(logged_user()->getId());
						$project->save();

						if(!logged_user()->isOwner()) {

							$project_user = new ProjectUser();
							
							$project_user->setProjectId($project->getId());
							$project_user->setUserId(logged_user()->getId());
							
							$project_user->save();
							
						}
						
						
						$this->ActivityLogs->create($project, lang('c_241'), 'add', true);
						
						$this->Projects->updateProjectsCache($target_source);

						$this->db->trans_commit();
						set_flash_success(sprintf(lang('c_79'), lang('c_23')));
						
					}catch(Exception $e){

						$this->db->trans_rollback();
						set_flash_error(lang('e_1'));

					}

					$this->renderText(output_ajax_request(true));

				}		
			}
		}
					
	}

	public function edit($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('projects/_project_form');

		$project = $this->Projects->findById($id);
		if(is_null($project) || $project->getIsTrashed() || $project->isCompleted() 
		|| (logged_user()->isAdmin() && !logged_user()->isProjectUser($project)) ) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('project', $project);

		$name = input_post_request('name', $project->getName());
		tpl_assign("name", $name);

		$description = input_post_request('description', $project->getDescription());
		tpl_assign("description", $description);

		$start_date_timestamp = $project->getStartDate();
		$formatted_start_date = $start_date_timestamp ? format_date($start_date_timestamp, 'm/d/Y') : null;
		
		$start_date = input_post_request('start_date', $formatted_start_date);
		tpl_assign("start_date", $start_date);

		$due_date_timestamp = $project->getDueDate();
		$formatted_due_date = $due_date_timestamp ? format_date($due_date_timestamp, 'm/d/Y') : null;
		
		$due_date = input_post_request('due_date', $formatted_due_date);
		tpl_assign("due_date", $due_date);

		$company_id = input_post_request('company_id', $project->getCompanyId());
		tpl_assign("company_id", $company_id);
		
		$label_id = input_post_request('label_id', $project->getLabelId());
		tpl_assign("label_id", $label_id);

		$is_submited = input_post_request('submited') ==  'submited';

//		$enable_timelog = $is_submited ? input_post_request('enable_timelog') == 'on' : $project->getIsEnableTimelog();
//		tpl_assign("enable_timelog", $enable_timelog);
//
		$is_visible_timelog = $is_submited ? input_post_request('is_visible_timelog') == 'on' : $project->getIsTimelogVisible();
		tpl_assign("is_visible_timelog", $is_visible_timelog);
				
		if ($is_submited) {
	
			$this->form_validation->set_rules('name', lang('c_237'), 'trim|required|max_length[100]');

			$this->form_validation->set_rules('start_date', lang('c_192'), 'required|callback_validate_date',  
			array('validate_due_date' => lang('c_193')));

			$this->form_validation->set_rules('due_date', lang('c_238'), 'required|callback_validate_date',  
			array('validate_due_date' => lang('c_239')));
		
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$project->setName($name);
					$project->setDescription($description);

					$project->setCompanyId($company_id);
					$project->setLabelId($label_id);
					
//					$project->setIsEnableTimelog($enable_timelog);
					$project->setIsTimelogVisible($is_visible_timelog);

					$project->setStartDate($start_date);
					$project->setDueDate($due_date);

					$project->save();

					$this->ActivityLogs->create($project, lang('c_240'), 'edit', true);
					
					$this->db->trans_commit();
					set_flash_success(sprintf(lang('c_81'), lang('c_23')));
					
				}catch(Exception $e){

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
	
	public function manage_people($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('projects/_project_people_form');

		$project = $this->Projects->findById($id);
		if(is_null($project) || $project->getIsTrashed() || $project->isCompleted() 
		|| (logged_user()->isAdmin() && !logged_user()->isProjectUser($project)) ) {
			set_flash_error(lang('e_3'), true);
		}
		
		$project_users_data = array();
		$project_object_users = $project->getUsers(false, false);

		if(isset($project_object_users) && is_array($project_object_users) && count($project_object_users)) {
			foreach($project_object_users as $project_object_user) {
				$project_users_data[$project_object_user->getId()] = array('id' => $project_object_user->getId(), 'name' => $project_object_user->getName(), 'usertype' => ($project_object_user->isMember() ? lang('c_28') : lang('c_29')));
			}
		}
		
		tpl_assign('project', $project);
		tpl_assign("project_users_data", json_encode($project_users_data));

		$project_user_ids = json_decode(input_post_request('project_user_ids'));
		$is_submited = input_post_request('submited') ==  'submited';

		if($is_submited) {

			try{
				
				$this->db->trans_begin();
				
				$project->clearProjectUsers();
				if(isset($project_user_ids) && is_array($project_user_ids) && count($project_user_ids)) {

					foreach($project_user_ids as $project_user_id){
						
						$user_object = $this->Users->findById($project_user_id);
						if(isset($user_object)) {

							$project_user = new ProjectUser();
							
							$project_user->setProjectId($project->getId());
							$project_user->setUserId($user_object->getId());
							
							$project_user->save();
							
						}
						
					}

				} 

				$this->ActivityLogs->create($project, lang('c_242'), 'edit', true);
				
				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_243'), lang('c_244')));
				
			}catch(Exception $e){

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}

			$this->renderText(output_ajax_request(true));
					
		}
		
	}
	
	public function complete($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		only_ajax_request_allowed();
	
		$this->setLayout('modal');
	
		$project = $this->Projects->findById($id);
		if(is_null($project) || $project->getIsTrashed() || $project->isCompleted() 
		|| (logged_user()->isAdmin() && !logged_user()->isProjectUser($project)) ) {
			set_flash_error(lang('e_3'), true);
		}
		
		tpl_assign('project', $project);

		$is_submited = input_post_request('submited') ==  'submited';		

		if ($is_submited) {

			try {
				
				$project->setCompletedAt(time());
				$project->setCompletedById(logged_user()->getId());
				
				$project->save();

				$target_source = logged_user()->getTargetSource();
				$this->Projects->updateProjectsCache($target_source);

				$this->ActivityLogs->create($project, lang('c_245'), 'close', true);
				
				set_flash_success(sprintf(lang('c_168'), lang('c_23')));
				
			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}

			$this->renderText(output_ajax_request(true));
			
		}

	}

	public function reopen($id) {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) redirect("dashboard");
	
		$project = $this->Projects->findById($id);
		if(is_null($project) || $project->getIsTrashed() || !$project->isCompleted() 
		|| (logged_user()->isAdmin() && !logged_user()->isProjectUser($project)) ) {
			set_flash_error(lang('e_3'));
		}else {

			try {
				
				$project->setCompletedAt(null);
				$project->setCompletedById(NULL);
				
				$project->save();

				$target_source = logged_user()->getTargetSource();
				$this->Projects->updateProjectsCache($target_source);

				$this->ActivityLogs->create($project, lang('c_246'), 'open', true);
				set_flash_success(sprintf(lang('c_41'), lang('c_23')));
				
			} catch(Exception $e) {
	
				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));
	
			}
		
		}

		redirect('projects?sort_by=completed');
	
	}	

	public function access($id, $tab = 'overview', $object_id = null) {
		
		$project = $this->Projects->findById($id);
		if(is_null($project) || $project->getIsTrashed() 
		|| !(logged_user()->isProjectUser($project) || logged_user()->isOwner())) {
			
			set_flash_error(lang('e_3'));
			redirect("projects");
			
		}else {
		
			$this->setTemplate('projects/'.$tab);
			tpl_assign("is_project_dashboard", true);

			tpl_assign('project', $project);
			tpl_assign('object_id', $object_id);
			tpl_assign("active_tab", $tab);
			
		}

	}
		
}
