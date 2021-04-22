<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_482')); ?>

<p><div class="row">
	<div class="col-md-12">
		
	</div>
</div>
</p>
<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-newspaper-o"></i>
	  <h3 class="box-title"><?php echo lang('c_482'); ?></h3>
	  <div class="box-tools pull-right">
		<a href="javascript:void();" data-url="<?php echo get_page_base_url($lead->getEditURL()); ?>"  data-toggle="commonmodal" class="btn btn-box-tool custom-m-3"><i class="fa fa-edit"></i> <?php echo lang('c_508'); ?></a>
	  </div>	  
	</div>
	
	<div class="box-body">

		<div class="row">
			
			<div class="col-md-8">
		
				<div class="box">
				
					<div class="box-header">
					  <h3 class="box-title"><?php $form = $lead->getForm(); echo $form->getTitle(); ?></h3>
					</div>
					
					<div class="box-body">
						<div class="table-responsive">
							<table class="table table-striped">

								<?php if($form->getIsCollectUserinfo()) : ?>

								<tr><td width="25%"><h4><?php echo lang('c_489'); ?></h4></td><td align="right"><?php $client = $lead->getClient(); if(!isset($client)) : ?><a href="<?php echo get_page_base_url($lead->getCreateClientURL()); ?>" class="btn btn-sm btn-success" onclick="return confirm('<?php echo lang('c_511'); ?>')"><?php echo lang('c_510'); ?></a><?php endif; ?></td></tr>
								<tr><td><?php echo lang('c_141'); ?></td><td><?php echo $lead->getName(); ?></td></tr>
								<tr><td><?php echo lang('c_475'); ?></td><td><?php echo $lead->getEmail(); ?></td></tr>
								<tr><td><?php echo lang('c_77'); ?></td><td><?php echo $lead->getAddress(); ?></td></tr>
								<tr><td><?php echo lang('c_476'); ?></td><td><?php echo $lead->getCity(); ?></td></tr>
								<tr><td><?php echo lang('c_477'); ?></td><td><?php echo $lead->getState(); ?></td></tr>
								<tr><td><?php echo lang('c_478'); ?></td><td><?php echo $lead->getPostcode(); ?></td></tr>
								<tr><td><?php echo lang('c_479'); ?></td><td><?php echo $lead->getCountry(); ?></td></tr>
								<tr><td><?php echo lang('c_78'); ?></td><td><?php echo $lead->getPhoneNumber(); ?></td></tr>
								
								<?php endif; ?>
								
								<tr><td width="25%" colspan="2"><h4><?php echo lang('c_493'); ?></h4></td></tr>
								
								<?php $lead_elements = $lead->getElements();
								if(isset($lead_elements) && is_array($lead_elements) && count($lead_elements)) : ?>
								<?php foreach ($lead_elements as $lead_element) : ?>
								<tr><td><?php echo $lead_element->getElement()->getFieldName(); ?></td><td><?php echo $lead_element->getElementValue(); ?></td></tr>
								<?php endforeach; endif; ?>


							</table>
						</div>
					</div>
						
				</div>
		
			</div>
		
			<div class="col-md-4">
			
				<div class="box">
				
					<div class="box-header">
					  <h3 class="box-title">Lead</h3>
					</div>
					
					<div class="box-body">
						<div class="table-responsive">
							<table class="table table-striped">
								<tr><td><?php echo lang('c_23'); ?></td><td><?php $lead_project = $lead->getProject(); if(isset($lead_project)) : ?>
									<a href="<?php echo $lead_project->getObjectURL(); ?>"><?php echo $lead_project->getName(); ?></a>
								<?php else : echo lang('c_153'); endif; ?></td></tr>
								<tr><td><?php echo lang('c_465'); ?></td><td><?php echo $lead->getStatus()->getName(); ?></td></tr>
								<tr><td><?php echo lang('c_461'); ?></td><td><?php echo $lead->getSource()->getName(); ?></td></tr>
								<tr><td><?php echo lang('c_297'); ?></td><td><?php $lead_assignee = $lead->getAssignee(); if(isset($lead_assignee)) : ?>
								<u><?php echo $lead_assignee->getName(); ?></u>
								<?php else : echo lang('c_153'); endif; ?></td></tr>
								<tr><td><?php echo lang('c_205'); ?></td><td><?php echo format_date($lead->getCreatedAt()); ?></td></tr>
								<tr><td><?php echo lang('c_506'); ?></td><td><?php echo $lead->getIpAddress(); ?></td></tr>
							</table>
						</div>
					</div>
						
				</div>
			
			</div>
		
		</div>

		<div class="row">
			
			<div class="col-md-12">
		
				<div class="box">
				
					<div class="box-header">
					  <h3 class="box-title"><?php echo lang('c_509'); ?></h3>
					</div>
					
					<div class="box-body">
						
						<div id="responseForm"></div>

						<div class="well">		
										
						<form method="post" action="<?php echo get_page_base_url($lead->getEditNotesURL()); ?>" id="i_leadnotes_form" class="form-horizontal">
												
						<div class="form-group">
							<textarea class="form-control" name="notes" rows="10"><?php echo clean_field($lead->getNotes()); ?></textarea>
						</div>
						
						<input type="hidden" name="submited" value="submited" />

						<div class="form-group text-right">
							<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_leadnotes_submit"><?php echo lang('c_226'); ?></button>
						</div>
						
						</form>
						
						</div>
						
					</div>
				
				</div>
			
			</div>
		
		</div>
					
	</div>
		
</div>		
