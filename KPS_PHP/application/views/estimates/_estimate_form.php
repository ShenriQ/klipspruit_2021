<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($estimate->isNew() ? lang('c_133') : lang('c_134').' # '.$estimate->getEstimateNo())); ?>

<form method="post" action="<?php echo get_page_base_url($estimate->isNew() ? 'estimates/create' : $estimate->getEditURL()); ?>" id="i_estimate_form" class="form-horizontal">

<div class="form-group">
	<input type="text" name="subject" value="<?php echo clean_field($subject); ?>" class="form-control" placeholder="<?php echo lang('c_126'); ?>" />
</div>

<div class="form-group">
<?php $clients_options = array(lang('c_132'));

if(isset($client_users) && is_array($client_users) && count($client_users)) :
	foreach($client_users as $client_user) :
		$clients_options[$client_user->getId()] = $client_user->getName();
	endforeach;
endif;

echo select_box("client_id", $clients_options, $client_id, ' class="form-control"');

?>	
</div>

<div class="form-group">
<?php $projects_options = array(lang('c_135'));

if(isset($projects) && is_array($projects) && count($projects)) :
	foreach($projects as $project) :
		$projects_options[$project->getId()] = $project->getName();
	endforeach;
endif;

echo select_box("project_id", $projects_options, $project_id, ' class="form-control"');

?>	
</div>

<div class="form-group">
	<select name="status" class="form-control">
	<option value="1"<?php echo $status == 1 ? ' selected="selected"' : ''; ?>><?php echo lang('c_136'); ?></option>
	<option value="0"<?php echo $status == 0 ? ' selected="selected"' : ''; ?>><?php echo lang('c_137'); ?></option>
	</select>
</div>

<div class="form-group">
	<input type="text" name="due_date" class="form-control due_datepicker" value="<?php echo clean_field($due_date); ?>" placeholder="<?php echo lang('c_138'); ?>" />
</div>

<div class="form-group">
  <textarea class="form-control" rows="5" name="note" placeholder="<?php echo lang('c_523.54'); ?>"><?php echo clean_field($note); ?></textarea>
</div>

<div class="form-group">
  <textarea class="form-control" rows="5" name="private_note" placeholder="<?php echo lang('c_523.55'); ?>"><?php echo clean_field($private_note); ?></textarea>
</div>

<div class="form-group">
	<label class="label-heading"><h4><?php echo lang('c_140'); ?></h4></label>
</div>

<div class="form-group">
	<div class="table-responsive">

	<table class="table table-bordered" id="item-table">
		<tr><td><b><?php echo lang('c_50'); ?></b></td><td class="col-md-4"><b><?php echo lang('c_141'); ?></b></td><td><b><?php echo lang('c_142'); ?></b></td><td><b><?php echo lang('c_143'); ?></b></td><td><b><?php echo lang('c_144'); ?></b></td></tr>
		<?php if ($estimate->isNew()) : 
			$items_count=1; ?>
			<tr id="invoice_element_<?php echo $items_count; ?>"><td><a class="btn btn-sm btn-danger" onclick="javascript:remove_invoice_item('invoice_element_<?php echo $items_count; ?>'); return false;"><i class="fa fa-trash"></i></a></td><td><input type="text" name="name[]" value="" class="form-control" /></td><td><input type="text" name="quantity[]" id="quantity_<?php echo $items_count; ?>" class="form-control item_quantity" value="0.00"></td><td><input type="text" name="amount[]" id="amount_<?php echo $items_count; ?>" class="form-control item_amount" value="0.00"></td><td><div id="total_<?php echo $items_count; ?>">0.00</div></td></tr>
		<?php else : ?>
				<?php 
				$items_count=0;
				$inv_items = $estimate->getItems(); 
				if(isset($inv_items) && is_array($inv_items) && count($inv_items)) :
					foreach($inv_items as $inv_item) :
						$items_count++; ?>
						<tr id="invoice_element_<?php echo $items_count; ?>"><td><a class="btn btn-sm btn-danger" onclick="javascript:remove_invoice_item('invoice_element_<?php echo $items_count; ?>'); return false;"><i class="fa fa-trash"></i></a></td><td><input type="text" name="name[]" value="<?php echo $inv_item->getDescription(); ?>" class="form-control" /></td><td><input type="text" name="quantity[]" id="quantity_<?php echo $items_count; ?>" class="form-control item_quantity" value="<?php echo number_format($inv_item->getQuantity(), 2); ?>" ></td><td><input type="text" name="amount[]" id="amount_<?php echo $items_count; ?>" class="form-control item_amount" value="<?php echo number_format($inv_item->getAmount(), 2); ?>"></td><td><div id="total_<?php echo $items_count; ?>"><?php echo number_format($inv_item->getAmount()*$inv_item->getQuantity(), 2); ?></div></td></tr>
					<?php
					endforeach;
				else : $items_count=1; ?>
				<tr id="invoice_element_<?php echo $items_count; ?>"><td><a class="btn btn-sm btn-danger" onclick="javascript:remove_invoice_item('invoice_element_<?php echo $items_count; ?>'); return false;"><i class="fa fa-trash"></i></a></td><td><input type="text" name="name[]" value="" class="form-control" /></td><td><input type="text" name="quantity[]" id="quantity_<?php echo $items_count; ?>" class="form-control item_quantity" value="0.00"></td><td><input type="text" name="amount[]" id="amount_<?php echo $items_count; ?>" class="form-control item_amount" value="0.00"></td><td><div id="total_<?php echo $items_count; ?>">0.00</div></td></tr>
			<?php endif; ?>				
		<?php endif; ?>
	</table></div>
	<input type="hidden" name="items" id="items" value="<?php echo $items_count; ?>" />
	<input type="hidden" name="items_count" id="items_count" value="<?php echo $items_count; ?>" />
   <input type="button" class="btn btn-info" value="<?php echo lang('c_145'); ?>" onclick='add_new_item()' /> 
</div>

<div class="form-group">
	<label class="col-md-4 label-heading"><?php echo lang('c_146'); ?></label>
	<div class="col-md-8">
		<input type="text" name="tax" class="form-control" id="tax" value="<?php echo clean_field($tax); ?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-md-4 label-heading"><?php echo lang('c_147'); ?> %</label>
	<div class="col-md-8">
	<input type="text" name="tax_rate" class="form-control" id="tax_rate" value="<?php echo clean_field($tax_rate); ?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-md-4 label-heading"><?php echo lang('c_523.56'); ?></label>
	<div class="col-md-8">
		<input type="text" name="tax2" class="form-control" id="tax2" value="<?php echo clean_field($tax2); ?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-md-4 label-heading"><?php echo lang('c_523.57'); ?> %</label>
	<div class="col-md-8">
	<input type="text" name="tax_rate2" class="form-control" id="tax_rate2" value="<?php echo clean_field($tax_rate2); ?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-md-4 label-heading"><?php echo lang('c_523.58'); ?></label>
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-8">
				<input class="form-control" id="discount_amount" name="discount_amount" type="text" value="<?php echo clean_field($discount_amount); ?>" placeholder="<?php echo lang('c_523.58'); ?>">
			</div>
			<div class="col-md-4">
				<select name="discount_amount_type" id="discount_amount_type" class="form-control">
					<option value="percentage"<?php echo $discount_amount_type == "percentage" ? ' selected="selected"' : ''; ?>><?php echo lang('c_523.59'); ?></option>
					<option value="fixed"<?php echo $discount_amount_type == "fixed" ? ' selected="selected"' : ''; ?>><?php echo lang('c_523.60'); ?></option>
				</select>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 label-heading"><?php echo lang('c_148'); ?></label>
	<div class="col-md-9">
		<div class="table-responsive">	
		<table class="table table-bordered">
		<tr><td><?php echo lang('c_149'); ?><br><span class="custom-small-grey-color"><?php echo lang('c_523.63'); ?></span></td><td><div id="sub_total">0.00</div></td></tr>
		<tr><td><?php echo lang('c_146'); ?> <div id="tax_name"></div></td><td><div id="tax_amount">0%</div><div id="tax_total_amount">0.00</div></td></tr>
		<tr><td><?php echo lang('c_523.56'); ?> <div id="tax2_name"></div></td><td><div id="tax2_amount">0%</div><div id="tax2_total_amount">0.00</div></td></tr>
		<tr><td><?php echo lang('c_149'); ?><br><span class="custom-small-grey-color"><?php echo lang('c_523.64'); ?></span></td><td><div id="total_payment"></div></td></tr>
		<tr class="custom-backgound-lightyellow"><td><?php echo lang('c_523.58'); ?></td><td><div id="display_discount_amount">0.00</div></td></tr>
		<tr><td><b><?php echo lang('c_144'); ?></b></td><td><div id="total_payment_after_discount"></div></td></tr>
		</table></div>
	</div>
</div>

<div class="form-group well">
	<label class="label-heading"><input name="is_online_payment_disabled" type="checkbox"<?php echo ($is_online_payment_disabled ? ' checked="checked"' : ''); ?> /> &nbsp; <?php echo lang('c_523.53'); ?></label><br>
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_estimate_submit"><?php echo ($estimate->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<script type="text/javascript">
(function ($) {
	"use strict";
	$(document).ready(function() {  
		sum_sub_total(); $('.due_datepicker').datepicker(); 
	}); 
})(jQuery);
</script>