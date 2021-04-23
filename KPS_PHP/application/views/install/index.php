<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_535'));
tpl_assign("heading_for_dialog", "PROMS-SAAS " . lang('c_535'));

tpl_assign("header_for_layout", '
<meta name="robots" content="noindex">
');	
?>

<form id="install-form" method="post">

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_533'); ?>" name="purchase_key" type="text" maxlength="100" value="<?php echo clean_field($purchase_key); ?>">
</div>

<div class="form-group">
	<input class="form-control" placeholder="Your name" name="name" type="text" maxlength="30" value="<?php echo clean_field($name); ?>">
</div>

<div class="form-group">
	<input class="form-control" placeholder="Your email" name="email" type="text" maxlength="100" value="<?php echo clean_field($email); ?>">
</div>

<div class="form-group">
	<input class="form-control" placeholder="Login password" name="password" type="password" value="">
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block"><?php echo lang('c_534'); ?></button></form>