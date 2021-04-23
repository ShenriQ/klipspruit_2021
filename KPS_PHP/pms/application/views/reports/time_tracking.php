<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_311'));

tpl_assign("header_for_layout", '
<link href="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.css').'" rel="stylesheet">
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
');	

tpl_assign("footer_for_layout", '
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>
<script type="text/javascript" src="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.js').'"></script>
<script type="text/javascript">
(function ($) {
	"use strict";
	$(document).ready(function(){
		$(".timelogs").DataTable({
		"iDisplayLength": 25,
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": false,
		language: {
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

$cache_projects = array();
$cache_members = array();
$total_hours = 0;

if(isset($timelog_stats) && is_array($timelog_stats) && count($timelog_stats)) {

	foreach($timelog_stats as $timelog_stat) {

		$total_hours = $total_hours + $timelog_stat->summary;

		$project_id_x = $timelog_stat->project_id;
		if(!isset($cache_projects[$project_id_x])) {
			$cache_projects[$project_id_x] = $this->Projects->findById($project_id_x);
		}

		$member_id_x = $timelog_stat->member_id;
		if(!isset($cache_members[$member_id_x])) {
			$cache_members[$member_id_x] = $this->Users->findById($member_id_x);
		}
		
	}

}

?>
<form action="" method="POST" id="_reports">
<div class="row">     
  <div class="col-md-4">
	<div class="form-group">
	<?php $members_options = array(lang('c_312'));
	
	if(isset($members) && is_array($members) && count($members)) :
		foreach($members as $member_rt) :
			$members_options[$member_rt->getId()] = $member_rt->getName();
		endforeach;
	endif;
	
	echo select_box("member_id", $members_options, $member_id, ' id="memberOptions" class="form-control"');
	
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
	  <input class="btn btn-primary" name="send" type="submit" value="<?php echo lang('c_306'); ?>" required />
	</div>
  </div>
</div>
</form>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-area-chart"></i>
	  <h3 class="box-title"><?php echo lang('c_311'); ?></h3>
	</div>
	
	<div class="box-body">
	  	  
	  <div class="row">
	
		 <div class="col-lg-6 col-md-12">
			  <h5><?php echo lang('c_307'); ?></h5>
			  <h4 class="custom-pb-15"><?=$stats_start;?> &mdash; <?=$stats_end;?></h4>
		 </div>
	 
		 <div class="col-lg-6 col-md-12">
			<div class="col-sm-4 text-right">
				<h4><?php echo lang('c_278'); ?></h4>
				<h2><?php echo count($cache_projects); ?></h2>
			</div>
			<div class="col-sm-4 text-right">
				<h4><?php echo lang('c_200'); ?></h4>
				<h2><?php echo count($cache_members); ?></h2>
			</div>
			<div class="col-sm-4 text-right">
				<h4><?php echo lang('c_314'); ?></h4>
				<h2 class="custom-color-green"><?php echo number_format($total_hours, 2); ?></h2>
			</div>
		 </div>
	 	
	  </div>
	
	  <hr />

	  <div class="row">

	  <div class="col-lg-12">

	  <?php if(isset($timelog_stats) && is_array($timelog_stats) && count($timelog_stats)) : ?>
	
		<div class="table-responsive">
		<table class="table table-striped table-bordered timelogs">
		
		<thead>
			<th><?php echo lang('c_28'); ?></th>
			<th><?php echo lang('c_23'); ?></th>
			<th><?php echo lang('c_313'); ?></th>
		</thead><tbody>
	
		<?php foreach($timelog_stats as $timelog_stat) : ?>			
	
		<tr>
	
			<td><?php $member_c = $cache_members[$timelog_stat->member_id];
			echo isset($member_c) ? $member_c->getName() : lang('c_153'); ?></td>

			<td><?php $project_c = $cache_projects[$timelog_stat->project_id];
			echo isset($project_c) ? '<a href="'.get_page_base_url($project_c->getObjectURL()).'">'.$project_c->getName().'</a>' : lang('c_153'); ?></td>

			<td><?php echo $timelog_stat->summary; ?></td>
			
		</tr>
	
		<?php endforeach; ?>
		
		</tbody></table></div>
	
	<?php else : ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>

	</div>
	</div>
	
</div>
</div>