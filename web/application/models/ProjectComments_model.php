<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ProjectComments Model */

class ProjectComments_model extends Application_model {

    function __construct() {
        parent::__construct('project_comments', 'ProjectComment');
    }

	public function getByParent(Application_object $parent_object, $include_trashed = false, $created_by_id = null) {
	
		$arguments = array('conditions' => array('`is_trashed` <= ? AND `parent_type` = ? AND `parent_id` = ?'.
		(isset($created_by_id) && $created_by_id > 0 ? ' AND `created_by_id` = '.$created_by_id : ''), 
		$include_trashed, $parent_object->getModelName(), $parent_object->getId()), 'order' => '`created_at` ASC');
		
		return $this->find($arguments);
	
	}

	public function countByParent(Application_object $parent_object, $include_trashed = false) {
			
		return $this->count(array('`is_trashed` <= ? AND `parent_type` = ? AND `parent_id` = ?', 
		$include_trashed, $parent_object->getModelName(), $parent_object->getId()));

	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', true), 'order' => '`created_at` DESC'));
	}

}

/* ProjectComment Object */

class ProjectComment extends Application_object {

	protected $is_searchable = true;
	protected $searchable_fields = array('text');

	function __construct() {
		parent::__construct('project_comments');
	}

	public function saveFile($attach_file, $can_download){
		return $this->CI_instance()->ProjectFiles->saveFile($attach_file, $can_download, $this);
	}
	
	public function getName() {
		return shorter($this->getText(), 65);
	}
	
	public function getFiles($include_private = false, $include_trashed = false){
		return $this->CI_instance()->ProjectFiles->getByParent($this, $include_private, $include_trashed);
	}

	public function moveToTrash() {
		
		$this->setIsTrashed(true);
		$this->save();
			
	}

	public function restoreFromTrash() {
		
		$this->setIsTrashed(false);
		$this->save();

	}

	public function clearProjectFiles() {
		
		$files = $this->getFiles(true, true);
		if(isset($files) && is_array($files) && count($files)) {
			foreach($files as $file) $file->delete();	
		}
			
	}

	public function delete() {
		
		$this->clearProjectFiles();
		parent::delete();
		
	}
	
	public function getIsPrivate() {
		return $this->getParent()->getIsPrivate();
	}
	
	/* Static URLs */

	public function getObjectURL() {
	
		return 'projects/access/'.$this->getProjectId().'/'.str_replace("project_", "", 
		underscore_string($this->getParentType())).'/'.$this->getParentId().'#comment_'.$this->getId();
	
	}
	
}