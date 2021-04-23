<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", "Edit Option"); ?>

<form method="post" action="<?php echo get_page_base_url($option->getEditURL()); ?>" id="i_option_form" class="form-horizontal">
	
<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="100" placeholder="Option" readonly="" />
</div>

<div class="form-group">
	<textarea class="form-control" name="value" placeholder="Value"><?php echo clean_field($value); ?></textarea>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="Saving..." id="i_option_submit">Update</button>
	<a class="btn btn-default" data-dismiss="modal">Cancel</a>
</div>

</form>
