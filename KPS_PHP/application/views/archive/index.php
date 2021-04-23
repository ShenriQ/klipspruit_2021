<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_26')); ?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-users"></i>
	  <h3 class="box-title"><?php echo lang('c_27'); ?></h3>
	</div>
	
	<div class="box-body">
		
<?php if(isset($archived_users) && is_array($archived_users) && count($archived_users)) : ?>

	<div class="table-responsive">
	<table class="table table-striped table-bordered" width="100%">
	<tbody>
	
	<?php foreach($archived_users as $archived_user) : ?>
	
	<tr>
		<td><b><?php echo $archived_user->getName(); ?></b> <small><?php echo $archived_user->isMember() ? lang('c_28') : lang('c_29'); ?></small></td>
		<td>
			<div class="pull-right">
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
					<ul class="dropdown-menu pull-right" role="menu">
						
						<li><a href="<?php echo get_archive_action_url($archived_user, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
						<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($archived_user, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
						
					</ul>
				</div>
			</div>
		</td>
	</tr>
	
	<?php endforeach; ?>
	
	</tbody>
	
	</table>
	</div>

<?php else : ?>
<p><?php echo lang('e_2'); ?></p>
<?php endif; ?>
</div></div>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-address-book"></i>
	  <h3 class="box-title"><?php echo lang('c_80'); ?></h3>
	</div>
	
	<div class="box-body">

<?php if(isset($archived_companies) && is_array($archived_companies) && count($archived_companies)) : ?>

	<div class="table-responsive">

	<table class="table table-striped table-bordered" width="100%">
	<tbody>
	
	<?php foreach($archived_companies as $archived_company) : ?>
	
	<tr>
		<td><?php echo $archived_company->getName(); ?></td>
		<td>
			<div class="pull-right">
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
					<ul class="dropdown-menu pull-right" role="menu">
						
						<li><a href="<?php echo get_archive_action_url($archived_company, 'restore'); ?>"><?php echo lang('c_30'); ?></a></li>
						<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($archived_company, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
						
					</ul>
				</div>
			</div>
		</td>
	</tr>
	
	<?php endforeach; ?>
	
	</tbody>
	
	</table>
	</div>

<?php else : ?>
<p><?php echo lang('e_2'); ?></p>
<?php endif; ?>
</div></div>