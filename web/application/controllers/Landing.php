<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends Application_controller {
	
	function __construct() {
		parent::__construct();		

		$this->load->model('IWidgets');
	}
	
	public function index() {
		$this->setLayout('landing');	
		$this->setTemplate('landing');	
		
		$widgets = $this->IWidgets->find();
		tpl_assign('widgets', $widgets);
	}

}
