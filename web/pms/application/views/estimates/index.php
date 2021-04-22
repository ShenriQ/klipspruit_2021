<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_150')); 

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
		$(".estimates").DataTable({
		"iDisplayLength": 15,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"order": [[ 0, "desc" ]],
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

$default_currency = config_option('default_currency', "$");

?>

<p><div class="row">
	<div class="col-md-12 col-sm-12">
		<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
		<a href="javascript:void();" data-url="<?php echo get_page_base_url('estimates/create'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_133'); ?></a>
		<?php endif; ?>
	</div>
</div>
</p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-file-text-o"></i>
	  <h3 class="box-title"><?php echo lang('c_150'); ?></h3>
	</div>
	
	<div class="box-body">
	
	
<?php if(isset($estimates) && is_array($estimates) && count($estimates)) : ?>

	<div class="table-responsive">
	<table class="table table-striped table-bordered estimates">
	
	<thead>
		<th><?php echo lang('c_151'); ?></th>
		<th><?php echo lang('c_29'); ?></th>
		<th><?php echo lang('c_23'); ?></th>
		<th><?php echo lang('c_152'); ?></th>
		<th><?php echo lang('c_114'); ?></th>
		<th><?php echo lang('c_138'); ?></th>
		<th></th>
	</thead><tbody>

	<?php foreach($estimates as $estimate) : ?>			

	<tr>

		<td><b><a href="<?php echo get_page_base_url($estimate->getObjectURL()); ?>" target="_blank"><?php echo $estimate->getEstimateNo(); ?></a></b></td>
		<td><b><?php echo ($estimate->getClient() ? '<a href="' . get_page_base_url($estimate->getClient()->getObjectURL()) . '">' . $estimate->getClient()->getName() . '</a></b><br><small>' . ($estimate->getClient()->getCompany() ? '<a href="' . get_page_base_url($estimate->getClient()->getCompany()->getObjectURL()) . '">'.$estimate->getClient()->getCompany()->getName().'</a>' : '<span class="custom-small-grey-color">' . lang('c_523.52') . '</span>')  . '</small>' : lang('c_153')); ?></td>
		<td><?php echo ($estimate->getProject() ? '<a href="' . get_page_base_url($estimate->getProject()->getObjectURL()) . '">' . $estimate->getProject()->getName() . '</a>' : lang('c_153')); ?></td>
		<td><?php echo $default_currency; ?><?php echo number_format($estimate->getTotalAmount(), 2); ?></td>
		<td><?php 
		  if($estimate->getStatus()) {
			  $status = "<span class='label label-success'>".lang('c_136')."</span>";
		  } else {
			  $status = "<span class='label label-danger'>".lang('c_137')."</span>"; 
		  }
		  echo $status;
		?></td>
		<td><?php $estimate_duedate = $estimate->getDueDate(); 
		if(isset($estimate_duedate)) { 
			echo format_date($estimate_duedate, "m-d-Y");
			if($estimate_duedate < time() && $estimate->getStatus()) echo '&nbsp; <b class="custom-color-red">'.lang('c_154').'</b>';
			} else { echo lang('c_153');  }
			?>	
		</td>
		<td width="10%">

		<div class="pull-right">
			<div class="btn-group">
				<?php if(logged_user()->isOwner()) : ?>
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($estimate->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_134'); ?></a></li>
					<li><a href="<?php echo get_page_base_url($estimate->getObjectURL()); ?>" target="_blank"><?php echo lang('c_156'); ?></a></li>
					<li><a href="<?php echo get_page_base_url($estimate->getDownloadURL()); ?>" target="_blank"><?php echo lang('c_523.77'); ?></a></li>
					<li><a href="<?php echo get_page_base_url($estimate->getSendNotificationURL()).'?ref='.base64_encode(current_url()); ?>"><?php echo lang('c_523.97'); ?></a></li>
					<li><a href="<?php echo get_page_base_url($estimate->getConvertToInvoiceURL()); ?>"  onclick="return confirm('<?php echo lang('c_157'); ?>');"><?php echo lang('c_155'); ?></a></li>
					<li class="divider"></li>
					<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($estimate, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
				</ul>
				<?php else : ?>
				<a href="<?php echo get_page_base_url($estimate->getObjectURL()); ?>" class="btn btn-xs btn-default" target="_blank"><?php echo lang('c_156'); ?></a>
				<?php endif; ?>

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