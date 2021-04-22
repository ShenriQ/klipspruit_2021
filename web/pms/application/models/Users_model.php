<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* Users Model */

class Users_model extends Application_model {

    function __construct() {
        parent::__construct('users', 'User');
    }

	public function getByEmail($email) {
	
		return $this->find(array('conditions' => 
		array('`email` = ?', $email), 'one' => true));
	
	}

	public function getByToken($token) {
		
		return $this->find(array('conditions' => 
		array('`token` = ?', $token), 'one' => true));
		
	}

    public function tokenExists($token) {
		return  $this->count(array('`token` = ?', $token)) > 0;
    }

	public function getByWithoutCompany() {
		
		$companies_table = $this->Companies->getTableName(true);
		$users_table = $this->getTableName(true);
		
		$trashed_company_users = (array) $this->find(array('conditions' => array($users_table.'.`is_trashed` = ? AND '.$users_table.'.`is_active` = ?', false, true),
		'joins' => array('table' => $companies_table, 'cond' => $companies_table.'.`id` = '.$users_table.'.`company_id` 
		AND ('.$companies_table.'.`is_trashed` = 1 OR '.$companies_table.'.`is_active` = 0) AND  '.$companies_table.'.`parent_id` <> 0', 'type' => 'INNER')));
		
		$without_company_users = (array) $this->find(array('conditions' => array('`company_id` = ? AND `is_trashed` = ? AND '.$users_table.'.`is_active` = ?', 0, false, true)));
		
		$merge_users = array_merge($trashed_company_users, $without_company_users);
		return count($merge_users) ? $merge_users : null;
		
	}

	public function getByProject(Project $project, $with_conditions = true, $include_owner = true) {
		
		$project_users_table = $this->ProjectUsers->getTableName(true);
		$users_table = $this->getTableName(true);

		$arguments = array('joins' => array('table' => $project_users_table, 'cond' => $project_users_table.'.`user_id` = '.$users_table.'.`id` 
		AND '.$project_users_table.'.`project_id` = '.$project->getId(), 'type' => 'INNER'));
		
		if($with_conditions) {
			$arguments['conditions'] = array($users_table.'.`is_trashed` = ? AND '.$users_table.'.`is_active` = ?', false, true);
		}
		
		$project_users = $this->find($arguments);

		if($include_owner) {
			
			$owner_company = owner_company();
			$project_users[] = $owner_company->getCreatedBy();
		
		}
				
		return $project_users;
				
	}

	public function getByEvent(Event $event, $with_conditions = true) {
		
		$event_users_table = $this->EventUsers->getTableName(true);
		$users_table = $this->getTableName(true);

		$arguments = array('joins' => array('table' => $event_users_table, 'cond' => $event_users_table.'.`user_id` = '.$users_table.'.`id` 
		AND '.$event_users_table.'.`event_id` = '.$event->getId(), 'type' => 'INNER'));
		
		if($with_conditions) {
			$arguments['conditions'] = array($users_table.'.`is_trashed` = ? AND '.$users_table.'.`is_active` = ?', false, true);
		}
		
		return $this->find($arguments);
				
	}

	public function getByClients() {

		$owner_company = owner_company();
		return $this->find(array('conditions' => array('`company_id` <> ? AND `is_trashed` = ? AND `is_active` = ?', $owner_company->getId(), false, true)));

	}

	public function getAllMembers() {

		$owner_company = owner_company();
		return $this->find(array('conditions' => array('`company_id` = ? AND `is_trashed` = ? AND `is_active` = ?', $owner_company->getId(), false, true)));

	}

	public function updateUsersCache($target_source) {

		$all_users_count = $this->allUsersCount();
		$target_source->setUsersCreated($all_users_count);
		$target_source->save();

	}

	public function allUsersCount() {
		return $this->count(array('`target_source_id` = ? AND `is_trashed` = ? AND `is_active` = ?', 
		get_target_source_id(), false, true));
    }

	public function getByTrashed() {
		return $this->find(array('conditions' => array('`is_trashed` = ?', true), 'order' => '`name`'));
	}

	public function getByArchived() {
		return $this->find(array('conditions' => array('`is_trashed` = ? AND `is_active` = ?', false, false), 'order' => '`name`'));
	}
	
    public function getOnlineUsers($active_in = 15) {
      
		if((integer) $active_in < 1) $active_in = 15;

		$datetime = date('Y-m-d H:i:s', time()+(-1 * $active_in * 60));
		return $this->find(array('conditions' => array('`last_activity` > ?', $datetime)));
	
	}

	public function getAll($include_owner = false, $include_admin = false) {
		return $this->find(array('conditions' => array('`id` <> ? AND `is_admin` <= ?', ($include_owner ? 0 : owner_company()->getCreatedById()), $include_admin), 'order' => '`name`'));
	}

	
}

/* User Object */

class User extends Application_object {

    private $is_member = null;
    private $is_owner  = null;

    private $all_projects  = null;
    private $active_projects  = null;
    private $completed_projects  = null;

    private $events  = null;

	private $unassigned_projects = null;
	private $is_project_user = array();

	private $my_started_timer = null;
	private $target_source;
	
	function __construct() {
		parent::__construct('users');
	}

	/* Auth methods */

    public function resetPassword($save = true) {

		$new_password = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);

		$this->setPassword($new_password);
		if($save) $this->save();

		return $new_password;

    }
	
    public function setPassword($value) {

	 	if(trim($value) == '') {
			throw new Exception("The password field is required");
		}
		
		do {

			$salt = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
			$token = sha1($salt . $value);

		} while($this->CI_instance()->Users->tokenExists($token));
		
		$this->setToken($token);
		$this->setSalt($salt);

	}
    
    public function getEncodedToken() {
		return $this->CI_instance()->encryption->encrypt($this->getToken());
    }
    
    public function isValidPassword($password) {
		return sha1($this->getSalt().$password) == $this->getToken();
    }
    
    public function isValidToken($encoded_token) {
		return $this->CI_instance()->encryption->decrypt($encoded_token) == $this->getToken();
    }
		
	/* Avatar methods */

	function getAvatar(){

		$filename = ($this->getAvatarFile() != '') ? $this->getAvatarFile() : get_avatar_default($this->getName());
		return get_page_base_url('public/avatars/'.$filename);
	
	}

	function setLogo($source, $save = false, $max_width = 96, $max_height = 96) {
    
	  if(!is_readable($source)) return false;
      
      do {
		$temp_file = APPPATH . '/cache/' . sha1(uniqid(rand(), true));
      } while(is_file($temp_file));
      
      try {
        
        $image = new SimpleGdImage($source);
        $thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
        $thumb->saveAs($temp_file, IMAGETYPE_PNG);

		$public_filename = add_avatar($temp_file, 'png');
		if($public_filename) {
		
			$this->setAvatarFile($public_filename);
			if($save) {
				$this->save();
			}
		
		}
        
        $result = true;
      
	  } catch(Exception $e) {
	  	  $result = false;
      }
      
      // Cleanup
      if(!$result && $public_filename) {
          delete_avatar($public_filename);
      }
	  
      @unlink($temp_file);
      
      return $result;
	  
    }
    
    function deleteLogo() {
		
		$avatar_file = $this->getAvatarFile();
		
		if($avatar_file != ""){
		
			delete_avatar($avatar_file);
			$this->setAvatarFile('');
		
		}
		
    }
    
    function getLogoPath() {
		return get_avatar_file_path($this->getAvatarFile());
    }
				
	/* Privilege methods */
		
    public function isMemberOf(Company $company) {
		return $this->getCompanyId() == $company->getId();
    }
	
	public function isMember() {
		
		if(is_null($this->is_member)) {
			$this->is_member = $this->isMemberOf(owner_company());
		}
		
		return $this->is_member;
		
	}

	public function isOwner() {
		
		if(is_null($this->is_owner)) {
			$this->is_owner = ($this->isMember() && owner_company()->getCreatedById() == $this->getId());
		}
		
		return $this->is_owner;
		
	}

	public function isAdmin() {
		return ($this->isMember() && $this->getIsAdmin());
	}

	public function isProjectUser(Project $project) {
		
		if(!isset($this->is_project_user[$project->getId()])) {
		
			$project_user = $this->CI_instance()->ProjectUsers->findById(array('project_id' => $project->getId(), 'user_id' => $this->getId()));
			$this->is_project_user[$project->getId()] = is_null($project_user) ? false : true;
		
		}
					
		return $this->is_project_user[$project->getId()];

	}
	
	public function getProjectsCount() {
		return $this->CI_instance()->ProjectUsers->countByUser($this);
	}

	public function getTimelogStats() {
	
		$project_timelogs_table = $this->CI_instance()->ProjectTimelogs->getTableName(true);
		
		$query = $this->CI_instance()->db->query("SELECT SUM(`total_hours`) AS `summary` FROM ".$project_timelogs_table." 
		WHERE `is_approved` = 1 AND `member_id` = " . $this->getId() . " AND `target_source_id` = ".get_target_source_id());
		
		return $query->result();

	}

	public function getActiveProjects() {
	
		if(is_null($this->active_projects)) {
			
			if($this->isOwner()) {

				$arguments = array('conditions' => array('`is_trashed` = ? AND `completed_at` = ?', false, '0000-00-00 00:00:00'));
				$this->active_projects = $this->CI_instance()->Projects->find($arguments);			

			} else {

				$projects_table = $this->CI_instance()->Projects->getTableName(true);
				$this->active_projects = $this->CI_instance()->Projects->getByUser($this, array($projects_table.'.`is_trashed` = ? AND '.$projects_table.'.`completed_at` = ?', false, '0000-00-00 00:00:00'));
			
			}
			
		}
	
		return $this->active_projects;
	
	}

	public function getAllProjects() {
	
		if(is_null($this->all_projects)) {
			
			if($this->isOwner()) {

				$arguments = array('conditions' => array('`is_trashed` = ?', false));
				$this->all_projects = $this->CI_instance()->Projects->find($arguments);			

			} else {

				$projects_table = $this->CI_instance()->Projects->getTableName(true);
				$this->all_projects = $this->CI_instance()->Projects->getByUser($this, array($projects_table.'.`is_trashed` = ?', false));
			
			}
			
		}
	
		return $this->all_projects;
	
	}

	public function getEvents($additional_conditions = null) {
	
		if(is_null($this->events)) {

			$can_manage_calendar = (logged_user()->isOwner() || logged_user()->isAdmin()) ? true : false;
			$this->events = $can_manage_calendar ? $this->CI_instance()->Events->getAll($additional_conditions) 
			: $this->CI_instance()->Events->getByUser($this, $additional_conditions);
		
		}
	
		return $this->events;
	
	}

	public function getCompletedProjects() {
		
		if(is_null($this->completed_projects)) {


			if($this->isOwner()) {

				$arguments = array('conditions' => array('`is_trashed` = ? AND `completed_at` > ?', false, '0000-00-00 00:00:00'));
				$this->completed_projects = $this->CI_instance()->Projects->find($arguments);			

			} else {

				$projects_table = $this->CI_instance()->Projects->getTableName(true);
				$this->completed_projects = $this->CI_instance()->Projects->getByUser($this, array($projects_table.'.`is_trashed` = ? AND '.$projects_table.'.`completed_at` > ?', false, '0000-00-00 00:00:00'));
			
			}
			
		}
	
		return $this->completed_projects;
	
	}

	public function getMyTasks($include_trashed = false, $include_completed = false, $limit = null, $offset = null) {
		return $this->CI_instance()->ProjectTasks->getByUser($this, $include_trashed, $include_completed, $limit, $offset);
	}

	public function getMyStartedTimer() {
		if(is_null($this->my_started_timer)) {
			$this->my_started_timer = $this->CI_instance()->ProjectTimelogs->getStartedTimerByUser($this);
		}
		return $this->my_started_timer;
	}

	public function getTargetSource() {
		if(is_null($this->target_source)) {
			$this->target_source = $this->CI_instance()->TargetSources->findById($this->getTargetSourceId());	
		}		
		return $this->target_source;
	}

	public function getMyLeads($limit = null, $offset = null) {
		return $this->CI_instance()->Leads->getByUser($this, $limit, $offset);
	}

	public function getMyNotifications($include_read = false, $limit = null, $offset = null) {
		return $this->CI_instance()->UserNotifications->getByUser($this, $include_read, $limit, $offset);
	}

	public function getMyTickets($include_trashed = false, $include_completed = false, $limit = null, $offset = null) {
		return $this->CI_instance()->Tickets->getByUser($this, $include_trashed, $include_completed, $limit, $offset);
	}
	
	public function getUnassignedProjects() {
	
		if(is_null($this->unassigned_projects)) {

			if(!$this->isOwner()) {

				$projects_table = $this->CI_instance()->Projects->getTableName(true);
				$project_users_table =  $this->CI_instance()->ProjectUsers->getTableName(true);
		
				$this->unassigned_projects = $this->CI_instance()->Projects->getByUser($this, array($projects_table.'.`is_trashed` = ? AND '.$projects_table.'.`completed_at` = ? 
				AND ('.$project_users_table.'.`project_id` IS NULL AND '.$project_users_table.'.`user_id` IS NULL)', false, '0000-00-00 00:00:00'), 'LEFT');
			
			}
			
		}
	
		return $this->unassigned_projects;
	
	}
	
	public function moveToTrash($is_group_trashed = false) {
		
		$this->setIsTrashed(true);

		$this->setIsGroupTrashed($is_group_trashed);
		$this->save();
			
	}

	public function restoreFromTrash($is_group_trashed = false) {
		
		if($is_group_trashed) {
	
			if($this->getIsGroupTrashed()) {

				$this->setIsTrashed(false);
				$this->setIsGroupTrashed(false);
		
				$this->save();
			
			}
						
		} else {
	
			$this->setIsTrashed(false);
			$this->save();
	
		}
								
	}

	public function moveToArchive() {
		
		if(!$this->getIsTrashed()) {		

			$this->setIsActive(false);
			$this->save();
		
		}
					
	}

	public function restoreFromArchive() {
		
		if(!$this->getIsTrashed()) {		

			$this->setIsActive(true);
			$this->save();
	
		}
								
	}

	public function clearUserProjects() {
		return $this->CI_instance()->ProjectUsers->clearByUser($this);
	}

	public function clearUserNotifications() {
		
		$notifications = $this->getMyNotifications(true);
		if(isset($notifications) && is_array($notifications) && count($notifications)) {
			foreach($notifications as $notification) $notification->delete();	
		}
			
	}


	public function delete() {
		
		$this->clearUserProjects();	
		$this->clearUserNotifications();
		
		$deleted = parent::delete();
		
		$CI =& get_instance();
		$target_source = logged_user()->getTargetSource();
		$CI->Users->updateUsersCache($target_source);
	
		return $deleted;

	}
	
	/* Static URLs */

	public function getForgotPasswordURL() {
		return 'access/forgot_password/'.$this->getToken();
	}

	public function getEditProfileURL() {
		return 'dashboard/edit_profile';
	}

	public function getEditURL() {
		return 'people/edit/'.$this->getId();
	}

	public function getAddToProjectURL() {
		return 'people/add_to_project/'.$this->getId();
	}

	public function getObjectURL() {
		return 'people/view/'.$this->getId();
	}

}