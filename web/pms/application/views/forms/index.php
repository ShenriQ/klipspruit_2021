<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_470')); ?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('forms/create'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_467'); ?></a></p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-align-justify"></i>
	  <h3 class="box-title"><?php echo lang('c_470'); ?></h3>
	</div>
	
	<div class="box-body">
	
	<?php if(isset($forms) && is_array($forms) && count($forms)) : ?>

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<thead>
			<th width="60%"><?php echo lang('c_468'); ?></th>
			<th width="20%"><?php echo lang('c_297'); ?></th>
			<th width="20%"></th>
		</thead><tbody>
		
		<?php foreach($forms as $form) : 
		$assignee = $form->getAssignee(); ?>
		
		<tr>
			<td><?php echo $form->getTitle(); ?></td>
			<td><?php echo (isset($assignee) ? '<img src="'.$assignee->getAvatar().'" class="img-circle-sm" 
			title="'.$assignee->getName().'">' : 'None'); ?></td>
			<td>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($form->getObjectURL()); ?>" data-toggle="commonmodal" class="btn btn-xs btn-info"><?php echo lang('c_469'); ?></a>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($form->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-xs btn-primary"><?php echo lang('c_220'); ?></a>
				<a href="<?php echo get_page_base_url($form->getDeleteURL()); ?>" class="btn btn-xs btn-danger" onclick="return confirm('<?php echo lang('c_209'); ?>')"><?php echo lang('c_50'); ?></a>
			</td>
		</tr>
		
		<?php endforeach; ?>
		
		</tbody>
		
		</table>
		</div>
	<?php else: ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>
		
	</div>

</div>

