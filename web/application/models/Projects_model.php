<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Projects Model */

class Projects_model extends Application_model {

    function __construct() {
        parent::__construct('projects', 'Project');
    }

	public function getByUser(User $user, $additional_conditions = null, $type = 'INNER') {

		$project_users_table =  $this->ProjectUsers->getTableName(true);
		$projects_table = $this->getTableName(true);

		$arguments = array('joins' => array('table' => $project_users_table, 'cond' => $project_users_table.'.`project_id` = '.$projects_table.'.`id` 
		AND '.$project_users_table.'.`user_id` = '.$user->getId(), 'type' => $type));
		
		if(!is_null($additional_conditions) && is_array($additional_conditions)) {
			$arguments['conditions'] = $additional_conditions;
		}
		
		return $this->find($arguments);
		
	}

	public function updateProjectsCache($target_source) {

		$all_projects_count = $this->allProjectsCount();
		$target_source->setProjectsCreated($all_projects_count);
		$target_source->save();

	}

	public function allProjectsCount() { 
		return $this->count(array('`target_source_id` = ? AND `is_trashed` = ? AND `completed_at` = ?', 
		get_target_source_id(), false, '0000-00-00 00:00:00'));
    }

	public function getAll() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', false)));
	}

	public function getOverdues($project_ids) {
		return $this->find(array('conditions' => array('`is_trashed` = ? AND `completed_at` = ? AND `id` IN (?) AND `due_date` < NOW()', false, '0000-00-00 00:00:00', $project_ids), 'order' => '`id`'));
	}
		
}

/* Project Object */

class Project extends Application_object {

	private $is_commentable = true;

	protected $is_searchable = true;
	protected $searchable_fields = array('name', 'description');

	function __construct() {
		parent::__construct('projects');
	}
	
	public function getCreatedForCompany() {
		
		$company = $this->getCompany();
		return $company ? $company : owner_company();
		
	}
	
	public function getLabel() {
		return $this->CI_instance()->GlobalLabels->findById($this->getLabelId());
	}

	public function isCommentable() {
		return $this->is_commentable;
	}

	public function getProject() {
		return $this;
	}
	
	public function isCompleted() {
		return (boolean) $this->getCompletedAt();
	}

	public function getUsers($with_conditions = true, $include_owner = true) {
		return $this->CI_instance()->Users->getByProject($this, $with_conditions, $include_owner);
	}

	public function getClients() {
		return $this->CI_instance()->Users->getClientsByProject($this);
	}

	public function getTasks($include_completed = false, $include_trashed = false) {
		return $this->CI_instance()->ProjectTasks->getByProject($this, $include_completed, $include_trashed);
	}

	public function getDatedTasks($include_completed = false, $include_trashed = false) {
		return $this->CI_instance()->ProjectTasks->getDatedByProject($this, $include_completed, $include_trashed);
	}

	public function getTasksCount($include_completed = false, $include_trashed = false) {
		return $this->CI_instance()->ProjectTasks->countByProject([$this->getId()], $include_completed, $include_trashed);
	}

	public function getInvoices($include_trashed = false) {
		return $this->CI_instance()->Invoices->getByProject($this, $include_trashed);
	}

	public function clearInvoices() {

		$invoices = $this->getInvoices(true);
		if(isset($invoices) && is_array($invoices) && count($invoices)) {
			foreach($invoices as $invoice) $invoice->delete();	
		}

	}

	public function getTimesheet($include_trashed = false) {
		return $this->CI_instance()->ProjectTimelogs->getByProjects(array($this->getId()), $include_trashed);
	}

	public function clearTimesheet() {

		$timelogs = $this->getTimesheet(true);
		if(isset($timelogs) && is_array($timelogs) && count($timelogs)) {
			foreach($timelogs as $timelog) $timelog->delete();	
		}

	}

	public function clearProjectUsers() {
		return $this->CI_instance()->ProjectUsers->clearByProject($this);
	}

	public function getDiscussions($include_trashed = false, $include_private = false, $limit = null, $offset = null) {
		return $this->CI_instance()->ProjectDiscussions->getByProject($this, $include_trashed, $include_private, $limit, $offset);
	}

	public function clearProjectDiscussions() {

		$discussions = $this->getDiscussions(true, true);
		if(isset($discussions) && is_array($discussions) && count($discussions)) {
			foreach($discussions as $discussion) $discussion->delete();	
		}

	}

	public function getFiles($include_trashed = false, $include_private = false, $limit = null, $offset = null) {
		return $this->CI_instance()->ProjectFiles->getByProject($this, $include_trashed, $include_private, $limit, $offset);
	}

	public function clearProjectFiles() {

		$files = $this->getFiles(true);
		if(isset($files) && is_array($files) && count($files)) {
			foreach($files as $file) $file->delete();	
		}
	
	}

	public function getTaskLists($include_private = false, $include_trashed = false, $limit = null, $offset = null) {
		return $this->CI_instance()->ProjectTaskLists->getByProject($this, $include_private, $include_trashed, $limit, $offset);
	}

	public function getDatedTaskLists($include_private = false, $include_trashed = false, $limit = null, $offset = null) {
		return $this->CI_instance()->ProjectTaskLists->getDatedByProject($this, $include_private, $include_trashed, $limit, $offset);
	}

	public function clearProjectTasks() {

		$task_lists = $this->getTaskLists(true, true);
		if(isset($task_lists) && is_array($task_lists) && count($task_lists)) {
			foreach($task_lists as $task_list) $task_list->delete();	
		}

	}

	public function getComments($include_trashed = false, $created_by_id = null) {
		return $this->CI_instance()->ProjectComments->getByParent($this, $include_trashed, $created_by_id);
	}

	public function clearProjectComments() {
		
		$comments = $this->getComments(true);
		if(isset($comments) && is_array($comments) && count($comments)) {
			foreach($comments as $comment) $comment->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearProjectUsers();
		$this->clearProjectDiscussions();
		$this->clearProjectTasks();
		$this->clearProjectComments();
		$this->clearProjectFiles();
		$this->clearInvoices();
		$this->clearTimesheet();
		
		parent::delete();
		
	}

	/* Static URLs */

	public function getEditURL() {
		return 'projects/edit/'.$this->getId();
	}

	public function getManagePeopleURL() {
		return 'projects/manage_people/'.$this->getId();
	}

	public function getCompleteURL() {
		return 'projects/complete/'.$this->getId();
	}

	public function getReopenURL() {
		return 'projects/reopen/'.$this->getId();
	}
	
	public function getCreateDiscussionURL() {
		return 'discussions/create/'.$this->getId();
	}

	public function getCreateTaskListURL() {
		return 'tasks/create_task_list/'.$this->getId();
	}

	public function getUploadFilesURL() {
		return 'files/upload/'.$this->getId();
	}

	public function getObjectURL($tab = 'overview') {
		return 'projects/access/'.$this->getId().'/'.$tab;
	}

}