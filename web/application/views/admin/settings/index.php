<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", "Settings");

tpl_assign("footer_for_layout", '
<script>
$(document).ready(function() {
$(\'#configFilter\').on(\'change\', function() {
	var location = "'.get_page_base_url('admin/settings').'";
	var by = $("#configFilter").val();
	if (by != "") {
		location = location + "?by=" + by;
	}
	window.location.href = location;
	return false;
});
});
</script>
');	

?>

<p><div class="row">
	<div class="col-md-6 col-sm-6">
		<select class="form-control" id="configFilter" class="custom-smallselect-box">
			<option value="" <?php echo ($by == "" ? ' selected="selected"' : ''); ?>>System</option>
			<option value="mailing" <?php echo ($by == "mailing" ? ' selected="selected"' : ''); ?>>Mailing</option>
		</select>            
	</div>
</div></p>

<div class="panel panel-default">

<div class="panel-heading">
	<span class="custom-font-size18">Settings &raquo; <?php echo ($by == "mailing" ? "Mailing" : "System"); ?></span>
</div>

<div class="panel-body">
	
<?php if(isset($options) && is_array($options) && count($options)) : ?>

	<table class="table table-hover table-bordered projects">
	
	<thead>
		<th>Option</th>
		<th>Value</th>
		<th></th>
	</thead>
	
	<tbody>

	<?php foreach($options as $config_option) : ?>			
	<tr>
	<td><?php echo clean_config_option($config_option->getName()); ?></td>
	<td><?php echo $config_option->getValue(); ?></td>
	<td><a href="javascript:void();" data-url="<?php echo get_page_base_url($config_option->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-sm btn-success">Edit</a></td>
	</tr>
	
	<?php endforeach; ?>
	
	</tbody>
	
	</table>
	
<?php else : ?> 
<p>No record found.</p>
<?php endif; ?> 

</div>

</div>

