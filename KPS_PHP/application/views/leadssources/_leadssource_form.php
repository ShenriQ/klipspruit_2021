<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($source->isNew() ? lang('c_460') : lang('c_462'))); ?>

<form method="post" action="<?php echo get_page_base_url($source->isNew() ? 'leadssources/create' : $source->getEditURL()); ?>" id="i_source_form" class="form-horizontal">

<div class="form-group">
	<input type="text" name="name" value="<?php echo clean_field($name); ?>" class="form-control" placeholder="<?php echo lang('c_461'); ?>" />
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_source_submit"><?php echo ($source->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>