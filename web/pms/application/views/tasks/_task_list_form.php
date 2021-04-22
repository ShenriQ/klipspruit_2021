<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($task_list->isNew() ? lang('c_289') : lang('c_290'))); ?>

<form method="post" action="<?php echo get_page_base_url($task_list->isNew() ? $project->getCreateTaskListURL() : $task_list->getEditURL()); ?>" id="i_task_list_form" class="form-horizontal">

<?php if($task_list->isNew()) : ?><div class="form-group">
<?php echo lang('c_334'); ?>
</div><?php endif; ?>
	
<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="100" placeholder="<?php echo lang('c_325'); ?>" />
</div>

<div class="form-group">
	<textarea class="form-control" name="description" placeholder="<?php echo lang('c_326'); ?>"><?php echo clean_field($description); ?></textarea>
</div>

<div class="form-group">
	<input type="text" name="start_date" class="form-control datepicker" value="<?php echo clean_field($start_date); ?>" placeholder="<?php echo lang('c_192'); ?>" readonly />
</div>

<div class="form-group">
	<input type="text" name="due_date" class="form-control datepicker" value="<?php echo clean_field($due_date); ?>" placeholder="<?php echo lang('c_138'); ?>" readonly />
</div>

<div class="form-group">

<?php if(logged_user()->isMember()) : ?>
<label><input name="is_private" type="checkbox"<?php echo ($is_private ? ' checked="checked"' : ''); ?> /> <?php echo lang('c_122'); ?> <small class="custom-backgound-lightyellow-underline"><?php echo lang('c_123'); ?></small></label><br>
<?php endif; ?>
<label><input name="is_high_priority" type="checkbox"<?php echo ($is_high_priority ? ' checked="checked"' : ''); ?> /> <?php echo lang('c_333'); ?></label>

</div>


<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_task_list_submit"><?php echo ($task_list->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
<script type="text/javascript">(function ($) {
	"use strict"; $(document).ready(function() { $('.datepicker').datepicker(); }); })(jQuery);</script>