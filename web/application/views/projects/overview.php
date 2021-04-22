<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_413')); 

tpl_assign("header_for_layout", '
');	

?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-clock-o"></i>
	  <h3 class="box-title"><?php echo lang('c_415'); ?></h3>
	</div>
	
	<div class="box-body">
		
		<?php  $include_private = logged_user()->isMember();
		$include_hidden = logged_user()->isAdmin() || logged_user()->isOwner();
		
		$activity_logs = $this->ActivityLogs->getByProject($project, $include_private, $include_hidden, 5, 0);
		if(isset($activity_logs) && is_array($activity_logs) && count($activity_logs)) : ?>
		

		<ul class="timeline">

			<?php foreach($activity_logs as $activity_log) : 
			list($date_element_class, $log_date_value) = get_date_format_array($activity_log->getCreatedAt()); ?>

            <li>
              <i class="<?php echo get_activity_icon($activity_log->getModel()); ?>"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> <?php echo $log_date_value; ?></span>

                <div class="timeline-body <?php echo $date_element_class; ?>"><?php 
				
				$created_by_name = '';
				$create_by = $activity_log->getCreatedBy();
				
				if($create_by) {
					$created_by_name = ($create_by->getId() == logged_user()->getId() ? lang('c_24') : '<u>'.$create_by->getName().'</u>').' ';
				}
				
				$log_object = $activity_log->getObject();
				$raw_data = unserialize_data($activity_log->getRawData());
				
				$log_row = $created_by_name.' '.$raw_data['message'].' '.(isset($log_object) && $log_object->getObjectURL() ? '<a href="'.get_page_base_url($log_object->getObjectURL()).'">'.$raw_data['title'].'</a>' : $raw_data['title']);
				
				echo $log_row;?>
							
				</div>
				
              </div>
            </li>
			
			<?php endforeach; ?>
			
		</ul>

		<?php else : ?>
		<p><?php echo lang('e_2'); ?></p>
		<?php endif; ?>
		
	</div>

</div>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-commenting-o"></i>
	  <h3 class="box-title"><?php echo lang('c_263'); ?></h3>
	</div>
	
	<div class="box-body no-padding">
		
	<?php $include_private = logged_user()->isMember(); 
	$project_discussions = $project->getDiscussions(false, $include_private, 5, 0);
	if(isset($project_discussions) && is_array($project_discussions) && count($project_discussions)) : ?>
	
		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>
		
		<?php foreach($project_discussions as $project_discussion) : 
			$pd_comment_counts = $project_discussion->getCommentsCount(); ?>		
		
			<tr<?php echo ($project_discussion->getIsSticky() ? ' class="custom-background-light-yellow"' : ''); ?>>
	
				<td><b><a href="<?php echo get_page_base_url($project_discussion->getObjectURL()); ?>"><?php echo $project_discussion->getTitle(); ?></a><?php echo ($project_discussion->getIsPrivate() ? ' <i class="fa fa-lock"></i>' : ''); ?>
				<?php if($pd_comment_counts > 0): ?>&nbsp; <span class="label label-primary"><?php echo $pd_comment_counts; ?></span><?php endif; ?></b>
				<p class="custom-small-grey-color"><b><?php echo lang('c_188'); ?> </b> <u><?php echo $project_discussion->getCreatedByName(true); ?></u> on <?php echo format_date($project_discussion->getCreatedAt()); ?></p>
				<p><?php echo $project_discussion->getText(); ?></p>
				</td>	
			</tr>

		<?php endforeach; ?>
		
		</tbody>
		
		</table></div>

		<p align="right"><a href="<?php echo get_page_base_url($project->getObjectURL('discussions')); ?>" class="btn btn-sm btn-info"><?php echo lang('c_417'); ?></a></p>

	<?php else : ?>
	<p><?php echo lang('e_2'); ?></p>
	<?php endif; ?>
		
	</div>

</div>


<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-list"></i>
	  <h3 class="box-title"><?php echo lang('c_398'); ?></h3>
	</div>
	
	<div class="box-body no-padding">

	<?php $include_private = logged_user()->isMember(); 
	$project_task_lists = $project->getTaskLists($include_private, false, false, 5, 0);
	if(isset($project_task_lists) && is_array($project_task_lists) && count($project_task_lists)) : ?>

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>
		
		<?php foreach($project_task_lists as $project_task_list) : ?>
		
		<tr>

			<td><b><a href="<?php echo get_page_base_url($project_task_list->getObjectURL()); ?>"><?php echo $project_task_list->getName(); ?></a> <?php echo ($project_task_list->getIsPrivate() ? ' <i class="fa fa-lock"></i>' : ''); ?></b>
			<p class="custom-small-grey-color"><b><?php echo lang('c_188'); ?> </b> <u><?php echo $project_task_list->getCreatedByName(true); ?></u> on <?php echo format_date($project_task_list->getCreatedAt()); ?></p>
			<p><span class="more"><?php echo $project_task_list->getDescription(); ?></span></p>
			</td>
			<td width="15%"><?php echo $project_task_list->getTasksCount(); ?> <?php echo lang('c_286'); ?></td>

		</tr>

		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
		<p align="right"><a href="<?php echo get_page_base_url($project->getObjectURL('task_lists')); ?>" class="btn btn-sm btn-info"><?php echo lang('c_418'); ?></a></p>

	<?php else : ?>
	<p><?php echo lang('e_2'); ?></p>
	<?php endif; ?>
		
	</div>

</div>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-file"></i>
	  <h3 class="box-title"><?php echo lang('c_267'); ?></h3>
	</div>
	
	<div class="box-body no-padding">

	<?php $include_private = logged_user()->isMember(); 
	$project_files = $project->getFiles(false, $include_private, 5, 0);
	$display_files_count = 0;
	if(isset($project_files) && is_array($project_files) && count($project_files)) : ?>
		
		<?php foreach($project_files as $project_file) : ?>
			
			<?php $file_parent = $project_file->getParent();
			if($file_parent && ($file_parent instanceof ProjectComment) && $file_parent->getParentType() == 'Projects') continue; // Private Notes
			$is_file_locked = $file_parent ? $file_parent->getIsPrivate() : $project_file->getIsPrivate();
			
			if(!logged_user()->isMember() && $is_file_locked) continue; 
			$display_files_count++; ?>
		
			<?php if($display_files_count == 1) : ?>
			<div class="table-responsive">
			<table class="table table-striped table-bordered" width="100%">
			<tbody><?php endif; ?>
					
			<tr>
	
				<td><a href="<?php echo get_page_base_url($project_file->getObjectURL()); ?>" target="_blank"><?php echo $project_file->getFileName(); ?></a><?php echo ($is_file_locked ? ' <i class="fa fa-lock"></i>' : ''); ?>
				<p class="custom-small-grey-color"><b><?php echo lang('c_72'); ?> </b> <u><?php echo $project_file->getCreatedByName(true); ?></u> &mdash; <?php echo format_date($project_file->getCreatedAt()); ?></p></td>
				<td><?php echo $project_file->getFileTypeString(); ?></td>
	
			</tr>

		<?php endforeach; ?>

	<?php endif; ?>


	<?php if($display_files_count == 0) :?>
		<p><?php echo lang('e_2'); ?></p>
	<?php else: ?>
		</tbody></table></div>
		<p align="right"><a href="<?php echo get_page_base_url($project->getObjectURL('files')); ?>" class="btn btn-sm btn-info"><?php echo lang('c_419'); ?></a></p>
	<?php endif; ?>

		
	</div>

</div>
