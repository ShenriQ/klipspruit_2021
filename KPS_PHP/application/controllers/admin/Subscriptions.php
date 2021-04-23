<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriptions extends Admin_controller {
	
	function __construct() {
		parent::__construct();
		initial_admin_data_contoller($this);
	}

	public function get_subscriptions_json() {
		
		$data = array();
		$packages = get_packages();
		
		$search = input_get_request('search');
		$search = isset($search['value']) ? $search['value'] : null;

		$draw = input_get_request('draw');
		$start = input_get_request('start', 0);
		$length = input_get_request('length', 10);

		$by = input_get_request('by');
		$by = $by != "" ? $by : "all";

		list($subscriptions, $total_items) = $this->TargetSources->getPaginate($length, $start, $search, $by);
		if(isset($subscriptions) && is_array($subscriptions) && count($subscriptions)) {
		
			foreach($subscriptions as $subscription) {
				
				$actions = '<a href="javascript:;" data-url="'.get_page_base_url($subscription->getEditURL()).'" class="btn btn-xs btn-success" data-toggle="commonmodal">Edit</a>';

				$user = $this->Users->findById($subscription->getCreatedById());
				$user_email = (isset($user)) ? $user->getEmail() : 'none';

				$actions = '<a href="javascript:;" data-url="'.get_page_base_url($subscription->getEditURL()).'" class="btn btn-sm btn-success" data-toggle="commonmodal">Edit</a>
				 <a href="'.get_page_base_url('admin/orders?target='.$user_email).'" class="btn btn-sm btn-primary">Orders</a>';

				$data[] = array(
				'Id' => $subscription->getId(),
				'Subscription' => $packages[$subscription->getSubscriptionId()],
				'Features' => '<p><b>Storage: </b> '.$subscription->getStorageLimit().' GB <em class="custom-backgound-lightyellow-underline">('.format_filesize($subscription->getStorageUsed()).' used)</em><br><b>Projects: </b> '.$subscription->getProjectsLimit().' <em class="custom-backgound-lightyellow-underline">('.$subscription->getProjectsCreated().' created)</em><br><b>Users: </b> '.$subscription->getUsersLimit().' <em class="custom-backgound-lightyellow-underline">('.$subscription->getUsersCreated().' created)</em></p>',
				'User' => '<a href="'.base_url('admin/orders?target='.$user_email).'">'.$user_email.'</a>',
				'Status' => ($subscription->getIsActive() ? '<font color="green">Active</font>' : '<font color="red">Block</font>'),
				'ExpireDate' => ($subscription->getSubscriptionId() > 1 ? format_date($subscription->getExpireDate(), 'm-d-Y').($subscription->getExpireDate() < time() ? '<br><small class="custom-color-red">Out of date</small>' : '') : 'Unlimited'),
				'JoinDate' => format_date($subscription->getCreatedAt()),
				'Actions' => $actions
				);
				
			}
					
		}
		
		$json_data = array(
			"draw"            => intval($draw),   
			"recordsTotal"    => $total_items,  
			"recordsFiltered" => $total_items,
			"data"            => $data
		);

		header('Content-Type: application/json');		
		die(json_encode($json_data));
	
	}
	
	public function index() {

		$by = input_get_request('by');
		$by = $by != "" ? $by : "all";

		tpl_assign('by', $by);

	}

	public function edit($id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('admin/subscriptions/_subscription_form');

		$target_source = $this->TargetSources->findById($id);
		if(is_null($target_source)) set_flash_error("The page you requested was not found.", true);

		tpl_assign('packages_options', get_packages());
		tpl_assign('target_source', $target_source);
		
		$subscription_id = input_post_request('subscription_id', $target_source->getSubscriptionId());
		tpl_assign("subscription_id", $subscription_id);

		$storage_limit = input_post_request('storage_limit', $target_source->getStorageLimit());
		tpl_assign("storage_limit", $storage_limit);

		$projects_limit = input_post_request('projects_limit', $target_source->getProjectsLimit());
		tpl_assign("projects_limit", $projects_limit);

		$users_limit = input_post_request('users_limit', $target_source->getUsersLimit());
		tpl_assign("users_limit", $users_limit);

		$status_id = input_post_request('status_id', (int) $target_source->getIsActive());
		tpl_assign("status_id", $status_id);

		$expire_date_timestamp = $target_source->getExpireDate();
		$formatted_expire_date = $expire_date_timestamp ? format_date($expire_date_timestamp, 'm/d/Y') : null;
		
		$expire_date = input_post_request('expire_date', $formatted_expire_date);
		tpl_assign("expire_date", $expire_date);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('storage_limit', 'Storage Limit (GB)', 'required|numeric');
			$this->form_validation->set_rules('users_limit', 'No.of Users', 'required|numeric');
			$this->form_validation->set_rules('projects_limit', 'No.of Projects', 'required|numeric');

			$this->form_validation->set_rules('expire_date', "Expire Date", 'required|callback_validate_date',  
			array('validate_date' => 'The Expire Date field must contain a valid date dd/mm/yyyy format.'));

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$target_source->setSubscriptionId($subscription_id);
					$target_source->setUsersLimit($users_limit);
					$target_source->setProjectsLimit($projects_limit);
					$target_source->setStorageLimit($storage_limit);
					$target_source->setIsActive($status_id == 1);					
					$target_source->setExpireDate($expire_date);
					$target_source->save();

					set_flash_success("Subscription has been saved successfully.");
															
				}catch(Exception $e){
					set_flash_error("An error was encountered.");
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}

	public function validate_date($date) {
		return $date != '' ? validate_date($date) : true;
	}
	
}
