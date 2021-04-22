<?php defined('BASEPATH') OR exit('No direct script access allowed');
tpl_assign("title_for_layout", "Packages"); ?>

<div class="panel panel-default">
<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('projects/add'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal"> + New Widget</a></p>
<div class="panel-heading">
	<span class="custom-font-size18">Widgets</span>
</div>

<div class="panel-body">
	
<?php if(isset($packages) && is_array($packages) && count($packages)) : ?>

	<table class="table table-hover table-bordered projects">
	
	<thead>
		<th width="20%">Photo</th>
		<th width="20%">Name</th>
		<th >Description</th>
		<th width="100"></th>
	</thead>
	
	<tbody>

	<?php foreach($packages as $package) : ?>			
	<tr>
	<td><?php echo $package->getName(); ?></td>
	<td><?php echo $package->getPricePerMonth(); ?></td>
	<td><?php echo $package->getMaxStorage(); ?></td>
	<td><a href="javascript:void();" data-url="<?php echo get_page_base_url($package->getEditURL()); ?>" data-toggle="commonmodal" class="btn btn-sm btn-success">Edit</a></td>
	</tr>
	
	<?php endforeach; ?>
	
	</tbody>
	
	</table>
	
<?php else : ?> 
<p>No record found.</p>
<?php endif; ?> 

</div>

</div>

