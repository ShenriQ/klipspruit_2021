<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("header_for_layout", '
<meta name="robots" content="noindex">');	
?>

<?php if(isset($token)) :
tpl_assign("title_for_layout", lang('c_8')); ?>
	
<p><?php echo lang('c_9'); ?></p>

<form id="reset-password-form" method="post">

<div class="form-group">
	<input class="form-control" id="rs_password" name="new_password" placeholder="<?php echo lang('c_10'); ?>" maxlength="20" type="password">
</div>

<div class="form-group">
	<input class="form-control" id="rs_retype_password" name="confirm_password" placeholder="<?php echo lang('c_11'); ?>" maxlength="20" type="password">
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block"><?php echo lang('c_12'); ?></button>
	
</form>

<?php else : 
tpl_assign("title_for_layout", lang('c_5')); ?>

<p><?php echo lang('c_13'); ?></p>

<form id="recover-password-form" method="post">
	
<div class="form-group">
	<input class="form-control" id="rs_email" name="user_email" placeholder="<?php echo lang('c_14'); ?>" maxlength="100" value="<?php echo clean_field($user_email); ?>" type="text">
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block"><?php echo lang('c_15'); ?></button>

<p>&nbsp;</p>
<p align="center"><a href="<?php echo get_page_base_url('access/login'); ?>"><?php echo lang('c_16'); ?></a></p>
	
</form>

<?php endif; ?>