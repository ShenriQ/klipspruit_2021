<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forms extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!(logged_user()->isOwner() || logged_user()->isAdmin())) redirect('dashboard');
		
	}
	
	public function index() {
		tpl_assign('forms', $this->LeadForms->getAll());
	}
	

	public function view($access_key) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('forms/_lead_form');

		$form = $this->LeadForms->getByAccessKey($access_key);
		if(is_null($form)) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('form', $form);

		$status_id = input_post_request('status_id', $form->getDefaultStatusId());
		tpl_assign("status_id", $status_id);

		$source_id = input_post_request('source_id', $form->getDefaultSourceId());
		tpl_assign("source_id", $source_id);

		$assigned_id = input_post_request('assigned_id', $form->getAssignedId());
		tpl_assign("assigned_id", $assigned_id);

		$projects = logged_user()->getAllProjects();
		tpl_assign('projects', $projects);

		$project_id = input_post_request('project_id', 0);
		tpl_assign("project_id", $project_id);
		
		$client_id = input_post_request('client_id', 0);
		tpl_assign("client_id", $client_id);

		$name = input_post_request('name');
		tpl_assign("name", $name);

		$email = input_post_request('email');
		tpl_assign("email", $email);

		$address = input_post_request('address');
		tpl_assign("address", $address);

		$city = input_post_request('city');
		tpl_assign("city", $city);

		$state = input_post_request('state');
		tpl_assign("state", $state);

		$postcode = input_post_request('postcode');
		tpl_assign("postcode", $postcode);

		$country = input_post_request('country');
		tpl_assign("country", $country);

		$phone_number = input_post_request('phone_number');
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
		  $this->form_validation->set_rules('email', lang('c_475'), 'trim|valid_email|required|max_length[100]|is_unique['.$this->Leads->getTableName().'.email]', 
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
					$lead = new Lead();

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

					set_flash_success(sprintf(lang('c_128'), lang('c_482')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));
				
			}
				  
		  }
		  		  	
		}
		
	}
	
	public function create() {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('forms/_base_form');

		$form = new LeadForm();
		tpl_assign('form', $form);

		$title = input_post_request('title');
		tpl_assign("title", $title);

		$welcome_message = input_post_request('welcome_message');
		tpl_assign("welcome_message", $welcome_message);

		$assigned_id = input_post_request('assigned_id');
		tpl_assign("assigned_id", $assigned_id);

		$status_id = input_post_request('status_id');
		tpl_assign("status_id", $status_id);

		$source_id = input_post_request('source_id');
		tpl_assign("source_id", $source_id);

		$field_count = input_post_request('field_count');
		$is_submited = input_post_request('submited') ==  'submited';
				
		if ($is_submited) {

			$this->form_validation->set_rules('title', lang('c_486'), 'trim|required|max_length[100]');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {

				try{
					
					$this->db->trans_begin();
					
					$form->setTitle($title);
					$form->setDescription($welcome_message);

					$form->setAssignedId($assigned_id);
					$form->setDefaultStatusId($status_id);
					$form->setDefaultSourceId($source_id);

					$is_collect_userinfo = intval(input_post_request("collect_user"));
					$form->setIsCollectUserinfo($is_collect_userinfo);

					$form->setCreatedById(logged_user()->getId());
					$form->save();

					$form->setAccessKey(sha1(uniqid(rand(), true).$form->getId().time()));
					$form->save();
					
					for($i=1; $i<=$field_count; $i++) {

						$delete = intval(input_post_request("field_delete_" . $i));
						if (!$delete) {
					
							$field_title = input_post_request('field_title_'.$i);
							$field_type = input_post_request('field_type_'.$i);
							$field_require = input_post_request('field_require_'.$i);
							$field_desc = input_post_request('field_desc_'.$i);
							$field_options = input_post_request('field_options_'.$i);
	
							$form_element = new LeadFormElement();
							
							$form_element->setFieldName($field_title);
							$form_element->setFieldCategory($field_type);
							
							$form_element->setFieldData($field_options);
							$form_element->setIsRequired($field_require == 1);
	
							$form_element->setHelpText($field_desc);
							$form_element->setFormId($form->getId());
							
							$form_element->save();
						
						}
						
					}
					
					$this->db->trans_commit();

					set_flash_success(sprintf(lang('c_128'), lang('c_505')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}		
		
		}
		
	}

	public function edit($id) {

		//only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('forms/_base_form');

		$form = $this->LeadForms->findById($id);
		if(is_null($form)) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('form', $form);

		$title = input_post_request('title', $form->getTitle());
		tpl_assign("title", $title);

		$welcome_message = input_post_request('welcome_message', $form->getDescription());
		tpl_assign("welcome_message", $welcome_message);

		$assigned_id = input_post_request('assigned_id', $form->getAssignedId());
		tpl_assign("assigned_id", $assigned_id);

		$status_id = input_post_request('status_id', $form->getDefaultStatusId());
		tpl_assign("status_id", $status_id);

		$source_id = input_post_request('source_id', $form->getDefaultSourceId());
		tpl_assign("source_id", $source_id);

		$field_count = input_post_request('field_count');
		$is_submited = input_post_request('submited') ==  'submited';


		if ($is_submited) {

			$this->form_validation->set_rules('title', lang('c_486'), 'trim|required|max_length[100]');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {

				try{
					
					$this->db->trans_begin();
					
					$form->setTitle($title);
					$form->setDescription($welcome_message);

					$form->setAssignedId($assigned_id);
					$form->setDefaultStatusId($status_id);
					$form->setDefaultSourceId($source_id);

					$is_collect_userinfo = intval(input_post_request("collect_user"));
					$form->setIsCollectUserinfo($is_collect_userinfo);

					$form->setCreatedById(logged_user()->getId());
					$form->save();
					
					for($i=1; $i<=$field_count; $i++) {

						$element_id = input_post_request('element_'.$i);
						$form_element = $this->LeadFormElements->findById($element_id);

						$delete = intval(input_post_request("field_delete_" . $i));
						if (!$delete) {
					
							$field_title = input_post_request('field_title_'.$i);
							$field_type = input_post_request('field_type_'.$i);
							$field_require = input_post_request('field_require_'.$i);
							$field_desc = input_post_request('field_desc_'.$i);
							$field_options = input_post_request('field_options_'.$i);
	
							if(!isset($form_element)) {
								$form_element = new LeadFormElement();
							}
												
							$form_element->setFieldName($field_title);
							$form_element->setFieldCategory($field_type);
							
							$form_element->setFieldData($field_options);
							$form_element->setIsRequired($field_require == 1);
	
							$form_element->setHelpText($field_desc);
							$form_element->setFormId($form->getId());
							
							$form_element->save();
						
					   } elseif(isset($form_element)) {
					   		$form_element->delete();
					   }
					
					}
					
					$this->db->trans_commit();

					set_flash_success(sprintf(lang('c_57'), lang('c_505')));
		
				} catch (Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}

				$this->renderText(output_ajax_request(true));

			}		
		
		}

	}

	public function delete($id) {

		$form = $this->LeadForms->findById($id);
		if(is_null($form)) {
			set_flash_error(lang('e_3'));
		} else {

			try {
			
				$form->delete();
				set_flash_success(sprintf(lang('c_56'), lang('c_505')));

			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}
		
		}
							
		redirect('forms');

	}	
	
}
