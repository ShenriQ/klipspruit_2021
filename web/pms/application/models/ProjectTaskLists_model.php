<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ProjectTaskLists Model */

class ProjectTaskLists_model extends Application_model {

    function __construct() {
        parent::__construct('project_task_lists', 'ProjectTaskList');
    }

	public function getByProject(Project $project, $include_private = false, $include_trashed = false, $limit = null, $offset = null) {
		
		return $this->find(array('conditions' => array('`is_private` <= ? AND `is_trashed` <= ? AND `project_id` = ?', $include_private, $include_trashed, $project->getId()), 
		'order' => '`sort_order` ASC, `id` DESC',
		'limit' => $limit,
        'offset' => $offset
		));
						
	}

	public function getDatedByProject(Project $project, $include_private = false, $include_trashed = false, $limit = null, $offset = null) {
		
		return $this->find(array('conditions' => array('`is_private` <= ? AND `is_trashed` <= ? AND `project_id` = ? AND (`start_date` <> \'0000-00-00\' AND `due_date` <> \'0000-00-00\')', $include_private, $include_trashed, $project->getId()), 
		'order' => '`sort_order` ASC, `id` DESC',
		'limit' => $limit,
        'offset' => $offset
		));
						
	}
	
	public function getByTrashed() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', true), 'order' => '`name`'));
	}

}

/* ProjectTaskList Object */

class ProjectTaskList extends Application_object {
	
	private $tasks;
	private $project;

	protected $is_searchable = true;
	protected $searchable_fields = array('name', 'description');
	
	function __construct() {
		parent::__construct('project_task_lists');
	}
	
	public function moveToTrash() {
		
		$this->setIsTrashed(true);
		$this->save();
			
	}

	public function restoreFromTrash() {
		
		$this->setIsTrashed(false);
		$this->save();

	}
	
	public function getProject() {
	
		if(is_null($this->project)) {
			$this->project = parent::getProject();
		}
		
		return $this->project;
	
	}

	public function getTasks($include_completed = false, $include_trashed = false) {
		return $this->CI_instance()->ProjectTasks->getByProjectTaskList($this, $include_completed, $include_trashed);
	}

	public function getDatedTasks($include_completed = false, $include_trashed = false) {
		return $this->CI_instance()->ProjectTasks->getDatedByProjectTaskList($this, $include_completed, $include_trashed);
	}

	public function getCompletedTasks($include_trashed = false) {
		return $this->CI_instance()->ProjectTasks->getCompletedByProjectTaskList($this, $include_trashed);
	}

	public function getTasksCount($include_completed = false, $include_trashed = false) {
		return $this->CI_instance()->ProjectTasks->countByProjectTaskList($this, $include_completed, $include_trashed);
	}

	public function clearProjectTasks() {
		
		$tasks = $this->getTasks(true, true);
		if(isset($tasks) && is_array($tasks) && count($tasks)) {
			foreach($tasks as $task) $task->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearProjectTasks();
		parent::delete();
		
	}
	
	/* Static URLs */

	public function getCreateTaskURL() {
		return 'tasks/create_task/'.$this->getId();
	}

	public function getEditURL() {
		return 'tasks/edit_task_list/'.$this->getId();
	}
	
	public function getObjectURL() {
		return 'projects/access/'.$this->getProjectId().'/task_lists/'.$this->getId();
	}
	
}