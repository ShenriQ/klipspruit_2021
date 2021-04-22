<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Timesheet extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

 		if(!logged_user()->isMember()) redirect('dashboard');
				
	}
	
	public function invoice_items_json() {

		only_ajax_request_allowed();

		$invoice_entries = array();

		$project_id = (int) input_get_request('project_id');
		if($project_id > 0) {

			$group_by = input_get_request('group_by');

			$date_filter_from = input_get_request('date_filter_from');
			$date_filter_to = input_get_request('date_filter_to');

			$invoice_item_project_timelogs_table = $this->InvoiceItemProjectTimelogs->getTableName(true);
			$project_timelogs_table = $this->ProjectTimelogs->getTableName(true);

			$conditions = array($invoice_item_project_timelogs_table.'.`project_timelog_id` IS NULL AND `project_id` = ? AND `is_billable` = ? AND `is_approved` = ?', 
			$project_id, true, true);

			if(isset($date_filter_from) && validate_date($date_filter_from)) {

				$filter_from = format_mysql_date($date_filter_from);
			
				$conditions[0] .= " AND DATE(`created_at`) >= ?";
				array_push($conditions, $filter_from);
			
			}

			if(isset($date_filter_to) && validate_date($date_filter_to)) {
			
				$filter_to = format_mysql_date($date_filter_to);
			
				$conditions[0] .= " AND DATE(`created_at`) <= ?";
				array_push($conditions, $filter_to);
			
			}

			$joins = array('table' => $invoice_item_project_timelogs_table, 'cond' => $invoice_item_project_timelogs_table.'.`project_timelog_id` = '.$project_timelogs_table.'.`id`', 'type' => 'LEFT');
			$arguments = array('joins' => $joins, 'conditions' => $conditions);
			
			$project_timelogs = $this->ProjectTimelogs->find($arguments);
			if(isset($project_timelogs) && is_array($project_timelogs) && count($project_timelogs)) {
				
				foreach($project_timelogs as $project_timelog) {

					$linked_task = $project_timelog->getTask();
					$linked_project = $project_timelog->getProject();

					if($group_by == 'separate') {
						array_push($invoice_entries, [
							'description' => lang("c_356") . " #" . $project_timelog->getId() . " > " . $project_timelog->getMemo() . (isset($linked_task) ? " (" . $linked_task->getName() . ")" : "") . " : " . $linked_project->getName(),
							'unit_cost' => number_format($project_timelog->getHourlyRate(), 2),
							'quantity' => number_format($project_timelog->getTotalHours(), 2),
							'total' => number_format($project_timelog->getHourlyRate() * $project_timelog->getTotalHours(), 2),
							'timelog_ids' => array($project_timelog->getId())
						]);	
					} elseif ($group_by == 'task') {
						if(isset($linked_task)) {
							if(isset($invoice_entries[$linked_task->getId()])) {
								$this->update_invoice_entries($invoice_entries, $project_timelog, $linked_task->getId());
							} else {
								$description = lang("c_183") . " #" . $linked_task->getId() . " > " . $linked_task->getName() . " : " . $linked_project->getName();
								$this->add_invoice_entry($invoice_entries, $project_timelog, $linked_task->getId(), $description);
							}
						} else {
							if(isset($invoice_entries[0])) {
								$this->update_invoice_entries($invoice_entries, $project_timelog, 0);
							} else {
								$description = lang("c_523.48") . " : " . $linked_project->getName();
								$this->add_invoice_entry($invoice_entries, $project_timelog, 0, $description);
							}
						}
					} elseif ($group_by == 'all') {

						if(isset($invoice_entries[0])) {
							$this->update_invoice_entries($invoice_entries, $project_timelog, 0);
						} else {
							$description = lang("c_523.49") . " : " . $linked_project->getName();
							$this->add_invoice_entry($invoice_entries, $project_timelog, 0, $description);
						}

					}
				}
			}

		}

		$this->renderText(json_encode($invoice_entries));

	}

	private function update_invoice_entries(&$invoice_entries, $project_timelog, $index) {

		$prev_entry = $invoice_entries[$index];

		$quantity = number_format($prev_entry['quantity'] + $project_timelog->getTotalHours(), 2);
		$total = number_format($prev_entry['total'] + ($project_timelog->getHourlyRate() * $project_timelog->getTotalHours()), 2);
		$unit_cost =  number_format($total/$quantity, 2);
		$timelog_ids = (array) $prev_entry['timelog_ids'];
		array_push($timelog_ids, $project_timelog->getId());

		$invoice_entries[$index] = [
			'description' => $prev_entry['description'],
			'unit_cost' => $unit_cost,
			'quantity' => $quantity,
			'total' => $total,
			'timelog_ids' => $timelog_ids
		];

	}

	private function add_invoice_entry(&$invoice_entries, $project_timelog, $index, $description) {

		$invoice_entries[$index] = [
			'description' => $description,
			'unit_cost' => number_format($project_timelog->getHourlyRate(), 2),
			'quantity' => number_format($project_timelog->getTotalHours(), 2),
			'total' => number_format($project_timelog->getHourlyRate() * $project_timelog->getTotalHours(), 2),
			'timelog_ids' => [$project_timelog->getId()]
		];

	}

	public function index() {
		
		$year = date('Y', time()); 

		$start = input_get_request('start', $year."-01-01");
		tpl_assign("stats_start_short", $start);
 		
 		$end = input_get_request('end', $year."-12-31");
		tpl_assign("stats_end_short", $end);

		tpl_assign("stats_start", date('Y/m/d', human_to_unix($start.' 00:00')));
		tpl_assign("stats_end", date('Y/m/d', human_to_unix($end.' 00:00')));

		$projects = logged_user()->getActiveProjects();
		tpl_assign('projects', $projects);
		
		$project_ids = get_objects_ids($projects);

		$project_id = input_get_request('project_id', 0);
		tpl_assign('project_id', $project_id);

		$selected_project_ids = ($project_id > 0 && in_array($project_id, $project_ids)) ? 
		array($project_id) : $project_ids;

		$timelogs = $this->ProjectTimelogs->getByProjects($selected_project_ids, false, $start, $end);
		tpl_assign('timelogs', $timelogs);
	
	}

	public function validate_date($date) {
		return $date != '' ? validate_date($date, 'Y-m-d H:i') : true;
	}

	public function compare_dates() {
	
		$start = strtotime($this->input->post('start_date'));
		$end = strtotime($this->input->post('end_date'));
	
		if($start > $end) {
			$this->form_validation->set_message('compare_dates',lang('c_191'));
			return false;
		}

	}

	public function create($request_project_id = null) {
			
		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('timesheet/_timelog_form');

		$project_timelog = new ProjectTimelog();
		tpl_assign('project_timelog', $project_timelog);

		if(logged_user()->isOwner() || logged_user()->isAdmin()) {

			$user_id = input_post_request('user_id', 0);
			tpl_assign("user_id", $user_id);

			$is_approved = input_post_request('is_approved', 1);
			tpl_assign("is_approved", $is_approved);

		} else {

			$is_approved = false;
			tpl_assign("is_approved", $is_approved);
		
			$user_id = logged_user()->getId();
			tpl_assign("user_id", $user_id);
		
		} 
				
		$memo = input_post_request('memo');
		tpl_assign("memo", $memo);

		$hourly_rate = (float) input_post_request('hourly_rate', logged_user()->getHourlyRate());
		tpl_assign("hourly_rate", $hourly_rate);

		$is_billable = input_post_request('is_billable') == 'on';
		tpl_assign("is_billable", $is_billable);

		$start_date = input_post_request('start_date');
		tpl_assign("start_date", $start_date);
		
		$end_date = input_post_request('end_date');
		tpl_assign("end_date", $end_date);

		$projects = logged_user()->getActiveProjects();
		tpl_assign('projects', $projects);

		$project_id = (int) input_post_request('project_id', $request_project_id);
		tpl_assign("project_id", $project_id);

		$project = $this->Projects->findById($project_id);
		if(!(isset($project) && (logged_user()->isOwner() || (logged_user()->isMember() 
		&& logged_user()->isProjectUser($project)))) ) {
			$project_id = 0; // invalid project selected
		}

		$task_id = (int) input_post_request('task_id');
		$task = $this->ProjectTasks->findById($task_id);
		$task_id = !is_null($task) && $project_id > 0 && $task->getProjectId() == $project_id ? $task->getId() : 0;

		tpl_assign("task_id", $task_id);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('project_id', lang('c_23'), 'required|greater_than[0]',  array('greater_than' => lang('c_336')));
			
			if(logged_user()->isOwner() || logged_user()->isAdmin()) {
				$this->form_validation->set_rules('user_id', lang('c_28'), 'required|greater_than[0]',  array('greater_than' => lang('c_351')));
			}
			
			$this->form_validation->set_rules('start_date', lang('c_192'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_352')));

			$this->form_validation->set_rules('end_date', lang('c_198'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_353')));

			$this->form_validation->set_rules('end_date', lang('c_198'), 'trim|callback_compare_dates');

			$this->form_validation->set_rules('memo', lang('c_354'), 'trim|required|max_length[100]');
							
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$this->db->trans_begin();

					$project_timelog->setMemo($memo);
					$project_timelog->setProjectId($project_id);
					$project_timelog->setTaskId($task_id);					
					$project_timelog->setMemberId($user_id);
					$project_timelog->setStartTime($start_date);
					$project_timelog->setEndTime($end_date);
					$project_timelog->setTotalHours(round((strtotime($end_date)-strtotime($start_date))/3600, 2));
					$project_timelog->setIsBillable($is_billable);
					$project_timelog->setHourlyRate($hourly_rate);

					$project_timelog->setIsApproved($is_approved == 1);

					$project_timelog->setCreatedById(logged_user()->getId());
					$project_timelog->save();

					$this->ActivityLogs->create($project_timelog, lang('c_355'), 'add', true, true);

					$this->db->trans_commit();
					set_flash_success(sprintf(lang('c_54'), lang('c_356')));
		
				} catch (Exception $e) {
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}

		}
			
	}

	public function edit($id) {

		only_ajax_request_allowed();

		$project_timelog = logged_user()->getMyStartedTimer();
		if(isset($project_timelog) && $project_timelog->getId() == $id) {
			$is_my_timer = true;
		} else {

			if(!(logged_user()->isOwner() || logged_user()->isAdmin())) die();
		
			$project_timelog = $this->ProjectTimelogs->findById($id);
			$is_my_timer = false;
		
		}


		$this->setLayout('modal');
		$this->setTemplate('timesheet/_timelog_form');
		
		$active_projects = logged_user()->getActiveProjects();
		$project_ids = get_objects_ids($active_projects);

		if(is_null($project_timelog) || !in_array($project_timelog->getProjectId(), $project_ids) ) {
			die();
		}
		
		tpl_assign('is_my_timer', $is_my_timer);
		tpl_assign('project_timelog', $project_timelog);
		tpl_assign('projects', $active_projects);

		if(!$is_my_timer) {

			$user_id = input_post_request('user_id', $project_timelog->getMemberId());
			tpl_assign("user_id", $user_id);

		} else {
			
			if(logged_user()->getId() == $project_timelog->getMemberId()) {
				$user_id = $project_timelog->getMemberId();
				tpl_assign("user_id", $user_id);
			} else {
				die();
			}		
	
		} 

		$is_submited = input_post_request('submited') ==  'submited';

		$memo = input_post_request('memo', $project_timelog->getMemo());
		tpl_assign("memo", $memo.$project_timelog->getStartDate());

		$start_date_timestamp = $project_timelog->getStartTime();
		$formatted_start_date = $start_date_timestamp ? format_date($start_date_timestamp, 'Y-m-d H:i') : null;
		
		$hourly_rate = (float) input_post_request('hourly_rate', $project_timelog->getHourlyRate());
		tpl_assign("hourly_rate", $hourly_rate);

		$is_billable = $is_submited ? input_post_request('is_billable') == 'on' : $project_timelog->getIsBillable();
		tpl_assign("is_billable", $is_billable);

		$start_date = input_post_request('start_date', $formatted_start_date);
		tpl_assign("start_date", $start_date);

		$end_date_timestamp = $project_timelog->getEndTime();
		$formatted_end_date = $end_date_timestamp ? format_date($end_date_timestamp, 'Y-m-d H:i') : (($project_timelog->getIsTimer() && $project_timelog->getIsTimerStarted()) ? format_date(time(), 'Y-m-d H:i') : null);
		
		$end_date = input_post_request('end_date', $formatted_end_date);
		tpl_assign("end_date", $end_date);

		$project_id = (int) input_post_request('project_id', $project_timelog->getProjectId());
		$project = $this->Projects->findById($project_id);
		if(!(isset($project) && (logged_user()->isOwner() || (logged_user()->isMember() 
		&& logged_user()->isProjectUser($project)))) ) {
			$project_id = 0; // invalid project selected
		}

		tpl_assign("project_id", $project_id);

		$task_id = (int) input_post_request('task_id', $project_timelog->getTaskId());
		$task = $this->ProjectTasks->findById($task_id);
		$task_id = !is_null($task) && $project_id > 0 && $task->getProjectId() == $project_id ? $task->getId() : 0;

		tpl_assign("task_id", $task_id);

		$is_approved = input_post_request('is_approved', (int) $project_timelog->getIsApproved());
		tpl_assign("is_approved", $is_approved);
	
		if ($is_submited) {

			if(!$is_my_timer) {

				$this->form_validation->set_rules('user_id', lang('c_28'), 'required|greater_than[0]',  array('greater_than' => lang('c_351')));
			
				$this->form_validation->set_rules('start_date', lang('c_192'), 'required|callback_validate_date',  
				array('validate_date' => lang('c_352')));

				$this->form_validation->set_rules('end_date', lang('c_198'), 'required|callback_validate_date',  
				array('validate_date' => lang('c_353')));

				$this->form_validation->set_rules('end_date', lang('c_198'), 'trim|callback_compare_dates');
			
			}

			$this->form_validation->set_rules('memo', lang('c_354'), 'trim|required|max_length[100]');
							
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$this->db->trans_begin();

					$project_timelog->setMemo($memo);
					$project_timelog->setTaskId($task_id);

					$project_timelog->setIsBillable($is_billable);
					$project_timelog->setHourlyRate($hourly_rate);

					if($is_my_timer) {

						$project_timelog->setIsTimerStarted(false);
						$project_timelog->setEndTime($formatted_end_date);
						$project_timelog->setTotalHours(round((strtotime($formatted_end_date)-strtotime($formatted_start_date))/3600, 2));

						$project_timelog->save(false);

					} else {

						$project_timelog->setStartTime($start_date);
						$project_timelog->setEndTime($end_date);	
						$project_timelog->setTotalHours(round((strtotime($end_date)-strtotime($start_date))/3600, 2));

						$project_timelog->setMemberId($user_id);
						$project_timelog->setIsApproved($is_approved == 1);

						$project_timelog->save();

					}
					
					$this->ActivityLogs->create($project_timelog, lang('c_357'), 'edit', true, true);
					$this->db->trans_commit();

					set_flash_success(sprintf(lang('c_57'), lang('c_356')));
		
				} catch (Exception $e) {
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}

		} 
			
	}
		
}
