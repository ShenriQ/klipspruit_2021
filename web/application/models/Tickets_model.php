<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Tickets Model */

class Tickets_model extends Application_model {

    function __construct() {
        parent::__construct('tickets', 'Ticket');
    }

	public function getAll($status = true, $include_trashed = false) {

		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `is_open` = ?', $include_trashed, $status), 
		'order' => '`id` DESC'));
						

	}

	public function countAll($status = true, $include_trashed = false) {
		return $this->count(array('`is_trashed` <= ? AND `is_open` = ?', $include_trashed, $status));
	}

	public function getByUser(User $user, $include_trashed = false, $include_closed = false, $limit = null, $offset = null) {

		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `is_open` >= ? AND `assignee_id` = ?',
		$include_trashed, !$include_closed, $user->getId()),
		'order' => '`id` DESC',
		'limit' => $limit,
        'offset' => $offset
		));
						
	}

	public function getByProjects($project_ids, $status = true, $include_trashed = false) {

		return $this->find(array('conditions' => array('`is_trashed` <= ? AND `project_id` IN (?) AND `is_open` = ?', $include_trashed, $project_ids, $status), 
		'order' => '`id` DESC'));
						
	}

	public function getByAccessKey($access_key) {
		return  $this->find(array('conditions' => array('`access_key` = ?', $access_key), 'one' => true));
	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', true), 'order' => '`id`'));
	}

}

/* Ticket Object */

class Ticket extends Application_object {

	private $assignee;
	private $ticket_type;
	private $is_commentable = true;

	protected $is_searchable = true;
	protected $searchable_fields = array('subject', 'description');

	function __construct() {
		parent::__construct('tickets');
	}

	public function getName() {
		return $this->getSubject();
	}

	public function isCommentable() {
		return $this->is_commentable;
	}

	public function getAssignee() {
		
		if(is_null($this->assignee)) {
			$this->assignee = $this->CI_instance()->Users->findById($this->getAssigneeId());	
		}
		
		return $this->assignee;
		
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

	public function getTicketType() {
		
		if(is_null($this->ticket_type)) {
			$this->ticket_type = $this->CI_instance()->TicketTypes->findById($this->getTypeId());	
		}
		
		return $this->ticket_type;
		
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

	public function delete() {
		
		$this->clearProjectComments();
		parent::delete();
		
	}

	/* Static URLs */

    function getEditURL() {
		return 'tickets/edit/'.$this->getId();
	}

	public function getCloseURL() {
		return 'tickets/close/'.$this->getId();
	}

	public function getCompleteURL() {
		return 'tickets/complete/'.$this->getId();
	}

    function getObjectURL() {
		return 'projects/access/'.$this->getProjectId().'/tickets/'.$this->getId();
	}

}