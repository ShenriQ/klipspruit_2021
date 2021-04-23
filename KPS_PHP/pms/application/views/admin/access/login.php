<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", "Login");
tpl_assign("heading_for_dialog", "Please Sign In");

tpl_assign("header_for_layout", '
<meta name="robots" content="noindex">');?>

<form id="login-form" method="post">

<div class="form-group">
	<input class="form-control" placeholder="Email" name="email" type="text" maxlength="100" value="<?php echo clean_field($email); ?>" autofocus>
</div>

<div class="form-group">
	<input class="form-control" placeholder="Password" name="password" type="password" value="">
</div>

<div class="form-group">
	<label><input name="remember" type="checkbox"<?php echo ($remember ? ' checked="checked"' : ''); ?>> Remember Me</label>
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block">Login</button>

<p>&nbsp;</p>
<p align="center"><a href="<?php echo get_page_base_url('admin/access/forgot_password'); ?>">Forgot password</a></p>

</form>