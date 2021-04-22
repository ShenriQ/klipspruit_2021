<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_455')); 

tpl_assign("header_for_layout", '
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
');	

tpl_assign("footer_for_layout", '
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>

<script>
(function ($) {
	"use strict";
	$(document).ready(function() {
		$(".leads").DataTable({
			"iDisplayLength": 10,
			"bLengthChange": false,
			"order": [[ 0, "desc" ]],
			dom: "Bfrtip",
			language: {
				search: "_INPUT_",
				searchPlaceholder: "'.lang('c_25').'",
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"
			},
			buttons: [
				{
					extend: "copyHtml5",
					exportOptions: {
						columns: [ 0, 1, 2, 4, 5, 6, 7]
					}
				},
				{
					extend: "excelHtml5",
					exportOptions: {
						columns: [ 0, 1, 2, 4, 5, 6, 7]
					}
				},
				{
					extend: "pdfHtml5",
					exportOptions: {
						columns: [ 0, 1, 2, 4, 5, 6, 7]
					}
				}
			]	
		});
	});
})(jQuery);
</script>
');	
?>

<p><div class="row">
	<div class="col-md-12">
		<a href="<?php echo get_page_base_url('forms'); ?>" class="btn btn-success custom-m-3">+ <?php echo lang('c_469'); ?></a>
	</div>
</div>
</p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-newspaper-o"></i>
	  <h3 class="box-title"><?php echo lang('c_455'); ?></h3>
	</div>
	
	<div class="box-body">
	
	
<?php if(isset($leads) && is_array($leads) && count($leads)) : ?>

	<div class="table-responsive">
	<table class="table table-bordered table-striped leads">
	
	<thead>
		<th><?php echo lang('c_505'); ?></th>
		<th><?php echo lang('c_23'); ?></th>
		<th><?php echo lang('c_216'); ?></th>
		<th><?php echo lang('c_297'); ?></th>
		<th><?php echo lang('c_465'); ?></th>
		<th><?php echo lang('c_461'); ?></th>
		<th><?php echo lang('c_205'); ?></th>
		<th><?php echo lang('c_506'); ?></th>
		<th width="10%"></th>
	</thead><tbody>

	<?php foreach($leads as $lead) : ?>			

	<tr>

		<td><b><?php echo $lead->getForm()->getTitle(); ?></b></td>

		<td><?php $lead_project = $lead->getProject(); 
		if(isset($lead_project)) : ?>
		  <a href="<?php echo $lead_project->getObjectURL(); ?>"><?php echo $lead_project->getName(); ?></a>
		<?php else : echo lang('c_153'); endif; ?>
		</td>

		<td><?php $client = $lead->getClient(); echo (isset($client) ? $client->getName() : '<b>Guest:</b> '.$lead->getName());?></td>		

		<td><?php $lead_assignee = $lead->getAssignee(); 
		if(isset($lead_assignee)) : ?>
		<ul class="users-list clearfix">
		<li class="custom-full-width">
		  <img src="<?php echo $lead_assignee->getAvatar(); ?>" class="img-circle-sm" alt="<?php echo $lead_assignee->getName(); ?>">
		</li></ul><?php else : echo lang('c_153'); endif; ?>
		</td>

		<td><?php echo $lead->getStatus()->getName(); ?></td>
		<td><?php echo $lead->getSource()->getName(); ?></td>
		<td><?php echo format_date($lead->getCreatedAt()); ?></td>
		<td><?php echo $lead->getIpAddress(); ?></td>

		<td><div class="pull-right">
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($lead->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_508'); ?></a></li>
					<li><a href="<?php echo get_page_base_url($lead->getObjectURL()); ?>"><?php echo lang('c_507'); ?></a></li>
					<li class="divider"></li>
					<li><a href="<?php echo get_page_base_url($lead->getDeleteURL()); ?>" onclick="return confirm('<?php echo lang('c_209'); ?>')"><?php echo lang('c_50'); ?></a></li>
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