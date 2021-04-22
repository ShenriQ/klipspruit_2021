<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($project->isNew() ? lang('c_247') : lang('c_248'))); ?>

<form method="post" action="<?php echo get_page_base_url($project->isNew() ? 'projects/add' : $project->getEditURL()); ?>" id="i_project_form" class="form-horizontal">

<?php if($project->isNew()) : ?><div class="form-group">
<?php echo lang('c_249'); ?>
</div><?php endif; ?>
	
<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="100" placeholder="<?php echo lang('c_237'); ?>" />
</div>

<div class="form-group">
	<textarea class="form-control" name="description" placeholder="<?php echo lang('c_250'); ?>"><?php echo clean_field($description); ?></textarea>
</div>

<div class="form-group">
	<input type="text" name="start_date" class="form-control datepicker" value="<?php echo clean_field($start_date); ?>" placeholder="<?php echo lang('c_192'); ?>" readonly />
</div>

<div class="form-group">
	<input type="text" name="due_date" class="form-control datepicker" value="<?php echo clean_field($due_date); ?>" placeholder="<?php echo lang('c_238'); ?>" readonly />
</div>

<div class="form-group">
<?php

$labels = $this->GlobalLabels->getByType('PROJECT');

$labels_options = array(lang('c_251'));
if(isset($labels) && is_array($labels) && count($labels)) :
foreach($labels as $label) :
	$labels_options[$label->getId()] = $label->getName();
endforeach;
endif;

echo select_box("label_id", $labels_options, $label_id, ' class="form-control"');

?>
</div>

<div class="form-group">
<?php

$companies = $this->Companies->getClients(owner_company());

$companies_options = array(lang('c_132'));
if(isset($companies) && is_array($companies) && count($companies)) :
foreach($companies as $company) :
	$companies_options[$company->getId()] = $company->getName();
endforeach;
endif;
echo select_box("company_id", $companies_options, $company_id, ' class="form-control"');

?>
</div>

<div class="form-group">
	<label><input name="is_visible_timelog" type="checkbox"<?php echo ($is_visible_timelog ? ' checked="checked"' : ''); ?> /> <?php echo lang('c_252'); ?> <small class="custom-backgound-lightyellow-underline"><?php echo lang('c_253'); ?></small></label>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_project_submit"><?php echo ($project->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
<script type="text/javascript">(function ($) {
	"use strict"; $(document).ready(function() { $('.datepicker').datepicker(); }); 
})(jQuery);</script>