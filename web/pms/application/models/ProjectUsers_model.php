<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ProjectUsers Model */

class ProjectUsers_model extends Application_model {

    function __construct() {
        parent::__construct('project_users', 'ProjectUser');
    }

	public function clearByProject(Project $project) {
		return $this->db->simple_query('DELETE FROM ' . $this->getTableName(true) . ' WHERE `project_id` = '.$project->getId());
	}

	public function clearByUser(User $user) {
		return $this->db->simple_query('DELETE FROM ' . $this->getTableName(true) . ' WHERE `user_id` = '.$user->getId());
	}

	
	public function countByUser(User $user) {			
		return $this->count(array('`user_id` = ?', $user->getId()));
	}

}

/* ProjectUser Object */

class ProjectUser extends Application_object {

	function __construct() {
		parent::__construct('project_users');
	}
	
}