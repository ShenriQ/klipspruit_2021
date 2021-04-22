<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
				
	}
	
	public function index() {

		$total_items = 0;
		$total_pages = 0;
		
		$page = (int) input_get_request('page', 1);
		$items_per_page = 20;
		$start = ($page - 1) * $items_per_page;
		tpl_assign("items_per_page", $items_per_page);

		$activity_logs = null;
		$act_projects = logged_user()->getActiveProjects();
		
		if(is_array($act_projects) && count($act_projects)) {
		
			$include_private = logged_user()->isMember();
			$include_hidden = logged_user()->isAdmin() || logged_user()->isOwner();
		
			$project_ids = array();
			foreach($act_projects as $act_project) {
				$project_ids[] = $act_project->getId();
			}
		
			list($activity_logs, $total_items) = $this->ActivityLogs->getPaginate($include_private, $include_hidden, $project_ids, $items_per_page, $start);
			$total_pages = (int) ceil($total_items/$items_per_page);

		}


		tpl_assign("total_items", $total_items);
		tpl_assign('total_pages', $total_pages);
		tpl_assign("activity_logs", $activity_logs);
		tpl_assign('current_page', $page);

		$current_url = current_url(); unset($_GET['page']);
		$query_string = http_build_query($_GET);
		if($query_string) {
			$current_page_url = $current_url.'?'.$query_string;
			$is_filtered = true;
		} else {
			$current_page_url = $current_url;
			$is_filtered = false;
		}

		tpl_assign('is_filtered', $is_filtered);
		tpl_assign('current_page_url', $current_page_url);

	}
	
}
