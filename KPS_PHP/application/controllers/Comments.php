<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Comments extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
	}
	
	private function validate_request($parent_id, $parent_type) {
		
		$parent_model = camelize_string($parent_type);
		$parent_object = $this->$parent_model->findById($parent_id);
		
		if(isset($parent_object) && $parent_object->isCommentable() && !$parent_object->getIsTrashed()) {
			
			$project = $parent_object->getProject();
			if(isset($project) && !$project->getIsTrashed() &&
			(logged_user()->isProjectUser($project) || logged_user()->isOwner())) {
				return array($project, $parent_object);
			}
		
		}

		return false;
		
	}
	
	public function add($parent_id, $parent_type) {

		only_ajax_request_allowed();

		$valid_request_data = $this->validate_request($parent_id, $parent_type);		
		if(!$valid_request_data) die();
		
		list($project, $parent_object) = $valid_request_data;
						
		$message = input_post_request('message');
		$attach_files = get_form_files('attachFiles');

		$notify_users = input_post_request('notify_users');		
		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('message', lang('c_59'), 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$this->db->trans_begin();
							
					$project_comment = new ProjectComment();

					$project_comment->setText($message);
					$project_comment->setProjectId($project->getId());
					
					$project_comment->setParentType($parent_object->getModelName());
					$project_comment->setParentId($parent_object->getId());
					
					$project_comment->setCreatedById(logged_user()->getId());
					$project_comment->save();
						
					if(isset($attach_files) && is_array($attach_files) && count($attach_files)){
					
						foreach($attach_files as $attach_file) {
							if(!$project_comment->saveFile($attach_file, false)) throw new Exception();
						}
					
					}
					
					if($parent_object != $project) {	
						$this->ActivityLogs->create($project_comment, lang('c_60'), 'add', $parent_object->getIsPrivate());
					}
					
					$this->db->trans_commit();
					set_flash_success(sprintf(lang('c_61'), lang('c_59')));
					
					// Send notification ..
					if(isset($notify_users) && is_array($notify_users) && count($notify_users)) {
						
						$notification_message = '<p>'.$message.'</p>';
						$a_comment_files = $project_comment->getFiles(true);

						if(isset($a_comment_files) && is_array($a_comment_files) && count($a_comment_files)) { 
			
							$notification_message .= '<p class="custom-mt-10"><b>'.lang('c_62').'</b><br>';
			
							foreach($a_comment_files as $a_comment_file) {
								$notification_message .= '<a href="'.get_page_base_url($a_comment_file->getObjectURL()).'" target="_blank">'.$a_comment_file->getFileName().'</a><br>';
							}
			
							$notification_message .= '</p>';
			
						}					
						
						// Prepare email ..
						$notify_data = array("company_name" => $project->getCreatedForCompany()->getName(), "project_name" => $project->getName(), 
						"message" => $notification_message, "message_link" => get_page_base_url($project_comment->getObjectURL()));
						
						$notify_message = $this->load->view("emails/comment", $notify_data, true);
						$notify_subject = lang('c_63');
						
						foreach($notify_users as $notify_user) {
						
							$notify_user_object = $this->Users->findById($notify_user);
							if(isset($notify_user_object)) {
								try {
									$this->UserNotifications->create($notify_user_object, $notify_subject, $notify_message);
									send_mail_SMTP($notify_user_object->getEmail(), $notify_user_object->getName(), $notify_subject, $notify_message);
								} catch (Exception $e){}
							}
						
						}
							
					}
													
				}catch(Exception $e){
					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
			
}
