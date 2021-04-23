<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($transaction_log->isNew() ? lang('c_180').' ('.lang('c_173').' # '.$invoice->getInvoiceNo().')' : lang('c_374').' # '.$transaction_log->getId())); ?>

<form method="post" action="<?php echo get_page_base_url($transaction_log->isNew() ? $invoice->getCreatePaymentURL() : $transaction_log->getEditURL("payment")); ?>" id="i_payment_form" class="form-horizontal">

<div class="form-group">
	<label class="col-md-4 label-heading"><?php echo lang('c_152'); ?></label>
	<div class="col-md-8">
	<input type="text" name="amount" value="<?php echo clean_field($amount); ?>" class="form-control" />
	</div>
</div>

<div class="form-group">
	<label class="col-md-4 label-heading"><?php echo lang('c_48'); ?></label>
	<div class="col-md-8">
		<input type="text" name="description" class="form-control" id="description" value="<?php echo clean_field($description); ?>" />
	</div>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_payment_submit"><?php echo ($transaction_log->isNew() ? lang('c_219') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>