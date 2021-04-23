<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
				
	}
	
	public function index() {

		$valid_models = $this->SearchableObjects->getModels();
		tpl_assign("valid_models", $valid_models);

		$time_periods = $this->SearchableObjects->getTimePeriods();
		tpl_assign("time_periods", $time_periods);

		$data = array();
		$total_items = 0;
		$total_pages = 0;
		
		$page = (int) input_get_request('page', 1);
		$items_per_page = 20;
		$start = ($page - 1) * $items_per_page;
		tpl_assign("items_per_page", $items_per_page);

		$active_projects = logged_user()->getActiveProjects();
		tpl_assign("active_projects", $active_projects);

		$phrase = input_get_request('term');
		tpl_assign("term", $phrase);

		$model = input_get_request('model');
		$time_period = input_get_request('time_period');
		$project = input_get_request('project');

		if(isset($active_projects) && is_array($active_projects) && count($active_projects)) {

			$project_ids = array();
			
			foreach($active_projects as $active_project) {
				$project_ids[] = $active_project->getId();
			}

		} else {
			$project = null;
		}

		$include_private = logged_user()->isMember();
		if(isset($project) && in_array($project, $project_ids)) {
			$project_ids = array($project);
		} else {
			$project_ids = null;
		}

		if(!(isset($model) && isset($valid_models[$model]))) $model = null;
		if(!(isset($time_period) && isset($time_periods[$time_period]))) $time_period = null;

		if(isset($phrase) && $phrase != "") {
			list($data, $total_items) = $this->SearchableObjects->getPaginate($phrase, $model, $project_ids, $time_period, $include_private, $items_per_page, $start);
			$total_pages = (int) ceil($total_items/$items_per_page);
		}
			

		tpl_assign("model", $model);
		tpl_assign("time_period", $time_period);
		tpl_assign("project", $project);
		tpl_assign("total_items", $total_items);
		tpl_assign('total_pages', $total_pages);
		tpl_assign("search_results", $data);
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
