<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_100')); ?>

<div class="panel panel-default">

	<div class="panel-heading">
		<span class="custom-font-size18"><?php echo lang('c_100'); ?></span>
	</div>
	
	<div class="panel-body">
	
		<?php  if(isset($my_tasks) && is_array($my_tasks) && count($my_tasks)) : ?>
		<div class="table-responsive">
		<table class="table table-hover table-bordered mytasks">

		<thead>
			<th width="5%"></th>
			<th><?php echo lang('c_183'); ?></th>
			<th><?php echo lang('c_184'); ?></th>
			<th><?php echo lang('c_23'); ?></th>
			<th width="20%"><?php echo lang('c_138'); ?></th>
		</thead>

		<tbody>
		
			<?php foreach($my_tasks as $task) : ?>
	
			<tr<?php echo ($task->getIsHighPriority() ? ' style="background-color:#FFECD9;"' : ''); ?>>
				
				<td class="text-center"><a href="<?php echo get_page_base_url($task->getCompleteURL()); ?>"><i class="fa fa-square-o"></i></a></td>
	
				<td><span class="more"><?php echo $task->getDescription(); ?></span>
				<?php $task_label = $task->getLabel();
				if(isset($task_label) && $task_label->getIsActive()) : ?>
				<small style="background-color:#<?php echo $task_label->getBgColorHex(); ?>; color: white; padding:2px; margin-left:5px;"><?php echo $task_label->getName(); ?></small>
				<?php endif; ?>
				</td>

				<td><?php $task_list = $task->getTaskList(); 
				echo '<a href="'.$task_list->getObjectURL().'">'.$task_list->getName().'</a>'; ?>
				</td>
						
				<td><?php $task_project = $task->getProject();
				echo '<a href="'.$task_project->getObjectURL().'">'.$task_project->getName().'</a>'; ?>
				</td>
				
				<td><?php $due_date = $task->getDueDate();
				if(isset($due_date)) :
					echo format_date($due_date, 'j M. Y');
					if($due_date < time()) : ?><p><small class="custom-color-red">Overdue</small></p><?php endif;
				else : ?>None<?php endif; ?>
	
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($task->getEditURL()); ?>" data-toggle="commonmodal">Edit Task</a></li>
							<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($task, 'move'); ?>" data-toggle="commonmodal">Move to Trash</a></li>
						</ul>
					</div>
				</div>
				
				</td>
				
			</tr>
					
			<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
		<?php else : ?>
		<p><?php echo lang('e_2'); ?></p>
		<?php endif; ?>

	</div>
	
</div>