<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", "Subscriptions"); 

tpl_assign("header_for_layout", '
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
');	

tpl_assign("footer_for_layout", '
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>

<script>

$(document).ready(function() {
	"use strict";
	$("#subsFilter").on("change", function() {
		var location = "'.get_page_base_url('admin/subscriptions').'";
		var by = $("#subsFilter").val();
		if (by != "") {
			location = location + "?by=" + by;
		}
		window.location.href = location;
		return false;
	});	
	$(".subscriptions").dataTable({
		"sScrollY": "100%",
		"scrollCollapse": true,
		"responsive": true,
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"bSort": false,	
		"deferRender": true,
		"ajax": "'.get_page_base_url("admin/subscriptions/get_subscriptions_json/?by=".$by).'",
		"columns": [
			{data: "Id", name: "Id", orderable: false, searchable: false},
			{data: "Subscription", name: "Subscription", orderable: false, searchable: false},
			{data: "User", name: "User", orderable: false, searchable: false},
			{data: "Features", name: "Features", orderable: false, searchable: false},
			{data: "Status", name: "Status", orderable: false, searchable: false},
			{data: "ExpireDate", name: "ExpireDate", orderable: false, searchable: false},
			{data: "JoinDate", name: "JoinDate", orderable: false, searchable: false},
			{data: "Actions", name: "Actions", orderable: false, searchable: false}
		],
		"oLanguage": {
			"sProcessing": "Loading..."
		}
	});
			
});
</script>
');	

?>

<p><div class="row">
	<div class="col-md-6 col-sm-6">
		<select class="form-control" id="subsFilter" class="custom-smallselect-box">
			<option value="" <?php echo ($by == "" || $by == "all" ? ' selected="selected"' : ''); ?>>All</option>
			<option value="active" <?php echo ($by == "active" ? ' selected="selected"' : ''); ?>>Active</option>
			<option value="expired" <?php echo ($by == "expired" ? ' selected="selected"' : ''); ?>>Expired</option>
		</select>            
	</div>
</div></p>

<div class="panel panel-default">

	<div class="panel-heading">
		<span class="custom-font-size18">Subscriptions</span>
	</div>

	<div class="panel-body">

		<table class="table table-hover table-bordered subscriptions">
			<thead>
			<th width="10%">#</th>
			<th width="10%">Subscription</th>
			<th width="20%">Owner</th>
			<th width="15%">Features</th>
			<th width="10%">Status</th>
			<th width="10%">Expire Date</th>
			<th width="10%">Join Date</th>
			<th width="15%">Actions</th>
			</thead>
			<tbody>
		
		</tbody></table>
	
	</div>

</div>
