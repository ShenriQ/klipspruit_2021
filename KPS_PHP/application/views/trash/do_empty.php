<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_392'));?>

<form method="post" action="<?php echo get_page_base_url('trash/do_empty');?>" id="i_empty_trash_form" class="form-horizontal">

<div class="form-group">

<h4><?php echo lang('c_391'); ?></h4>

</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-danger" data-loading-text="<?php echo lang('c_277'); ?>" id="i_empty_trash_submit"><?php echo lang('c_392'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
