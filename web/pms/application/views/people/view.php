<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_523.115') . ' / ' . $user->getName()); 

tpl_assign("header_for_layout", '
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
');	

tpl_assign("footer_for_layout", '
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>
<script>
(function ($) {
	"use strict";
	$(document).ready(function() {
		$(".simple_table_list").DataTable({
		"iDisplayLength": 20,
		"bLengthChange": false,
		"bFilter": false,
		"bSort": false,
		"bInfo": false,
		});
	});
})(jQuery);
</script>');	

?>

<div class="row">
	<div class="col-md-3">
		<div class="box box-primary">
			<div class="box-body box-profile">
				<img src="<?php echo $user->getAvatar(); ?>" class="profile-user-img img-responsive img-circle" alt="<?php echo $user->getName(); ?>">

				<h3 class="profile-username text-center"><?php echo $user->getName(); ?></h3>

				<p class="text-muted text-center">
					<?php if($user->isOwner()) : ?><?php echo lang('c_235'); ?><?php 
					elseif($user->isAdmin()) : ?><?php echo lang('c_221'); ?><?php 
					elseif($user->isMember()) : ?><?php echo lang('c_28'); ?><?php 
					else : ?><?php echo lang('c_29'); ?><?php endif; ?>
				</p>

				<ul class="list-group list-group-unbordered">
					<li class="list-group-item">
						<b><?php echo lang('c_523.112'); ?></b> <a class="pull-right"><?php echo format_date($user->getCreatedAt(), 'M. Y'); ?></a>
					</li>
					<?php if($u_company = $user->getCompany()) : ?>
						<li class="list-group-item">
						<b><?php echo lang('c_80'); ?></b> <a class="pull-right"><?php echo $u_company->getName(); ?></a>
						</li>
					<?php endif; ?>
					<li class="list-group-item">
						<b><?php echo lang('c_278'); ?></b> <a class="pull-right"><?php echo ($user->isOwner() ? "All" : $user->getProjectsCount()); ?></a>
					</li>
					<?php if($user->isMember()) : ?>
					<li class="list-group-item">
						<b><?php echo lang('c_523.31'); ?></b> <a class="pull-right"><?php echo config_option('default_currency', "$"); ?><?php echo $user->getHourlyRate(); ?></a>
					</li>
					<li class="list-group-item">
						<?php $timelog_stats = $user->getTimelogStats(); ?>
					<b><?php echo lang('c_523.113'); ?></b> <a class="pull-right"><?php if(isset($timelog_stats[0]) && $timelog_stats[0]->summary > 0) : ?><?php echo number_format($timelog_stats[0]->summary, 2); else: ?>0<?php endif; ?></a>
					</li>
					<?php endif; ?>
				</ul>
				<?php if(!$user->isOwner()) : ?>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($user->getAddToProjectURL()); ?>" data-toggle="commonmodal"class="btn btn-primary"><b><i class="fa fa-plus"></i> <?php echo lang('c_223'); ?></b></a>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($user->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-info"><b><i class="fa fa-pencil"></i> <?php echo lang('c_523.114'); ?></b></a>
				<?php endif; ?>
			</div>
		</div>
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo lang('c_523.117'); ?></h3>
			</div>
			<div class="box-body">
				<?php if($user->getAddress() != "") : ?>
				<strong><i class="fa fa-map-marker margin-r-5"></i> <?php echo lang('c_77'); ?></strong>
				<p class="text-muted"><?php echo $user->getAddress(); ?></p>
				<?php endif; ?>
				<?php if($user->getPhoneNumber() != "") : ?>
				<strong><i class="fa fa-phone margin-r-5"></i> <?php echo lang('c_78'); ?></strong>
				<p class="text-muted"><?php echo $user->getPhoneNumber(); ?></p>
				<?php endif; ?>
				<?php if($user->getNotes() != "") : ?>
				<strong><i class="fa fa-file-text-o margin-r-5"></i> <?php echo lang('c_284'); ?></strong>
				<p><?php echo $user->getNotes(); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#projects" data-toggle="tab"><?php echo lang('c_278'); ?></a></li>
				<?php if($user->isMember()) : ?>
				<li><a href="#tasks" data-toggle="tab"><?php echo lang('c_286'); ?></a></li>
				<li><a href="#tickets" data-toggle="tab"><?php echo lang('c_295'); ?></a></li>
				<li><a href="#leads" data-toggle="tab"><?php echo lang('c_455'); ?></a></li>
				<?php endif; ?>
			</ul>
			<div class="tab-content">

				<div class="active tab-pane" id="projects">

					<div class="box box-solid">

						<div class="box-body">

						<?php $user_projects = $user->getActiveProjects();
						if(isset($user_projects) && is_array($user_projects) && count($user_projects)) : ?>

							<div class="table-responsive">
							<table class="table table-hover table-bordered simple_table_list">
								<thead>
									<th width="25%"><?php echo lang('c_23'); ?></th>
									<th width="15%"><?php echo lang('c_80'); ?></th>
									<th width="15%"><?php echo lang('c_192'); ?></th>
									<th width="10%"><?php echo lang('c_238'); ?></th>
									<th width="10%"><?php echo lang('c_523.23'); ?></th>
									<th width="25%"><?php echo lang('c_27'); ?></th>
								</thead>
								<tbody>

								<?php foreach($user_projects as $user_project) :
								list($date_tr_class, $log_date_value) = get_date_format_array($user_project->getCreatedAt()); ?>
								
								<tr>
								
									<td><h4 class="no-margin"><a href="<?php echo get_page_base_url($user_project->getObjectURL()); ?>"><?php echo $user_project->getName(); ?></a></h4>
										<p class="custom-small-grey-color"><?php echo lang('c_187'); ?> <em><?php echo $user_project->getCreatedByName(true); ?></em></p>
									</td>

									<td><?php echo $user_project->getCreatedForCompany()->getName(); ?></td>
									<td><?php echo format_date($user_project->getCreatedAt(), 'j M. Y'); ?></td>

									<td>
										<span <?php echo ($user_project->getDueDate() < time() && !$user_project->isCompleted() ? 'class="custom-color-red"' : '');?>>
										<?php echo format_date($user_project->getDueDate(), 'j M. Y'); ?></span>
									</td>		
									
									<?php
									$total_project_tasks = $user_project->getTasksCount(true);
									$completed_project_tasks = $total_project_tasks - $user_project->getTasksCount();
									$completed_percentage = round(($completed_project_tasks/$total_project_tasks)*100);
									?>
									<td>
										<div class="progress progress-sm">
											<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="<?php echo $completed_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $completed_percentage; ?>%">
											</div>
										</div>
										<small><?php echo $completed_project_tasks; ?>/<?php echo $total_project_tasks; ?></small>
									</td>

									<td><?php $involved_users = $user_project->getUsers(false);
									if(isset($involved_users) && is_array($involved_users) && count($involved_users)) : ?>
										<div class="item">
											<?php foreach($involved_users as $involved_user) : ?>
											<img src="<?php echo $involved_user->getAvatar(); ?>" class="img-circle-sm" title="<?php echo $involved_user->getName(); ?>">
											<?php endforeach; ?>
										</div>
									<?php else : ?>
										<?php echo lang('c_153'); ?>
									<?php endif; ?></td>

								</tr>
		
								<?php endforeach; ?>

							</tbody></table></div>

							<?php else : ?>
								<p><?php echo lang('c_211'); ?></p>
							<?php endif; ?>

						</div>
				
					</div>
		
				</div>
				<?php if($user->isMember()) : ?>
				<div class="tab-pane" id="tasks">	
					<div class="box box-solid">
						<div class="box-body">

							<?php $user_tasks = $user->getMyTasks(); ?>

							<?php  if(isset($user_tasks) && is_array($user_tasks) && count($user_tasks)) : ?>

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

								<?php foreach($user_tasks as $task) : ?>

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
				</div>
				<div class="tab-pane" id="tickets">
					<?php $user_tickets = $user->getMyTickets(); ?>
					<div class="box box-solid">
											
						<div class="box-body">

						<?php if(isset($user_tickets) && is_array($user_tickets) && count($user_tickets)) : ?>

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

							<?php foreach($user_tickets as $ticket) : ?>			

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
				</div>
				<div class="tab-pane" id="leads">
					<?php $user_leads = $user->getMyLeads(); ?>
					<div class="box box-solid">
						<div class="box-body">

							<?php if(isset($user_leads) && is_array($user_leads) && count($user_leads)) : ?>

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

							<?php foreach($user_leads as $user_lead) : ?>			

							<tr>

								<td><b><?php echo $user_lead->getForm()->getTitle(); ?></b></td>
								<td><?php $client = $user_lead->getClient(); echo (isset($client) ? $client->getName() : '<b>Guest:</b> '.$user_lead->getName());?></td>		

								<td><?php echo $user_lead->getStatus()->getName(); ?></td>
								<td><?php echo $user_lead->getSource()->getName(); ?></td>
								<td><?php echo format_date($user_lead->getCreatedAt()); ?></td>
								<td><?php echo $user_lead->getIpAddress(); ?></td>

								<td><div class="pull-right">
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
										<ul class="dropdown-menu pull-right" role="menu">
											<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($user_lead->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_508'); ?></a></li>
											<li><a href="<?php echo get_page_base_url($user_lead->getObjectURL()); ?>"><?php echo lang('c_507'); ?></a></li>
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
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>