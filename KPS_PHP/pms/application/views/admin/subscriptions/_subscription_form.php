<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($target_source->isNew() ? "New" : "Edit Subscription # ".$target_source->getId())); ?>

<form method="post" action="<?php echo get_page_base_url($target_source->isNew() ? 'admin/subscriptions/create' : $target_source->getEditURL()); ?>" id="i_subscription_form" class="form-horizontal">

<div class="form-group">
<?php echo select_box("subscription_id", $packages_options, $subscription_id, ' class="form-control"'); ?>
</div>

<div class="form-group">
	<input type="text" class="form-control" name="storage_limit" placeholder="Storage Limit (GB)" value="<?php echo clean_field($storage_limit); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="projects_limit" placeholder="No.of Projects" value="<?php echo clean_field($projects_limit); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="users_limit" placeholder="No.of Users" value="<?php echo clean_field($users_limit); ?>" />
</div>

<div class="form-group">
<?php $status_options = array(0 => 'Block', 1 => 'Active');
echo select_box("status_id", $status_options, $status_id, ' class="form-control"'); ?>
</div>

<div class="form-group">
	<input type="text" name="expire_date" value="<?php echo clean_field($expire_date); ?>" class="form-control datepicker" readonly />
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="Saving..." id="i_subscription_submit"><?php echo ($target_source->isNew() ? "Add" : "Update"); ?></button>
	<a class="btn btn-default" data-dismiss="modal">Cancel</a>
</div>

</form>

<script type="text/javascript"> $(document).ready(function() { 
		"use strict";
		$('.datepicker').datepicker(); 
}); </script>