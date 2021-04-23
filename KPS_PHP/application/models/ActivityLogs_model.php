<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ActivityLogs Model */

class ActivityLogs_model extends Application_model {

	protected static $types = array('project', 'invoice', 'projectdiscussion', 'projecttasklist', 'projecttask', 'projectfile', 'projectcomment', 'ticket', 'projecttimelog', 'tickettype');
	protected static $actions = array('add', 'edit', 'delete', 'close', 'open');

    function __construct() {
        parent::__construct('activity_logs', 'ActivityLog');
    }
	
	public function create(Application_object $object, $message, $action = 'add', $is_private = false, $is_hidden = false) {

		if(in_array($object->getTypeName(), self::$types) && 
		in_array($action, self::$actions)) {
			
			$log = new ActivityLog();

			$log->setModel($object->getModelName());
			$log->setObjectId($object->getId());
			
			if($object->isProjectRelated()) $log->setProjectId($object->getProjectId());
			elseif($object instanceof Project) $log->setProjectId($object->getId());
			
			$log->setAction($action);
			
			$object_parent = $object->getParent();
			$object_name = $object_parent ? $object_parent->getName() : $object->getName();
			
			$raw_data = serialize_data(array('title' => html_escape($object_name), 'message' => $message));
			$log->setRawData($raw_data);
			
			$log->setCreatedById(logged_user()->getId());
			$log->setIsPrivate($is_private);
			$log->setIsHidden($is_hidden);
			
			$log->save();

	        $this->setIsPrivateByObject($object, $is_private);
			
		}
		
	}
	
	public function getByObject(Application_object $object) {
		return $this->find(array('conditions' => array('model = ? AND object_id = ?', $object->getModelName())));
	}

	public function clearByObject(Application_object $object) {
      return $this->delete(array('model = ? AND object_id = ?', $object->getModelName(), $object->getId()));
    }

	public function setIsPrivateByObject(Application_object $object, $is_private) {
		
		$this->update(array('is_private' => $is_private), 
		array('model = ? AND object_id = ?', $object->getModelName(), $object->getId()));
		
		// Update child comments ..
		if($object->getModelName() == "ProjectDiscussions" || $object->getModelName() == "ProjectTaskLists") {
		
			$activity_logs_table = $this->getTableName(true);
			$project_comments_table = $this->ProjectComments->getTableName(true);
	
			$this->db->simple_query($sql="UPDATE ".$activity_logs_table." al 
			INNER JOIN ".$project_comments_table." pc on pc.id = al.object_id AND al.model = 'ProjectComments'
			SET al.is_private = ".($is_private ? 1 : 0)." WHERE pc.parent_id = ".$object->getId()." 
			AND pc.parent_type = '".$object->getModelName()."'");
			
			if($object->getModelName() == "ProjectTaskLists") {

				$activity_logs_table = $this->getTableName(true);
				$project_tasks_table = $this->ProjectTasks->getTableName(true);
		
				$this->db->simple_query($sql2="UPDATE ".$activity_logs_table." al 
				INNER JOIN ".$project_tasks_table." pt on pt.id = al.object_id AND al.model = 'ProjectTasks'
				SET al.is_private = ".($is_private ? 1 : 0)." WHERE pt.task_list_id = ".$object->getId());
			
			}
			
		}
		  				
	}
		
	public function getByProject(Project $project, $include_private = false, $include_hidden = false, $limit = null, $offset = null) {

		return $this->find(array('conditions' => array('project_id = ? AND is_private <= ? AND is_hidden <= ?', 
		$project->getId(), $include_private, $include_hidden),
		'order' => 'created_at DESC',
        'limit' => $limit,
        'offset' => $offset,
		));

	}
	
	public function clearByProject(Project $project) {
      return $this->delete(array('project_id = ?', $project->getId()));
    }

	public function getAll($include_private = false, $include_hidden = false, $project_ids = null, $limit = null, $offset = null) {

		
		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
			$conditions = array('is_private <= ? AND is_hidden <= ? AND project_id IN (?)', $include_private, $include_hidden, $project_ids);
		} else {
			$conditions = array('is_private <= ? AND is_hidden <= ?', $include_private, $include_hidden);
		}

		return $this->find(array('conditions' => $conditions,
		'order' => 'created_at DESC',
        'limit' => $limit,
        'offset' => $offset,
		));

	}

	public function getPaginate($include_private = false, $include_hidden = false, $project_ids = null, $limit = null, $offset = null) {

		if(isset($project_ids) && is_array($project_ids) && count($project_ids)) {
			$conditions = array('is_private <= ? AND is_hidden <= ? AND project_id IN (?)', $include_private, $include_hidden, $project_ids);
		} else {
			$conditions = array('is_private <= ? AND is_hidden <= ?', $include_private, $include_hidden);
		}

		$arguments = array('conditions' => $conditions,
		'order' => 'created_at DESC');
		
		return $this->paginate($arguments, $limit, $offset);

	}

}

/* ActivityLog Object */

class ActivityLog extends Application_object {

	function __construct() {
		parent::__construct('activity_logs');
	}
	
	public function getObject() {

		$model = $this->getModel();
		return $this->CI_instance()->$model->findById($this->getObjectId());

	}
	
}