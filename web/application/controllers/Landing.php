<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends Application_controller {
	
	function __construct() {
		parent::__construct();		
		
	}
	
	public function index() {
		$this->setLayout('landing');	
		$this->setTemplate('landing');			
	}

}
