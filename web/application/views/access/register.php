<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_524'));
tpl_assign("heading_for_dialog", lang('c_525'));

tpl_assign("header_for_layout", '
<meta name="robots" content="noindex">
');	?>

<form id="login-form" method="post">

<div class="form-group">
<?php echo select_box("subscription_id", $packages_options, $subscription_id, ' class="form-control"'); ?>
</div>

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_526'); ?>" name="name" type="text" maxlength="30" value="<?php echo clean_field($name); ?>">
</div>

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_527'); ?>" name="email" type="text" maxlength="100" value="<?php echo clean_field($email); ?>">
</div>

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_528'); ?>" name="company_name" type="text" maxlength="100" value="<?php echo clean_field($company_name); ?>">
</div>

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_529'); ?>" name="password" type="password" value="">
</div>

<div class="form-group">
	<input class="form-control" placeholder="<?php echo lang('c_530'); ?>" name="workspace_name" type="text" maxlength="100" value="<?php echo clean_field($workspace_name); ?>">
</div>

<input type="hidden" name="submited" value="submited" />

<button type="submit" class="btn btn-lg btn-success btn-block"><?php echo lang('c_524'); ?></button>

<p>&nbsp;</p>
<p align="center"><a href="<?php echo get_page_base_url('access/login'); ?>"><?php echo lang('c_16'); ?></a></p>

</form>