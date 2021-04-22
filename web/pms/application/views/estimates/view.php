<div class="row">
<div class="col-md-12">

<table width="100%">
<tr><td><h2><?php echo lang('c_158'); ?></h2></td>
<td class="text-right">
<a onclick="javascript:window.print();" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Print</a>
<a class="btn btn-sm btn-info" href="<?php echo get_page_base_url($estimate->getDownloadURL()); ?>"><i class="fa fa-file"></i> <?php echo lang('c_523.77'); ?></a>
<?php if(logged_user()->isOwner()) : ?>
<a class="btn btn-sm btn-info" href="<?php echo get_page_base_url($estimate->getConvertToInvoiceURL()); ?>"  onclick="return confirm('<?php echo lang('c_157'); ?>');"><i class="fa fa-refresh"></i> <?php echo lang('c_155'); ?></a>
<a class="btn btn-sm btn-success" href="javascript:void();" data-url="<?php echo get_page_base_url($estimate->getEditURL()); ?>" data-toggle="commonmodal"><i class="fa fa-edit"></i> <?php echo lang('c_134'); ?></a>
<?php endif; ?>
</td></tr></table>

<div class="row">
<div class="col-md-6">
<div class="table-responsive">
<table class="table table-bordered">
<tr><td><?php echo lang('c_159'); ?></td><td><?php $created_by = $estimate->getCreatedBy();
if($c_company = $created_by->getCompany()) : ?><b><?php echo $c_company->getName(); ?></b> <?php echo ($c_company->getVatNo() != "" ? "<u>(".lang('c_523.118').") " . $c_company->getVatNo() . "</u>" : ""); ?><br /><?php endif; ?>
<?php echo $created_by->getName(); ?> [ <em><?php echo $created_by->getEmail(); ?></em> ]
<?php if($c_company->getAddress() != "") : ?><br /><?php echo $c_company->getAddress(); endif; ?>
<?php if($c_company->getPhoneNumber() != "") : ?><br /><?php echo $c_company->getPhoneNumber(); endif; ?>
</td></tr>
<tr><td><?php echo lang('c_160'); ?></td><td><?php $client_to = $estimate->getClient();
if($estimate->getCompanyName() != "") : ?><b><?php echo $estimate->getCompanyName(); ?></b><br /><?php endif; ?>
<?php echo $client_to->getName(); ?> [ <em><?php echo $client_to->getEmail(); ?></em> / <u>#<?php echo "CUS-".str_pad($client_to->getId(), 6, '0', STR_PAD_LEFT); ?></u> ]
<?php if($estimate->getCompanyAddress() != "") : ?><br /><?php echo $estimate->getCompanyAddress(); endif; ?></td></tr>
<tr><td><?php echo lang('c_126'); ?></td><td><?php echo $estimate->getSubject(); ?></td></tr>
<tr><td><?php echo lang('c_23'); ?></td><td> <?php $estimate_project = $estimate->getProject();
echo (isset($estimate_project) ? $estimate_project->getName() : "-"); ?></td></tr>
</table></div>
</div>
<div class="col-md-6">
<div class="table-responsive">
<table class="table table-bordered">
<tr><td><?php echo lang('c_161'); ?></td><td><b><?php echo $estimate->getEstimateNo(); ?></b></td></tr>
<tr><td><?php echo lang('c_162'); ?></td><td><?php echo date("jS F Y", $estimate->getCreatedAt()) ?></td></tr>
<tr><td><?php echo lang('c_138'); ?></td><td><?php echo date("jS F Y", $estimate->getDueDate()) ?>
<?php $estimate_duedate = $estimate->getDueDate();
if(isset($estimate_duedate) && $estimate_duedate < time() && $estimate->getStatus()) { echo" <span style=\"font-weight:bold;color:red;\">".lang('c_154')."</span>"; } ?>
</td></tr>
<tr><td><?php echo lang('c_114'); ?></td><td><?php 
  if($estimate->getStatus()) {
	  $status = "<span class='label label-success'>".lang('c_136')."</span>";
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
<table class="table">
	<thead><tr><th><?php echo lang('c_163'); ?></th><th><?php echo lang('c_142'); ?></th><th><?php echo lang('c_143'); ?></th><th class="text-right"><?php echo lang('c_144'); ?></th></tr></thead>
	<?php $sub_total = 0;
	$items = $estimate->getItems();
	if(isset($items) && is_array($items) && count($items)) :
	foreach($items as $r) : ?>
	<?php $total = number_format($r->getQuantity()*$r->getAmount(),2);
	$sub_total += $r->getAmount()*$r->getQuantity(); ?>
	<tr><td><?php echo $r->getDescription(); ?></td><td><?php echo $r->getQuantity(); ?></td><td><?php echo $r->getAmount() ?></td><td class="text-right"><?php echo $total ?></td></tr>
	<?php endforeach; 
	else : ?><tr><td colspan="4"><?php echo lang('e_2'); ?></td></tr><?php endif; ?>

	<?php $total_amount = $sub_total; ?>
	<tr class="warning text-right"><td colspan="4"><?php echo lang('c_144'); ?>: <?php echo number_format($sub_total,2) ?>
	<br /><?php if($estimate->getTaxRate() > 0) : ?>
	<?php $tax_addon = abs($sub_total/100*$estimate->getTaxRate());
	$total_amount += $tax_addon; ?>
	<?php echo lang('c_146'); ?> <?php if($estimate->getTax() != "") : ?> (<?php echo $estimate->getTax() ?>) <?php endif; ?>@ <?php echo $estimate->getTaxRate() ?>% : <?php echo number_format($tax_addon,2) ?><br />
	<?php endif; ?>
	<?php if($estimate->getTaxRate2() > 0) : ?>
	<?php $tax_addon2 = abs($sub_total/100*$estimate->getTaxRate2());
	$total_amount += $tax_addon2; ?>
	<?php echo lang('c_146.1'); ?> <?php if($estimate->getTax2() != "") : ?> (<?php echo $estimate->getTax2() ?>) <?php endif; ?>@ <?php echo $estimate->getTaxRate2() ?>% : <?php echo number_format($tax_addon2,2) ?><br />
	<?php endif; ?>
	<b><?php echo lang('c_149'); ?>: <?php echo config_option('default_currency', "$"); ?><?php echo number_format($total_amount,2) ?></b><br />
	<?php 
		$calculated_discount_amount = $estimate->getDiscountAmount();
		if($calculated_discount_amount > 0) {
			if($estimate->getDiscountAmountType() == 'percentage') {
				$calculated_discount_amount = abs($calculated_discount_amount/100*$total_amount);
			}
		}
		$total_amount = ($total_amount - $calculated_discount_amount);
	?>
	<?php if($calculated_discount_amount != "") : ?> <?php echo lang('c_523.58'); ?> (<?php echo $estimate->getDiscountAmountType() == 'percentage' ? $estimate->getDiscountAmount() . lang('c_523.59') : lang('c_523.60'); ?>) : <?php echo number_format($calculated_discount_amount,2) ?><br /><?php endif; ?>
	<b><?php echo lang('c_149.1'); ?>: <?php echo config_option('default_currency', "$"); ?><?php echo number_format($total_amount,2) ?></b>
	</td></tr>
</table></div>
</div></div>

<?php if($estimate->getNote() != "") : ?>
<hr><p><?php echo $estimate->getNote();?></p>
<?php endif; ?>

</div>
</div>