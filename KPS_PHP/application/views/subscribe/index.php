<div class="well">
<h1>Upgrade Account</h1>
<?php $is_submited = input_post_request('submited') ==  'submited';
$packages = get_packages(false);
if($is_submited) : 

$subscription = input_post_request('subscription', 1);
$my_target_source = logged_user()->getTargetSource();

foreach($packages as $package) {
	if($package->getId() == $subscription) {
		$select_package = $package;
		break;
	}
}

if(!isset($select_package)) {
	set_flash_error("Invalid package selected");
	redirect('subscribe');	
}

if($my_target_source->getSubscriptionId() > $select_package->getId() && (
	$my_target_source->getUsersCreated() > $select_package->getMaxUsers() ||
	$my_target_source->getProjectsCreated() > $select_package->getMaxProjects() ||
	$my_target_source->getStorageUsed() > $select_package->getMaxStorage()
)) { // downgrade ..
	set_flash_error("You can't downgrade account due to limit exceeded");
	redirect('subscribe');	
}

$paypal_email = i_config_option('paypal_account');
$paypal_return_url = get_page_base_url('dashboard');

?>

<form method="post" name="paypal_deposit_payment" action="https://www.paypal.com/cgi-bin/webscr" accept-charset="UTF-8">
<input type="hidden" name="charset" value="utf-8" />
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="item_number" value="<?php echo logged_user()->getId(); ?>" />
<input type="hidden" name="item_name" value="Subscription '<?php echo $select_package->getName(); ?>' for <?php echo logged_user()->getName(); ?>" />
<input type="hidden" name="amount" value="<?php echo number_format($select_package->getPricePerMonth(), 2); ?>" />
<input type="hidden" name="quantity" value="1" />
<input type="hidden" name="custom" value="<?php echo md5(logged_user()->getToken().$subscription).'-'.$subscription; ?>" />
<input type="hidden" name="business" value="<?php echo $paypal_email; ?>" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="notify_url" value="<?php echo get_page_base_url("subscribe/ipn"); ?>" />
<input type="hidden" name="return" value="<?php echo $paypal_return_url; ?>" />
<input type="hidden" name="cancel_return" value="<?php echo $paypal_return_url; ?>" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="no_note" value="1" />
</form>
	
<p><?php echo lang('c_537'); ?></p>
<script language="javascript">document.forms['paypal_deposit_payment'].submit();</script>

<?php else: ?>

<form method="post">

<div class="row">
	<div class="form-group">
		<div class="col-md-6"> 										
			<label><?php echo lang('c_536'); ?></label>
			<select class="form-control" id="rg_subscription" name="subscription">
				<?php foreach($packages as $package) :  if($package->getId() == 1) continue; ?>
				<option value="<?php echo $package->getId(); ?>"><?php echo $package->getName(); ?><?php echo ($package->getPricePerMonth() > 0 ? " $" . $package->getPricePerMonth() . "/month" : ""); ?></option>
				<?php endforeach; ?>
			</select>							  								
		</div>
	</div>

	<input type="hidden" name="submited" value="submited" />

	<div class="form-group">
		<div class="col-md-6">
			<p></p>									
			<button type="submit" class="btn btn-success">Upgrade now</button>
		</div>
	</div>

</div>

</form>


<?php endif; ?>
</div>