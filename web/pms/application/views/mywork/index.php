<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_182')); ?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-tasks"></i>
	  <h3 class="box-title"><?php echo lang('c_100'); ?></h3>
	</div>
	
	<div class="box-body">
		
		<?php  if(isset($my_tasks) && is_array($my_tasks) && count($my_tasks)) : ?>

		<div class="table-responsive">
		<table class="table table-bordered table-striped mytasks">

		<thead>
			<th colspan="2"><?php echo lang('c_183'); ?></th>
			<th><?php echo lang('c_184'); ?></th>
			<th><?php echo lang('c_23'); ?></th>
			<th width="15%"><?php echo lang('c_138'); ?></th>
			<th width="5%"></th>
		</thead>

		<tbody>
		
			<?php foreach($my_tasks as $task) : ?>
	
			<tr<?php echo ($task->getIsHighPriority() ? ' class="custom-background-light-yellow"' : ''); ?>>
				
				<td class="text-center"><a href="<?php echo get_page_base_url($task->getCompleteURL()); ?>"><i class="fa fa-square-o"></i></a></td>
	
				<td><a href="<?php echo get_page_base_url($task->getObjectURL()); ?>"><?php echo $task->getName(); ?></a>
				<?php $task_label = $task->getLabel();
				if(isset($task_label) && $task_label->getIsActive()) : ?>
				<small class="label" style="background-color:#<?php echo $task_label->getBgColorHex(); ?>;margin-left:5px;"><?php echo $task_label->getName(); ?></small>
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
					if($due_date < time()) : ?><p><small class="custom-color-red"><?php echo lang('c_154'); ?></small></p><?php endif;
				else : ?>None<?php endif; ?></td>
				
				<td valign="top">

				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($task->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_185'); ?></a></li>
							<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($task, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
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

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-ticket"></i>
	  <h3 class="box-title"><?php echo lang('c_102'); ?></h3>
	</div>
	
	<div class="box-body">
	
	<?php if(isset($my_tickets) && is_array($my_tickets) && count($my_tickets)) : ?>
	
		<div class="table-responsive">
		<table class="table table-bordered table-striped tickets">
		
		<thead>
			<th width="15%"><?php echo lang('c_151'); ?></th>
			<th><?php echo lang('c_186'); ?></th>
			<th><?php echo lang('c_187'); ?></th>
			<th><?php echo lang('c_23'); ?></th>
			<th width="10%"><?php echo lang('c_114'); ?></th>
			<th width="5%"></th>
		</thead><tbody>
	
		<?php foreach($my_tickets as $ticket) : ?>			
	
		<tr>
	
			<td><b><?php echo $ticket->getTicketNo(); ?></b></td>
			<td><b><a href="<?php echo get_page_base_url($ticket->getObjectURL()); ?>"><?php echo $ticket->getName(); ?></a></b> &mdash; <u><?php echo $ticket->getTicketType()->getName(); ?></u><br>
			<p class="custom-small-grey-color"><b><?php echo lang('c_188'); ?> </b> <u><?php echo $ticket->getCreatedByName(true); ?></u><br>
			<?php echo format_date($ticket->getCreatedAt()); ?></p></td>
			
			<td><?php $ticket_created_by = $ticket->getCreatedBy();
			echo ($ticket_created_by ? $ticket_created_by->getName() : lang('c_153')); ?></td>
	
			<td><?php $ticket_project = $ticket->getProject(); 
			echo ($ticket_project ? $ticket_project->getName() : lang('c_153')); ?></td>
	
			<td>
			<?php $ticket_label = $ticket->getLabel();
			if(isset($ticket_label) && $ticket_label->getIsActive()) : ?>
			<span class="label" style="background-color:#<?php echo $ticket_label->getBgColorHex(); ?>;"><?php echo $ticket_label->getName(); ?></span>
			<?php endif; ?>
			</td>
			<td width="10%" valign="top">
	
			<div class="pull-right">
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
					<ul class="dropdown-menu pull-right" role="menu">
						<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
						<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($ticket->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_189'); ?></a></li>
						<?php endif; ?>
						<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($ticket->getCloseURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_190'); ?></a></li>
						<li><a href="<?php echo get_page_base_url($ticket->getObjectURL()); ?>"><?php echo lang('c_301'); ?></a></li>
						<li class="divider"></li>
						<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($ticket, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
					</ul>
				</div>
			</div>
	
			</td>
			
		</tr>
	
		<?php endforeach; ?>
		
		</tbody></table></div>
	
	<?php else : ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>
	
	</div>
	
</div>


<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-newspaper-o"></i>
	  <h3 class="box-title"><?php echo lang('c_513'); ?></h3>
	</div>
	
	<div class="box-body">
	
	
	<?php if(isset($my_leads) && is_array($my_leads) && count($my_leads)) : ?>

	<div class="table-responsive">
	<table class="table table-bordered table-striped leads">
	
	<thead>
		<th><?php echo lang('c_505'); ?></th>
		<th><?php echo lang('c_216'); ?></th>
		<th><?php echo lang('c_465'); ?></th>
		<th><?php echo lang('c_461'); ?></th>
		<th><?php echo lang('c_205'); ?></th>
		<th><?php echo lang('c_506'); ?></th>
		<th width="10%"></th>
	</thead><tbody>

	<?php foreach($my_leads as $my_lead) : ?>			

	<tr>

		<td><b><?php echo $my_lead->getForm()->getTitle(); ?></b></td>
		<td><?php $client = $my_lead->getClient(); echo (isset($client) ? $client->getName() : '<b>Guest:</b> '.$my_lead->getName());?></td>		

		<td><?php echo $my_lead->getStatus()->getName(); ?></td>
		<td><?php echo $my_lead->getSource()->getName(); ?></td>
		<td><?php echo format_date($my_lead->getCreatedAt()); ?></td>
		<td><?php echo $my_lead->getIpAddress(); ?></td>

		<td><div class="pull-right">
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($my_lead->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_508'); ?></a></li>
					<li><a href="<?php echo get_page_base_url($my_lead->getObjectURL()); ?>"><?php echo lang('c_507'); ?></a></li>
				</ul>
			</div>
		</div>
		</td>
		
	</tr>

	<?php endforeach; ?>
	
	</tbody></table></div>

	<?php else : ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>

</div>

</div>