<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ProjectDiscussions Model */

class ProjectDiscussions_model extends Application_model {

    function __construct() {
        parent::__construct('project_discussions', 'ProjectDiscussion');
    }

	public function getByProject(Project $project, $include_trashed = false, $include_private = false, $limit = null, $offset = null) {

		return $this->find(array('conditions' => array('is_trashed <= ? AND is_private <= ? AND project_id = ?', $include_trashed, $include_private, $project->getId()), 
		'order' => 'is_sticky DESC, created_at DESC',
		'limit' => $limit,
        'offset' => $offset
		));
						
	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('is_trashed = ?', true), 'order' => 'title'));
	}

}

/* ProjectDiscussion Object */

class ProjectDiscussion extends Application_object {
	
	private $is_commentable = true;		
	private $comments;
	private $project;

	protected $is_searchable = true;
	protected $searchable_fields = array('title', 'text');

	function __construct() {
		parent::__construct('project_discussions');
	}
	
	public function getName() {
		return $this->getTitle();
	}

	public function moveToTrash() {
		
		$this->setIsTrashed(true);
		$this->save();
			
	}

	public function restoreFromTrash() {
		
		$this->setIsTrashed(false);
		$this->save();

	}
	
	public function isCommentable() {
		return $this->is_commentable;
	}
	
	public function getProject() {
	
		if(is_null($this->project)) {
			$this->project = parent::getProject();
		}
		
		return $this->project;
	
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
		return 'discussions/edit/'.$this->getId();
	}
	
	public function getObjectURL() {
		return 'projects/access/'.$this->getProjectId().'/discussions/'.$this->getId();
	}
	
}