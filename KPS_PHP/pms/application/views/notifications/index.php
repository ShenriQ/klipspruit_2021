<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_210')); 

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
		$(".notifications").DataTable({
		"iDisplayLength": 10,
		"bLengthChange": false,
		"bFilter": false,
		"bSort": false,
		"bInfo": false,
		});
	});
})(jQuery);
var checked = false; 
ToggleAll = function (source) {
	checkboxes = document.getElementsByName(\'itemid[]\'); 
	for(var i=0, n=checkboxes.length;i<n;i++) { 
		checkboxes[i].checked = source.checked; 
	} 
}
</script>
');	

?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-bell-o"></i>
	  <h3 class="box-title"><?php echo lang('c_210'); ?></h3>
	</div>
	
	<div class="box-body">
			
		<form action="" method="post" name="notifications_removal_form">
	
		<?php if(isset($user_notifications) && is_array($user_notifications) && count($user_notifications)) : ?>
		
		<p class="custom-p-5"><input name="read" type="submit" value="Read selected" class="btn btn-info" /> &nbsp;
		<input name="delete" type="submit" value="Delete selected" class="btn btn-default" /></p>
		<input type="hidden" name="submited" value="submited" />

		<div class="table-responsive">
		<table class="table table-hover table-bordered notifications">
			<thead>
			<th width="5%"><input type="checkbox" name="checkallitems" onclick="ToggleAll(this);"></th>
			<th width="80%"><?php echo lang('c_126'); ?></th>
			<th><?php echo lang('c_205'); ?></th>
			</thead>
			<tbody>

			<?php foreach($user_notifications as $user_notification) :
			list($date_tr_class, $log_date_value) = get_date_format_array($user_notification->getCreatedAt()); 
			$custom_tr_class = $user_notification->getIsRead() ? '' : 'unread-notification';?>
			
			<tr class="<?php echo $custom_tr_class; ?>">
				<td class="text-center"><input type="checkbox" name="itemid[]" value="<?php echo $user_notification->getId(); ?>"></td>
				<td class="text-left">
				 <h5 class="custom-m-0"><a href="<?php echo get_page_base_url($user_notification->getObjectURL());?>"><?php echo $user_notification->getSubject(); ?></a></h5>
				</td>
				<td  class="text-right"><small><span class="sl-date"><?php echo $log_date_value; ?></span></small></td>
			</tr>
			<?php endforeach; ?>

		</tbody></table></div>
		<?php else : ?>
			<p><?php echo lang('c_211'); ?></p>
		<?php endif; ?>
		
		</form>
			
	</div>

</div>

