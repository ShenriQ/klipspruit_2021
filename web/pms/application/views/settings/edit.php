<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_320')); ?>

<form method="post" action="<?php echo get_page_base_url($option->getEditURL()); ?>" id="i_option_form" class="form-horizontal">
	
<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="100" placeholder="<?php echo lang('c_316'); ?>" readonly="" />
</div>

<div class="form-group">
	<textarea class="form-control" name="value" placeholder="<?php echo lang('c_315'); ?>"><?php echo clean_field($value); ?></textarea>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_option_submit"><?php echo lang('c_53'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
