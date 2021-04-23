<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($company->isNew() ? lang('c_82') : lang('c_83'))); ?>

<form method="post" action="<?php echo get_page_base_url($company->isNew() ? 'companies/add' : $company->getEditURL()); ?>" id="i_company_form" class="form-horizontal">
	
<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="50" placeholder="<?php echo lang('c_76'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="address" value="<?php echo clean_field($address); ?>" maxlength="200" placeholder="<?php echo lang('c_77'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="phone_number" value="<?php echo clean_field($phone_number); ?>" maxlength="30" placeholder="<?php echo lang('c_78'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="vat_no" value="<?php echo clean_field($vat_no); ?>" maxlength="100" placeholder="<?php echo lang('c_523.118'); ?>" />
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_company_submit"><?php echo ($company->isNew() ? lang('c_82') :  lang('c_84')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>
