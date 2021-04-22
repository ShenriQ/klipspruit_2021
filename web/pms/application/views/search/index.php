<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_523.1'));?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-search"></i>
	  <h3 class="box-title"><?php echo lang('c_523.1'); ?></h3>
	</div>
	
	<div class="box-body">
		
		<div class="row">		
			<div class="col-sm-12">

				<form action="" method="GET" id="_searchpage" class="well">
					<div class="row">
						<div class="col-md-3">
							<input class="form-control" id="term" name="term" placeholder="<?php echo lang('c_523.4'); ?>" value="<?php echo clean_field($term); ?>" type="text">
						</div>
						<div class="col-md-3">
							<div class="form-group">
							<?php $projects_options = array("" => lang('c_523.5'));
							
							if(isset($active_projects) && is_array($active_projects) && count($active_projects)) :
								foreach($active_projects as $active_project) :
									$projects_options[$active_project->getId()] = $active_project->getName();
								endforeach;
							endif;
							
							echo select_box("project", $projects_options, $project, ' id="projectOptions" class="form-control"');
							
							?>	
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							<?php $models_options = array("" => lang('c_523.3'));
							
							if(isset($valid_models) && is_array($valid_models) && count($valid_models)) :
								foreach($valid_models as $key => &$valid_model) :
									$models_options[$key] = lang("c_523.3.".$key);
								endforeach;
							endif;
							
							echo select_box("model", $models_options, $model, ' id="modelOptions" class="form-control"');
							
							?>	
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							<?php $time_periods_options = array("" => lang('c_523.6'));
							
							if(isset($time_periods) && is_array($time_periods) && count($time_periods)) :
								foreach($time_periods as $key => &$time_period_value) :
									$time_periods_options[$key] = lang("c_523.6.".$key);
								endforeach;
							endif;
							
							echo select_box("time_period", $time_periods_options, $time_period, ' id="timePeriodsOptions" class="form-control"');
							
							?>	
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							<input class="btn btn-primary" type="submit" value="<?php echo lang('c_523.4'); ?>" required />
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<?php if(isset($search_results) && is_array($search_results) && count($search_results)) : ?>

			<h4 class="custom-m-20"><?php echo sprintf(lang('c_523.2'), $total_items, (($current_page-1)*$items_per_page)+1, ($current_page*$items_per_page)); ?></h4>

			<ul class="timeline">

				<?php foreach($search_results as $search_result) : 
				list($date_element_class, $log_date_value) = get_date_format_array($search_result->getCreatedAt()); ?>

				<li>
				<i class="<?php echo get_activity_icon($search_result->getModelName()); ?>"></i>

				<div class="timeline-item">
					<span class="time"><i class="fa fa-clock-o"></i> <?php echo $log_date_value; ?></span>

					<div class="timeline-body <?php echo $date_element_class; ?>"><?php 
					
					$created_by_name = '';
					$create_by = $search_result->getCreatedBy();
					
					if($create_by) {
						$created_by_name = ($create_by->getId() == logged_user()->getId() ? lang('c_24') : '<u>'.$create_by->getName().'</u>').' ';
					}
										
					$result_row = $created_by_name.': '.($search_result->getObjectURL() ? '<a href="'.get_page_base_url($search_result->getObjectURL()).'">'.$search_result->getName().'</a>' : $search_result->getName());
					echo $result_row;?>
								
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
			<h5><?php echo lang('e_2'); ?><h5>
		<?php endif; ?>

	</div>
	
</div>