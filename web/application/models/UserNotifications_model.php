<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* UserNotifications Model */

class UserNotifications_model extends Application_model {

    function __construct() {
        parent::__construct('user_notifications', 'UserNotification');
    }

	public function create(User $user, $subject, $message) {

		$user_notification = new UserNotification();

		$user_notification->setSubject($subject);
		$user_notification->setMessage($message);
		$user_notification->setCreatedById($user->getId());

		$user_notification->save();
		
	}

	public function getByUser(User $user, $include_read = false, $limit = null, $offset = null) {
		return $this->find(array('conditions' => array('`is_read` <= ? AND `created_by_id` = ?',
		$include_read, $user->getId()),
		'order' => '`id` DESC', 'limit' => $limit, 
		 'offset' => $offset
		));

	}

}

/* UserNotification Object */

class UserNotification extends Application_object {
	
	function __construct() {
		parent::__construct('user_notifications');
	}

	
	/* Static URLs */

	public function getObjectURL() {
		return 'notifications/view/'.$this->getId();
	}

	public function getRemoveURL() {
		return 'notifications/remove/'.$this->getId();
	}
	
}