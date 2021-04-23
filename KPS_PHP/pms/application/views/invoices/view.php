<?php $default_currency = config_option('default_currency', "$"); 
$payable_amount = $invoice->getTotalAmount()-$invoice->getPaidAmount();
$is_payment_enabled = ($invoice->getPaidAmount() < $invoice->getTotalAmount() && !$invoice->getIsOnlinePaymentDisabled() && !$invoice->getIsCancelled());

if($is_payment_enabled && $is_submited) : 

if($paymentmode == "paypal") : 

$paypal_currency_code = config_option('paypal_currency_code', 'USD');

$paypal_sandbox = config_option('paypal_sandbox', 'yes');
$paypal_host = $paypal_sandbox == 'no' ? 'www.paypal.com' : 'www.sandbox.paypal.com';

$paypal_email = config_option('paypal_email');
$paypal_return_url = get_page_base_url($invoice->getObjectURL());

?>

<form method="post" name="paypal_deposit_payment" action="https://<?php echo $paypal_host; ?>/cgi-bin/webscr" accept-charset="UTF-8">
<input type="hidden" name="charset" value="utf-8" />
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="item_number" value="<?php echo $invoice->getId(); ?>" />
<input type="hidden" name="item_name" value="Invoice #<?php echo $invoice->getInvoiceNo(); ?> for <?php echo logged_user()->getName(); ?>" />
<input type="hidden" name="amount" value="<?php echo number_format($amount,2); ?>" />
<input type="hidden" name="quantity" value="1" />
<input type="hidden" name="custom" value="<?php echo $invoice->getAccessKey(); ?>" />
<input type="hidden" name="business" value="<?php echo $paypal_email; ?>" />
<input type="hidden" name="currency_code" value="<?php echo $paypal_currency_code; ?>" />
<input type="hidden" name="notify_url" value="<?php echo get_page_base_url("ipn/process"); ?>" />
<input type="hidden" name="return" value="<?php echo $paypal_return_url; ?>" />
<input type="hidden" name="cancel_return" value="<?php echo $paypal_return_url; ?>" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="no_note" value="1" />
</form>
	
<h2 align="center">Your payment is being processed...</h2>
<script language="javascript">document.forms['paypal_deposit_payment'].submit();</script>

<?php elseif($paymentmode == "stripe") :

$stripe_publishable_key = config_option('stripe_publishable_key'); 
$site_name = config_option('site_name'); 
$stripe_currency_code = config_option('stripe_currency_code'); 


?>

<h3>Payment for Invoice <a href="<?php echo get_page_base_url($invoice->getObjectURL()); ?>"><?php echo $invoice->getInvoiceNo(); ?></a></h3>
<h4>Amount: <?php echo $default_currency; ?><?php echo $amount; ?></h4>

<form action="<?php echo get_page_base_url($invoice->getPaymentURL('stripe')); ?>" method="POST">

	<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
		data-key="<?php echo $stripe_publishable_key; ?>"
		data-amount="<?php echo round(($amount)*100); ?>"
		data-name="<?php echo $site_name; ?>"
		data-description="Payment for Invoice <?php echo $invoice->getInvoiceNo(); ?>";
		data-locale="auto"
		data-currency="<?php echo $stripe_currency_code; ?>">
	</script>
								
	<input type="hidden" name="amount" value="<?php echo $amount; ?>" />

</form>
														

<?php endif;

else : ?>

<div class="row">
<div class="col-md-12">

<table width="100%">
<tr><td><h2><?php echo lang('c_428'); ?></h2></td>
<td class="text-right">
<a onclick="javascript:window.print();" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Print</a>
<?php if(logged_user()->isOwner() && !$invoice->getIsCancelled()) : ?>
<a class="btn btn-sm btn-success" href="javascript:void();" data-url="<?php echo get_page_base_url($invoice->getEditURL()); ?>" data-toggle="commonmodal"><i class="fa fa-edit"></i> <?php echo lang('c_175'); ?></a>
<a class="btn btn-sm btn-info" href="<?php echo get_page_base_url($invoice->getDownloadURL()); ?>"><i class="fa fa-file"></i> <?php echo lang('c_523.77'); ?></a>
<?php endif;  if($is_payment_enabled) : ?>
 <a href="#pay_invoice" class="btn btn-success btn-sm pay-now-btn">Pay Invoice</a>
<?php endif; ?>
</td>
</tr></table>

<div class="row">
<div class="col-md-6">
<div class="table-responsive">
<table class="table table-bordered">
<tr><td><?php echo lang('c_159'); ?></td><td><?php $created_by = $invoice->getCreatedBy();
if($c_company = $created_by->getCompany()) : ?><b><?php echo $c_company->getName(); ?></b> <?php echo ($c_company->getVatNo() != "" ? "<u>(".lang('c_523.118').") " . $c_company->getVatNo() . "</u>" : ""); ?><br /><?php endif; ?>
<?php echo $created_by->getName(); ?> [ <em><?php echo $created_by->getEmail(); ?></em> ]
<?php if($c_company->getAddress() != "") : ?><br /><?php echo $c_company->getAddress(); endif; ?>
<?php if($c_company->getPhoneNumber() != "") : ?><br /><?php echo $c_company->getPhoneNumber(); endif; ?>
</td></tr>
<tr><td><?php echo lang('c_160'); ?></td><td><?php $client_to = $invoice->getClient();
if($invoice->getCompanyName() != "") : ?><b><?php echo $invoice->getCompanyName(); ?></b><br /><?php endif; ?>
<?php echo $client_to->getName(); ?> [ <em><?php echo $client_to->getEmail(); ?></em> / <u>#<?php echo "CUS-".str_pad($client_to->getId(), 6, '0', STR_PAD_LEFT); ?></u> ]
<?php if($invoice->getCompanyAddress() != "") : ?><br /><?php echo $invoice->getCompanyAddress(); endif; ?>

</td></tr>
<tr><td><?php echo lang('c_126'); ?></td><td><?php echo $invoice->getSubject(); ?></td></tr>
<tr><td><?php echo lang('c_23'); ?></td><td> <?php $invoice_project = $invoice->getProject();
echo (isset($invoice_project) ? $invoice_project->getName() : "-"); ?></td></tr>
</table></div>
</div>
<div class="col-md-6">
<div class="table-responsive">
<table class="table table-bordered">
<tr><td><?php echo lang('c_430'); ?></td><td><b><?php echo $invoice->getInvoiceNo(); ?></b></td></tr>
<tr><td><?php echo lang('c_523.92'); ?></td><td><?php echo date("jS F Y", $invoice->getIssueDate()); ?></td></tr>
<tr><td><?php echo lang('c_138'); ?></td><td><?php echo date("jS F Y", $invoice->getDueDate()); ?>
<?php $invoice_due_date = $invoice->getDueDate(); if(isset($invoice_due_date) && $invoice_due_date < time() && !$invoice->getIsCancelled() && $invoice->getPaidAmount() < $invoice->getTotalAmount()) { echo" <span style=\"font-weight:bold;color:red;\">" . lang("c_154") . "</span>"; } ?>
</td></tr>
<?php if($invoice->getReference() != '') : ?>
<tr><td><?php echo lang('c_523.96'); ?></td><td><?php echo $invoice->getReference(); ?></td></tr>
<?php endif; ?>
<tr><td><?php echo lang('c_114'); ?></td><td><?php 
	if(!$invoice->getIsCancelled()) {
		if($invoice->getPaidAmount() > 0) {
			if($invoice->getPaidAmount() < $invoice->getTotalAmount()) {
			  $status = "<span class='label label-success'>".lang('c_177')."</span>";
			} else {
			  $status = "<span class='label label-success'>".lang('c_178')."</span>";
			}
		} else {
		  $status = "<span class='label label-warning'>".lang('c_179')."</span>";
		}
  } else {
	  $status = "<span class='label label-danger'>".lang('c_137')."</span>";
  }
  echo $status;
?></td></tr>
</table></div>
</div>

</div>

<div class="row">
<div class="col-md-12">
<div class="table-responsive">
<table class="table table-striped">
	<thead><tr><th>Item</th><th>Quantity</th><th>Cost</th><th class="text-right">Total</th></tr></thead>
	<?php $sub_total = 0;
	$items = $invoice->getItems();
	if(isset($items) && is_array($items) && count($items)) :
	foreach($items as $r) : ?>
	<?php $total = number_format($r->getQuantity()*$r->getAmount(),2);
	$sub_total += $r->getAmount()*$r->getQuantity(); ?>
	<tr><td><?php echo $r->getDescription(); ?></td><td><?php echo $r->getQuantity(); ?></td><td><?php echo $r->getAmount() ?></td><td class="text-right"><?php echo $total ?></td></tr>
	<?php endforeach; 
	else : ?><tr><td colspan="4"><?php echo lang('e_2'); ?></td></tr><?php endif; ?>
	<?php $total_amount = $sub_total; ?>
	<tr class="warning text-right"><td colspan="4"><?php echo lang('c_144'); ?>: <?php echo number_format($sub_total,2) ?>
	<br /><?php if($invoice->getTaxRate() > 0) : ?>
	<?php $tax_addon = abs($sub_total/100*$invoice->getTaxRate());
	$total_amount += $tax_addon; ?>
	<?php echo lang('c_146'); ?> <?php if($invoice->getTax() != "") : ?> (<?php echo $invoice->getTax() ?>) <?php endif; ?>@ <?php echo $invoice->getTaxRate() ?>% : <?php echo number_format($tax_addon,2) ?><br />
	<?php endif; ?>
	<?php if($invoice->getTaxRate2() > 0) : ?>
	<?php $tax_addon2 = abs($sub_total/100*$invoice->getTaxRate2());
	$total_amount += $tax_addon2; ?>
	<?php echo lang('c_146.1'); ?> <?php if($invoice->getTax2() != "") : ?> (<?php echo $invoice->getTax2() ?>) <?php endif; ?>@ <?php echo $invoice->getTaxRate2() ?>% : <?php echo number_format($tax_addon2,2) ?><br />
	<?php endif; ?>
	<b><?php echo lang('c_149'); ?>: <?php echo config_option('default_currency', "$"); ?><?php echo number_format($total_amount,2) ?></b><br />
	<?php 
		$calculated_discount_amount = $invoice->getDiscountAmount();
		if($calculated_discount_amount > 0) {
			if($invoice->getDiscountAmountType() == 'percentage') {
				$calculated_discount_amount = abs($calculated_discount_amount/100*$total_amount);
			}
		}
		$total_amount = ($total_amount - $calculated_discount_amount);
	?>
	<?php if($calculated_discount_amount != "") : ?> <?php echo lang('c_523.58'); ?> (<?php echo $invoice->getDiscountAmountType() == 'percentage' ? $invoice->getDiscountAmount() . lang('c_523.59') : lang('c_523.60'); ?>) : <?php echo number_format($calculated_discount_amount,2) ?><br /><?php endif; ?>
	<b><?php echo lang('c_149.1'); ?>: <?php echo config_option('default_currency', "$"); ?><?php echo number_format($total_amount,2) ?></b>
	</td></tr>
	<?php if($invoice->getPaidAmount() > 0) : ?>
	<tr class="info text-right"><td colspan="4"><?php echo lang('c_432'); ?>: <?php echo $default_currency; ?><?php echo number_format($invoice->getPaidAmount(),2); ?></td></tr>
	<?php if($invoice->getPaidAmount() < $invoice->getTotalAmount()) : ?><tr class="text-right"><td colspan="4"><b><span class="custom-color-red"><?php echo lang('c_433'); ?>:</span> <?php echo $default_currency; ?><?php $due_amount = $invoice->getTotalAmount()-$invoice->getPaidAmount(); echo number_format($due_amount,2); ?></b></td></tr><?php endif; ?>
	<?php endif; ?>
</table></div>
</div></div>

<?php if($invoice->getNote() != "") : ?>
<hr><p><?php echo $invoice->getNote();?></p>
<?php endif; ?>

<div class="table-responsive">
<table width="100%">
<tr><td><h3><?php echo lang('c_375'); ?></h3></td>
<?php if(logged_user()->isOwner() && !$invoice->getIsCancelled()) : ?>
<td class="text-right"><a class="btn btn-sm btn-primary" href="javascript:void();" data-url="<?php echo get_page_base_url($invoice->getCreatePaymentURL()); ?>" data-toggle="commonmodal"><i class="fa fa-plus"></i> <?php echo lang('c_180'); ?></a></td>
<?php endif; ?>
</tr></table></div>


<div class="row">
<div class="col-md-12">
<div class="table-responsive">
<table class="table table-striped ">
	<thead><tr><th>#</th><th><?php echo lang('c_205'); ?></th><th><?php echo lang('c_48'); ?></th><th class="text-right"><?php echo lang('c_152'); ?></th></tr></thead>
	<?php $payments = $invoice->getPayments(); 
	if(isset($payments) && is_array($payments) && count($payments)) :
	foreach($payments as $payment) : ?>
	<tr><td><?php echo $payment->getId(); ?></td><td><?php echo date("m-d-Y H:i", $payment->getCreatedAt()); ?></td><td><?php echo $payment->getDescription(); ?></td>
	<td class="text-right"><?php echo $default_currency; ?><?php echo $payment->getAmount(); ?>
	<?php if(logged_user()->isOwner()) : ?>
	<div class="pull-right custom-ml-10">
		<div class="btn-group">
			<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
			<ul class="dropdown-menu pull-right" role="menu">
				<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($payment->getEditURL("payment")); ?>" data-toggle="commonmodal"><?php echo lang('c_220'); ?></a></li>
				<li><a href="<?php echo get_page_base_url($payment->getRemoveURL()).'?ref='.base64_encode(current_url()); ?>" onclick="return confirm('<?php echo lang('c_209'); ?>');"><?php echo lang('c_208'); ?></a></li>
			</ul>
		</div>
	</div>
	<?php endif; ?>	
	</td>
	</tr>
	<?php endforeach; ?>
	<?php else : ?>
	<tr><td colspan="4"><?php echo lang('c_434'); ?></td></tr>
	<?php endif; ?>	
</table>
</div>

</div></div>

<?php  if($is_payment_enabled) : 

$offline_bank_name = config_option('offline_bank_name'); 
$offline_bank_account = config_option('offline_bank_account');  ?>

<div class="well" id="pay_invoice">

	<div class="row">
	
		<div class="col-md-6 text-left">
		
			<h4><?php echo lang('c_450'); ?></h4>
			<form action="<?php echo get_page_base_url($invoice->getObjectURL()); ?>" method="post">
			
			<p><input type="radio" value="paypal" id="opt_paypal" checked="checked" name="paymentmode">
			<label for="opt_paypal">Paypal</label><br>
			
			<input type="radio" value="stripe" id="opt_stripe" name="paymentmode">
			<label for="opt_stripe">Stripe</label></p>
	
			<div class="form-group">
				<label for="amount" class="control-label"><?php echo lang('c_152'); ?></label>
				<div class="input-group">
					<input type="number" required max="<?php echo $payable_amount; ?>" data-total="<?php echo $payable_amount; ?>" name="amount" class="form-control" value="<?php echo $payable_amount; ?>">
					<span class="input-group-addon"><?php echo $default_currency; ?></span>
				</div>
			</div>

			<input type="hidden" name="submited" value="submited" />
			<input type="submit" class="btn btn-success" value="Pay Now">
			
			</form>                    
	
		</div>
	
		<div class="col-md-6 text-right">
			<h4><?php echo lang('c_451'); ?></h4>
			<h5><?php echo lang('c_452'); ?></h5>
			<p><?php echo lang('c_454'); ?>:<br>
			<?php echo lang('c_452'); ?>: <?php echo $offline_bank_name; ?><br>
			<?php echo lang('c_453'); ?>: <?php echo $offline_bank_account; ?>                      
			</p>
		</div>
	
	</div>
	
</div>

<?php endif; ?>

</div>
</div>

<?php endif; ?>