<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_317'));

tpl_assign("footer_for_layout", '
<script>
(function ($) {
	"use strict";
	$(document).ready(function() {
		$(\'#configFilter\').on(\'change\', function() {
			var location = "'.get_page_base_url('settings').'";
			var by = $("#configFilter").val();
			if (by != "") {
				location = location + "?by=" + by;
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
		<select class="form-control custom-fixed-width-select" id="configFilter">
			<option value="" <?php echo ($by == "" ? ' selected="selected"' : ''); ?>><?php echo lang('c_318'); ?></option>
			<option value="mailing" <?php echo ($by == "mailing" ? ' selected="selected"' : ''); ?>><?php echo lang('c_319'); ?></option>
		</select>            
	</div>
</div></p>


<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-gear"></i>
	  <h3 class="box-title"><?php echo lang('c_317'); ?> / <?php echo ($by == "mailing" ? lang('c_319') : lang('c_318')); ?></h3>
	</div>
	
	<div class="box-body">
	
<?php if(isset($options) && is_array($options) && count($options)) : ?>

	<div class="table-responsive">
	<table class="table table-striped table-bordered projects">
	
	<thead>
		<th><?php echo lang('c_316'); ?></th>
		<th><?php echo lang('c_315'); ?></th>
		<th></th>
	</thead>
	
	<tbody>

	<?php foreach($options as $config_option) : ?>			
	<tr>
	<td><?php echo clean_config_option($config_option->getName()); ?></td>
	<td><?php echo $config_option->getValue(); ?></td>
	<td><a href="javascript:void();" data-url="<?php echo get_page_base_url($config_option->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit fa-lg"></i></a></td>
	</tr>
	
	<?php endforeach; ?>
	
	</tbody>
	
	</table></div>
	
<?php else : ?> 
<p><?php echo lang('e_2'); ?></p>
<?php endif; ?> 

</div>

</div>

