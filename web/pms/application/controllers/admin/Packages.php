<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Packages extends Admin_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_admin_data_contoller($this);

	}
	
	public function index() {
		
		$packages = $this->IPackages->find();
		tpl_assign('packages', $packages);
		
	}
	
	public function edit($id){

		only_ajax_request_allowed();
		$this->setLayout('modal');

		$package = $this->IPackages->findById($id);
		if(is_null($package)) {
			set_flash_error("The page you requested was not found.", true);
		}

		tpl_assign('package', $package);

		$name = input_post_request('name', $package->getName());
		tpl_assign("name", $name);

		$price_per_month = input_post_request('price_per_month', $package->getPricePerMonth());
		tpl_assign("price_per_month", $price_per_month);

		$max_storage = input_post_request('max_storage', $package->getMaxStorage());
		tpl_assign("max_storage", $max_storage);

		$max_users = input_post_request('max_users', $package->getMaxUsers());
		tpl_assign("max_users", $max_users);

		$max_projects = input_post_request('max_projects', $package->getMaxProjects());
		tpl_assign("max_projects", $max_projects);

		$is_submited = input_post_request('submited') ==  'submited';
	
		if ($is_submited) {
	
			$this->form_validation->set_rules('price_per_month', 'Price (Per Month)', 'required|numeric');
			$this->form_validation->set_rules('max_storage', 'Storage (GB)', 'required|numeric');
			$this->form_validation->set_rules('max_users', 'No.of Users', 'required|numeric');
			$this->form_validation->set_rules('max_projects', 'No.of Projects', 'required|numeric');
		
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {

				try{
					if($id > 1) { // Disabled price for free
						$package->setPricePerMonth($price_per_month);
					}
					$package->setMaxStorage($max_storage);
					$package->setMaxUsers($max_users);
					$package->setMaxProjects($max_projects);
					
					$package->save();
					
					set_flash_success("Package has been saved successfully.");
					
				}catch(Exception $e){
					set_flash_error("An error was encountered.");
				}

				$this->renderText(output_ajax_request(true));
	
			}
		
		}
		
	}

}
