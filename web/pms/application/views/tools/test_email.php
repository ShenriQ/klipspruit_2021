<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_515')); ?>

<form method="post" action="<?php echo get_page_base_url("tools/test_email"); ?>" id="i_test_email_form" class="form-horizontal">
	
<div class="form-group">
	<input type="text" class="form-control" name="test_recepient_email" value="<?php echo clean_field($test_recepient_email); ?>" maxlength="100" placeholder="<?php echo lang('c_518'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="test_recepient_subject" value="<?php echo clean_field($test_recepient_subject); ?>" maxlength="200" placeholder="<?php echo lang('c_519'); ?>" />
</div>

<div class="form-group">
	<textarea class="form-control" name="test_recepient_message" placeholder="<?php echo lang('c_520'); ?>" rows="7"><?php echo clean_field($test_recepient_message); ?></textarea>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_522'); ?>" id="i_test_email_submit"><?php echo lang('c_521'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
