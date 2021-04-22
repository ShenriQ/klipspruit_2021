<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Noticeboard extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);

		if(!logged_user()->isOwner()) redirect('dashboard');
		
	}

	public function validate_date($date) {
		return $date != '' ? validate_date($date) : true;
	}
	
	public function compare_dates() {
	
		$start = strtotime($this->input->post('start_date'));
		$end = strtotime($this->input->post('end_date'));
	
		if($start > $end) {
			$this->form_validation->set_message('compare_dates',lang('c_191'));
			return false;
		}

	}
	
	public function index() {
		tpl_assign('announcements', $this->Announcements->getAll());
	}
	
	public function create() {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('noticeboard/_announcement_form');

		$announcement = new Announcement();
		tpl_assign('announcement', $announcement);

		$title = input_post_request('title');
		tpl_assign("title", $title);

		$description = input_post_request('description');
		tpl_assign("description", $description);

		$end_date = input_post_request('end_date');
		tpl_assign("end_date", $end_date);

		$start_date = input_post_request('start_date');
		tpl_assign("start_date", $start_date);

		$share_with = input_post_request('share_with');
		tpl_assign("share_with", $share_with);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('title', lang('c_45'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('description', lang('c_48'), 'trim|required');
			
			$this->form_validation->set_rules('start_date', lang('c_192'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_193')));

			$this->form_validation->set_rules('end_date', lang('c_198'), 'required|callback_validate_date',  
			array('validate_date' => lang('c_194')));

			$this->form_validation->set_rules('end_date', lang('c_198'), 'trim|callback_compare_dates');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$announcement->setTitle($title);
					$announcement->setDescription($description);
					$announcement->setStartDate($start_date);
					$announcement->setEndDate($end_date);
					$announcement->setShareWith($share_with);

					$announcement->setCreatedById(logged_user()->getId());
					$announcement->save();

					set_flash_success(sprintf(lang('c_54'), lang('c_195')));
															
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}

	public function edit($id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('noticeboard/_announcement_form');

		$announcement = $this->Announcements->findById($id);
		if(is_null($announcement)) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('announcement', $announcement);

		$title = input_post_request('title', $announcement->getTitle());
		tpl_assign("title", $title);

		$description = input_post_request('description', $announcement->getDescription());
		tpl_assign("description", $description);

		$start_date_timestamp = $announcement->getStartDate();
		$formatted_start_date = $start_date_timestamp ? format_date($start_date_timestamp, 'm/d/Y') : null;
		
		$start_date = input_post_request('start_date', $formatted_start_date);
		tpl_assign("start_date", $start_date);

		$end_date_timestamp = $announcement->getEndDate();
		$formatted_end_date = $end_date_timestamp ? format_date($end_date_timestamp, 'm/d/Y') : null;
		
		$end_date = input_post_request('end_date', $formatted_end_date);
		tpl_assign("end_date", $end_date);

		$share_with = input_post_request('share_with', $announcement->getShareWith());
		tpl_assign("share_with", $share_with);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[100]');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			
			$this->form_validation->set_rules('start_date', 'Start Date', 'required|callback_validate_date',  
			array('validate_date' => lang('c_193')));

			$this->form_validation->set_rules('end_date', 'End Date', 'required|callback_validate_date',  
			array('validate_date' => lang('c_194')));

			$this->form_validation->set_rules('end_date', 'End Date', 'trim|callback_compare_dates');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$announcement->setTitle($title);
					$announcement->setDescription($description);
					$announcement->setStartDate($start_date);
					$announcement->setEndDate($end_date);
					$announcement->setShareWith($share_with);

					$announcement->save();

					set_flash_success(sprintf(lang('c_57'), lang('c_195')));
															
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}

	public function remove($id) {

		$announcement = $this->Announcements->findById($id);
		if(isset($announcement)) {

			try {
			
				$announcement->delete();
				set_flash_success(sprintf(lang('c_56'), lang('c_195')));

			} catch(Exception $e) {
				set_flash_error(lang('e_1'));
			}
			
		}
		
		redirect('noticeboard');

	}
						
}
