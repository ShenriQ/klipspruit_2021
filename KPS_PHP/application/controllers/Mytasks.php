<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mytasks extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isMember()) redirect('dashboard');
				
	}
	
	public function index() {
	
		$my_tasks = logged_user()->getMyTasks();
		tpl_assign('my_tasks', $my_tasks);
	
	}
	
}
