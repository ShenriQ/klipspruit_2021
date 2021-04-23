<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* SearchableObjects Model */

class SearchableObjects_model extends Application_model {

	private $all_models = [
		"estimates" => "Estimates",
		"invoices" => "Invoices",
		"comments" => "ProjectComments",
		"discussions" => "ProjectDiscussions",
		"files" => "ProjectFiles",
		"task_lists" => "ProjectTaskLists",
		"tasks" => "ProjectTasks",
		"tickets" => "Tickets"
	];

	private $member_models = [
		"comments" => "ProjectComments",
		"discussions" => "ProjectDiscussions",
		"files" => "ProjectFiles",
		"task_lists" => "ProjectTaskLists",
		"tasks" => "ProjectTasks",
		"tickets" => "Tickets"
	];

	private $timePeriods = [
		"today" => "CONVERT(datetime,  created_at) = GETDATE()",
		"yesterday" => "(CONVERT(datetime,  created_at) BETWEEN DATE_SUB(GETDATE(), INTERVAL 1 DAY) AND GETDATE())",
		"pastweek" => "(CONVERT(datetime,  created_at) BETWEEN DATE_SUB(GETDATE(), INTERVAL 7 DAY) AND GETDATE())",
		"pastmonth" => "(CONVERT(datetime,  created_at) BETWEEN DATE_SUB(GETDATE(), INTERVAL 1 MONTH) AND GETDATE())",
		"past3months" => "(CONVERT(datetime,  created_at) BETWEEN DATE_SUB(GETDATE(), INTERVAL 3 MONTH) AND GETDATE())",
		"past6months" => "(CONVERT(datetime,  created_at) BETWEEN DATE_SUB(GETDATE(), INTERVAL 6 MONTH) AND GETDATE())",
		"pastyear" => "(CONVERT(datetime,  created_at) BETWEEN DATE_SUB(GETDATE(), INTERVAL 1 YEAR) AND GETDATE())"
	];

    function __construct() {
        parent::__construct('searchable_objects', 'SearchableObject');
	}

	public function getTimePeriods() {
		return $this->timePeriods;
	}

	public function getModels() {
		return (logged_user()->isOwner() || logged_user()->isAdmin()) ?  $this->all_models : $this->member_models;
	}

	private function getSearchConditions($search_terms, $model = null, $project_ids = null, $time_period = null, $include_private = false) {

		$conditions = ['MATCH (field_content) AGAINST (? IN BOOLEAN MODE) AND target_source_id = ?'];
		$values = [$search_terms, get_target_source_id()];

		$models = $this->getModels();
		if(isset($model) && isset($models[$model])) {
			array_push($conditions, 'model = ?');
			array_push($values, $models[$model]);
		} else {
			array_push($conditions, 'model IN (?)');
			array_push($values, $models);
		}

		$timePeriods = $this->getTimePeriods();
		if(isset($time_period) && isset($timePeriods[$time_period])) {
			array_push($conditions, $timePeriods[$time_period]);
		}

		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
			array_push($conditions, 'project_id IN (?)');
			array_push($values, $project_ids);
		}

		if(!$include_private) {
			array_push($conditions, 'is_private = ?');
			array_push($values, false);
		}

		$final_conditions = array_merge(array(implode(" AND ", $conditions)), $values);
		return db_prepare_conditions($final_conditions);

	}
	
	public function search($search_terms, $model = null, $project_ids = null, $time_period = null, $include_private = false) {
		$conditions = $this->getSearchConditions($search_terms, $model, $project_ids, $time_period, $include_private);
		return $this->doSearch($conditions);
	}

	public function getPaginate($search_terms, $model = null, $project_ids = null, $time_period = null, $include_private = false, $limit = null, $offset = null) {

		$conditions = $this->getSearchConditions($search_terms, $model, $project_ids, $time_period, $include_private);
	
		$items = $this->doSearch($conditions, $limit, $offset);
		$total_items = $this->countUniqueObjects($conditions);

		return array($items, $total_items);
	
	}
	  
	function doSearch($conditions, $limit = null, $offset = null) {

		$searchable_objects_table = $this->getTableName(true);
		$where = (trim($conditions <> '') ? "WHERE " . $conditions : "");

		$limit_string = '';
		if((integer) $limit > 0) {
			$offset = (integer) $offset > 0 ? (integer) $offset : 0;
			$limit_string = " LIMIT $offset, $limit";
		}

		$query = $this->db->query("SELECT DISTINCT model, object_id FROM $searchable_objects_table $where $limit_string");
		$result = $query->result();
		if(!is_array($result)) return null;

		$objects = array();	
		foreach($result as $row) {						
			$object = get_object_by_model_and_id($row->model, $row->object_id);
			if(isset($object)) $objects[] = $object;
		}

		return count($objects) ? $objects : null;
	
	}

	public function countUniqueObjects($conditions) {
	
		$searchable_objects_table = $this->getTableName(true);
		$where = (trim($conditions <> '') ? "WHERE " . $conditions : "");

		$query = $this->db->query("SELECT DISTINCT model, object_id FROM $searchable_objects_table $where");
		$result = $query->result();
		if(!is_array($result) || !count($result)) return 0;

		$counter = 0;
		foreach($result as $row) {						
			$object = get_object_by_model_and_id($row->model, $row->object_id);
			if(isset($object)) $counter++;
		}

		return $counter;

	}

	public function clearByObject(Application_object $object) {
		return $this->delete(array('model = ? AND object_id = ?', $object->getModelName(), $object->getId()));
	}
	  	
}

/* SearchableObject Object */

class SearchableObject extends Application_object {
	
	function __construct() {
		parent::__construct('searchable_objects');
	}

}