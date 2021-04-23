<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Widgets extends Admin_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_admin_data_contoller($this);

	}
	
	public function index() {
		$widgets = $this->IWidgets->find();
		tpl_assign('widgets', $widgets);
	}
	
	public function validate_photo() {

		$photo = input_file_request('photo');		
		if(!empty($photo['name'])){

			$valid_image_types = array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
			if(in_array($photo['type'], $valid_image_types) && $image = getimagesize($photo['tmp_name'])) {
				return TRUE;
			}
			
		}

		$this->form_validation->set_message('validate_photo', 'The {field} is not valid image.');
		return FALSE;
	
	}

	public function add(){
		only_ajax_request_allowed();
		$this->setLayout('modal');
		$this->setTemplate('admin/widgets/add');

		$widget = new IWidget();

		$title = input_post_request('title', $widget->getTitle());
		tpl_assign("title", $title);

		$description = input_post_request('description', $widget->getDescription());
		tpl_assign("description", $description);

		$photo = input_file_request('photo');
		$remove_photo = input_post_request('remove_photo');

		$is_submited = input_post_request('submited') ==  'submited';
	
		if ($is_submited) {
	
			$this->form_validation->set_rules('title', 'Title', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required');
		
			if(!empty($photo['name'])){
				$this->form_validation->set_rules('photo', 'Photo', 'callback_validate_photo');
			}
			else {
				$this->form_validation->set_rules('photo', 'Photo', 'required');
			}
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
				try{
					$widget->setTitle($title);
					$widget->setDescription($description);

					if($remove_photo == "on"){
						$widget->deletePhoto();
					}
					else{	
						if(!empty($photo['name'])){
							$old_photo = $widget->getPhotoUrl();
							if(!$widget->savePhotoFile($photo['tmp_name'])) {
								throw new Exception();
							}
							else{
								if(is_file($old_photo)) {
									@unlink($old_photo);
								}
							}
						}
					}
					$widget->save();
					set_flash_success("Widget has been saved successfully.");
				}catch(Exception $e){
					set_flash_error("An error was encountered.");
				}
				$this->renderText(output_ajax_request(true));
			}
		}
	}

	public function edit($id){

		only_ajax_request_allowed();
		$this->setLayout('modal');
		$this->setTemplate('admin/widgets/edit');

		$widget = $this->IWidgets->findById($id);
		if(is_null($widget)) {
			set_flash_error("The page you requested was not found.", true);
		}

		tpl_assign('widget', $widget);

		$title = input_post_request('title', $widget->getTitle());
		tpl_assign("title", $title);

		$description = input_post_request('description', $widget->getDescription());
		tpl_assign("description", $description);

		$photo = input_file_request('photo');
		$remove_photo = input_post_request('remove_photo');

		$is_submited = input_post_request('submited') ==  'submited';
	
		if ($is_submited) {
	
			$this->form_validation->set_rules('title', 'Title', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required');
		
			if(!empty($photo['name'])){
				$this->form_validation->set_rules('photo', 'Photo', 'callback_validate_photo');
			}
			else {
				// $this->form_validation->set_rules('photo', 'Photo', 'required');
			}
			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
				try{
					$widget->setTitle($title);
					$widget->setDescription($description);

					if($remove_photo == "on"){
						$widget->deletePhoto();
					}
					else{	
						if(!empty($photo['name'])){
							$old_photo = $widget->getPhotoUrl();
							if(!$widget->savePhotoFile($photo['tmp_name'])) {
								throw new Exception();
							}
							else{
								if(is_file($old_photo)) {
									@unlink($old_photo);
								}
							}
						}
					}
					$widget->save();
					set_flash_success("Widget has been saved successfully.");
				}catch(Exception $e){
					set_flash_error("An error was encountered.");
				}
				$this->renderText(output_ajax_request(true));
			}
		}
	}

	
	public function delete($id){

		only_ajax_request_allowed();
		$this->setLayout('modal');
		$this->setTemplate('admin/widgets/delete');

		$widget = $this->IWidgets->findById($id);
		if(is_null($widget)) {
			set_flash_error("The page you requested was not found.", true);
		}

		tpl_assign('widget', $widget);

		$is_submited = input_post_request('submited') ==  'submited';
	
		if ($is_submited) {
			try{
				$widget->deletePhoto();
				$widget->delete();
				set_flash_success("Widget has been removed successfully.");
			}
			catch(Exception $e){
				set_flash_error("An error was encountered.");
			}
			$this->renderText(output_ajax_request(true));
		}
	}
}
