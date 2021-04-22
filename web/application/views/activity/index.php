<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_21'));?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-clock-o"></i>
	  <h3 class="box-title"><?php echo lang('c_21'); ?></h3>
	</div>
	 
	<div class="box-body">
		
		<?php
		
		if(isset($activity_logs) && is_array($activity_logs) && count($activity_logs)) : ?>

			<h4 class="custom-m-20"><?php echo sprintf(lang('c_523.2'), $total_items, (($current_page-1)*$items_per_page)+1, ($current_page*$items_per_page)); ?></h4>

			<ul class="timeline">

				<?php foreach($activity_logs as $activity_log) : 
				list($date_element_class, $log_date_value) = get_date_format_array($activity_log->getCreatedAt()); ?>

				<li>
				<i class="<?php echo get_activity_icon($activity_log->getModel()); ?>"></i>

				<div class="timeline-item">
					<span class="time"><i class="fa fa-clock-o"></i> <?php echo $log_date_value; ?></span>

					<div class="timeline-body <?php echo $date_element_class; ?>"><?php 
					
					$created_by_name = '';
					$create_by = $activity_log->getCreatedBy();
					
					if($create_by) {
						$created_by_name = ($create_by->getId() == logged_user()->getId() ? lang('c_24') : '<u>'.$create_by->getName().'</u>').' ';
					}
					
					$log_object = $activity_log->getObject();
					$raw_data = unserialize_data($activity_log->getRawData());
					
					$log_row = $created_by_name.' '.$raw_data['message'].' '.(isset($log_object) && $log_object->getObjectURL() ? '<a href="'.get_page_base_url($log_object->getObjectURL()).'">'.$raw_data['title'].'</a>' : $raw_data['title']);
					
					echo $log_row;?>
								
					</div>
					
				</div>
				</li>
				
				<?php endforeach; ?>
				
			</ul>

			<?php if(isset($total_pages) && $total_pages > 1) : ?>
				<div class="clearfix"></div>
				<div class="pagination-container margin-top-20 margin-bottom-20">		
					<?php echo get_simple_paginate($total_pages, $current_page, $current_page_url, ($is_filtered ? '&page=#PAGE#' : '?page=#PAGE#')); ?>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<p><?php echo lang('e_2'); ?></p>
		<?php endif; ?>

	</div>
	
</div>