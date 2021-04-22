<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($user->isNew() ? lang('c_219').' ' : lang('c_220').' ').$user_group); ?>

<form method="post" action="<?php echo get_page_base_url($user->isNew() ? 'people/add/'.$user_group : $user->getEditURL()); ?>" id="i_user_form" class="form-horizontal">

<?php if($user_group == 'client') : 

	echo '<div class="form-group">';
	
	$companies = $this->Companies->getClients(owner_company());
	
	$companies_options = array(lang('c_218'));
	if(isset($companies) && is_array($companies) && count($companies)) :
		foreach($companies as $company) :
			$companies_options[$company->getId()] = $company->getName();
		endforeach;
	endif;		
	echo select_box("company_id", $companies_options, $company_id, ' class="form-control"');
	
	echo '</div>';

elseif($user->isNew() || !$user->isOwner()) : 

	echo '<div class="form-group">';
	
	$member_options = array(lang('c_28'), lang('c_221'));
	echo select_box("member_type", $member_options, $member_type, ' id="member_type" class="form-control"'); 
	
	echo '</div>';

endif; ?>

<div class="form-group">
	<input type="text" class="form-control" name="name" value="<?php echo clean_field($name); ?>" maxlength="30" placeholder="<?php echo lang('c_85'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="email" value="<?php echo clean_field($email); ?>" maxlength="100" placeholder="<?php echo lang('c_1'); ?>" />
</div>

<div class="form-group">

	<input class="form-control" name="password" placeholder="<?php echo lang('c_2'); ?>" maxlength="20" type="password" autocomplete="off">

	<?php if(!$user->isNew()) : ?>
	<small class="custom-small-grey-color"><?php echo lang('c_222'); ?></small>
	<?php endif; ?>

</div>

<?php if($user_group != 'client') : ?>
	<div class="form-group">
		<input type="number" class="form-control" name="hourly_rate" min="1" value="<?php echo clean_field($hourly_rate); ?>" placeholder="<?php echo lang('c_523.31'); ?>" />
	</div>
<?php endif; ?>

<div class="form-group">
	<input type="text" class="form-control" name="address" value="<?php echo clean_field($address); ?>" maxlength="200" placeholder="<?php echo lang('c_77'); ?>" />
</div>

<div class="form-group">
	<input type="text" class="form-control" name="phone_number" value="<?php echo clean_field($phone_number); ?>" maxlength="30" placeholder="<?php echo lang('c_78'); ?>" />
</div>

<div class="form-group">
	<textarea class="form-control" name="notes" placeholder="<?php echo lang('c_284'); ?>"><?php echo clean_field($notes); ?></textarea>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_user_submit"><?php echo ($user->isNew() ? lang('c_219').' ' : lang('c_53').' ').$user_group; ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<?php if($user->isNew() || !$user->isOwner()) : ?>

<script type="text/javascript"> 

(function ($) {
	"use strict";
	$(document).ready(function() {  
		$("#member_type").change(function(){
			let selected_option = parseInt($(this).val());
			if(selected_option === 1) {
				$("#canAccessInvoicesEstimates").show();
			} else {
				$("#canAccessInvoicesEstimates").hide();
			}
		});
	});
})(jQuery);
</script>

<?php endif; ?>