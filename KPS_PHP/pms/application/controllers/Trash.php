<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trash extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
				
	}
	
	public function index() {

		if(!logged_user()->isOwner()) redirect('dashboard');		

		tpl_assign('trashed_users', $this->Users->getByTrashed());
		tpl_assign('trashed_companies', $this->Companies->getByTrashed());
		tpl_assign('trashed_discussions', $this->ProjectDiscussions->getByTrashed());
		tpl_assign('trashed_comments', $this->ProjectComments->getByTrashed());
		tpl_assign('trashed_tasks', $this->ProjectTasks->getByTrashed());
		tpl_assign('trashed_tasklists', $this->ProjectTaskLists->getByTrashed());
		tpl_assign('trashed_files', $this->ProjectFiles->getByTrashed());
		tpl_assign('trashed_invoices', $this->Invoices->getByTrashed());
		tpl_assign('trashed_estimates', $this->Estimates->getByTrashed());
		tpl_assign('trashed_tickets', $this->Tickets->getByTrashed());
		tpl_assign('trashed_timesheets', $this->ProjectTimelogs->getByTrashed());

	}

	public function do_empty() {
		
		if(!logged_user()->isOwner()) die();		

		only_ajax_request_allowed();
		$this->setLayout('modal');
		
		$is_submited = input_post_request('submited') ==  'submited';		

		if ($is_submited) {

			try {

				$this->db->trans_begin();
			
				$trashed_users = $this->Users->getByTrashed();
				if(isset($trashed_users) && is_array($trashed_users) && count($trashed_users)) {
					
					foreach($trashed_users as $trashed_user) {

						$trashed_user->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}
				
				$trashed_companies = $this->Companies->getByTrashed();
				if(isset($trashed_companies) && is_array($trashed_companies) && count($trashed_companies)) {
					
					foreach($trashed_companies as $trashed_company) {

						$trashed_company->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_discussions = $this->ProjectDiscussions->getByTrashed();
				if(isset($trashed_discussions) && is_array($trashed_discussions) && count($trashed_discussions)) {
					
					foreach($trashed_discussions as $trashed_discussion) {

						$trashed_discussion->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_comments = $this->ProjectComments->getByTrashed();
				if(isset($trashed_comments) && is_array($trashed_comments) && count($trashed_comments)) {
					
					foreach($trashed_comments as $trashed_comment) {

						$trashed_comment->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_tasks = $this->ProjectTasks->getByTrashed();
				if(isset($trashed_tasks) && is_array($trashed_tasks) && count($trashed_tasks)) {
					
					foreach($trashed_tasks as $trashed_task) {

						$trashed_task->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_tasklists = $this->ProjectTaskLists->getByTrashed();
				if(isset($trashed_tasklists) && is_array($trashed_tasklists) && count($trashed_tasklists)) {
					
					foreach($trashed_tasklists as $trashed_tasklist) {

						$trashed_tasklist->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_files = $this->ProjectFiles->getByTrashed();
				if(isset($trashed_files) && is_array($trashed_files) && count($trashed_files)) {
					
					foreach($trashed_files as $trashed_file) {

						$trashed_file->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_invoices = $this->Invoices->getByTrashed();
				if(isset($trashed_invoices) && is_array($trashed_invoices) && count($trashed_invoices)) {
					
					foreach($trashed_invoices as $trashed_invoice) {

						$trashed_invoice->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_estimates = $this->Estimates->getByTrashed();
				if(isset($trashed_estimates) && is_array($trashed_estimates) && count($trashed_estimates)) {
					
					foreach($trashed_estimates as $trashed_estimate) {

						$trashed_estimate->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_tickets = $this->Tickets->getByTrashed();
				if(isset($trashed_tickets) && is_array($trashed_tickets) && count($trashed_tickets)) {
					
					foreach($trashed_tickets as $trashed_ticket) {

						$trashed_ticket->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$trashed_timesheets = $this->ProjectTimelogs->getByTrashed();
				if(isset($trashed_timesheets) && is_array($trashed_timesheets) && count($trashed_timesheets)) {
					
					foreach($trashed_timesheets as $trashed_timesheet_o) {

						$trashed_timesheet_o->delete();
						if ($this->db->trans_status() === FALSE) throw new Exception();
				
					}
					
				}

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_377'), lang('c_378')));
				
			} catch(Exception $e) {
	
				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));
	
			}

			$this->renderText(output_ajax_request(true));
			
		}
				
	}
		
	public function move($item_name, $item_id) {

		only_ajax_request_allowed();
		$this->setLayout('modal');
		
		switch($item_name) {
			
			case 'user' : 

				if(!logged_user()->isOwner() || $item_id == logged_user()->getId()) {
					set_flash_error(lang('c_379'), true);
				}
				
				$item_object = $this->Users->findById($item_id);
				break;
			
			case 'company' : 

				if(!logged_user()->isOwner() || $item_id == owner_company()->getId()) {
					set_flash_error(lang('c_380'), true);
				}
				
				$item_object = $this->Companies->findById($item_id);
				break;
			
			case 'projectdiscussion' :
				
				$item_object = $this->ProjectDiscussions->findById($item_id);
				if(is_null($item_object) || !($item_object->isObjectOwner(logged_user()) || logged_user()->isOwner() 
				|| (logged_user()->isAdmin() && logged_user()->isProjectUser($item_object->getProject())))) {
					set_flash_error(lang('c_381'), true);
				}
				
				break;
							
			case 'projectcomment' :
				
				$item_object = $this->ProjectComments->findById($item_id);
				if(is_null($item_object) || !($item_object->isObjectOwner(logged_user()) || logged_user()->isOwner() 
				|| (logged_user()->isAdmin() && logged_user()->isProjectUser($item_object->getProject())))) {
					set_flash_error(lang('c_382'), true);
				}
				
				break;

			case 'projecttask' :

				$item_object = $this->ProjectTasks->findById($item_id);
				if(!is_null($item_object)) $task_list = $item_object->getTaskList();
				
				if(!isset($task_list) || !($task_list->isObjectOwner(logged_user()) || logged_user()->isOwner() 
				|| (logged_user()->isAdmin() && logged_user()->isProjectUser($task_list->getProject())))) {
					set_flash_error(lang('c_383'), true);
				}

				break;

			case 'projecttasklist' :

				$item_object = $this->ProjectTaskLists->findById($item_id);
				if(is_null($item_object) || !($item_object->isObjectOwner(logged_user()) || logged_user()->isOwner() 
				|| (logged_user()->isAdmin() && logged_user()->isProjectUser($item_object->getProject())))) {
					set_flash_error(lang('c_384'), true);
				}

				break;

			case 'projectfile' :

				$item_object = $this->ProjectFiles->findById($item_id);
				if(is_null($item_object) || (!logged_user()->isMember() && $item_object->getIsPrivate()) || !($item_object->isObjectOwner(logged_user()) || logged_user()->isOwner() 
				|| (logged_user()->isAdmin() && logged_user()->isProjectUser($item_object->getProject())))) {
					set_flash_error(lang('c_385'), true);
				}

				break;
		
			case 'invoice' :

				$item_object = $this->Invoices->findById($item_id);
				if(is_null($item_object) || !logged_user()->isOwner()) {
					set_flash_error(lang('c_386'), true);
				}

				break;
			
			case 'estimate' :

				$item_object = $this->Estimates->findById($item_id);
				if(is_null($item_object) || !logged_user()->isOwner()) {
					set_flash_error(lang('c_387'), true);
				}

				break;
											
			case 'ticket' :

				$item_object = $this->Tickets->findById($item_id);
				if(is_null($item_object) || !(!logged_user()->isMember() || logged_user()->isAdmin() || logged_user()->isOwner())) {
					set_flash_error(lang('c_388'), true);
				}

				break;
											
			case 'projecttimelog' :

				$item_object = $this->ProjectTimelogs->findById($item_id);
				if(is_null($item_object) || !(logged_user()->isMember() || logged_user()->isAdmin() || logged_user()->isOwner())) {
					set_flash_error(lang('c_389'), true);
				}

				break;
											
		}
		
		if(!(isset($item_object) && $item_object instanceof Application_object
		&& ($item_object->isTrashable() && !$item_object->getIsTrashed())) ) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('item_object', $item_object);	
		
		$is_submited = input_post_request('submited') ==  'submited';		

		if ($is_submited) {
			
			try {
				
				$this->db->trans_begin();

				$item_object->setTrashedById(logged_user()->getId()); // trash user ..
				$item_object->moveToTrash();																	
				if ($this->db->trans_status() === FALSE) throw new Exception();

				if($item_object instanceof User) {
					$target_source = logged_user()->getTargetSource();
					$this->Users->updateUsersCache($target_source);
				}

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_390'), ucfirst(str_replace("project", "", $item_name))));
				
			} catch(Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}

			$this->renderText(output_ajax_request(true));

		}	

	}

	public function restore($item_name, $item_id) {

		if(!logged_user()->isOwner()) redirect("dashboard");		

		switch($item_name) {
			
			case 'user' : 
				
				$item_object = $this->Users->findById($item_id);
				break;

			case 'company' : 
				
				$item_object = $this->Companies->findById($item_id);
				break;

			case 'projectdiscussion' : 
				
				$item_object = $this->ProjectDiscussions->findById($item_id);
				break;

			case 'projectcomment' : 
				
				$item_object = $this->ProjectComments->findById($item_id);
				break;

			case 'projecttask' : 
				
				$item_object = $this->ProjectTasks->findById($item_id);
				break;

			case 'projecttasklist' :

				$item_object = $this->ProjectTaskLists->findById($item_id);
				break;

			case 'projectfile' :

				$item_object = $this->ProjectFiles->findById($item_id);
				break;

			case 'invoice' :

				$item_object = $this->Invoices->findById($item_id);
				break;

			case 'estimate' :

				$item_object = $this->Estimates->findById($item_id);
				break;

			case 'ticket' :

				$item_object = $this->Tickets->findById($item_id);
				break;

			case 'projecttimelog' :

				$item_object = $this->ProjectTimelogs->findById($item_id);
				break;

		}
				
		if(!(isset($item_object) && $item_object instanceof Application_object
		&& ($item_object->isTrashable() && $item_object->getIsTrashed())) ) {
			set_flash_error(lang('e_3'));
		} else {

			try {

				$this->db->trans_begin();
				
				$item_object->setTrashedById(0); // reset user ..
				$item_object->restoreFromTrash();									
				if ($this->db->trans_status() === FALSE) throw new Exception();

				if($item_object instanceof User) {
					$target_source = logged_user()->getTargetSource();
					$this->Users->updateUsersCache($target_source);
				}

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_390'), ucfirst(str_replace("project", "", $item_name))));
				
			} catch(Exception $e) {
	
				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));
	
			}
		
		}

		redirect('trash');
		
	}
	
	public function delete($item_name, $item_id) {

		if(!logged_user()->isOwner()) redirect("dashboard");		

		switch($item_name) {
			
			case 'user' : 
				
				$item_object = $this->Users->findById($item_id);
				break;

			case 'company' : 
				
				$item_object = $this->Companies->findById($item_id);
				break;

			case 'projectdiscussion' : 
				
				$item_object = $this->ProjectDiscussions->findById($item_id);
				break;

			case 'projectcomment' : 
				
				$item_object = $this->ProjectComments->findById($item_id);
				break;

			case 'projecttask' : 
				
				$item_object = $this->ProjectTasks->findById($item_id);
				break;

			case 'projecttasklist' :

				$item_object = $this->ProjectTaskLists->findById($item_id);
				break;

			case 'projectfile' :

				$item_object = $this->ProjectFiles->findById($item_id);
				break;

			case 'invoice' :

				$item_object = $this->Invoices->findById($item_id);
				break;

			case 'estimate' :

				$item_object = $this->Estimates->findById($item_id);
				break;

			case 'projecttimelog' :

				$item_object = $this->ProjectTimelogs->findById($item_id);
				break;

		}
				
		if(!(isset($item_object) && $item_object instanceof Application_object
		&& ($item_object->isTrashable() && $item_object->getIsTrashed())) ) {
			set_flash_error(lang('e_3'));
		} else {

			try {

				$this->db->trans_begin();
				
				$item_object->delete();									
				if ($this->db->trans_status() === FALSE) throw new Exception();

				$this->db->trans_commit();
				set_flash_success(sprintf(lang('c_56'), ucfirst(str_replace("project", "", $item_name))));
				
			} catch(Exception $e) {
	
				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));
	
			}
		
		}

		redirect('trash');

	}

}
