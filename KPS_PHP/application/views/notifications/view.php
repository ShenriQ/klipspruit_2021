<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_212')); 
list($date_tr_class, $log_date_value) = get_date_format_array($notification->getCreatedAt()); 
?>

<p class="text-right"><small>&larr; <a href="<?php echo get_page_base_url('notifications'); ?>"><?php echo lang('c_210'); ?></a></small></p>
<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-bell-o"></i>
	  <h3 class="box-title"><?php echo lang('c_212'); ?></h3>
	  <div class="pull-right"><a href="<?php echo get_page_base_url($notification->getRemoveURL()); ?>" onclick="return confirm('<?php echo lang('c_209'); ?>');" class="btn btn-sm btn-danger"><?php echo lang('c_208'); ?></a></div>
	</div>
	
	<div class="box-body">

		<h4><?php echo $notification->getSubject(); ?></h4>
		<span class="small"><?php echo $log_date_value; ?></span>
		<p class="custom-mt-20"><?php echo html_entity_decode($notification->getMessage()); ?></p>
	</div>

</div>

