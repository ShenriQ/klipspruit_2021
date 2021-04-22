<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_113')); ?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('departments/create'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_108'); ?></a></p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-cubes"></i>
	  <h3 class="box-title"><?php echo lang('c_112'); ?></h3>
	</div>
	
	<div class="box-body">
	
	<?php if(isset($ticket_types) && is_array($ticket_types) && count($ticket_types)) : ?>

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<thead>
			<th width="65%"><?php echo lang('c_104'); ?></th>
			<th><?php echo lang('c_114'); ?></th>
			<th width="15%"></th>
		</thead><tbody>
		
		<?php foreach($ticket_types as $ticket_type) : ?>
		
		<tr>
			<td><?php echo $ticket_type->getName(); ?></td>
			<td><?php echo ($ticket_type->getIsActive() ? '<font color="green">'.lang('c_110').'</font>' : '<font color="red">'.lang('c_111').'</font>'); ?></td>
			<td>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($ticket_type->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-xs btn-primary"><?php echo lang('c_109'); ?></a>
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

