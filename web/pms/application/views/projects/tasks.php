<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_286')); 

if(isset($object_id) && $object_id > 0) {

	$task = $this->ProjectTasks->findById($object_id);
	if(!is_null($task)) $task_list = $task->getTaskList();
	
	if(is_null($task_list) || !(logged_user()->isOwner() || logged_user()->isProjectUser($task_list->getProject())) ) {
		set_flash_error(lang('e_3'));
		redirect('dashboard');
	}

}

?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-tasks"></i>
	  <h3 class="box-title"><a href="<?php echo get_page_base_url($task_list->getObjectURL());?>"><?php echo $task_list->getName(); ?></a> &raquo; <?php echo lang('c_183'); ?> <?php echo ($task_list->getIsPrivate() ? ' <i class="fa fa-lock"></i>' : ''); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="well"<?php echo ($task->getIsHighPriority() ? ' style="background-color:#FFEED6;"' : ''); ?>>
		
			<div class="row">
			
				<div class="col-md-10">

					<span class="text-center"><?php if ($task->getCompletedById() > 0) : ?>
						<a href="<?php echo get_page_base_url($task->getReopenURL()); ?>"><i class="fa fa-check-square-o fa-lg"></i></a>
					<?php else : ?>
						<a href="<?php echo get_page_base_url($task->getCompleteURL()); ?>"><i class="fa fa-square-o fa-lg"></i></a>
					<?php endif; ?>
					</span>

					<span class="text-left">

						<?php if ($task->getCompletedById() > 0) : ?><strike><?php endif; ?>
						<span class="more"><?php echo $task->getDescription(); ?></span>
						<?php if ($task->getCompletedById() > 0) : ?></strike><?php endif; ?>
	
						<?php $task_label = $task->getLabel();
						if(isset($task_label) && $task_label->getIsActive() && $task->getCompletedById() == 0) : ?>
						<small style="background-color:#<?php echo $task_label->getBgColorHex(); ?>; color: white; padding:2px;"><?php echo $task_label->getName(); ?></small>
						<?php endif; ?>

						<p><small><?php $assigned_to = $task->getAssignedTo();
						if(!is_null($assigned_to)) : ?><u><?php echo $assigned_to->getName();?></u> &nbsp; <?php endif; ?>
						
						<?php $due_date = $task->getDueDate();
						if(isset($due_date)) :
							echo format_date($due_date, 'j M. Y');
							if($due_date < time() && !($task->getCompletedById() > 0)) : ?>&nbsp;<small class="custom-color-red "><?php echo lang('c_154'); ?></small><?php endif;
						endif; ?></small></p>
						
					</span>
				</div>
				
				<div class="col-md-2">
					<?php if(logged_user()->isMember()) : ?>
					<div class="pull-right">
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($task->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_185'); ?></a></li>
								<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($task, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
							</ul>
						</div>
					</div>
					<?php endif; ?>
				</div>
			
			</div>
			
		</div>
		
		<?php if($task->isCommentable()) : ?>
		
		<h4><?php echo sprintf(lang('c_266'), lang('c_183')); ?></h4>
		<?php tpl_assign('parent_object', $task);
		tpl_display('comments/_comment_box'); 
		
		endif;?>

	</div>

</div>
