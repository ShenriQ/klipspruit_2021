<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sysadmin extends Admin_controller {
	
	function __construct() {
		parent::__construct();
	}

	public function index() {
		if(logged_admin_user() instanceof IUser) {
			redirect('admin/dashboard');
		} 
		else {
			redirect('admin/access/login');
		}
	}
}