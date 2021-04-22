<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_302'));

tpl_assign("header_for_layout", '
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
<link href="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.css').'" rel="stylesheet">
');	

tpl_assign("footer_for_layout", '
<script type="text/javascript" src="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.js').'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>
<script type="text/javascript" src="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.js').'"></script>
<script>
(function ($) {
	"use strict";
	$(document).ready(function() {
		$(".timelogs").DataTable({
			"iDisplayLength": 10,
			"bLengthChange": true,
			"bFilter": true,
			"bInfo": false,
			"bSort": true,
			"order": [[ 0, "desc" ]],
			"language": {
					search: "_INPUT_",
					searchPlaceholder: "'.lang('c_25').'",
					"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"

			}  
		});
		$(".datepicker").datetimepicker({format:\'Y-m-d\', timepicker:false});
	});
})(jQuery);	
</script>
');	

$total_hours = 0;
$total_billable_hours = 0;

$pending_hours = 0;
$pending_billable_hours = 0;

$approved_hours = 0;
$approved_billable_hours = 0;

$total_paid_hours = 0;
$total_pending_hours = 0;

if(isset($timelogs) && is_array($timelogs) && count($timelogs)) {

	foreach($timelogs as $timelog) {

		$current_hours = round(($timelog->getEndTime()-$timelog->getStartTime())/3600, 2);
		$total_hours += $current_hours;

		if($timelog->getIsBillable()) {
			$total_billable_hours += $current_hours;
			if($timelog->getIsPaid()) {
				$total_paid_hours += $current_hours;
			} else {
				$total_pending_hours += $current_hours;
			}
		}

		if($timelog->getIsApproved()) {
			$approved_hours += $current_hours;
			if($timelog->getIsBillable()) {
				$approved_billable_hours += $current_hours;
			}	
		} else {
			$pending_hours += $current_hours;
			if($timelog->getIsBillable()) {
				$pending_billable_hours += $current_hours;
			}
		}
		
	}

}

?>

<?php if(!isset($is_project_panel)) : ?>

<form action="" method="GET" id="_timesheet_filters">
<div class="row">
	<div class="col-md-4 col-sm-6">
		<div class="form-group">
		<?php $projects_options = array(lang('c_344'));
		
		if(isset($projects) && is_array($projects) && count($projects)) :
			foreach($projects as $project_dp) :
				$projects_options[$project_dp->getId()] = $project_dp->getName();
			endforeach;
		endif;
		
		echo select_box("project_id", $projects_options, $project_id, ' id="projectOptions" class="form-control"');
		
		?>	
		</div>
	</div>
	<div class="col-md-3">
	<div class="form-group">
	  <input class="form-control datepicker" name="start" id="start" type="text" value="<?=$stats_start_short;?>" placeholder="<?php echo lang('c_192'); ?>" readonly /> 
	</div>
  </div>
  <div class="col-md-3">
	<div class="form-group">
	  <input class="form-control datepicker" name="end" id="end" type="text" value="<?=$stats_end_short;?>" placeholder="<?php echo lang('c_198'); ?>" readonly />
	</div>
  </div>
  <div class="col-md-2">
	<div class="form-group">
	  <input class="btn btn-primary" type="submit" value="<?php echo lang('c_306'); ?>" required />
	  <a class="btn btn-info" href="<?php echo get_page_base_url('timesheet'); ?>"><?php echo lang('c_523.22'); ?></a>
	</div>
  </div>
</div>
</form>

<?php endif; ?>

<div class="row">
<div class="col-xs-12">
<div class="box box-solid">

	<?php if(!isset($is_project_panel)) : ?>
	<div class="box-header with-border">
	  <i class="fa fa-calendar-check-o"></i>
	  <h3 class="box-title"><?php echo lang('c_302'); ?></h3>
	  <div class="box-tools pull-right">
		<?php if(logged_user()->isMember()) : ?>
			<a href="javascript:void();" data-url="<?php echo get_page_base_url('timesheet/create'.(isset($project) ? '/'.$project->getId() : '')); ?>" class="btn btn-success" data-toggle="commonmodal" >+ <?php echo lang('c_364'); ?></a>
		<?php endif; ?>
	  </div>
	</div>
	<?php endif; ?>
	
	<div class="box-body table-responsive<?php echo (isset($is_project_panel) ? ' no-padding' : ''); ?>">
	
	<?php if(isset($is_project_panel)) : ?>
		<div class="row custom-mb-10">
			<div class="col-md-12 text-left">
			<?php if(logged_user()->isMember()) : ?>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url('timesheet/create'.(isset($project) ? '/'.$project->getId() : '')); ?>" class="btn btn-success" data-toggle="commonmodal" >+ <?php echo lang('c_364'); ?></a>
			<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="row custom-m-3 well">
	
		<div class="col-lg-3 col-md-12">
			<h4><?php echo lang('c_307'); ?></h4>
			<?php if(isset($is_project_panel)) : ?>
				<h4> &mdash; </h4>
			<?php else: ?>
				<h4 class="custom-pb-15"><?=$stats_start;?> &mdash; <?=$stats_end;?></h4>
			<?php endif; ?>
		</div>

		<div class="col-lg-9 col-md-12">
			<div class="col-sm-3 text-right">
				<h4><?php echo lang('c_523.21'); ?> <small>(<?php echo lang('c_523.35'); ?>)</small></h4>
				<h3><?php echo number_format($total_hours, 2); ?> <small>(<?php echo number_format($total_billable_hours, 2); ?>)</small></h3>
			</div>
			<div class="col-sm-3 text-right">
				<h4><?php echo lang('c_523.19'); ?> <small>(<?php echo lang('c_523.35'); ?>)</small></h4>
				<h3 class="custom-color-red"><?php echo number_format($pending_hours, 2); ?> <small>(<?php echo number_format($pending_billable_hours, 2); ?>)</small></h3>
			</div>
			<div class="col-sm-3 text-right">
				<h4><?php echo lang('c_523.20'); ?> <small>(<?php echo lang('c_523.35'); ?>)</small></h4>
				<h3><?php echo number_format($approved_hours, 2); ?> <small>(<?php echo number_format($approved_billable_hours, 2); ?>)</small></h3>
			</div>
			<div class="col-sm-3 text-right">
				<h4><?php echo lang('c_523.37'); ?> <small>(<?php echo lang('c_523.38'); ?>)</small></h4>
				<h3 class="custom-color-green"><?php echo $total_paid_hours; ?> <small>(<?php echo $total_pending_hours; ?>)</small></h3>
			</div>
		</div>
		
	</div>

	<hr />
		
	<?php if(isset($timelogs) && is_array($timelogs) && count($timelogs)) : ?>
	
		<table class="table table-bordered table-striped timelogs">

		<thead>
			<th width="7%"><?php echo lang('c_365'); ?></th>
			<th><?php echo lang('c_28'); ?></th>
			<th width="15%"><?php echo lang('c_23'); ?></th>
			<th width="15%"><?php echo lang('c_183'); ?></th>
			<th><?php echo lang('c_366'); ?></th>
			<th><?php echo lang('c_523.17'); ?></th>
			<th><?php echo lang('c_523.34'); ?></th>
			<th><?php echo lang('c_205'); ?></th>
			<?php if(logged_user()->isOwner()) : ?>
				<th><?php echo lang('c_204'); ?></th>
			<?php endif; ?>
			<?php if(logged_user()->isMember()) : ?>
			<th></th>
			<?php endif; ?>
		</thead><tbody>

		<?php foreach($timelogs as $timelog) : 
			$member = $timelog->getMember(); ?>			

			<tr>
				<td><?php echo $timelog->getId(); ?></td>
	
				<td><ul class="users-list clearfix">
					<li class="custom-full-width">
						<img src="<?php echo $member->getAvatar(); ?>" class="img-circle-md" title="<?php echo $member->getName(); ?>">
					</li>
				</ul>
				</td>
				
				<td><?php $timlog_project = $timelog->getProject(); ?>
					<?php if (!is_null($timlog_project)): ?><h5><a href="<?php echo base_url($timlog_project->getObjectURL()); ?>"><?php echo $timlog_project->getName(); ?></a></h5><?php else: ?>&mdash;<?php endif; ?>
				</td>
				
				<td><?php $timlog_task = $timelog->getTask(); ?>
					<?php if (!is_null($timlog_task)): ?><h5><a href="<?php echo base_url($timlog_task->getObjectURL()); ?>"><?php echo $timlog_task->getName(); ?></a></h5><?php else: ?>&mdash;<?php endif; ?>
				</td>				
				
				<td><h4 class="custom-m-0"><?php echo round(($timelog->getEndTime()-$timelog->getStartTime())/3600, 2); ?> <?php echo lang('c_313'); ?> &mdash; <?php echo ($timelog->getIsApproved() ? '<small class="custom-color-green">'.lang('c_362').'</small>' : '<small class="custom-color-red">'.lang('c_363').'</small>'); ?></h4><br>
					<p><small><b><?php echo lang('c_523.31'); ?>:</b> <?php echo config_option('default_currency', "$") . $timelog->getHourlyRate(); ?></small></p>
					<p><small><b><?php echo lang('c_368'); ?>: </b><?php echo format_date($timelog->getStartTime(), "m-d-Y H:i"); ?><br>
					<b><?php echo lang('c_369'); ?>:</b> <?php echo format_date($timelog->getEndTime(), "m-d-Y H:i"); ?><br>
					<br><?php echo $timelog->getMemo(); ?></small></p>
				</td>
			
				<td>
					<?php echo $timelog->getIsTimer() ? lang('c_523.13') : lang('c_523.14') ;?>
				</td>
				
				<td>
					<?php $invoice_status_code = $timelog->getIsBillable() ? $timelog->getInvoiceStatusCode() : 0;
					echo $timelog->getIsBillable() ? '<b>' . lang('c_523.35.'.$invoice_status_code) . '</b>' : lang('c_523.36'); ?>
				</td>

				<td><?php echo format_date($timelog->getCreatedAt(), "m-d-Y"); ?></td>
				
				<?php if(logged_user()->isOwner()) : ?>
					<td><?php echo $timelog->getCreatedBy()->getName(); ?></td>
				<?php endif; ?>

				<?php if(logged_user()->isMember() ) : ?>
				<td>
					<?php if($invoice_status_code == 0) : ?>
						<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
						
						<div class="pull-right">
							<div class="btn-group">
								<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
								<ul class="dropdown-menu pull-right" role="menu">
									<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($timelog->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_359'); ?></a></li>
									<li class="divider"></li>
									<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($timelog, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
								</ul>
							</div>
						</div>
						
						<?php else : ?>
							<a href="javascript:void();" data-url="<?php echo get_trash_action_url($timelog, 'move'); ?>" data-toggle="commonmodal" class="btn btn-xs btn-danger"><?php echo lang('c_31'); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</td>											
				<?php endif; ?>

			</tr>

		<?php endforeach; ?>
		
		</tbody></table>
	
	<?php else : ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>

</div></div>
</div></div>