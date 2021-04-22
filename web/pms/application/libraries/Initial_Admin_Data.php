<?php defined('BASEPATH') OR exit('No direct script access allowed');

final class Initial_Admin_Data {
    
	const SESSION_SHORTLIFE = 3600;
	const SESSION_LONGLIFE = 604800;
	
    private $logged_admin_user;

	function __construct() {

		$this->CI_instance()->load->model('Users');
		$this->CI_instance()->load->model('IUsers');
		$this->CI_instance()->load->model('IOrders');
		$this->CI_instance()->load->model('IPackages');
		$this->CI_instance()->load->model('TargetSources');
		$this->CI_instance()->load->model('IConfigurations');
		
		$this->init();

	} 
	    
	public function init() {

		$this->CI_instance()->IOrders->setSkipTargetSource(true);
		$this->CI_instance()->Users->setSkipTargetSource(true);

		if(isset($this) && ($this instanceof Initial_Admin_Data)) {
			$this->initLoggedUser();
		} else {
			Initial_Admin_Data::instance()->init();
		}

	}

	private function CI_instance() {
		
		$CI =& get_instance();
		return $CI;
		
	}
        
    private function initLoggedUser() {

		$user_id  = get_cookie('i_id');
		$token    = get_cookie('i_token');
		
		$remember = get_cookie('i_remember');
		$remember = is_null($remember) ? false : true;
		
		if(empty($user_id) || empty($token)) return false;      
		$user = $this->CI_instance()->IUsers->findById($user_id);
		
		if(!($user instanceof IUser)) return false;
		if(!$user->getIsActive() || $user->getIsTrashed()) return false;

		if(!$user->isValidToken($token)) return false;
		
		$session_expires_time = $user->getLastActivity()+(self::SESSION_SHORTLIFE);
		$log_user_in = (time() > $session_expires_time); 
		$this->setLoggedUser($user, $remember, $log_user_in);

		return true;
	  
    }
    
	public function setLoggedUser(IUser $user, $remember = false, $log_user_in = true) {
		
		$now_time = time();
		if($log_user_in) {

			$user->setLastLogin($now_time);
			if(is_null($user->getLastActivity())) {
				$user->setLastVisit($now_time);
			} else {
				$user->setLastVisit($user->getLastActivity());
			}
		
		}
		
		$user->setLastActivity($now_time);
		$user->save(false);

		$expiration = $remember ? self::SESSION_LONGLIFE : self::SESSION_SHORTLIFE;
				
		set_cookie('i_id', $user->getId(), $expiration);
		set_cookie('i_token', $user->getEncodedToken(), $expiration);
		
		if($remember) set_cookie('i_remember', 1, $expiration);
		else delete_cookie('i_remember');
		
		$this->logged_admin_user = $user;
		
	}
    
    public function logUserOut() {
	
		$this->logged_admin_user = null;

		delete_cookie('i_id');
		delete_cookie('i_token');
		delete_cookie('i_remember');
    
	}
    
    public function getLoggedUser() {
		return $this->logged_admin_user;
    }
        
	public static function instance() {

		static $instance;

		if(!($instance instanceof Initial_Admin_Data)) {
			$instance = new Initial_Admin_Data();
		}

		return $instance;

	}
  
}

if (!function_exists('logged_admin_user')) {

	function logged_admin_user() {
		return Initial_Admin_Data::instance()->getLoggedUser();
	}

}

if (!function_exists('initial_admin_data_contoller')) {

	function initial_admin_data_contoller(My_Controller $controller, $layout = 'admin_dashboard') {
	
		if(!(logged_admin_user() instanceof IUser)) {
			redirect('admin/access/login?ref='.base64_encode(current_url()));
		}
	
		$controller->setLayout($layout);

	} 

}