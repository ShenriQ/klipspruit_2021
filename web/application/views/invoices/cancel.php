<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_523.68').': <u>'.$invoice->getInvoiceNo().'</u>');?>

<form method="post" action="<?php echo get_page_base_url($invoice->getCancelURL());?>" id="i_cancel_invoice_form" class="form-horizontal">

<div class="form-group">

<h4><?php echo lang('c_523.69'); ?></h4>
<p><?php echo lang('c_523.70'); ?></p>

</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-danger" data-loading-text="<?php echo lang('c_277'); ?>" id="i_cancel_invoice_submit"><?php echo lang('c_523.71'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_350'); ?></a>
</div>

</form>
