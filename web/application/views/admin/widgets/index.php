<?php defined('BASEPATH') OR exit('No direct script access allowed');
tpl_assign("title_for_layout", "Widgets"); ?>

<div class="panel panel-default">
<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('admin/widgets/add'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal"> + New Widget</a></p>
<div class="panel-heading">
	<span class="custom-font-size18">Widgets</span>
</div>

<div class="panel-body">
	
<?php if(isset($widgets) && is_array($widgets) && count($widgets)) : ?>

	<table class="table table-hover table-bordered widgets_table">
	
	<thead>
		<th width="20%">Photo</th>
		<th width="20%">Name</th>
		<th >Description</th>
		<th width="100"></th>
	</thead>
	
	<tbody>

	<?php foreach($widgets as $widget) : ?>			
	<tr>
	<td><img src="<?php echo $widget->getPhotoUrl(); ?>" /> </td>
	<td><?php echo $widget->getTitle(); ?></td>
	<td><?php echo $widget->getDescription(); ?></td>
	<td>
		<a href="javascript:void();" data-url="<?php echo get_page_base_url($widget->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-sm btn-success">
			<i class="fa fa-edit fa-fw"></i>
		</a>
		<a href="javascript:void();" data-url="<?php echo get_page_base_url($widget->getDeleteUrl()); ?>" data-toggle="commonmodal" class="btn btn-sm btn-success">
			<i class="fa fa-trash fa-fw"></i>
		</a>
	</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php else : ?> 
<p>No record found.</p>
<?php endif; ?> 

</div>

</div>

