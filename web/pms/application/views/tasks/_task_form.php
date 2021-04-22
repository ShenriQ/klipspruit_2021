<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($task->isNew() ? lang('c_291') : lang('c_185').' [#'.$task->getId().']')); ?>

<form method="post" action="<?php echo get_page_base_url($task->isNew() ? $task_list->getCreateTaskURL() : $task->getEditURL()); ?>" id="i_task_form" class="form-horizontal">

<div class="form-group">
	<textarea class="form-control" name="description" placeholder="<?php echo lang('c_48'); ?>"><?php echo clean_field($description); ?></textarea>
</div>

<div class="form-group">
<?php $users_options = array(lang('c_332'));
$project_users = $project->getUsers(true, true);

if(isset($project_users) && is_array($project_users) && count($project_users)) :
	foreach($project_users as $project_user) :
		if($project_user->isMember()) $users_options[$project_user->getId()] = $project_user->getName();
	endforeach;
endif;

echo select_box("assignee_id", $users_options, $assignee_id, ' class="form-control"');

?>	
</div>

<div class="form-group">
	<input type="text" name="start_date" class="form-control datepicker" value="<?php echo clean_field($start_date); ?>" placeholder="<?php echo lang('c_192'); ?>" readonly />
</div>

<div class="form-group">
	<input type="text" name="due_date" class="form-control datepicker" value="<?php echo clean_field($due_date); ?>" placeholder="<?php echo lang('c_138'); ?>" readonly />
</div>

<div class="form-group">
<?php

$labels = $this->GlobalLabels->getByType('TASK');

$labels_options = array(lang('c_251'));
foreach($labels as $label) :
	$labels_options[$label->getId()] = $label->getName();
endforeach;

echo select_box("label_id", $labels_options, $label_id, ' class="form-control"');

?>
</div>

<div class="form-group">
	<label><input name="is_high_priority" type="checkbox"<?php echo ($is_high_priority ? ' checked="checked"' : ''); ?> /> <?php echo lang('c_333'); ?></label>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_task_submit"><?php echo ($task->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<script type="text/javascript"> (function ($) {
	"use strict"; $(document).ready(function() { $('.datepicker').datepicker(); }); })(jQuery);</script>