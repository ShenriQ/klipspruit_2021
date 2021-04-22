<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
				
	}
	
	public function index() {
		
		$online_users = $this->Users->getOnlineUsers();
		tpl_assign('online_users', $online_users);

		$projects = logged_user()->getActiveProjects();
		tpl_assign('projects', $projects);

		$my_tasks = logged_user()->getMyTasks(false, false, 5, 0);
		tpl_assign('my_tasks', $my_tasks);
		
		$my_tasks = logged_user()->getMyTickets(false, false, 5, 0);
		tpl_assign('my_tickets', $my_tasks);
		
	}

	function delete_timer() {
		$my_started_timer = logged_user()->getMyStartedTimer();
		if(isset($my_started_timer)) {

			try {
				
				$this->db->trans_begin();

				$this->ActivityLogs->create($my_started_timer, lang('c_523.15'), 'delete', true, true);
				$my_started_timer->delete();

				$this->db->trans_commit();
				set_flash_success(lang('c_523.16'));

			} catch(Exception $e) {

				$this->db->trans_rollback();
				set_flash_error(lang('e_1'));

			}

		} else {
			set_flash_error(lang('e_3'));
		}
	
		$ref = input_get_request('ref');
		$redirect_url = $ref != '' ? base64_decode($ref) : '';
		
		redirect($redirect_url);
    
	}

	function start_timer($project_id) {
		
		$project = $this->Projects->findById($project_id);
		if(is_null($project) || $project->getIsTrashed() 
		|| !(logged_user()->isProjectUser($project) || logged_user()->isOwner())) {			
			set_flash_error(lang('e_3'));
		} else {

			$my_started_timer = logged_user()->getMyStartedTimer();
			if(isset($my_started_timer)) {
				set_flash_error(lang('c_523.10')); // already started ..
			} else {

				try {
				
					$this->db->trans_begin();

					$project_timelog = new ProjectTimelog();

					$project_timelog->setIsTimer(true);
					$project_timelog->setIsTimerStarted(true);
					$project_timelog->setIsApproved(false);

					$project_timelog->setProjectId($project_id);
					$project_timelog->setStartTime(time());

					$project_timelog->setMemberId(logged_user()->getId());
					$project_timelog->setCreatedById(logged_user()->getId());
					$project_timelog->save();

					$this->ActivityLogs->create($project_timelog, lang('c_523.12'), 'add', true, true);

					$this->db->trans_commit();
					set_flash_success(lang('c_523.11'));

				} catch(Exception $e) {

					$this->db->trans_rollback();
					set_flash_error(lang('e_1'));

				}
			}
		}

		$ref = input_get_request('ref');
		$redirect_url = $ref != '' ? base64_decode($ref) : '';
		
		redirect($redirect_url);
    
	}

	function switch_theme($theme = "") {
		
		$themes = array('default', 'default-dark', 'blue', 'blue-dark', 'gray', 
		'gray-dark', 'green', 'green-dark', 'megna', 'megna-dark', 'purple', 'purple-dark');
		    
	    $theme = ($theme != "" && in_array($theme, $themes)) ? $theme : "blue-dark";
        $this->session->set_userdata('site_theme', $theme);

        redirect('/');
    
	}

	function switch_language($language = "") {
    
	    $language = ($language != "") ? $language : "english";
        $this->session->set_userdata('site_lang', $language);

        redirect('/');
    
	}
	
	public function validate_photo() {

		$avatar = input_file_request('avatar_file');		
		if(!empty($avatar['name'])){

			$valid_image_types = array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
			if(in_array($avatar['type'], $valid_image_types) && $image = getimagesize($avatar['tmp_name'])) {
				return TRUE;
			}
			
		}

		$this->form_validation->set_message('validate_photo', 'The {field} is not valid image.');
		return FALSE;
	
	}
	
	public function edit_profile() {

		only_ajax_request_allowed();
		$this->setLayout('modal');
				
		$user = logged_user();
		tpl_assign('user', $user);
				
		$name = input_post_request('name', $user->getName());
		tpl_assign("name", $name);

		$email = input_post_request('email', $user->getEmail());
		tpl_assign("email", $email);

		$password = input_post_request('password');

		$address = input_post_request('address', $user->getAddress());
		tpl_assign("address", $address);

		$phone_number = input_post_request('phone_number', $user->getPhoneNumber());
		tpl_assign("phone_number", $phone_number);

		$avatar = input_file_request('avatar_file');
		$remove_avatar = input_post_request('remove_avatar');

		$is_submited = input_post_request('submited') ==  'submited';
				
		if ($is_submited) {

			$this->form_validation->set_rules('name', lang('c_85'), 'trim|required|max_length[30]');
			$this->form_validation->set_rules('password', lang('c_2'), 'trim|min_length[8]|max_length[20]');
			
			$this->form_validation->set_rules('address', lang('c_77'), 'trim|max_length[200]');
			$this->form_validation->set_rules('phone_number', lang('c_78'), 'trim|min_length[10]|max_length[30]');

			if(!empty($avatar['name'])){
				$this->form_validation->set_rules('avatar', lang('c_446'), 'callback_validate_photo');
			}
				
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{
					
					$user->setName($name);
					if($password != '') $user->setPassword($password);

					if($address != '') $user->setAddress($address);
					if($phone_number != '') $user->setPhoneNumber($phone_number);

					if($remove_avatar == "on"){
						$user->deleteLogo();
					}else{	
					
						if(!empty($avatar['name'])){
					
							$old_avatar = $user->getLogoPath();
							
							if(!$user->setLogo($avatar['tmp_name'])) {
								throw new Exception();
							}else{
		
								if(is_file($old_avatar)) {
									@unlink($old_avatar);
								}
							 
							}
							
						}
					
					}
									
					$user->save();
					
					set_flash_success(sprintf(lang('c_81'), lang('c_86')));
					
				}catch(Exception $e){
					set_flash_error("error", lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
	
}
