<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", 'Remove Widget');?>

<form method="post" action="<?php echo get_page_base_url($widget->getDeleteUrl());?>" id="i_delete_widget_form" class="form-horizontal">

<div class="form-group">

<h4>Are you sure to remove this widget?</h4>
<p>This will remove this widget from landing page.</p>

</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-primary" data-loading-text="<?php echo lang('c_277'); ?>" id="i_delete_widget_submit">Remove</button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
