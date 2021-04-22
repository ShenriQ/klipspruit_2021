<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($announcement->isNew() ? lang('c_196') : lang('c_197'))); ?>

<form method="post" action="<?php echo get_page_base_url($announcement->isNew() ? 'noticeboard/create' : $announcement->getEditURL()); ?>" id="i_announcement_form" class="form-horizontal">

<div class="form-group">
	<input type="text" name="title" value="<?php echo clean_field($title); ?>" class="form-control" placeholder="<?php echo lang('c_45'); ?>" />
</div>

<div class="form-group">
	<textarea class="form-control" name="description" placeholder="<?php echo lang('c_48'); ?>"><?php echo clean_field($description); ?></textarea>
</div>

<div class="form-group">
	<input type="text" name="start_date" class="form-control datepicker" value="<?php echo clean_field($start_date); ?>" placeholder="<?php echo lang('c_192'); ?>" readonly />
</div>

<div class="form-group">
	<input type="text" name="end_date" class="form-control datepicker" value="<?php echo clean_field($end_date); ?>" placeholder="<?php echo lang('c_198'); ?>" readonly />
</div>

<div class="form-group">
	<select name="share_with" class="form-control">
	<option value="clients"<?php echo $share_with == 'clients' ? ' selected="selected"' : ''; ?>><?php echo lang('c_199'); ?></option>
	<option value="members"<?php echo $share_with == 'members' ? ' selected="selected"' : ''; ?>><?php echo lang('c_200'); ?></option>
	<option value="all"<?php echo $share_with == 'all' ? ' selected="selected"' : ''; ?>><?php echo lang('c_201'); ?></option>
	</select>
</div>


<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_announcement_submit"><?php echo ($announcement->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<script type="text/javascript"> 
(function ($) {
	"use strict";
	$(document).ready(function() { $('.datepicker').datepicker(); }); 
})(jQuery);
</script>