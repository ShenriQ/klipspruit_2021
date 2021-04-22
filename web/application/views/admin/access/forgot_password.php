<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("header_for_layout", '
<meta name="robots" content="noindex">'); ?>

<?php if(isset($token)) :
tpl_assign("title_for_layout", "Rest Password"); ?>
	
<p>Please, enter a new password for your account.</p>

<form id="reset-password-form" method="post">

<div class="form-group">
	<input class="form-control" id="rs_password" name="new_password" placeholder="New password" maxlength="20" type="password">
</div>

<div class="form-group">
	<input class="form-control" id="rs_retype_password" name="confirm_password" placeholder="Retype password" maxlength="20" type="password">
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block">Set new password</button>
	
</form>

<?php else : 
tpl_assign("title_for_layout", "Forgot password"); ?>

<p>Please enter the e-mail address associated with your account.</p>

<form id="recover-password-form" method="post">
	
<div class="form-group">
	<input class="form-control" id="rs_email" name="user_email" placeholder="Email Address" maxlength="100" value="<?php echo clean_field($user_email); ?>" type="text">
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block">Send request</button>

<p>&nbsp;</p>
<p align="center"><a href="<?php echo get_page_base_url('admin/access/login'); ?>">Login to account</a></p>
	
</form>

<?php endif; ?>