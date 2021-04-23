<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_295')); 

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
		$(".tickets").DataTable({
		"iDisplayLength": 10,
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": false,
		"order": [[ 0, "desc" ]],
		language: {
				search: "_INPUT_",
				searchPlaceholder: "'.lang('c_25').'",
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"

		}  
		});
		$(\'#ticketFilter\').on(\'change\', function() {
			var location = "'.get_page_base_url($project->getObjectURL('tickets')).'";
			var sort_by = $("#ticketFilter").val();
			if (sort_by != "") {
				location = location + "?sort_by=" + sort_by;
			}
			window.location.href = location;
			return false;
		});
	});
})(jQuery);	
</script>
');	

if(isset($object_id) && $object_id > 0) : 

$ticket_object = $this->Tickets->findById($object_id);
if(is_null($ticket_object) || $ticket_object->getProjectId() != $project->getId()
|| $ticket_object->getIsTrashed()) {

	set_flash_error(lang('e_3'));
	redirect($project->getObjectURL('tickets'));

}

?>

<div class="box box-solid">

	<div class="box-header with-border">
		<div class="row">
			<div class="col-xs-10">
			
				<h3 class="box-title">[# <?php echo $ticket_object->getTicketNo(); ?>] <a href="<?php echo get_page_base_url($ticket_object->getObjectURL()); ?>"><?php echo $ticket_object->getName();?></a> </span> &mdash; <u><?php $ticket_type = $ticket_object->getTicketType(); echo (isset($ticket_type) ? $ticket_type->getName() : lang('c_296')); ?></u></h3>
				<p><small class="text-muted"><b><?php echo lang('c_187'); ?> </b> <u><?php $ticket_created_by = $ticket_object->getCreatedBy(); echo $ticket_created_by->getName(); ?></u> &nbsp; <?php echo format_date($ticket_object->getCreatedAt()); ?></small></p>
				<p class="custom-small-grey-color"><b><?php echo lang('c_297'); ?>:</b> <?php $ticket_assignee = $ticket_object->getAssignee(); 
				echo isset($ticket_assignee) ? $ticket_assignee->getName() : lang('c_153'); ?></p>

			</div>
			
			<div class="col-xs-2">
				<?php $ticket_label = $ticket_object->getLabel();
				if(isset($ticket_label) && $ticket_label->getIsActive()) : ?>
				<span class="label" style="background-color:#<?php echo $ticket_label->getBgColorHex(); ?>; color: white; padding:3px;"><?php echo $ticket_label->getName(); ?></span>
				<?php endif; ?>

				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($ticket_object->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_189'); ?></a></li>
							<?php endif; ?>
							<?php if($ticket_object->getIsOpen()) : ?>
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($ticket_object->getCloseURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_190'); ?></a></li>
							<?php else :
							$status_text = '<font color="red">Closed</font>';
							endif; ?>
							<li class="divider"></li>
							<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($ticket_object, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
						</ul>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="box-body">
		<p><span class="more"><?php echo $ticket_object->getDescription(); ?></span></p>
		<?php if(isset($status_text)) : ?><p><b><?php echo $status_text; ?></b></p><?php endif; ?>
	</div>
</div>

<?php if($ticket_object->isCommentable()) : ?>
<h4><?php echo sprintf(lang('c_266'), lang('c_186')); ?></h4>
<?php tpl_assign('parent_object', $ticket_object);
$notify_users = (isset($ticket_assignee) && $ticket_created_by->getId() != $ticket_assignee->getId() ? array($ticket_assignee, $ticket_created_by) : array($ticket_created_by));
tpl_assign('notify_users', $notify_users);
tpl_display('comments/_comment_box'); ?>
<?php endif;?>							
			
<?php else : 

$sort_by = input_get_request('sort_by');
$tickets_status = $sort_by == "closed" ? 0 : 1;

?>

<div class="well"><div class="row">

<div class="col-md-6 col-sm-6">

	<select class="form-control custom-fixed-width-select" id="ticketFilter">
		<option value="" <?php echo ($sort_by == "" ? ' selected="selected"' : ''); ?>><?php echo lang('c_298'); ?></option>
		<option value="closed" <?php echo ($sort_by == "closed" ? ' selected="selected"' : ''); ?>><?php echo lang('c_299'); ?></option>
	</select>            

</div>
	
<div class="col-md-6 col-sm-6 text-right">
<?php if(logged_user()->isOwner() || logged_user()->isAdmin() || !logged_user()->isMember()) : ?>
<a href="javascript:void();" data-url="<?php echo get_page_base_url('tickets/create'); ?>" class="btn btn-success text-right custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_300'); ?></a>
<?php endif; ?>
</div>

</div></div>

<?php

$tickets = $this->Tickets->getByProjects(array($project->getId()), $tickets_status);
if(isset($tickets) && is_array($tickets) && count($tickets)) : ?>
<div class="table-responsive">
<table class="table table-striped table-bordered tickets">
	
	<thead>
		<th width="15%"><?php echo lang('c_151'); ?></th>
		<th><?php echo lang('c_186'); ?></th>
		<th><?php echo lang('c_297'); ?></th>
		<th><?php echo lang('c_23'); ?></th>
		<th width="10%"><?php echo lang('c_114'); ?></th>
		<th width="5%"></th>
	</thead><tbody>

	<?php foreach($tickets as $ticket) : ?>			

	<tr>

		<td><b><?php echo $ticket->getTicketNo(); ?></b></td>
		<td><b><a href="<?php echo get_page_base_url($ticket->getObjectURL()); ?>"><?php echo $ticket->getName(); ?></a></b> &mdash; <u><?php $ticket_type_i = $ticket->getTicketType(); echo (isset($ticket_type_i) ? $ticket_type_i->getName() : lang('c_296')); ?></u><br>
		<p class="custom-small-grey-color"><b><?php echo lang('c_188'); ?> </b> <u><?php echo $ticket->getCreatedByName(true); ?></u><br>
		on <?php echo format_date($ticket->getCreatedAt()); ?></p></td>
		
		<td><?php $ticket_assignee = $ticket->getAssignee(); 
		if(isset($ticket_assignee)) : ?>
		<ul class="users-list clearfix">
		<li class="custom-full-width">
		  <img src="<?php echo $ticket_assignee->getAvatar(); ?>" class="img-circle-sm" title="<?php echo $ticket_assignee->getName(); ?>">
		</li></ul><?php else : echo lang('c_153'); endif; ?>
		</td>
		
		<td><?php $ticket_project = $ticket->getProject(); 
		echo ($ticket_project ? $ticket_project->getName() : lang('c_153')); ?></td>

		<td>
		<?php $ticket_label = $ticket->getLabel();
		if(isset($ticket_label) && $ticket_label->getIsActive()) : ?>
		<span class="label" style="background-color:#<?php echo $ticket_label->getBgColorHex(); ?>; color: white; padding:5px;"><?php echo $ticket_label->getName(); ?></span>
		<?php endif; ?>
		</td>
		<td width="10%">

		<div class="pull-right">
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
				<ul class="dropdown-menu pull-right" role="menu">
					<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($ticket->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_189'); ?></a></li>
					<?php endif; ?>
					<?php if($ticket->getIsOpen()) : ?>
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($ticket->getCloseURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_190'); ?></a></li>
					<?php endif; ?>
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
<p><?php echo lang('e_2'); ?></p>
<?php endif; ?>


<?php endif; ?>
