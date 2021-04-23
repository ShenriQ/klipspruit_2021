<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", $project->getName().' : '.lang('c_284')); ?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo lang('c_285'); ?></h3>
	</div>
	
	<div class="box-body">

	<?php  $is_private_object = true;
	tpl_assign('is_private_object', $is_private_object);
	
	tpl_assign('parent_object', $project);
	tpl_display('comments/_comment_box'); ?>

	</div>

</div>