<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ProjectTasks Model */

class ProjectTasks_model extends Application_model {

    function __construct() {
        parent::__construct('project_tasks', 'ProjectTask');
    }

	public function getByUser(User $user, $include_trashed = false, $include_completed = false, $limit = null, $offset = null) {

		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `completed_at` '.($include_completed ? '>=' : '=').' ? AND `assignee_id` = ?',
		$include_trashed, '0000-00-00 00:00:00', $user->getId()),
		'order' => '`sort_order` ASC, `id` DESC',
		'limit' => $limit,
        'offset' => $offset
		));
						
	}

	public function getByProjectTaskList(ProjectTaskList $project_task_list, $include_completed = false, $include_trashed = false) {
	
		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `task_list_id` = ? AND `completed_at` '.($include_completed ? '>=' : '=').' ?', 
		$include_trashed, $project_task_list->getId(), '0000-00-00 00:00:00'), 'order' => '`sort_order` ASC, `id` DESC'));
	
	}

	public function getDatedByProjectTaskList(ProjectTaskList $project_task_list, $include_completed = false, $include_trashed = false) {
	
		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `task_list_id` = ? AND `completed_at` '.($include_completed ? '>=' : '=').' ?  AND (`start_date` <> \'0000-00-00\' AND `due_date` <> \'0000-00-00\')', 
		$include_trashed, $project_task_list->getId(), '0000-00-00 00:00:00'), 'order' => '`sort_order` ASC, `id` DESC'));
	
	}

	public function getByProject(Project $project, $include_completed = false, $include_trashed = false) {

		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `project_id` = ? AND `completed_at` '.($include_completed ? '>=' : '=').' ?', 
		$include_trashed, $project->getId(), '0000-00-00 00:00:00'), 'order' => '`sort_order` ASC, `id` DESC'));
						
	}
	
	public function getCompletedByProjectTaskList(ProjectTaskList $project_task_list, $include_trashed = false) {
	
		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `task_list_id` = ? AND `completed_at` > ?', 
		$include_trashed, $project_task_list->getId(), '0000-00-00 00:00:00'), 'order' => '`sort_order` ASC, `id` DESC'));
	
	}

	public function countByProjectTaskList(ProjectTaskList $project_task_list, $include_completed = false, $include_trashed = false) {
			
		return $this->count(array('`is_trashed` <= ? AND `task_list_id` = ? AND `completed_at` '.($include_completed ? '>=' : '=').' ?', 
		$include_trashed, $project_task_list->getId(), '0000-00-00 00:00:00'));

	}

	public function countByProject($project_ids, $include_completed = false, $include_trashed = false) {
			
		return $this->count(array('`is_trashed` <= ? AND `project_id` IN (?) AND `completed_at` '.($include_completed ? '>=' : '=').' ?', 
		$include_trashed, $project_ids, '0000-00-00 00:00:00'));

	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', true), 'order' => '`sort_order` ASC, `id` DESC'));
	}

	public function getOverdues($project_ids, $assignee_ids) {
		return $this->find(array('conditions' => array('`is_trashed` = ? AND `completed_at` = ? AND `project_id` IN (?) AND `assignee_id` IN (?) AND (`due_date` <> \'0000-00-00\' AND `due_date` < NOW())', false, '0000-00-00 00:00:00', $project_ids, $assignee_ids), 'order' => '`id`'));
	}

}

/* ProjectTask Object */

class ProjectTask extends Application_object {

	private $is_commentable = true;
	private $assigned_to;
	private $task_list;

	protected $is_searchable = true;
	protected $searchable_fields = array('description');

	function __construct() {
		parent::__construct('project_tasks');
	}
	
	public function getName() {
		return shorter($this->getDescription(), 65);
	}

	public function getAssignedTo() {
		
		if(is_null($this->assigned_to)) {
			$this->assigned_to = $this->CI_instance()->Users->findById($this->getAssigneeId());	
		}
		
		return $this->assigned_to;
		
	}

	public function isCommentable() {
		return $this->is_commentable;
	}
	
	public function getTaskList() {
		
		if(is_null($this->task_list)) {
			$this->task_list = $this->CI_instance()->ProjectTaskLists->findById($this->getTaskListId());	
		}
		
		return $this->task_list;
		
	}

	public function getLabel() {
		return $this->CI_instance()->GlobalLabels->findById($this->getLabelId());
	}
		
	public function moveToTrash() {
		
		$this->setIsTrashed(true);
		$this->save();
			
	}

	public function restoreFromTrash() {
		
		$this->setIsTrashed(false);
		$this->save();

	}


	public function getComments($include_trashed = false) {
		return $this->CI_instance()->ProjectComments->getByParent($this, $include_trashed);
	}

	public function getCommentsCount($include_trashed = false) {
		return $this->CI_instance()->ProjectComments->countByParent($this, $include_trashed);
	}

	public function clearProjectComments() {
		
		$comments = $this->getComments(true);
		if(isset($comments) && is_array($comments) && count($comments)) {
			foreach($comments as $comment) $comment->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearProjectComments();
		parent::delete();
		
	}

	/* Static URLs */

	public function getEditURL() {
		return 'tasks/edit_task/'.$this->getId();
	}

	public function getCompleteURL() {
		return 'tasks/complete_task/'.$this->getId();
	}

	public function getReopenURL() {
		return 'tasks/reopen_task/'.$this->getId();
	}

	public function getObjectURL() {
		return 'projects/access/'.$this->getProjectId().'/tasks/'.$this->getId();
	}
	
}