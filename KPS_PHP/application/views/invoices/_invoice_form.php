<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", ($invoice->isNew() ? lang('c_174') : lang('c_175').' # '.$invoice->getInvoiceNo())); ?>

<form method="post" action="<?php echo get_page_base_url($invoice->isNew() ? 'invoices/create' : $invoice->getEditURL()); ?>" id="i_invoice_form" class="form-horizontal">

<?php if ($invoice->isNew() || $is_recurring) : ?>
<div class="form-group well">
	<div class="row">
		<div class="col-md-3">
			<label class="label-heading"><input name="is_recurring" id="isRecurring" type="checkbox"<?php echo ($is_recurring ? ' checked="checked"' : ''); ?><?php echo !$invoice->isNew() ? ' readonly="readonly"' : ''; ?> /> &nbsp; <?php echo lang('c_523.79'); ?></label>
		</div>
		<div class="col-md-9" id="recurringBox"<?php echo ($is_recurring ? '' : ' style="display: none;"'); ?>>
			<div class="row">
				<div class="col-xs-4 custom-p-2">
					<input type="number" name="recurring_value" value="<?php echo clean_field($recurring_value); ?>" id="recurring_value" min="1" class="form-control" placeholder="<?php echo lang('c_523.80'); ?>">
				</div>
				<div class="col-xs-4 custom-p-2">
					<select class="form-control" name="recurring_type" id="recurring_type">
						<option value="days"<?php echo $recurring_type == "days" ? ' selected="selected"' : ''; ?>><?php echo lang('c_523.81.days'); ?></option>
						<option value="weeks"<?php echo $recurring_type == "weeks" ? ' selected="selected"' : ''; ?>><?php echo lang('c_523.81.weeks'); ?></option>
						<option value="months"<?php echo $recurring_type == "months" ? ' selected="selected"' : ''; ?>><?php echo lang('c_523.81.months'); ?></option>
						<option value="years"<?php echo $recurring_type == "years" ? ' selected="selected"' : ''; ?>><?php echo lang('c_523.81.years'); ?></option>
					</select>
				</div>
				<div class="col-xs-4 custom-p-2">
					<input type="number" name="no_of_cycles" id="no_of_cycles" min="1" value="<?php echo $no_of_cycles > 0 ? $no_of_cycles : ''; ?>" class="form-control" placeholder="<?php echo lang('c_523.85'); ?>">
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

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

echo select_box("project_id", $projects_options, $project_id, ' id="project_id" class="form-control"');

?>	
</div>
 
<div class="form-group">
	<input type="text" name="issue_date" class="form-control issue_datepicker" value="<?php echo clean_field($issue_date); ?>" placeholder="<?php echo lang('c_523.92'); ?>" />
</div>

<div class="form-group">
	<input type="text" name="due_date" class="form-control due_datepicker" value="<?php echo clean_field($due_date); ?>" placeholder="<?php echo lang('c_138'); ?>" />
</div>

<div class="form-group">
	<input type="text" name="reference" value="<?php echo clean_field($reference); ?>" class="form-control" placeholder="<?php echo lang('c_523.96'); ?>" />
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

<?php if ($invoice->isNew()) : ?>
<div class="box box-primary" id="invoiceBasedTrackedTimeProject" style="display: none;">
	<div class="box-header with-border">
		<div class="form-group">
			<div class="radio">
				<label>
					<input type="radio" name="loadInvoiceItems" id="freeFormInvoice" value="0" checked="checked" />
					<?php echo lang('c_523.39'); ?>
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="loadInvoiceItems" id="invoiceBasedTrackedTime" value="1">
					<?php echo lang('c_523.40'); ?>
				</label>
			</div>
		</div>
	</div>
	<div class="box-body" id="invoiceBasedTrackedTimeOptions" style="display: none;">	
		<div class="form-group">
			<label for="TimePeriod" class="col-sm-3 control-label"><?php echo lang('c_523.41'); ?></label>
			<div class="col-sm-9">
				<div class="form-group">
					<div class="radio">
						<label>
							<input type="radio" name="TimePeriod" id="allUninvoicedEntries" value="0" checked="checked" />
							<?php echo lang('c_523.42'); ?>
						</label>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="TimePeriod" id="uninvoicedEntriesFrom " value="1">
							<?php echo lang('c_523.43'); ?>
						</label>
					</div>
				</div>
				<div class="row" id="uninvoicedEntriesFromOptions" style="display: none;">
					<div class="col-md-5">
						<input class="form-control time_period_datepicker" id="TimePeriodStart" type="text" placeholder="<?php echo lang('c_192'); ?>" readonly /> 
					</div>
					<div class="col-md-5">
						<input class="form-control time_period_datepicker" id="TimePeriodEnd" type="text" placeholder="<?php echo lang('c_198'); ?>" readonly />
					</div>
					<div class="col-md-2">
						<input type="button" value="Clear" class="btn btn-default" id="clear-dates">
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="GroupBy" class="col-sm-3 control-label"><?php echo lang('c_523.44'); ?></label>
			<div class="col-sm-9">
				<select class="form-control" id="GroupBy">
					<option value="separate"><?php echo lang('c_523.45'); ?></option>
					<option value="task"><?php echo lang('c_523.46'); ?></option>
					<option value="all"><?php echo lang('c_523.47'); ?></option>
				</select>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<div class="form-group">
	<div class="table-responsive">
	<table class="table table-bordered" id="item-table">
		<tr><td><b><?php echo lang('c_50'); ?></b></td><td class="col-md-4"><b><?php echo lang('c_141'); ?></b></td><td><b><?php echo lang('c_142'); ?></b></td><td><b><?php echo lang('c_143'); ?></b></td><td><b><?php echo lang('c_144'); ?></b></td></tr>
		<?php if ($invoice->isNew()) : 
			$items_count=1; ?>
			<tr id="invoice_element_<?php echo $items_count; ?>"><td><a class="btn btn-sm btn-danger" onclick="javascript:remove_invoice_item('invoice_element_<?php echo $items_count; ?>'); return false;"><i class="fa fa-trash"></i></a></td><td><input type="text" name="name[]" value="" class="form-control" /></td><td><input type="text" name="quantity[]" id="quantity_<?php echo $items_count; ?>" class="form-control item_quantity" value="0.00"></td><td><input type="text" name="amount[]" id="amount_<?php echo $items_count; ?>" class="form-control item_amount" value="0.00"></td><td><div id="total_<?php echo $items_count; ?>">0.00</div><input type="hidden" name="timelog_ids[]" id="timelog_ids_<?php echo $items_count; ?>" value="[]" /></td></tr>
		<?php else : ?>
				<?php 
				$items_count=0;
				$inv_items = $invoice->getItems(); 
				if(isset($inv_items) && is_array($inv_items) && count($inv_items)) :
					foreach($inv_items as $inv_item) :
						$items_count++; 

						$project_timelog_ids = array();
						$invoice_item_project_timelogs = $inv_item->getProjectTimelogs();
						if(isset($invoice_item_project_timelogs) && is_array($invoice_item_project_timelogs) && count($invoice_item_project_timelogs)) {
							foreach($invoice_item_project_timelogs as $invoice_item_project_timelog) {
								$project_timelog_ids[] = $invoice_item_project_timelog->getProjectTimelogId();
							}
						}
						?>
						<tr id="invoice_element_<?php echo $items_count; ?>"><td><a class="btn btn-sm btn-danger" onclick="javascript:remove_invoice_item('invoice_element_<?php echo $items_count; ?>'); return false;"><i class="fa fa-trash"></i></a></td><td><input type="text" name="name[]" value="<?php echo $inv_item->getDescription(); ?>" class="form-control" /></td><td><input type="text" name="quantity[]" id="quantity_<?php echo $items_count; ?>" class="form-control item_quantity" value="<?php echo number_format($inv_item->getQuantity(), 2); ?>" ></td><td><input type="text" name="amount[]" id="amount_<?php echo $items_count; ?>" class="form-control item_amount" value="<?php echo number_format($inv_item->getAmount(), 2); ?>"></td><td><div id="total_<?php echo $items_count; ?>"><?php echo number_format($inv_item->getAmount()*$inv_item->getQuantity(), 2); ?></div><input type="hidden" name="timelog_ids[]" id="timelog_ids_<?php echo $items_count; ?>" value="<?php echo json_encode($project_timelog_ids); ?>" /></td></tr>
					<?php
					endforeach;
				else : $items_count=1; ?>
				<tr id="invoice_element_<?php echo $items_count; ?>"><td><a class="btn btn-sm btn-danger" onclick="javascript:remove_invoice_item('invoice_element_<?php echo $items_count; ?>'); return false;"><i class="fa fa-trash"></i></a></td><td><input type="text" name="name[]" value="" class="form-control" /></td><td><input type="text" name="quantity[]" id="quantity_<?php echo $items_count; ?>" class="form-control item_quantity" value="0.00"></td><td><input type="text" name="amount[]" id="amount_<?php echo $items_count; ?>" class="form-control item_amount" value="0.00"></td><td><div id="total_<?php echo $items_count; ?>">0.00</div><input type="hidden" name="timelog_ids[]" id="timelog_ids_<?php echo $items_count; ?>" value="[]" /></td></tr>
			<?php endif; ?>				
		<?php endif; ?>
	</table></div>
	<input type="hidden" id="items" value="<?php echo $items_count; ?>" />
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
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_invoice_submit"><?php echo ($invoice->isNew() ? lang('c_52') : lang('c_53')); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<script type="text/javascript"> 

(function ($) {
	"use strict";
	$(document).ready(function() {  
		sum_sub_total();
		$('.issue_datepicker').datepicker(); 
		$('.due_datepicker').datepicker(); 
		<?php if ($invoice->isNew()) : ?>
		const clearTrackedTimeItems = function () {
			let tr_count = 0;
			let tr_remove = false;
			$('tr', $('#item-table')).each(function () {
				if(tr_count > 0) {
					let td_count = 0;
					$('td > input', $(this)).each(function () {
						if(td_count === 3) {
							if($(this).val() != "[]") {
								tr_remove = true;
							}
						}
						td_count++;
					});
					if(tr_remove === true) {
						remove_invoice_item($(this).attr("id"), true);
						tr_remove = false;
					}
				}
				tr_count++;
			});
		}
		const setTrackedTimeItems = function () {
			$.getJSON("<?php echo get_page_base_url("timesheet/invoice_items_json/"); ?>" + 
				"?project_id=" + $("#project_id").val() + 
				"&group_by=" + $("#GroupBy").children("option:selected").val() +
				(function() { 
					let timePeriod = $("input[name='TimePeriod']:checked").val();
					return (timePeriod == 1) ? "&date_filter_from=" + $("#TimePeriodStart").val() + "&date_filter_to=" + $("#TimePeriodEnd").val() : "";
				})(),
				function(result) {
					clearTrackedTimeItems();
					$.each(result, function(key, obj) {
						add_new_item(obj["description"], obj["quantity"], obj["unit_cost"], obj["total"], obj["timelog_ids"]);
					});
					sum_sub_total();
				});
		}
		$('#isRecurring').change(function() {
        	if(this.checked) {
				$("#recurringBox").show();
				$("#invoiceBasedTrackedTimeProject").hide();
				clearTrackedTimeItems();
			} else {
				$("#recurringBox").hide();
				let selected_project = parseInt($("#project_id").val());
				if(selected_project) {
					$("#invoiceBasedTrackedTimeProject").show();
				}
			}
		});
		$("#project_id").change(function(){
			let selected_option = parseInt($(this).val());
			let is_recurring = $('#isRecurring');
			if(selected_option > 0 && !is_recurring.is(":checked")) {
				$("#invoiceBasedTrackedTimeProject").show();
			} else {
				$("#invoiceBasedTrackedTimeProject").hide();
				clearTrackedTimeItems();
			}
		});
		$("#GroupBy").change(function(){
			setTrackedTimeItems();
		});
		let $dates = $('.time_period_datepicker').datepicker({
			onSelect: function(dateText) {
				setTrackedTimeItems();
			}
		}); 
		$('#clear-dates').on('click', function () {
			$dates.datepicker('setDate', null);
			setTrackedTimeItems();
		});

		$("input[name$='loadInvoiceItems']").on('click', function () {
			let selected_option = $(this).val();
			if(selected_option == 1) {
				$("#invoiceBasedTrackedTimeOptions").show();
				setTrackedTimeItems();
			} else {
				$("#invoiceBasedTrackedTimeOptions").hide();
				clearTrackedTimeItems();
			}
	   	 }); 
		 $("input[name$='TimePeriod']").on('click', function () {
			let selected_option = $(this).val();
			if(selected_option == 1) {
				$("#uninvoicedEntriesFromOptions").show();
			} else {
				$("#uninvoicedEntriesFromOptions").hide();
			}
			setTrackedTimeItems();
	   	 }); 
		<?php endif; ?>
	}); 
})(jQuery);
</script>