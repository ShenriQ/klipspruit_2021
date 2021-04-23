<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", "Edit Option"); ?>

<form method="post" action="<?php echo get_page_base_url($package->getEditURL()); ?>" id="i_package_form" class="form-horizontal">
	
<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="100" placeholder="Name" readonly="readonly" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="price_per_month" placeholder="Price (Per Month)" value="<?php echo clean_field($price_per_month); ?>"<?php echo $package->getId() == 1 ? " readonly" : ""; ?> />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="max_storage" placeholder="Storage (GB)" value="<?php echo clean_field($max_storage); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="max_users" placeholder="No.of Users" value="<?php echo clean_field($max_users); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="max_projects" placeholder="No.of Projects" value="<?php echo clean_field($max_projects); ?>" />
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="Saving..." id="i_package_submit">Update</button>
	<a class="btn btn-default" data-dismiss="modal">Cancel</a>
</div>

</form>
