<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", "Subscriptions"); 

tpl_assign("header_for_layout", '
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
');	

$selected_target = input_get_request('target');
tpl_assign("footer_for_layout", '
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>

<script>
$(document).ready(function() {

	$(".orders").dataTable({
		"sScrollY": "100%",
		"scrollCollapse": true,
		"responsive": true,
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"bSort": false,	
		"bFilter": false,
		"ajax": "'.get_page_base_url("admin/orders/get_orders_json?".(isset($selected_target) ? "target=".$selected_target : "")).'",
		"columns": [
			{data: "Date", name: "Date", orderable: false, searchable: false},
			{data: "Transaction", name: "Transaction", orderable: false, searchable: false},
			{data: "Customer", name: "Customer", orderable: false, searchable: false},
			{data: "PayerEmail", name: "PayerEmail", orderable: false, searchable: false},
			{data: "TXNId", name: "TXNId", orderable: false, searchable: false},
			{data: "Status", name: "Status", orderable: false, searchable: false},
			{data: "Amount", name: "Amount", orderable: false, searchable: false}
		],
		"oLanguage": {
			"sProcessing": "Loading..."
		}
	});
			
});
</script>
');	

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<span class="custom-font-size18">Orders <?php echo (isset($selected_target) && $selected_target != "" && 
			is_valid_email($selected_target) ? ' &mdash; '.$selected_target : ''); ?></span>
	</div>
	
	<div class="panel-body">

		<table class="table table-hover table-bordered orders">
			<thead>
			<th width="10%">Date</th>
			<th width="30%">Transaction</th>
			<th width="15%">Customer</th>
			<th width="15%">PayerEmail</th>
			<th width="10%">TXN Id</th>
			<th width="10%">Status</th>
			<th width="10%">Amount</th>
			</thead>
			<tbody>
		
		</tbody></table>
	
	</div>

</div>
