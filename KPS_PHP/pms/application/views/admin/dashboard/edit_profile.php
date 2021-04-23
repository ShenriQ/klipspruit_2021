<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", "Edit profile"); ?>

<form method="post" action="<?php echo get_page_base_url($user->getEditProfileURL()); ?>" id="i_edit_profile_form" class="form-horizontal">

<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="30" placeholder="Display Name" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="email" value="<?php echo clean_field($email); ?>" maxlength="100" placeholder="Email" readonly />
</div>

<div class="form-group">

	<input class="form-control" name="password" placeholder="Password" maxlength="20" type="password" autocomplete="off">
	<small class="custom-small-grey-color">To keep current password, please leave this box blank.</small>

</div>

<div class="form-group">
	<input type="text" class="form-control" name="address" value="<?php echo clean_field($address); ?>" maxlength="200" placeholder="Address" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="phone_number" value="<?php echo clean_field($phone_number); ?>" maxlength="30" placeholder="Phone Number" />
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="Saving..." id="i_edit_profile_submit">Update</button>
	<a class="btn btn-default" data-dismiss="modal">Cancel</a>
</div>

</form>
