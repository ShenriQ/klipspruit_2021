<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_459')); ?>

<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('leadssources/create'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_460'); ?></a></p>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-map-pin"></i>
	  <h3 class="box-title"><?php echo lang('c_459'); ?></h3>
	</div>
	
	<div class="box-body">
	
	<?php if(isset($sources) && is_array($sources) && count($sources)) : ?>

		<div class="table-responsive">
		<table class="table table-striped table-bordered" width="100%">
		<thead>
			<th width="80%"><?php echo lang('c_461'); ?></th>
			<th width="20%"></th>
		</thead><tbody>
		
		<?php foreach($sources as $source) : ?>
		
		<tr>
			<td><?php echo $source->getName(); ?></td>
			<td>
				<a href="javascript:void();" data-url="<?php echo get_page_base_url($source->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-xs btn-primary"><?php echo lang('c_220'); ?></a>
				<a href="<?php echo get_page_base_url($source->getDeleteURL()); ?>" class="btn btn-xs btn-danger" onclick="return confirm('<?php echo lang('c_209'); ?>')"><?php echo lang('c_50'); ?></a>
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

