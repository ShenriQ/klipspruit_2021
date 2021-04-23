<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_202')); 

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
		$(".announcements").DataTable({
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
	});
})(jQuery);
</script>
');	

?>
<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('noticeboard/create'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_203'); ?></a></p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-bullhorn"></i>
	  <h3 class="box-title"><?php echo lang('c_202'); ?></h3>
	</div>
	
	<div class="box-body">
	  
	<?php if(isset($announcements) && is_array($announcements) && count($announcements)) : ?>

		<div class="table-responsive">
		<table class="table table-striped table-bordered announcements">
		<thead>
			<th width="50%"><?php echo lang('c_195'); ?></th>
			<th><?php echo lang('c_204'); ?></th>
			<th><?php echo lang('c_205'); ?></th>
			<th><?php echo lang('c_206'); ?></th>
			<th width="10%"></th>
		</thead><tbody>
		
		<?php foreach($announcements as $announcement) : ?>
		
		<tr>

			<td><h4 class="custom-m-0"><?php echo $announcement->getTitle(); ?></h4>
			<p><span class="more"><?php echo $announcement->getDescription(); ?></span></p>
			<p><small><b><?php echo lang('c_192'); ?>:</b> <?php echo format_date($announcement->getStartDate(), 'j M. Y'); ?> &mdash; <b><?php echo lang('c_198'); ?>:</b> <span class="custom-background-light-yellow"><?php echo format_date($announcement->getEndDate(), 'j M. Y'); ?></span>
			<?php if($announcement->getEndDate() < time()) : ?>&nbsp; <b class="custom-color-red"><?php echo lang('c_207'); ?></b><?php endif; ?></small></p>
			</td>
			
			<td><?php echo $announcement->getCreatedBy()->getName(); ?></td>
			<td><?php echo format_date($announcement->getCreatedAt()) ; ?></td>
			
			<td><?php echo ucfirst($announcement->getShareWith()); ?></td>
			<td>

				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($announcement->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_197'); ?></a></li>
							<li><a href="<?php echo get_page_base_url($announcement->getRemoveURL()); ?>" onclick="return confirm('<?php echo lang('c_209'); ?>');"><?php echo lang('c_208'); ?></a></li>
							
						</ul>
					</div>
				</div>
			
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
		
	<?php else : ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>
		
	</div>

</div>

