<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Discussions extends Application_controller {
	
	function __construct() {
		
		parent::__construct();
		initial_data_contoller($this);
		
	}
		
	public function create($project_id) {

		only_ajax_request_allowed();

		$this->setLayout('modal');
		$this->setTemplate('discussions/_topic_form');

		$project = $this->Projects->findById($project_id);
		if(!(isset($project) && !$project->getIsTrashed() &&
		(logged_user()->isProjectUser($project) || logged_user()->isOwner()))) {
			die();
		}

		tpl_assign('project', $project);

		$discussion = new ProjectDiscussion();
		tpl_assign('discussion', $discussion);

		$title = input_post_request('title');
		tpl_assign("title", $title);

		$text = input_post_request('text');
		tpl_assign("text", $text);

		$is_private = input_post_request('is_private') == 'on';
		tpl_assign("is_private", $is_private);

		$is_sticky = input_post_request('is_sticky') == 'on';
		tpl_assign("is_sticky", $is_sticky);

		$is_submited = input_post_request('submited') ==  'submited';
		
		if ($is_submited) {

			$this->form_validation->set_rules('title', lang('c_115'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('text', lang('c_116'), 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$discussion->setProjectId($project->getId());

					$discussion->setTitle($title);
					$discussion->setText($text);
					
					if(logged_user()->isMember()) $discussion->setIsPrivate($is_private);
					$discussion->setIsSticky($is_sticky);
					
					$discussion->setCreatedById(logged_user()->getId());
					$discussion->save();

					$this->ActivityLogs->create($discussion, lang('c_117'), 'add', $is_private);
						
					set_flash_success(sprintf(lang('c_54'), "Topic"));
															
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
		$this->setTemplate('discussions/_topic_form');

		$discussion = $this->ProjectDiscussions->findById($id);
		if(is_null($discussion) || !($discussion->isObjectOwner(logged_user()) || logged_user()->isOwner() 
		|| (logged_user()->isAdmin() && logged_user()->isProjectUser($discussion->getProject())))) {
			set_flash_error(lang('e_3'), true);
		}

		tpl_assign('discussion', $discussion);
		tpl_assign('project', $discussion->getProject());

		$title = input_post_request('title', $discussion->getTitle());
		tpl_assign("title", $title);

		$text = input_post_request('text', $discussion->getText());
		tpl_assign("text", $text);

		$is_submited = input_post_request('submited') ==  'submited';

		$is_private = $is_submited ? input_post_request('is_private') == 'on' : $discussion->getIsPrivate();
		tpl_assign("is_private", $is_private);

		$is_sticky = $is_submited ? input_post_request('is_sticky') == 'on' : $discussion->getIsSticky();
		tpl_assign("is_sticky", $is_sticky);

		if ($is_submited) {

			$this->form_validation->set_rules('title', lang('c_115'), 'trim|required|max_length[100]');
			$this->form_validation->set_rules('text', lang('c_116'), 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$this->renderText(output_ajax_request(false, validation_errors()));
			} else {
		
				try{

					$discussion->setTitle($title);
					$discussion->setText($text);
					
					if(logged_user()->isMember()) $discussion->setIsPrivate($is_private);
					$discussion->setIsSticky($is_sticky);
					
					$discussion->save();

					$this->ActivityLogs->create($discussion, lang('c_118'), 'edit', $is_private);
						
					set_flash_success(sprintf(lang('c_81'), "Topic"));
															
				}catch(Exception $e){
					set_flash_error(lang('e_1'));
				}

				$this->renderText(output_ajax_request(true));

			}		

		}

	}
			
}
