<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_463')); ?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('leadsstatuses/create'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_464'); ?></a></p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-flash"></i>
	  <h3 class="box-title"><?php echo lang('c_463'); ?></h3>
	</div>
	
	<div class="box-body">
	
	<?php if(isset($statuses) && is_array($statuses) && count($statuses)) : ?>

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<thead>
			<th width="80%"><?php echo lang('c_461'); ?></th>
			<th width="20%"></th>
		</thead><tbody>
		
		<?php foreach($statuses as $status) : ?>
		
		<tr>
			<td><?php echo $status->getName(); ?></td>
			<td>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($status->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-xs btn-primary"><?php echo lang('c_220'); ?></a>
				<a href="<?php echo get_page_base_url($status->getDeleteURL()); ?>" class="btn btn-xs btn-danger" onclick="return confirm('<?php echo lang('c_209'); ?>')"><?php echo lang('c_50'); ?></a>
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

