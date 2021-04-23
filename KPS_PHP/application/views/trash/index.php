<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_378')); ?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('trash/do_empty'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >Empty Trash</a></p>

<div class="box box-solid">

<div class="box-header with-border">
  <i class="fa fa-trash-o"></i>
  <h3 class="box-title"><?php echo lang('c_378'); ?> </h3>
</div>

<div class="box-body no-padding">

<?php if(isset($trashed_users) && is_array($trashed_users) && count($trashed_users)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_27'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_users as $trashed_user) : ?>
		
		<tr>
			<td><b><?php echo $trashed_user->getName(); ?></b> <small><?php echo $trashed_user->isMember() ? lang('c_28') : lang('c_29'); ?></small></td>

			<td><?php $trashed_by = $trashed_user->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_user, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_user, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_companies) && is_array($trashed_companies) && count($trashed_companies)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_32'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_companies as $trashed_company) : ?>
		
		<tr>
			<td><?php echo $trashed_company->getName(); ?></td>

			<td><?php $trashed_by = $trashed_company->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_company, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_company, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table></div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_discussions) && is_array($trashed_discussions) && count($trashed_discussions)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_263'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_discussions as $trashed_discussion) : ?>
		
		<tr>
			<td><?php echo $trashed_discussion->getName(); ?></td>

			<td><?php $trashed_by = $trashed_discussion->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_discussion, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_discussion, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table></div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_comments) && is_array($trashed_comments) && count($trashed_comments)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_396'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_comments as $trashed_comment) : ?>
		
		<tr>
			<td><a href="<?php echo get_page_base_url($trashed_comment->getObjectURL()); ?>"><?php echo $trashed_comment->getName(); ?></a> by <u><?php echo $trashed_comment->getCreatedByName(true); ?></u></td>

			<td><?php $trashed_by = $trashed_comment->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_comment, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_comment, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_tasks) && is_array($trashed_tasks) && count($trashed_tasks)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_397'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_tasks as $trashed_task) : ?>
		
		<tr>
			<td><?php echo $trashed_task->getName(); ?><?php $task_list = $trashed_task->getTaskList(); if(!is_null($task_list)) : ?> in <a href="<?php echo $task_list->getObjectURL(); ?>"><?php echo $task_list->getName(); ?></a><?php endif; ?> by <u><?php echo $trashed_task->getCreatedByName(true); ?></u></td>

			<td><?php $trashed_by = $trashed_task->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_task, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_task, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_tasklists) && is_array($trashed_tasklists) && count($trashed_tasklists)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_398'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_tasklists as $trashed_tasklist) : ?>
		
		<tr>
			<td><?php echo $trashed_tasklist->getName(); ?></td>

			<td><?php $trashed_by = $trashed_tasklist->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_tasklist, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_tasklist, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_files) && is_array($trashed_files) && count($trashed_files)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_267'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_files as $trashed_file) : 
				$trashed_file_project = $trashed_file->getProject(); ?>
		<tr>
			<td><b><?php echo $trashed_file->getName(); ?></b> in <a href="<?php echo $trashed_file_project->getObjectURL('files'); ?>"><?php echo $trashed_file_project->getName(); ?></a> by <u><?php echo $trashed_file->getCreatedByName(true); ?></u></td>

			<td><?php $trashed_by = $trashed_file->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_file, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_file, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_invoices) && is_array($trashed_invoices) && count($trashed_invoices)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_172'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_invoices as $trashed_invoice) : ?>
		<tr>
			<td><?php echo $trashed_invoice->getName(); ?></td>

			<td><?php $trashed_by = $trashed_invoice->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_invoice, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_invoice, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_estimates) && is_array($trashed_estimates) && count($trashed_estimates)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_150'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_estimates as $trashed_estimate) : ?>
		<tr>
			<td><?php echo $trashed_estimate->getName(); ?></td>

			<td><?php $trashed_by = $trashed_estimate->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>

			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_estimate, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_estimate, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table></div>
		
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_tickets) && is_array($trashed_tickets) && count($trashed_tickets)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_295'); ?></h3>
	</div>
	
	<div class="box-body">

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_tickets as $trashed_ticket) : ?>
		<tr>
			<td><?php echo $trashed_ticket->getName(); ?></td>

			<td><?php $trashed_by = $trashed_ticket->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>
			
			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_ticket, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_ticket, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
	</div>

</div>

<?php endif; ?>

<?php if(isset($trashed_timesheets) && is_array($trashed_timesheets) && count($trashed_timesheets)) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
 		<h3 class="box-title"><?php echo lang('c_302'); ?></h3>
	</div>
	
	<div class="box-body">

		<table class="table table-striped table-bordered" width="100%">
		<tbody>		
		<?php foreach($trashed_timesheets as $trashed_timesheet) : ?>
		<tr>
			<td><?php echo $trashed_timesheet->getName(); ?></td>
			
			<td><?php $trashed_by = $trashed_timesheet->getTrashedBy();
			if($trashed_by) : ?><?php echo $trashed_by->getName(); else : ?><?php echo lang('c_153'); ?><?php endif; ?>
			</td>
			
			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="<?php echo get_trash_action_url($trashed_timesheet, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
							<li><a href="<?php echo get_trash_action_url($trashed_timesheet, 'delete'); ?>" onclick="return confirm('<?php echo lang('c_395'); ?>');"><?php echo lang('c_394'); ?></a></li>
							
						</ul>
					</div>
				</div>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		
	</div>

</div>

<?php endif; ?>

</div>

</div>