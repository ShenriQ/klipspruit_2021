<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_22')); 

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
		$(".activities").DataTable({
		"iDisplayLength": 15,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"bSort": false,
		language: {
			search: "_INPUT_",
			searchPlaceholder: "'.lang('c_25').'",
			"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"

		}  
		});
	});
})(jQuery);
</script>
');	

?>

<?php  

$page = (int) input_get_request('page', 1);
$items_per_page = 20;
$start = ($page - 1) * $items_per_page;

$include_private = logged_user()->isMember();
$include_hidden = logged_user()->isAdmin() || logged_user()->isOwner();

list($activity_logs, $total_items) = $this->ActivityLogs->getPaginate($include_private, $include_hidden, array($project->getId()), $items_per_page, $start);
$total_pages = (int) ceil($total_items/$items_per_page);

$current_url = current_url(); unset($_GET['page']);
$query_string = http_build_query($_GET);
if($query_string) {
	$current_page_url = $current_url.'?'.$query_string;
	$is_filtered = true;
} else {
	$current_page_url = $current_url;
	$is_filtered = false;
} ?>

<div class="box box-solid">
<div class="box-body">

<?php if(isset($activity_logs) && is_array($activity_logs) && count($activity_logs)) : ?>

	<h5 class="custom-m-20"><?php echo sprintf(lang('c_523.2'), $total_items, (($page-1)*$items_per_page)+1, ($page*$items_per_page)); ?></h5>

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

	<?php if(isset($total_pages) && $total_pages > 1) : ?>
		<div class="clearfix"></div>
		<div class="pagination-container margin-top-20 margin-bottom-20">		
			<?php echo get_simple_paginate($total_pages, $page, $current_page_url, ($is_filtered ? '&page=#PAGE#' : '?page=#PAGE#')); ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<p><?php echo lang('e_2'); ?></p>
<?php endif; ?>

</div>
</div>