<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_278')); 

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
		$(".projects").DataTable({
		"iDisplayLength": 10,
		"bLengthChange": true,
		"bFilter": true,
		"bSort": false,
		"bInfo": false,
		language: {
				search: "_INPUT_",
				searchPlaceholder: "'.lang('c_25').'",
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"

		}  
		});
		$(\'#projectFilter\').on(\'change\', function() {
			
			var location = "'.get_page_base_url('projects').'";
			var sort_by = $("#projectFilter").val();

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

?>

<p><div class="row">
	<div class="col-md-6 col-sm-6">
		<select class="form-control custom-fixed-width-select" id="projectFilter">
			<option value="" <?php echo ($sort_by == "" ? ' selected="selected"' : ''); ?>><?php echo lang('c_279'); ?></option>
			<option value="completed" <?php echo ($sort_by == "completed" ? ' selected="selected"' : ''); ?>><?php echo lang('c_280'); ?></option>
		</select>            
	</div>
	<div class="col-md-6 col-sm-6 text-right">
		<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
		<a href="javascript:void();" data-url="<?php echo get_page_base_url('projects/add'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_247'); ?></a>
		<?php endif; ?>
	</div>
</div>
</p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-building"></i>
	  <h3 class="box-title"><?php echo lang('c_278'); ?></h3>
	</div>
	
	<div class="box-body">
			
<?php if(isset($projects) && is_array($projects) && count($projects)) : ?>
	<div class="table-responsive">
	<table class="table table-striped table-bordered projects">
	
	<thead>
		<th width="25%"><?php echo lang('c_23'); ?></th>
		<th width="15%"><?php echo lang('c_80'); ?></th>
		<th width="10%"><?php echo lang('c_192'); ?></th>
		<th width="10%"><?php echo lang('c_238'); ?></th>
		<th width="10%"><?php echo lang('c_523.23'); ?></th>
		<th><?php echo lang('c_27'); ?></th>
		<th width="15%"><?php echo lang('c_114'); ?></th>
	</thead>
	
	<tbody>

	<?php foreach($projects as $project) : ?>			

	<tr>

		<td><h4 class="no-margin"><a href="<?php echo get_page_base_url($project->getObjectURL()); ?>"><?php echo $project->getName(); ?></a></h4>
		<p class="custom-small-grey-color"><?php echo lang('c_187'); ?> <em><?php echo $project->getCreatedByName(true); ?></em></p>
		</td>

		<td><?php echo $project->getCreatedForCompany()->getName(); ?></td>
		
		<td><?php echo format_date($project->getCreatedAt(), 'j M. Y'); ?></td>

		<td>
			<span <?php echo ($project->getDueDate() < time() && !$project->isCompleted() ? 'class="custom-color-red"' : '');?>>
			<?php echo format_date($project->getDueDate(), 'j M. Y'); ?></span>
		</td>		
		
		<?php
		$total_project_tasks = $project->getTasksCount(true);
		$completed_project_tasks = $total_project_tasks - $project->getTasksCount();
		$completed_percentage = $total_project_tasks > 0 ? round(($completed_project_tasks/$total_project_tasks)*100) : 0;
		?>
		<td>
			<div class="progress progress-sm">
				<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="<?php echo $completed_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $completed_percentage; ?>%">
            	</div>
            </div>
			<small><?php echo $completed_project_tasks; ?>/<?php echo $total_project_tasks; ?></small>
		</td>

		<td><?php $involved_users = $project->getUsers(false);
		if(isset($involved_users) && is_array($involved_users) && count($involved_users)) : ?>
		<div class="item">
		<?php foreach($involved_users as $involved_user) : ?>
		  <img src="<?php echo $involved_user->getAvatar(); ?>" class="img-circle-sm" title="<?php echo $involved_user->getName(); ?>">
		<?php endforeach; ?>
		</div>
		<?php else : ?>
		<?php echo lang('c_153'); ?>
		<?php endif; ?></td>
				
		<td>
		
		<?php $project_label = $project->getLabel();
		if(isset($project_label) && $project_label->getIsActive()) : ?>
		<span class="label" style="background-color:#<?php echo $project_label->getBgColorHex(); ?>; color: white; padding:5px;"><?php echo $project_label->getName(); ?></span>
		<?php endif; ?>
		<?php if(logged_user()->isOwner() || (logged_user()->isAdmin() && logged_user()->isProjectUser($project)) ) : ?>
		<div class="pull-right">
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
				<ul class="dropdown-menu pull-right" role="menu">
					<?php if(!$project->isCompleted()) : ?><li><a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_248'); ?></a></li>
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getManagePeopleURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_254'); ?></a></li>
					<li class="divider"></li>
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($project->getCompleteURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_281'); ?></a></li><?php else : ?>
					<li><a href="<?php echo get_page_base_url($project->getReopenURL()); ?>"><?php echo lang('c_282'); ?></a></li><?php endif; ?>

				</ul>
			</div>
		</div>
		<?php endif; ?>
		</td>
		
	</tr>

	<?php endforeach; ?>
	
	</tbody></table></div>

<?php else : ?>
<?php echo lang('c_283'); ?>
<?php endif; ?>

</div>

</div>

