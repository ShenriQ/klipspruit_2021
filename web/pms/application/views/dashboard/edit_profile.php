<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_87')); ?>

<form method="post" action="<?php echo get_page_base_url($user->getEditProfileURL()); ?>" id="i_edit_profile_form" enctype="multipart/form-data" class="form-horizontal">

<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="30" placeholder="<?php echo lang('c_85'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="email" value="<?php echo clean_field($email); ?>" maxlength="100" placeholder="<?php echo lang('c_1'); ?>" readonly />
</div>

<div class="form-group">

	<input class="form-control" name="password" placeholder="<?php echo lang('c_2'); ?>" maxlength="20" type="password" autocomplete="off">
	<small class="custom-small-grey-color"><?php echo lang('c_447'); ?></small>

</div>

<div class="form-group">
	<input type="text" class="form-control" name="address" value="<?php echo clean_field($address); ?>" maxlength="200" placeholder="<?php echo lang('c_77'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="phone_number" value="<?php echo clean_field($phone_number); ?>" maxlength="30" placeholder="<?php echo lang('c_78'); ?>" />
</div>

<div class="form-group">
	<label><?php echo lang('c_446'); ?></label>
	<p><input name="avatar_file" type="file" id="avatar_file">
	<label class="custom-background-light-yellow"><input name="remove_avatar" type="checkbox"> <?php echo lang('c_448'); ?></label></p>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_edit_profile_submit"><?php echo lang('c_53'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
