<?php defined('BASEPATH') OR exit('No direct script access allowed');

final class Initial_Data {
    
	const SESSION_SHORTLIFE = 3600;
	const SESSION_LONGLIFE = 604800;
	
    private $company;
    private $logged_user;

	function __construct() {
		
		$this->CI_instance()->load->model('SearchableObjects');
		$this->CI_instance()->load->model('Configurations');

		$this->CI_instance()->load->model('Companies');
		$this->CI_instance()->load->model('Users');

		$this->CI_instance()->load->model('IConfigurations');
		$this->CI_instance()->load->model('IPackages');
		$this->CI_instance()->load->model('TargetSources');

		$this->CI_instance()->load->model('GlobalLabels');		

		$this->CI_instance()->load->model('Projects');		
		$this->CI_instance()->load->model('ActivityLogs');		

		$this->CI_instance()->load->model('ProjectUsers');		
		$this->CI_instance()->load->model('ProjectDiscussions');		
		$this->CI_instance()->load->model('ProjectComments');		
		$this->CI_instance()->load->model('ProjectFiles');		

		$this->CI_instance()->load->model('ProjectTaskLists');		
		$this->CI_instance()->load->model('ProjectTasks');		

		$this->CI_instance()->load->model('Invoices');		
		$this->CI_instance()->load->model('InvoiceItems');		

		$this->CI_instance()->load->model('Estimates');		
		$this->CI_instance()->load->model('EstimateItems');		

		$this->CI_instance()->load->model('TransactionLogs');		
		$this->CI_instance()->load->model('Events');		
		$this->CI_instance()->load->model('EventUsers');		

		$this->CI_instance()->load->model('Tickets');		
		$this->CI_instance()->load->model('TicketTypes');		

		$this->CI_instance()->load->model('ProjectTimelogs');		
		$this->CI_instance()->load->model('Announcements');		

		$this->CI_instance()->load->model('UserNotifications');		

		$this->CI_instance()->load->model('LeadsSources');		
		$this->CI_instance()->load->model('LeadsStatuses');		

		$this->CI_instance()->load->model('LeadForms');		
		$this->CI_instance()->load->model('LeadFormElements');		

		$this->CI_instance()->load->model('Leads');		
		$this->CI_instance()->load->model('LeadFormElementValues');		

		$this->CI_instance()->load->model('InvoiceItemProjectTimelogs');

		$this->init();

	} 
	    
	public function init() {

		if(isset($this) && ($this instanceof Initial_Data)) {
	
			$this->initCompany();
			$this->initLoggedUser();
	
		} else {
			Initial_Data::instance()->init();
		}

	}

	private function CI_instance() {
		
		$CI =& get_instance();
		return $CI;
		
	}
    
    private function initCompany() {

		$company = $this->CI_instance()->Companies->getOwnerCompany();

		if(!($company instanceof Company)) {
//			throw new Exception("Owner company is not defined");
		} else {
			if(!($company->getCreatedBy() instanceof User)) {
				throw new Exception("Owner account is not defined");
			}
		}

		$this->company = $company;

    }
    
    private function initLoggedUser() {

		$user_id  = get_cookie('id');
		$token    = get_cookie('token');
		
		$remember = get_cookie('remember');
		$remember = is_null($remember) ? false : true;
		
		if(empty($user_id) || empty($token)) return false;      
		$user = $this->CI_instance()->Users->findById($user_id);
		

		if(!($user instanceof User)) return false;
		if(!$user->getIsActive() || $user->getIsTrashed()) return false;

		if(!$user->isValidToken($token)) return false;
		
		$session_expires_time = $user->getLastActivity()+(self::SESSION_SHORTLIFE);
		$log_user_in = (time() > $session_expires_time); 


		$this->setLoggedUser($user, $remember, $log_user_in);

		return true;
	  
    }
    
	public function setLoggedUser(User $user, $remember = false, $log_user_in = true) {
		
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
				
		set_cookie('id', $user->getId(), $expiration);
		set_cookie('token', $user->getEncodedToken(), $expiration);
		
		if($remember) set_cookie('remember', 1, $expiration);
		else delete_cookie('remember');
		
		$this->logged_user = $user;
		
	}
    
    public function logUserOut() {
	
		$this->logged_user = null;

		delete_cookie('id');
		delete_cookie('token');
		delete_cookie('remember');
    
	}
    
    public function getCompany() {
		return $this->company;
    }
    
    public function getLoggedUser() {
		return $this->logged_user;
    }
        
	public static function instance() {

		static $instance;

		if(!($instance instanceof Initial_Data)) {
			$instance = new Initial_Data();
		}

		return $instance;

	}
  
}

if (!function_exists('owner_company')) {

	function owner_company() {
		return Initial_Data::instance()->getCompany();
	}
  
}

if (!function_exists('logged_user')) {

	function logged_user() {
		return Initial_Data::instance()->getLoggedUser();
	}

}

if (!function_exists('initial_data_contoller')) {

	function initial_data_contoller(My_Controller $controller, $layout = 'dashboard') {
	
		if(!(logged_user() instanceof User)) {
			redirect('access/login?ref='.base64_encode(current_url()));
		}
	
		$controller->setLayout($layout);

	} 

}
