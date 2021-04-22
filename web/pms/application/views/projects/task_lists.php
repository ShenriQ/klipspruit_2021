<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_286')); 

tpl_assign("footer_for_layout", '<script>
$(".list-tasks").sortable({opacity:0.8, cursor:"move", connectWith:".column_sortable", placeholder:"focus_placeholder", start:function(c, a) {
  item = a.item;
  list = a.item.parent();
  removed_from_id = list.attr("id");
  add = "remove";
}, remove:function(c, a) {
  item = a.item;
  list = a.item.parent();
  add = "add";
}, update:function(c, a) {
  var b = $(this).closest(".tasklist").attr("id").replace("tasklist_", "");
  $.post("'.get_page_base_url("tasks/sort_tasks").'", {items:$(this).sortable("serialize"), tasklist_id:b, add_or_remove:add});
  b = $(this).closest(".tasklist").attr("id").replace("tasklist_", "");
  0 === $("#input-list-" + b + " li").length ? $("#input-list-" + b + " .empty_column").length ? ($("#input-list-" + b + " .empty_column").html("'.lang('c_287').'"), $("#input-list-" + b + " .empty_column").show()) : $("#input-list-" + b).append(\'<div class="empty_column focus_placeholder">'.lang('c_287').'</div>\') : ($("#input-list-" + b + " .empty_column").html(""), $("#input-list-" + b + " .empty_column").hide());
}});
$(".task-list").sortable({opacity:0.8, cursor:"move", activate:function(c, a) {
  a.item.addClass("focus_onmove");
}, update:function(c, a) {
  $.post("'.get_page_base_url("tasks/sort_tasklists/".$project->getId()."").'", {lists:$(this).sortable("serialize")});
}, stop:function(c, a) {
  a.item.removeClass("focus_onmove");
}});
(function ($) {
	"use strict";
	$(document).ready(function() {
		$(\'#list_id\').on(\'change\', function() {
			var location = "'.get_page_base_url($project->getObjectURL('task_lists')).'" ;
			var id = $(this).val();
			if (id > 0) {
				location = location + "/" + id;
			}
			window.location.href = location;
			return false;
		});
		$(\'#taskFilter\').on(\'change\', function() {
			var location = "'.get_page_base_url($project->getObjectURL('task_lists')).'";
			var id = '.(int) $object_id.';
			if (id > 0) {
				location = location + "/" + id;
			}
			var sort_by = $("#taskFilter").val();
			if (sort_by != "") {
				location = location + "?sort_by=" + sort_by;
			}
			window.location.href = location;
			return false;
		});		
	});
})(jQuery);
</script>');

?>

<div class="row custom-mb-10">
	<div class="col-md-4">
		<?php
		$sort_by = input_get_request('sort_by');

		$include_private = logged_user()->isMember(); 
		$task_list_objects = $project->getTaskLists($include_private);
		$selected_tasklist = null;
		
		$tasklist_options = array(lang('c_288'));
		if(isset($task_list_objects) && is_array($task_list_objects) && count($task_list_objects)) :
		foreach($task_list_objects as $task_list_object) :
			if($object_id == $task_list_object->getId()) $selected_tasklist = $task_list_object;
			$tasklist_options[$task_list_object->getId()] = $task_list_object->getName();
		endforeach;
		endif;
		echo select_box("list_id", $tasklist_options, $object_id, ' id="list_id" class="form-control"');
		
		?>
	</div>
	<div class="col-md-3">
		<select class="form-control" id="taskFilter">
			<option value="all" <?php echo ($sort_by == "" ? ' selected="selected"' : ''); ?>><?php echo lang('c_523.26'); ?></option>
			<option value="" <?php echo ($sort_by == "" ? ' selected="selected"' : ''); ?>><?php echo lang('c_523.27'); ?></option>
			<option value="completed" <?php echo ($sort_by == "completed" ? ' selected="selected"' : ''); ?>><?php echo lang('c_523.28'); ?></option>
		</select>            
	</div>
	<div class="col-md-5 text-right">
		<?php if(logged_user()->isMember()) : ?>
		<a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getCreateTaskListURL()); ?>" class="btn btn-sm btn-success custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_289'); ?></a>
		<?php endif; ?>
	</div>
</div>

<div class="custom-clear-both"></div>

<?php if(isset($selected_tasklist)) $task_list_objects = array($selected_tasklist);
if(isset($task_list_objects) && is_array($task_list_objects) && count($task_list_objects)) : ?>
	
	
	<div class="flow-view flow-content top_bottom_scrollbar">
	<div class="flow-lists" style="width:<?php echo (isset($selected_tasklist) ? 880 : round(count($task_list_objects)*295, 2)); ?>px;">

	<ul class="task-list ui-sortable" style="list-style:none;padding-left:15px!important;">
	
	<?php foreach($task_list_objects as $task_list_object) : ?>
	<li class="flow-list" id="list_<?php echo $task_list_object->getId(); ?>" style="cursor:grab;">
	<div class="tasklist well" id="tasklist_<?php echo $task_list_object->getId(); ?>"<?php echo ($task_list_object->getIsHighPriority() ? ' style="background-color:#FFFFD9!important;"' : ''); ?>>
		
		<div class="tasklist_detail_<?php echo $task_list_object->getId(); ?>">
		<div class="row">
		
			<div class="col-md-10">
				<h4 class="custom-m-0"><i class="fa fa-tasks"></i> <a href="<?php echo get_page_base_url($task_list_object->getObjectURL());?>"><?php echo $task_list_object->getName(); ?></a> <?php echo ($task_list_object->getIsPrivate() ? ' <i class="fa fa-lock"></i>' : ''); ?></h4>
				<p><small><?php if (isset($selected_tasklist)) : ?><span class="more"><?php echo $task_list_object->getDescription(); ?></span><?php else : echo shorter($task_list_object->getDescription(), 45); endif; ?></small></p>
				<p class="custom-small-grey-color"><b><?php echo lang('c_188'); ?> </b> <u><?php echo $task_list_object->getCreatedByName(true); ?></u><br><?php echo format_date($task_list_object->getCreatedAt()); ?></p>
				<?php  $task_list_start_date = $task_list_object->getStartDate();
				if(isset($task_list_start_date)) : ?>
				<p class="custom-mb-10 custom-mt-10 custom-small-dark-grey-color"><b><?php echo lang('c_192'); ?>:</b> <?php echo format_date($task_list_start_date, 'j M. Y'); ?><br>
				<?php endif; ?>
				<?php  $task_list_due_date = $task_list_object->getDueDate();
				if(isset($task_list_due_date)) : ?>
				<b><?php echo lang('c_238'); ?>:</b> <span class="custom-background-light-yellow"><?php echo format_date($task_list_object->getDueDate(), 'j M. Y'); ?></span>
				<?php if($task_list_due_date < time()) : ?>&nbsp; <b class="custom-color-red"><?php echo lang('c_154'); ?></b><?php endif; ?></p>
				<?php endif; ?>
			</div>
			
			<?php if(logged_user()->isMember()) : ?>
		
			<div class="col-md-2">	
		
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
						<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($task_list_object->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_290'); ?></a></li>
						<li class="divider"></li>
						<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($task_list_object, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
						</ul>
					</div>
				</div>
				
			</div>
			<?php endif; ?>
			
		</div>
		</div>
		
		<div class="custom-clear-both"></div>
			
		<?php 
		$no_of_total_tasks = 0;
		if(logged_user()->isMember()) : ?>
		<p><div class="row">
			<div class="col-md-12 text-right">
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($task_list_object->getCreateTaskURL()); ?>" class="btn btn-xs btn-success custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_291'); ?></a>
			</div>
		</div></p>
		<?php endif; ?>

		<div class="custom-clear-both"></div>
	
		<?php
		switch($sort_by) {
			case 'completed':
				$tasks = $task_list_object->getCompletedTasks();
				break;
			case 'all':
				$tasks = $task_list_object->getTasks(true);
				break;
			default:
				$tasks = $task_list_object->getTasks();
		}
		?>

		<ul class="list-tasks column_sortable flow_task_scrollbar ui-sortable" style="max-height:410px; list-style:none;" id="input-list-<?php echo $task_list_object->getId(); ?>">
		
		<?php if(isset($tasks) && is_array($tasks) && count($tasks)) : ?>

			<?php foreach($tasks as $task) : 
			$no_of_total_tasks++; ?>

			<li class="flow_task atask" id="task_<?php echo $task->getId(); ?>" data-cshared="false" style="position: relative; opacity: 1; left: 0px; top: 0px;<?php echo ($task->getIsHighPriority() ? 'background-color:#FFEED6;' : ''); ?>">
			<div class="row flow-task-text">

				<div class="col-md-10">

				<span class="text-center"><?php if ($task->getCompletedById() > 0) : ?>
					<a href="<?php echo get_page_base_url($task->getReopenURL()); ?>"><i class="fa fa-check-square-o fa-lg"></i></a>
				<?php else : ?>
					<a href="<?php echo get_page_base_url($task->getCompleteURL()); ?>"><i class="fa fa-square-o fa-lg"></i></a>
				<?php endif; ?>
				</span>
		
				<span class="text-left">

					<a href="<?php echo get_page_base_url($task->getObjectURL()); ?>"><?php if ($task->getCompletedById() > 0) : ?><strike><?php echo $task->getName(); ?></strike><?php else: ?><?php echo $task->getName(); ?><?php endif; ?> <?php $task_comments_count = $task->getCommentsCount();
					echo ($task_comments_count > 0 ? ' &nbsp; <span class="label label-info">'.$task_comments_count.'</span>' : ''); ?></a><?php if ($task->getCompletedById() > 0) : ?><?php endif; ?>

					<?php $task_label = $task->getLabel();
					if(isset($task_label) && $task_label->getIsActive() && $task->getCompletedById() == 0) : ?>
						<small style="background-color:#<?php echo $task_label->getBgColorHex(); ?>; color: white; padding:2px;"><?php echo $task_label->getName(); ?></small>
					<?php endif; ?>

					<p class="custom-pt-5"><small><?php $task_due_date = $task->getDueDate();
					if(isset($task_due_date)) :
						echo format_date($task_due_date, 'j M. Y');
						if($task_due_date < time() && !($task->getCompletedById() > 0)) : ?>&nbsp;<small class="custom-color-red"><?php echo lang('c_154'); ?></small><?php endif;
					?> &nbsp; <?php endif; ?>

					<?php $assigned_to = $task->getAssignedTo();
					if(!is_null($assigned_to)) : ?>
						<a href="#"><?php echo $assigned_to->getName();?></a>
					<?php endif; ?></small></p>
										
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

			<div class="custom-clear-both"></div>
			</li>			
			<?php endforeach; ?>
		
		<?php else :?>			
		<div class="empty_column focus_placeholder"><?php echo lang('c_287'); ?></div>
		<?php endif; ?>

		</ul>
		
	</div>
	</li>
	<?php endforeach; ?>

	</ul>
	
	</div></div>

<?php else : ?>
<p><?php echo lang('c_294'); ?><br>
<?php echo sprintf(lang('c_293'), '<u>+ '.lang('c_289').'</u>'); ?></p>
<?php endif; ?>	
