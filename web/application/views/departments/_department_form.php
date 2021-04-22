<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($ticket_type->isNew() ? lang('c_108') : lang('c_109'))); ?>

<form method="post" action="<?php echo get_page_base_url($ticket_type->isNew() ? 'departments/create' : $ticket_type->getEditURL()); ?>" id="i_department_form" class="form-horizontal">

<div class="form-group">
	<input type="text" name="name" value="<?php echo clean_field($name); ?>" class="form-control" placeholder="<?php echo lang('c_104'); ?>" />
</div>

<div class="form-group">
	<select name="is_active" class="form-control">
	<option value="1"<?php echo $is_active == 1 ? ' selected="selected"' : ''; ?>><?php echo lang('c_110'); ?></option>
	<option value="0"<?php echo $is_active == 0 ? ' selected="selected"' : ''; ?>><?php echo lang('c_111'); ?></option>
	</select>
</div>


<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_department_submit"><?php echo ($ticket_type->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>