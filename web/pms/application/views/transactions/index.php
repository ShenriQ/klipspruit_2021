<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_375'));

tpl_assign("header_for_layout", '
<link href="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.css").'" rel="stylesheet">
');	

tpl_assign("footer_for_layout", '
<script src="'.get_page_base_url("public/assets/vendor/datatables/js/jquery.dataTables.min.js").'"></script>
<script src="'.get_page_base_url("public/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js").'"></script>
<script>
(function ($) {
	"use strict";
	$(document).ready(function() {
		$(".transactions").DataTable({
			"iDisplayLength": 15,
			"bLengthChange": false,
			"bFilter": false,
			"bInfo": false,
			"bSort": false,
			language: {
					search: "_INPUT_",
					searchPlaceholder: "'.lang('c_25').'",
					"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"

			}  
		});
	});
})(jQuery);
</script>
');	

?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('transactions/create/expense/0'); ?>" class="btn btn-primary custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_372'); ?></a></p>
<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-list-alt"></i>
	  <h3 class="box-title"><?php echo lang('c_375'); ?></h3>
	</div>
	
	<div class="box-body">

	<?php if(isset($transaction_logs) && is_array($transaction_logs) && count($transaction_logs)) : ?>
	
		<div class="table-responsive">
		<table class="table table-striped table-bordered transactions">

		<thead>
			<th width="15%"><?php echo lang('c_205'); ?></th>
			<th width="40%" class="hidden-sm hidden-xs"><?php echo lang('c_48'); ?></th>
			<th  width="25%" class="hidden-xs"><?php echo lang('c_159'); ?></th>
			<th width="15%"><?php echo lang('c_152'); ?>&nbsp;(<?php echo config_option('default_currency', "$"); ?>)</th>
			<th></th>
		</thead><tbody>

		<?php foreach($transaction_logs as $transaction_log) : ?>			

			<tr>

				<td><?php echo format_date($transaction_log->getCreatedAt(), "m-d-Y H:i"); ?></b></td>
				<td class="hidden-sm hidden-xs"><?php echo $transaction_log->getDescription(); ?>
				<?php if($transaction_log->getTransactionType() == 'expense') { 
					$reference_object = $transaction_log->getReferenceProject(); 					
				} else {
					$reference_object = $transaction_log->getReferenceInvoice(); 
				}
				if(isset($reference_object)) : ?>(Ref# <a href="<?php echo get_page_base_url($reference_object->getObjectURL()); ?>" target="_blank"><?php echo $reference_object->getName(); ?></a>)<?php endif; ?>
				</td>

				<td class="hidden-xs">
				<?php $credit_account = $transaction_log->getCreditAccount();
				if($credit_account) : ?><?php echo $credit_account->getEmail(); else : ?>-<?php endif; ?> 
				</td>

				<td><?php if($transaction_log->getTransactionType() == 'payment') : ?>
				<b class="custom-color-green"><?php echo number_format($transaction_log->getAmount(), 2); ?></b>
				<?php else : ?>
				<b class="custom-color-red">-<?php echo number_format($transaction_log->getAmount(), 2); ?></b>
				<?php endif; ?></td>
								
				<td>
				
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($transaction_log->getEditURL($transaction_log->getTransactionType())); ?>" data-toggle="commonmodal"><?php echo lang('c_376'); ?></a></li>
							<li><a href="<?php echo get_page_base_url($transaction_log->getRemoveURL()).'?ref='.base64_encode(current_url()); ?>" onclick="return confirm('<?php echo lang('c_209'); ?>');"><?php echo lang('c_208'); ?></a></li>
						</ul>
					</div>
				</div>
								
				</td>

			</tr>

		<?php endforeach; ?>
		
		</tbody></table></div>
	
	<?php else : ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>

</div>
	
</div>