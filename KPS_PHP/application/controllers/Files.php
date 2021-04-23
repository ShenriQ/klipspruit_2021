<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Files extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
	}

	public function show($id) {

		$project_file = $this->ProjectFiles->findById($id);

		if(is_null($project_file) || !logged_user()->isMember() || $project_file->getParent() ||
		!($project_file->isObjectOwner(logged_user()) || logged_user()->isOwner() 
		|| (logged_user()->isAdmin() && logged_user()->isProjectUser($project_file->getProject())))) {
			
			set_flash_error(lang('c_164'));
			redirect("projects");
		
		} else {

			try {
				
				$project_file->setIsPrivate(false);
				$project_file->save();

				$this->ActivityLogs->setIsPrivateByObject($project_file, false);
				
				set_flash_success(sprintf("%s has been shown successfully.", "File"));
				
			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}

			redirect($project_file->getProject()->getObjectURL('files'));
		
		}

	}

	public function hide($id) {

		$project_file = $this->ProjectFiles->findById($id);

		if(is_null($project_file) || !logged_user()->isMember() || $project_file->getParent() ||
		!($project_file->isObjectOwner(logged_user()) || logged_user()->isOwner() 
		|| (logged_user()->isAdmin() && logged_user()->isProjectUser($project_file->getProject())))) {

			set_flash_error(lang('c_164'));
			redirect("projects");
		
		} else {

			try {
				
				$project_file->setIsPrivate(true);
				$project_file->save();

				$this->ActivityLogs->setIsPrivateByObject($project_file, true);
				
				set_flash_success(sprintf(lang('c_165'), lang('c_166')));
				
			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}

			redirect($project_file->getProject()->getObjectURL('files'));
		
		}

	}
			
	public function upload($project_id) {

		only_ajax_request_allowed();

		$project = $this->Projects->findById($project_id);
		if(!(isset($project) && !$project->getIsTrashed() &&
		(logged_user()->isProjectUser($project) || logged_user()->isOwner()))) {
			die();
		}

		tpl_assign('project', $project);
		$attach_files = get_form_files('attachFiles');

		$notify_users = input_post_request('notify_users');		
		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {
		
			try{
				
				$notification_files = '';
				$upload_counter = 0;
				
				foreach($attach_files as $attach_file) {

					if(is_array($attach_file) && $attach_file['name'] != "") {				
					
						if(!($project_file = $this->ProjectFiles->saveFile($attach_file, false, $project))) throw new Exception();	

						$notification_files .= '<a href="'.get_page_base_url($project_file->getObjectURL()).'" target="_blank">'.$project_file->getFileName().'</a><br>';
						
						$this->ActivityLogs->create($project_file, lang('c_167'), 'add', $project_file->getIsPrivate());
						$upload_counter++;
					
					}
								
				}
				
				if($upload_counter > 0) {

					set_flash_success(sprintf(lang('c_168'), "Upload"));
					
					// Send notification ..
					if(isset($notify_users) && is_array($notify_users) && count($notify_users)) {

						// Prepare email ..
						$notify_data = array("company_name" => $project->getCreatedForCompany()->getName(), "project_name" => $project->getName(), 
						"files" => $notification_files, "files_link" => get_page_base_url($project->getObjectURL('files')));
						
						$notify_message = $this->load->view("emails/files", $notify_data, true);
						$notify_subject = lang('c_169');
						
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
				
				}
																	
			}catch(Exception $e){
				set_flash_error(lang('c_170'));
			}

			$this->renderText(output_ajax_request(true));

		}

	}
	
	public function download($access_key) {

		$project_file = $this->ProjectFiles->getByAccessKey($access_key);
		if(is_null($project_file)) redirect('dashboard');
		
		$project_file_parent = $project_file->getParent();
		$is_private_file = $project_file_parent ? $project_file_parent->getIsPrivate() : $project_file->getIsPrivate();
		
		if(is_null($project_file) || !($project_file->isObjectOwner(logged_user()) || logged_user()->isOwner() 
		|| (logged_user()->isProjectUser($project_file->getProject()) && logged_user()->isMember()) 
		|| (logged_user()->isProjectUser($project_file->getProject()) && !logged_user()->isMember() && !$is_private_file) 
		)) {

			set_flash_error(lang('c_164'));
			redirect("projects");
		
		} else {
			if(!download_file($project_file)) {
				set_flash_error(lang('c_542'));
				redirect('dashboard');
			}
		}	
	
	}
			
}
