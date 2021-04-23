<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leads extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
	}
	
	public function index() {

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) redirect('dashboard');
		tpl_assign('leads', $this->Leads->getAll());

	}

	public function edit($id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('forms/_lead_form');

		$lead = $this->Leads->findById($id);
		
		if(!((isset($lead) && logged_user()->getId() == $lead->getAssignedId())
		|| logged_user()->isOwner() || logged_user()->isAdmin()) ) {
			die();
		}

		$form = $lead->getForm();

		tpl_assign('form', $form);
		tpl_assign('lead', $lead);

		$status_id = input_post_request('status_id', $lead->getStatusId());
		tpl_assign("status_id", $status_id);

		$source_id = input_post_request('source_id', $lead->getSourceId());
		tpl_assign("source_id", $source_id);

		$assigned_id = input_post_request('assigned_id', $lead->getAssignedId());
		tpl_assign("assigned_id", $assigned_id);

		$projects = logged_user()->getAllProjects();
		tpl_assign('projects', $projects);

		$project_id = input_post_request('project_id', $lead->getProjectId());
		tpl_assign("project_id", $project_id);

		$client_id = input_post_request('client_id', $lead->getClientId());
		tpl_assign("client_id", $client_id);

		$name = input_post_request('name', $lead->getName());
		tpl_assign("name", $name);

		$email = input_post_request('email', $lead->getEmail());
		tpl_assign("email", $email);

		$address = input_post_request('address', $lead->getAddress());
		tpl_assign("address", $address);

		$city = input_post_request('city', $lead->getCity());
		tpl_assign("city", $city);

		$state = input_post_request('state', $lead->getState());
		tpl_assign("state", $state);

		$postcode = input_post_request('postcode', $lead->getPostcode());
		tpl_assign("postcode", $postcode);

		$country = input_post_request('country', $lead->getCountry());
		tpl_assign("country", $country);

		$phone_number = input_post_request('phone_number', $lead->getPhoneNumber());
		tpl_assign("phone_number", $phone_number);

		$form_elements = $form->getElements();
		tpl_assign("form_elements", $form_elements);

		$is_submited = input_post_request('submited') ==  'submited';
		$is_custom_error_found = false;
		
		if ($is_submited) {

		 if(isset($form_elements) && is_array($form_elements) && count($form_elements)) {
		 
			 foreach ($form_elements as $form_element) {
		
				if($form_element->getFieldCategory() == 3) { // Multiple options ..
					
					$field_data = array();
					$field_values = explode(",", $form_element->getFieldData());
					
					if(isset($field_values) && is_array($field_values) && count($field_values)) {

						$option_counter_id = 0; 
						foreach($field_values as $field_value) {
							$field_part_value = input_post_request('fd_id_'.$form_element->getid().'_'.$option_counter_id);								
							if(isset($field_part_value) && $field_part_value == 1) $field_data[] = $field_value;
							$option_counter_id++;
						}
					
					}
					
					if(count($field_data)) {
						$fd['element_id_'.$form_element->getid()] = implode(",", $field_data);
					} else {
						$fd['element_id_'.$form_element->getid()] = "";
						if($form_element->getIsRequired()) $is_custom_error_found = true;
					}
																
				} elseif($form_element->getFieldCategory() == 4 || $form_element->getFieldCategory() == 5) {
					
					$field_data = "";
					$field_values = explode(",", $form_element->getFieldData());

					if(isset($field_values) && is_array($field_values) && count($field_values)) {
						
						$field_part_value = input_post_request('fd_id_'.$form_element->getid());
						if(isset($field_values[$field_part_value])) { 
							$field_data = $field_values[$field_part_value];
						}
					
					}


					if($field_data != "") {
						$fd['element_id_'.$form_element->getid()] = $field_data;
					} else {
						$fd['element_id_'.$form_element->getid()] = "";
						if($form_element->getIsRequired()) $is_custom_error_found = true;
					}
										
				} else {					
				    
					$fd['element_id_'.$form_element->getid()] = input_post_request('fd_id_'.$form_element->getid());

				    if($form_element->getIsRequired() && !($fd['element_id_'.$form_element->getid()] != "")) {
						$is_custom_error_found = true;
				    }

				}
				
			 }

		  }

		  $this->form_validation->set_rules('name', lang('c_474'), 'trim|required|max_length[30]');
		  $this->form_validation->set_rules('email', lang('c_475'), 'trim|valid_email|required|max_length[100]'.($lead->getEmail() != $email ? '|is_unique['.$this->Leads->getTableName().'.email]' : ''),
		  array('is_unique' => lang('c_214')));

		  if ($this->form_validation->run() == FALSE) {
			$this->renderText(output_ajax_request(false, validation_errors()));
		  } else {
			
			if($is_custom_error_found) {
				$this->renderText(output_ajax_request(false, lang('c_481')));
			} else {

				try{
					
					$this->db->trans_begin();
					
					// Save lead

					$lead->setName($name);
					$lead->setEmail($email);
					$lead->setAddress($address);
					$lead->setCity($city);
					$lead->setState($state);
					$lead->setPostcode($postcode);
					$lead->setCountry($country);
					$lead->setPhoneNumber($phone_number);

					if(logged_user()->isOwner() || logged_user()->isAdmin()) {
						$lead->setAssignedId($assigned_id);
					}
					
					$lead->setProjectId($project_id);
					$lead->setClientId($client_id);
					$lead->setAssignedId($assigned_id);
					$lead->setStatusId($status_id);
					$lead->setSourceId($source_id);
					$lead->setFormId($form->getId());
					$lead->setIpAddress($_SERVER['REMOTE_ADDR']);
					
					$lead->save();
					
					// Save additional info
					if(isset($fd) && is_array($fd) && count($fd)) {
						
						$lead->clearLeadElements();
						
						foreach($fd as $key => $fd_value) {
							
							$element_value = new LeadFormElementValue();

							$element_id = str_replace('element_id_', '', $key);
							$element_value->setElementId($element_id);

							$element_value->setLeadId($lead->getId());
							$element_value->setFormId($form->getId());
							$element_value->setElementValue($fd_value);
							
							$element_value->save();
							
						}

					}
					
					$this->db->trans_commit();

					set_flash_success(sprintf(lang('c_243'), lang('c_482')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));
				
			}
				  
		  }
		  		  	
		}
	
	}
	
	public function editnotes($id) {

		only_ajax_request_allowed();

		$lead = $this->Leads->findById($id);
		
		if(!((isset($lead) && logged_user()->getId() == $lead->getAssignedId())
		|| logged_user()->isOwner() || logged_user()->isAdmin()) ) {
			die();
		}

		$notes = input_post_request('notes');
		
		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			try{
				
				$lead->setNotes($notes);
				$lead->save();

				set_flash_success(sprintf(lang('c_57'), lang('c_284')));
				
			}catch(Exception $e){
				set_flash_error(lang('e_1'));
			}

			$this->renderText(output_ajax_request(true));
						
		}
		
	}
	
	public function view($id) {
		
		$lead = $this->Leads->findById($id);
		
		if(!((isset($lead) && logged_user()->getId() == $lead->getAssignedId())
		|| logged_user()->isOwner() || logged_user()->isAdmin()) ) {
			set_flash_error(lang('e_3'));
			redirect('leads');
		}
				
		tpl_assign('lead', $lead);
	
	}

	public function createclient($id) {
		
		$lead = $this->Leads->findById($id);
		
		if(!((isset($lead) && logged_user()->getId() == $lead->getAssignedId())
		|| logged_user()->isOwner() || logged_user()->isAdmin()) ) {
			set_flash_error(lang('e_3'));
		} else {

			$client = $lead->getClient();
			if(isset($client)) {
				set_flash_error(lang('c_512'));
			} else {
				
				try{
	
					$user = new User();
				
					$user->setName($lead->getName());
					$user->setCompanyId(0);
					$user->setEmail($lead->getEmail());
			
					$password = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
					$user->setPassword($password);
					
					$address = trim($lead->getAddress().' '.$lead->getCity().' '.$lead->getState().
					' '.$lead->getPostcode().' '.$lead->getCountry());
					
					$user->setAddress($address);
					$user->setPhoneNumber($lead->getPhoneNumber());
				
					$user->setIsActive(true);
					$user->setCreatedById(logged_user()->getId());

					$user->save();
					
					$lead->setClientId($user->getId());
					$lead->save();
					
					set_flash_success(sprintf(lang('c_54'), 'Client'));
					
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

			}
		
		}
		
		redirect('leads');
	
	}

	public function delete($id) {

		$lead = $this->Leads->findById($id);
		
		if(!(logged_user()->isOwner() || logged_user()->isAdmin()) ) {

			set_flash_error(lang('e_3'));
			redirect('dashboard');

		} else {

			try {
			
				$lead->delete();
				set_flash_success(sprintf(lang('c_56'), lang('c_482')));

			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}

			redirect('leads');
		
		}
							
	}	
							
}
