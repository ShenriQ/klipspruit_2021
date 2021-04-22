<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_200')); ?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('people/add/member'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_233'); ?></a></p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-address-book"></i>
	  <h3 class="box-title"><?php echo owner_company()->getName(); ?></h3>
		<div class="pull-right">
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><?php echo lang('c_234'); ?> <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url(owner_company()->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_230'); ?></a></li>
				</ul>
			</div>
		</div>  
	</div>
	
	<div class="box-body">
	
	<?php $company_members = owner_company()->getUsers();
	if(isset($company_members) && is_array($company_members) && count($company_members)) : ?>
	
		<div class="table-responsive">
		<table class="table table-striped table-bordered members">

		<thead>
			<th width="25%"><?php echo lang('c_141'); ?></th>
			<th><?php echo lang('c_1'); ?></th>
			<th><?php echo lang('c_78'); ?></th>
			<th>&mdash;</th>
		</thead>

		<tbody>
		
		<?php foreach($company_members as $company_member) :
			if($company_member->getIsActive()) : ?>			

			<tr>

				<td><b><?php echo $company_member->getName(); ?></b>
				<?php if ($company_member->getNotes() != "") : ?>
					<p class="custom-mt-5"><span class="more"><?php echo $company_member->getNotes(); ?></span></p>
				<?php endif; ?>
				</td>
				<td><?php echo $company_member->getEmail(); ?></td>
				<td><?php echo ($company_member->getPhoneNumber() != "" ? $company_member->getPhoneNumber() : "&mdash;"); ?></td>

				<td>
				<?php if($company_member->isOwner()) : ?><b><?php echo lang('c_235'); ?></b>
				<?php elseif($company_member->isMember() && $company_member->getIsAdmin()) : ?><?php echo lang('c_221'); ?>
				<?php else : ?><?php echo lang('c_28'); ?><?php endif; ?>
				
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($company_member->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_236'); ?></a></li>
							<?php if(!$company_member->isOwner()) : ?><li><a href="javascript:void();" data-url="<?php echo get_page_base_url($company_member->getAddToProjectURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_223'); ?></a><?php endif; ?>

							<?php if(!$company_member->isOwner()) : ?>
							<li class="divider"></li>
							<li><a href="javascript:void();" data-url="<?php echo get_archive_action_url($company_member, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_33'); ?></a>
							<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($company_member, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a>
							<?php endif; ?>
							
						</ul>
					</div>
				</div>
								
				</td>

			</tr>

		<?php endif;
		endforeach; ?>
		
		</tbody></table></div>
	
	<?php endif; ?>

	</div>
	
</div>