<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_172')); 

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
		$(".invoices").DataTable({
		"iDisplayLength": 15,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"order": [[ 0, "desc" ]],
		language: {
			search: "_INPUT_",
			searchPlaceholder: "'.lang('c_25').'",
			"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'.get_site_language(true).'.json"
		}  
		});
		$(\'#invoiceFilter\').on(\'change\', function() {
			var location = "'.$current_page_base_url.'";
			var sort_by = $("#invoiceFilter").val();
			if (sort_by != "") {
				location = location + "?sort_by=" + sort_by;
			}
			window.location.href = location;
			return false;
		});		
	});
})(jQuery);
</script>
');	

$default_currency = config_option('default_currency', "$");

?>

<div class="row custom-mb-10">
	<div class="col-md-6">
		<select class="form-control custom-fixed-width-select" id="invoiceFilter">
			<option value="" <?php echo ($sort_by == "" ? ' selected="selected"' : ''); ?>><?php echo lang('c_523.67'); ?></option>
			<?php if(logged_user()->isOwner()) : ?><option value="recurring" <?php echo ($sort_by == "recurring" ? ' selected="selected"' : ''); ?>><?php echo lang('c_523.65'); ?></option><?php endif; ?>
			<option value="paid_cancelled" <?php echo ($sort_by == "paid_cancelled" ? ' selected="selected"' : ''); ?>><?php echo lang('c_523.66'); ?></option>
		</select>            
	</div>
	<?php if(logged_user()->isOwner()) : ?>
	<div class="col-md-6 text-right">
		<a href="javascript:void();" data-url="<?php echo get_page_base_url('invoices/create'.(isset($project) ? '/'.$project->getId() : '')); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_174'); ?></a>
	</div>
	<?php endif; ?>
</div>

<div class="box box-solid">

	<?php if(!isset($is_project_panel)) : ?>

	<div class="box-header with-border">
		<i class="fa fa-file-text"></i>
		<?php if($sort_by == "recurring" && logged_user()->isOwner()): ?>
			<h3 class="box-title"><?php echo lang('c_523.65'); ?></h3>
		<?php elseif($sort_by == "paid_cancelled") : ?>
			<h3 class="box-title"><?php echo lang('c_523.66'); ?></h3>
		<?php else : ?>
			<h3 class="box-title"><?php echo lang('c_172'); ?></h3>
		<?php endif; ?>
	</div>

	<?php endif; ?>	

	<div class="box-body <?php echo (isset($is_project_panel) ? ' no-padding' : ''); ?>">

	
		<?php if(isset($invoices) && is_array($invoices) && count($invoices)) : ?>
			<?php if($sort_by == "recurring" && logged_user()->isOwner()): ?>

				<div class="box box-solid">
					<div class="box-body well">
						<h4><i class="fa fa-clock-o"></i> <?php echo lang('c_523.110'); ?></h4>
						<div class="form-group">
							<label class="col-md-2"><?php echo lang('c_523.111'); ?> *</label>
							<div class="col-md-10">
								<div>
									<pre><b class="custom-backgound-lightyellow-underline">wget <?php echo get_page_base_url("cron"); ?></b> or <b class="custom-backgound-lightyellow-underline">wget -q -O- <?php echo get_page_base_url("cron"); ?></b></pre>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
				
				<table class="table table-striped table-bordered invoices">
				
					<thead>
						<th>#</th>
						<th><?php echo lang('c_29'); ?></th>
						<th><?php echo lang('c_23'); ?></th>
						<th><?php echo lang('c_523.95'); ?></th>
						<th><?php echo lang('c_523.80'); ?></th>
						<th><?php echo lang('c_523.85'); ?></th>
						<th><?php echo lang('c_523.82'); ?></th>
						<th><?php echo lang('c_162'); ?></th>
						<th><?php echo lang('c_114'); ?></th>
						<th></th>
					</thead><tbody>

					<?php foreach($invoices as $invoice) : ?>

					<tr>
						<td><?php echo $invoice->getId(); ?></td>
						<td><b><?php echo ($invoice->getClient() ? '<a href="' . get_page_base_url($invoice->getClient()->getObjectURL()) . '">' . $invoice->getClient()->getName() . '</a></b><br><small class="custom-small-grey-color">' . ($invoice->getClient()->getCompany() ? $invoice->getClient()->getCompany()->getName() : lang('c_523.52'))  . '</small>' : lang('c_153')); ?></td>
						<td><?php echo ($invoice->getProject() ?  '<a href="' . get_page_base_url($invoice->getProject()->getObjectURL()) . '">' . $invoice->getProject()->getName() . '</a>' : lang('c_153')); ?></td>
						<td><?php $invoice_next_recurring_date = $invoice->getNextRecurringDate(); 
							if(isset($invoice_next_recurring_date)) { 
							echo format_date($invoice_next_recurring_date, "m-d-Y");
							} else { echo lang('c_153');  }			
							?>	
						</td>
						<td><?php echo $invoice->getRecurringValue(); ?> &nbsp; <?php echo lang('c_523.81.'.$invoice->getRecurringType()); ?></td>
						<td><?php echo $invoice->getNoOfCyclesCompleted(); ?>/<?php echo $invoice->getNoOfCycles() > 0 ? $invoice->getNoOfCycles() : '&infin;'; ?></td>
						<td><?php echo $default_currency; ?><?php echo number_format($invoice->getTotalAmount(), 2); ?></td>
						<td><?php echo format_date($invoice->getCreatedAt(), 'j M. Y H:i'); ?></td>
						<td><?php 
							if($invoice->getNoOfCycles() == 0 || $invoice->getNoOfCycles() > $invoice->getNoOfCyclesCompleted()) {
								$status = "<span class='label label-success'>".lang('c_523.83')."</span>";
							} else {
								$status = "<span class='label label-danger'>".lang('c_523.84')."</span>"; 
							}
							echo $status;
						?></td>
						<td width="10%">

						<div class="pull-right">
							<div class="btn-group">
								<?php if(logged_user()->isOwner()) : ?>
								<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
								<ul class="dropdown-menu pull-right" role="menu">
									<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($invoice->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_175'); ?></a></li>
									<li><a href="<?php echo get_page_base_url($invoice->getDownloadURL()); ?>" target="_blank"><?php echo lang('c_523.94'); ?></a></li>
									<li class="divider"></li>
									<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($invoice, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
								</ul>
								<?php endif; ?>
								
							</div>
						</div>

						</td>
						
					</tr>

					<?php endforeach; ?>

					</tbody></table>

			<?php else : ?>

				<div class="table-responsive">
				<table class="table table-striped table-bordered invoices">
				
					<thead>
						<th><?php echo lang('c_151'); ?></th>
						<th><?php echo lang('c_29'); ?></th>
						<th><?php echo lang('c_23'); ?></th>
						<th><?php echo lang('c_152'); ?></th>
						<th><?php echo lang('c_114'); ?></th>
						<th><?php echo lang('c_523.92'); ?></th>
						<th><?php echo lang('c_138'); ?></th>
						<th></th>
					</thead><tbody>

					<?php foreach($invoices as $invoice) : ?>			

					<tr>

						<td><b><a href="<?php echo get_page_base_url($invoice->getObjectURL()); ?>" target="_blank"><?php echo $invoice->getInvoiceNo(); ?></a></b><?php echo $invoice->getRecurringInvoiceId() > 0 && logged_user()->isOwner() ? ' <span class="custom-small-grey-color">(' . $invoice->getRecurringInvoiceId() . ')</span>' : ''; ?> </td>
						<td><b><?php echo ($invoice->getClient() ? '<a href="' . get_page_base_url($invoice->getClient()->getObjectURL()) . '">' . $invoice->getClient()->getName() . '</a></b><br><small class="custom-small-grey-color">' . ($invoice->getClient()->getCompany() ? $invoice->getClient()->getCompany()->getName() : lang('c_523.52'))  . '</small>' : lang('c_153')); ?></td>
						<td><?php echo ($invoice->getProject() ?  '<a href="' . get_page_base_url($invoice->getProject()->getObjectURL()) . '">' . $invoice->getProject()->getName() . '</a>' : lang('c_153')); ?></td>
						<td><?php echo $default_currency; ?><?php echo number_format($invoice->getTotalAmount(), 2); ?></td>
						<td class="text-center"><?php 
							if(!$invoice->getIsCancelled()) {
								if($invoice->getPaidAmount() > 0) {
									if($invoice->getPaidAmount() < $invoice->getTotalAmount()) {
									$status = "<span class='label label-success'>".lang('c_177')."</span>";
									} else {
									$status = "<span class='label label-success'>".lang('c_178')."</span>";
									}
									
									$status .= '<p class="custom-currency-text"><small>'.$default_currency.number_format($invoice->getPaidAmount(), 2).'</small></p>';
									
								} else {
									$status = "<span class='label label-warning'>".lang('c_179')."</span>";
								}	
						} else {
							$status = "<span class='label label-danger'>".lang('c_137')."</span>";
						}
						echo $status;
						if($invoice->getIsOnlinePaymentDisabled() && logged_user()->isOwner()) echo '<br><small class="text-blue">' . lang('c_523.53.1') . '</small>';
						?></td>
						<td><?php $invoice_issuedate = $invoice->getIssueDate(); 
							if(isset($invoice_issuedate)) { 
							echo format_date($invoice_issuedate, "m-d-Y");
							if($invoice_issuedate > time()) echo '<br><span class="label label-info">'.lang('c_523.93').'</span>';
							} else { echo lang('c_153');  }			
							?>	
						</td>
						<td><?php $invoice_duedate = $invoice->getDueDate(); 
							if(isset($invoice_duedate)) { 
							echo format_date($invoice_duedate, "m-d-Y");
							if($invoice_duedate < time() && $invoice->getPaidAmount() < $invoice->getTotalAmount()) echo '<br><span class="label label-danger">'.lang('c_154').'</span>';
							} else { echo lang('c_153');  }			
							?>	
						</td>
						<td width="10%">

						<div class="pull-right">
							<div class="btn-group">
								<?php if(logged_user()->isOwner()) : ?>
								<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
								<ul class="dropdown-menu pull-right" role="menu">
									<li><a href="<?php echo get_page_base_url($invoice->getObjectURL()); ?>" target="_blank"><?php echo lang('c_181'); ?></a></li>
									<?php if(!$invoice->getIsCancelled()) : ?>
									<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($invoice->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_175'); ?></a></li>
									<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($invoice->getCancelURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_523.74'); ?></a></li>
									<li><a href="<?php echo get_page_base_url($invoice->getDownloadURL()); ?>" target="_blank"><?php echo lang('c_523.94'); ?></a></li>
									<li><a href="<?php echo get_page_base_url($invoice->getCloneURL()).'?ref='.base64_encode(current_url()); ?>"><?php echo lang('c_523.99'); ?></a></li>
									<li><a href="<?php echo get_page_base_url($invoice->getSendNotificationURL()).'?ref='.base64_encode(current_url()); ?>"><?php echo lang('c_523.97'); ?></a></li>
									<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($invoice->getCreatePaymentURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_180'); ?></a></li>
									<?php endif; ?>
									<li class="divider"></li>
									<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($invoice, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
								</ul>
								<?php else : ?>
								<a href="<?php echo get_page_base_url($invoice->getObjectURL()); ?>" class="btn btn-xs btn-default" target="_blank"><?php echo lang('c_181'); ?></a>
								<?php endif; ?>
								
							</div>
						</div>

						</td>
						
					</tr>

					<?php endforeach; ?>
					
					</tbody></table>

				</div>

			<?php endif; ?>
		<?php else : ?>
			<?php echo lang('e_2'); ?>
		<?php endif; ?>

	</div>

</div>
