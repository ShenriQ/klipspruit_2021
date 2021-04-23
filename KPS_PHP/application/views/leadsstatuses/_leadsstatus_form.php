<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($status->isNew() ? lang('c_464') : lang('c_466'))); ?>

<form method="post" action="<?php echo get_page_base_url($status->isNew() ? 'leadsstatuses/create' : $status->getEditURL()); ?>" id="i_status_form" class="form-horizontal">

<div class="form-group">
	<input type="text" name="name" value="<?php echo clean_field($name); ?>" class="form-control" placeholder="<?php echo lang('c_465'); ?>" />
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_status_submit"><?php echo ($status->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>