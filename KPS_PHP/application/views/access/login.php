<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_4'));
tpl_assign("heading_for_dialog", lang('c_6'));

tpl_assign("header_for_layout", '
<meta name="robots" content="noindex">
');	

tpl_assign("footer_for_layout", '
');

?>

<form id="login-form" method="post">

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_1'); ?>" name="email" id="email" type="text" maxlength="100" value="<?php echo clean_field($email); ?>" autofocus>
</div>

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_2'); ?>" name="password" id="password" type="password" value="demo1234">
</div>

<div class="form-group">
	<label><input name="remember" type="checkbox"<?php echo ($remember ? ' checked="checked"' : ''); ?>> &nbsp;<?php echo lang('c_3'); ?></label>
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block"><?php echo lang('c_4'); ?></button>

<p>&nbsp;</p>
<p align="center"><a href="<?php echo get_page_base_url('access/forgot_password'); ?>"><?php echo lang('c_5'); ?></a></p>

</form>