<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mywork extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isMember()) redirect('dashboard');
				
	}
	
	public function index() {
	
		$my_tasks = logged_user()->getMyTasks();
		tpl_assign('my_tasks', $my_tasks);
	
		$my_tickets = logged_user()->getMyTickets();
		tpl_assign('my_tickets', $my_tickets);

		$my_leads = logged_user()->getMyLeads();
		tpl_assign('my_leads', $my_leads);
	
	}
	
}
